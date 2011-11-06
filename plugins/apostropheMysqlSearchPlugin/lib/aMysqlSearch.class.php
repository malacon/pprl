<?php

// See aSearchService for an explanation of the public methods

// IMPORTANT: to use this to add searchability to additional model classes
// you must set up appropriate refclasses and aliases. You can do so at
// project level or in your own plugins. See schema.yml in this plugin,
// specifically aPageToASearchDocument and aMediaItemToASearchDocument, as well
// as the relations added to aPage and aMediaItem

class aMysqlSearch extends aSearchService
{
  // We need raw SQL to have any hope of acceptable performance,
  // you can't insert without array hydration in Doctrine 1. But we
  // *do* get a way to add a search to a doctrine query in which you
  // can have unrelated WHERE clauses and joins. Which means that you
  // don't have to search two different databases and then get stuck
  // with giant IN queries and awful performance. You're welcome.
  
  protected $sql;

  public function __construct()
  {
  }

  /**
   * This has to be checked when we really try to use the service
   * because there is no "after Doctrine is ready" hook from which to call
   * the constructor
   */
  protected function initSql()
  {
    if (!isset($this->sql))
    {
      $this->sql = new aMysql();
    }
  }
  
  /**
   * update(array('item' => $item, 'text' => 'this is my text', 'info' => array(some-serializable-stuff), 'culture' => 'en')) 
   *
   * You may pass item_id and item_model options instead of item if you don't have a hydrated object. item_model must
   * be the class name, not the table name
   */
  
  public function update($options)
  {
    $this->initSql();
    
    $info = $this->getDocumentInfo($options);
    $document_id = $this->getDocumentId($info);
    if (!$document_id)
    {
      $this->sql->query('INSERT INTO a_search_document (culture) VALUES (:culture)', $info);
      $document_id = $this->sql->lastInsertId();
    }
    $relationTableName = $info['item_table'] . '_to_a_search_document';
    $relatedId = $info['item_table'] . '_id';
    $q = "select * from $relationTableName atsd INNER JOIN a_search_document asd ON asd.id = atsd.a_search_document_id WHERE atsd.$relatedId = :item_id ";
    if (isset($info['culture']))
    {
      $q .= 'AND asd.culture = :culture ';
    }
    $relation = $this->sql->queryOne($q, $info);
    if (!$relation)
    {
      $this->sql->query("INSERT INTO $relationTableName ($relatedId, a_search_document_id) VALUES (:item_id, :a_search_document_id)", array('item_id' => $info['item_id'], 'a_search_document_id' => $document_id));
    }
    else
    {
      $this->sql->update($relationTableName, $relation['id'], array('a_search_document_id' => $document_id));
    }
    if (isset($options['info']))
    {
      $this->sql->update('a_search_document', $document_id, array('info' => serialize($options['info'])));
    }
    else
    {
      $this->sql->query('UPDATE a_search_document asd SET asd.info = NULL WHERE asd.id = :document_id', array('id' => $document_id));
    }
    $this->deleteUsages($document_id);
    if ((!isset($options['texts'])) || (!is_array($options['texts'])))
    {
      $options['texts'] = array(array('weight' => 1.0, 'text' => $options['text']));
    }
    foreach ($options['texts'] as $textInfo)
    {
      $weight = $textInfo['weight'];
      $text = $textInfo['text'];
      $words = $this->split($text);
      $wordWeights = array();
      // Index each word just once per text but increase the weight for subsequent usages.
      // If we reversed the order here and multiplied by something a little less than one
      // at each pass we could weight early mentions more heavily
      foreach ($words as $word)
      {
        if (!isset($wordWeights[$word]))
        {
          $wordWeights[$word] = $weight;
        }
        else
        {
          $wordWeights[$word] += $weight;
        }
      }
      foreach ($wordWeights as $word => $weight)
      {
        $wordInfo = $this->sql->queryOne('SELECT * FROM a_search_word asw WHERE text = :text', array('text' => $word));
        if (!$wordInfo)
        {
          try
          {
            $this->sql->query('INSERT INTO a_search_word (text) VALUES (:text)', array('text' => $word));
          } catch (Exception $e)
          {
            // Duplicate key errors are unfortunately common because MySQL converts
            // bad UTF8 sequences into shorter keys that wind up redundant. Until I
            // have a better idea of how to quickly validate UTF8 I need to just skip these
            continue;
          }
          $word_id = $this->sql->lastInsertId();
        }
        else
        {
          $word_id = $wordInfo['id'];
        }
        $this->sql->insert('a_search_usage', array('word_id' => $word_id, 'document_id' => $document_id, 'weight' => $weight));
      }
    }
  }

