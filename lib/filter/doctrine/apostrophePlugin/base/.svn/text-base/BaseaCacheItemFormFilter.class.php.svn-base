<?php

/**
 * aCacheItem filter form base class.
 *
 * @package    asandbox
 * @subpackage filter
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseaCacheItemFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'value'    => new sfWidgetFormFilterInput(),
      'timeout'  => new sfWidgetFormFilterInput(),
      'last_mod' => new sfWidgetFormFilterInput(),
    ));

    $this->setValidators(array(
      'value'    => new sfValidatorPass(array('required' => false)),
      'timeout'  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'last_mod' => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
    ));

    $this->widgetSchema->setNameFormat('a_cache_item_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'aCacheItem';
  }

  public function getFields()
  {
    return array(
      'k'        => 'Text',
      'value'    => 'Text',
      'timeout'  => 'Number',
      'last_mod' => 'Number',
    );
  }
}
