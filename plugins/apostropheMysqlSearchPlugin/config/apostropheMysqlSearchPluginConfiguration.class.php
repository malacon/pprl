<?php

class apostropheMysqlSearchPluginConfiguration extends sfPluginConfiguration
{
  static $first = true;
  public function configure()
  {
    if (apostropheMysqlSearchPluginConfiguration::$first)
    {
      $this->dispatcher->connect('command.post_command', array($this,  
        'listenToCommandPostCommandEvent'));
      apostropheMysqlSearchPluginConfiguration::$first = false;
    }
  }
  
  // command.post_command
  public function listenToCommandPostCommandEvent(sfEvent $event)
  {
    $task = $event->getSubject();

    if ($task->getFullName() === 'apostrophe:migrate')
    {
      apostropheMysqlSearchPluginConfiguration::migrate();
    }
  }

  static public function migrate($sql = null)
  {
    if (is_null($sql))
    {
      $sql = new aMysql();
    }
    
    if (!$sql->tableExists('a_search_document'))
    {
      $sql->sql(array(
"       CREATE TABLE `a_search_document` (
          `id` bigint(20) NOT NULL AUTO_INCREMENT,
          `culture` varchar(5) DEFAULT NULL,
          `info` longtext,
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;",
"     CREATE TABLE `a_search_word` (
        `id` bigint(20) NOT NULL AUTO_INCREMENT,
        `text` varchar(100) NOT NULL,
        `refcount` bigint(20) NOT NULL,
        PRIMARY KEY (`id`),
        UNIQUE KEY `text` (`text`)
      ) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;",
"     CREATE TABLE `a_search_usage` (
        `id` bigint(20) NOT NULL AUTO_INCREMENT,
        `document_id` bigint(20) NOT NULL,
        `word_id` bigint(20) NOT NULL,
        `weight` float(18,2) NOT NULL,
        PRIMARY KEY (`id`),
        KEY `document_id_idx` (`document_id`),
        KEY `word_id_idx` (`word_id`),
        CONSTRAINT `a_search_usage_document_id_a_search_document_id` FOREIGN KEY (`document_id`) REFERENCES `a_search_document` (`id`) ON DELETE CASCADE,
        CONSTRAINT `a_search_usage_word_id_a_search_word_id` FOREIGN KEY (`word_id`) REFERENCES `a_search_word` (`id`) ON DELETE CASCADE
      ) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8;",
"     CREATE TABLE `a_page_to_a_search_document` (
        `id` bigint(20) NOT NULL AUTO_INCREMENT,
        `a_search_document_id` bigint(20) DEFAULT NULL,
        `a_page_id` bigint(20) DEFAULT NULL,
        PRIMARY KEY (`id`),
        KEY `a_search_document_id_idx` (`a_search_document_id`),
        KEY `a_page_id_idx` (`a_page_id`),
        CONSTRAINT `a_page_to_a_search_document_a_page_id_a_page_id` FOREIGN KEY (`a_page_id`) REFERENCES `a_page` (`id`) ON DELETE CASCADE,
        CONSTRAINT `aaai_2` FOREIGN KEY (`a_search_document_id`) REFERENCES `a_search_document` (`id`) ON DELETE CASCADE
      ) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8",
"    CREATE TABLE `a_media_item_to_a_search_document` (
      `id` bigint(20) NOT NULL AUTO_INCREMENT,
      `a_search_document_id` bigint(20) DEFAULT NULL,
      `a_media_item_id` bigint(20) DEFAULT NULL,
      PRIMARY KEY (`id`),
      KEY `a_search_document_id_idx` (`a_search_document_id`),
      KEY `a_media_item_id_idx` (`a_media_item_id`),
      CONSTRAINT `aaai` FOREIGN KEY (`a_search_document_id`) REFERENCES `a_search_document` (`id`) ON DELETE CASCADE,
      CONSTRAINT `aaai_1` FOREIGN KEY (`a_media_item_id`) REFERENCES `a_media_item` (`id`) ON DELETE CASCADE
    ) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8"
      ));
      return true;
    }
    return false;
  }
}