  /**
   * delete(array('item' => $item)), or item_id and item_model if you don't want to hydrate objects
   * If you don't specify a culture option, all matching documents are removed regardless of culture
   */
  public function delete($options)
  {
    $this->initSql();

    $info = $this->getDocumentInfo($options);
    $info['no-culture-means-all'] = true;
    $document_ids = $this->getDocumentIds($info);
    foreach ($document_ids as $document_id)
    {
      $this->deleteUsages($document_id);
    }
    // Careful, WHERE IN bombs on empty lists. Thanks to Paulo Ribeiro
    if (count($document_ids))
    {
      $this->sql->query('DELETE FROM a_search_document WHERE id IN :document_ids', array('document_ids' => $document_ids));
    }
  }

  /**
   * Add search to a Doctrine query. $q should be a Doctrine query object.
   * $search is the user's search text (don't pre-clean it for us, we've got it covered).
   * $options may contain 'culture'
   *
   * YOUR QUERY MUST HAVE EXPLICIT addSelect CALLS, OTHERWISE YOU WILL NOT GET RESULTS.
   * You don't want to hydrate all this extra stuff anyway, just your matching objects.
   */
  public function addSearchToQuery($q, $search, $options = array())
  {
    $alias = $q->getRootAlias();
    // Uses refclass to get to the search document
    $q->innerJoin($alias . '.aSearchDocuments asd');
    $q->innerJoin('asd.Usages asu');
    // Unicode: letters and spaces only, plus wildcard *
    $words = $this->split($search, true);
    
    $wildcards = array();
    $nwords = array();
    foreach ($words as $word)
    {
      if (preg_match('/^(.*?)\*(.*)$/', $word, $matches))
      {
        // Turn it into a LIKE pattern
        $wildcards[] = $matches[1] . '%' . $matches[2];
      }
      else
      {
        $nwords[] = $word;
      }
    }
    $words = $nwords;
    $q->innerJoin('asu.Word asw');
    $q->addGroupBy('asd.id');
    $q->addSelect('sum(asu.weight) as a_search_score, asd.info as a_search_info');
    // Build an OR of the wildcard LIKE clauses and an IN clause for the straightforward matches
    $clause = '';
    $args = array();
    if (count($wildcards))
    {
      foreach ($wildcards as $wildcard)
      {
        if (strlen($clause))
        {
          $clause .= 'OR ';
        }
        $clause .= 'asw.text LIKE ? ';
        $args[] = $wildcard;
      }
    }
    // Don't crash on an empty IN clause
    if (count($words))
    {
      if (strlen($clause))
      {
        $clause .= 'OR ';
      }
      // We'd put this in the innerJoin call but Doctrine doesn't support automatic
      // parenthesization of lists anywhere but addWhere, it seems
      $clause .= 'asw.text IN ?';
      $args[] = $words;
    }
    if (!strlen($clause))
    {
      // Searches for nothing should not return everything
      $q->andWhere('0 <> 0');
      return $q;
    }
    $q->andWhere($clause, $args);
    if (isset($options['culture']))
    {
      $q->addWhere('asd.culture = ?', $options['culture']);
    }
    $q->addHaving('a_search_score > 0');
    $q->addOrderBy('a_search_score desc');
    return $q;
  }
  
