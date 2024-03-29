<?php

/**
 * BaseaMediaItemToCategory
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $media_item_id
 * @property integer $category_id
 * @property aMediaItem $aMediaItem
 * @property aCategory $aCategory
 * 
 * @method integer              getMediaItemId()   Returns the current record's "media_item_id" value
 * @method integer              getCategoryId()    Returns the current record's "category_id" value
 * @method aMediaItem           getAMediaItem()    Returns the current record's "aMediaItem" value
 * @method aCategory            getACategory()     Returns the current record's "aCategory" value
 * @method aMediaItemToCategory setMediaItemId()   Sets the current record's "media_item_id" value
 * @method aMediaItemToCategory setCategoryId()    Sets the current record's "category_id" value
 * @method aMediaItemToCategory setAMediaItem()    Sets the current record's "aMediaItem" value
 * @method aMediaItemToCategory setACategory()     Sets the current record's "aCategory" value
 * 
 * @package    pprl
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseaMediaItemToCategory extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('a_media_item_to_category');
        $this->hasColumn('media_item_id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             ));
        $this->hasColumn('category_id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             ));

        $this->option('symfony', array(
             'form' => false,
             'filter' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('aMediaItem', array(
             'local' => 'media_item_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));

        $this->hasOne('aCategory', array(
             'local' => 'category_id',
             'foreign' => 'id',
             'onDelete' => 'CASCADE'));
    }
}