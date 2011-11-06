<?php

/**
 * BaseaGroupAccess
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $page_id
 * @property string $privilege
 * @property integer $group_id
 * @property sfGuardGroup $Group
 * @property aPage $Page
 * 
 * @method integer      getPageId()    Returns the current record's "page_id" value
 * @method string       getPrivilege() Returns the current record's "privilege" value
 * @method integer      getGroupId()   Returns the current record's "group_id" value
 * @method sfGuardGroup getGroup()     Returns the current record's "Group" value
 * @method aPage        getPage()      Returns the current record's "Page" value
 * @method aGroupAccess setPageId()    Sets the current record's "page_id" value
 * @method aGroupAccess setPrivilege() Sets the current record's "privilege" value
 * @method aGroupAccess setGroupId()   Sets the current record's "group_id" value
 * @method aGroupAccess setGroup()     Sets the current record's "Group" value
 * @method aGroupAccess setPage()      Sets the current record's "Page" value
 * 
 * @package    pprl
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseaGroupAccess extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('a_group_access');
        $this->hasColumn('page_id', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('privilege', 'string', 100, array(
             'type' => 'string',
             'length' => 100,
             ));
        $this->hasColumn('group_id', 'integer', null, array(
             'type' => 'integer',
             ));


        $this->index('pageindex', array(
             'fields' => 
             array(
              0 => 'page_id',
             ),
             ));
        $this->option('symfony', array(
             'form' => false,
             'filter' => false,
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('sfGuardGroup as Group', array(
             'local' => 'group_id',
             'foreign' => 'id',
             'onDelete' => 'cascade'));

        $this->hasOne('aPage as Page', array(
             'local' => 'page_id',
             'foreign' => 'id',
             'onDelete' => 'cascade'));
    }
}