  /**
   * 1. Replace everything that isn't considered a letter or whitespace by
   * Unicode with a space. (Otherwise, we get zillions of compound words made when
   * things like hyphens were removed, instead of hits for the individual words.)
   * 
   * 2. Convert to lowercase (again, respecting Unicode).
   *
   * 3. Split into words on whitespace boundaries (according to Unicode). 
   *
   * If wildcard is true allow *
   */
  public function split($text, $wildcard = false)
  {
    if (!function_exists('mb_strtolower'))
    {
      // It's more than just mb_strtolower
      throw new sfException('You must have full unicode support in PHP to use this plugin.');
    }
    $wildcardRegex = '';
    if ($wildcard)
    {
      $wildcardRegex = '\*';
    }
    $words = mb_strtolower(preg_replace('/[^\p{L}' . $wildcardRegex . '\p{Z}]+/u', ' ', $text), 'UTF8');
    $words = preg_split('/\p{Z}+/u', $words);
    $goodWords = array();
    foreach ($words as $word)
    {
      if ($wildcard)
      {
        $wildcardRegex = '\*?';
      }
      if (!preg_match('/^\p{L}+' . $wildcardRegex . '$/u', $word))
      {
        continue;
      }
      $goodWords[] = $word;
    }
    return $goodWords;
  }
  
  public function deleteAll($options)
  {
    $this->initSql();
    $itemTable = Doctrine::getTable($options['item_model'])->getTableName();
    $relationTableName = $itemTable . '_to_a_search_document';
    $relatedId = $itemTable . '_id';

    // Clean up the usages and the documents in one fell swoop
    $q = "DELETE asu,asd FROM a_search_usage AS asu INNER JOIN a_search_document AS asd ON asu.document_id = asd.id INNER JOIN $relationTableName AS refclass ON refclass.a_search_document_id = asd.id ";
    
    if (isset($info['culture']))
    {
      $q .= 'AND asd.culture = :culture ';
    }
    
    $this->sql->query($q);
  }
  
  /**
   * Returns the document id matching the specified item_id, item_model and optionally culture.
   * If you do not specify a culture you will get a predictable result only if the 
   * document was stored without a culture
   */
  protected function getDocumentId($info)
  {
    $this->initSql();

    $q = $this->buildDocumentIdQuery($info);
    return $this->sql->queryOneScalar($q, $info);
  }

  /**
   * Returns the document ids matching the specified item_id, item_model and optionally culture.
   * If you do not specify a culture you will get all document ids for this object, with or
   * without a culture
   */
  protected function getDocumentIds($info)
  {
    $this->initSql();

    $q = $this->buildDocumentIdQuery($info);
    return $this->sql->queryScalar($q, $info);
  }

  protected function buildDocumentIdQuery($info)
  {
    $relationTableName = $info['item_table'] . '_to_a_search_document';
    $relatedId = $info['item_table'] . '_id';
    $q = "select refclass.a_search_document_id FROM $relationTableName refclass INNER JOIN a_search_document asd ON asd.id = refclass.a_search_document_id WHERE refclass.$relatedId = :item_id ";
    if (isset($info['culture']))
    {
      $q .= 'AND asd.culture = :culture ';
    }
    elseif ((!isset($info['no-culture-means-all'])) || (!$info['no-culture-means-all']))
    {
      $q .= 'AND asd.culture IS NULL';
    }
    return $q;
  }
  
  protected function getDocumentInfo($options)
  {
    if (isset($options['item_id']))
    {
      $info = array('item_id' => $options['item_id'], 'item_model' => $options['item_model'], 'item_table' => Doctrine::getTable($options['item_model'])->getTableName());
    }
    else
    {
      $item = $options['item'];
      $info = array('item_id' => $item->id, 'item_model' => get_class($item), 'item_table' => $item->getTable()->getTableName());
    }
    if (isset($options['culture']))
    {
      $info['culture'] = $options['culture'];
    }
    else
    {
      // Explicit null so we can insert it correctly for things that don't need a culture
      $info['culture'] = null;
    }
    return $info;
  }

  protected function deleteUsages($document_id)
  {
    $this->initSql();
    $this->sql->query('DELETE FROM a_search_usage WHERE document_id = :document_id', array('document_id' => $document_id));
  }
  
  public function optimize()
  {
    // Drop any words that no longer have a reference. It's OK if you never do this, but
    // your search index will be smaller and faster if you do it occasionally (nightly is nice)
    $this->initSql();
    $this->sql->query('DELETE asw FROM a_search_word AS asw LEFT JOIN a_search_usage asu ON asu.word_id = asw.id WHERE asu.id IS NULL');
  }
}
