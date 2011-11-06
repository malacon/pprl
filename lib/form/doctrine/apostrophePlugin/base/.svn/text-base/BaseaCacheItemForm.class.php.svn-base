<?php

/**
 * aCacheItem form base class.
 *
 * @method aCacheItem getObject() Returns the current form's model object
 *
 * @package    asandbox
 * @subpackage form
 * @author     Your name here
 * @version    SVN: $Id: sfDoctrineFormGeneratedTemplate.php 29553 2010-05-20 14:33:00Z Kris.Wallsmith $
 */
abstract class BaseaCacheItemForm extends BaseFormDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'k'        => new sfWidgetFormInputHidden(),
      'value'    => new sfWidgetFormTextarea(),
      'timeout'  => new sfWidgetFormInputText(),
      'last_mod' => new sfWidgetFormInputText(),
    ));

    $this->setValidators(array(
      'k'        => new sfValidatorChoice(array('choices' => array($this->getObject()->get('k')), 'empty_value' => $this->getObject()->get('k'), 'required' => false)),
      'value'    => new sfValidatorString(array('required' => false)),
      'timeout'  => new sfValidatorInteger(array('required' => false)),
      'last_mod' => new sfValidatorInteger(array('required' => false)),
    ));

    $this->widgetSchema->setNameFormat('a_cache_item[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'aCacheItem';
  }

}
