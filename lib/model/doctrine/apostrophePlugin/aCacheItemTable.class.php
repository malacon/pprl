<?php

/**
 * aCacheItemTable
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class aCacheItemTable extends PluginaCacheItemTable
{
    /**
     * Returns an instance of this class.
     *
     * @return object aCacheItemTable
     */
    public static function getInstance()
    {
        return Doctrine_Core::getTable('aCacheItem');
    }
}