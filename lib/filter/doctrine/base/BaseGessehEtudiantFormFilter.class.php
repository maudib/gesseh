<?php

/**
 * GessehEtudiant filter form base class.
 *
 * @package    gesseh
 * @subpackage filter
 * @author     Pierre-François Pilou Angrand <tmp@angrand.fr>
 * @version    SVN: $Id: sfDoctrineFormFilterGeneratedTemplate.php 29570 2010-05-21 14:49:47Z Kris.Wallsmith $
 */
abstract class BaseGessehEtudiantFormFilter extends BaseFormFilterDoctrine
{
  public function setup()
  {
    $this->setWidgets(array(
      'promo_id'    => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('GessehPromo'), 'add_empty' => true)),
      'annee_promo' => new sfWidgetFormFilterInput(),
      'classement'  => new sfWidgetFormFilterInput(array('with_empty' => false)),
      'tel'         => new sfWidgetFormFilterInput(),
      'naissance'   => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate())),
      'anonyme'     => new sfWidgetFormChoice(array('choices' => array('' => 'yes or no', 1 => 'yes', 0 => 'no'))),
      'utilisateur' => new sfWidgetFormDoctrineChoice(array('model' => $this->getRelatedModelName('sfGuardUser'), 'add_empty' => true)),
      'updated_at'  => new sfWidgetFormFilterDate(array('from_date' => new sfWidgetFormDate(), 'to_date' => new sfWidgetFormDate(), 'with_empty' => false)),
    ));

    $this->setValidators(array(
      'promo_id'    => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('GessehPromo'), 'column' => 'id')),
      'annee_promo' => new sfValidatorPass(array('required' => false)),
      'classement'  => new sfValidatorSchemaFilter('text', new sfValidatorInteger(array('required' => false))),
      'tel'         => new sfValidatorPass(array('required' => false)),
      'naissance'   => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDate(array('required' => false)), 'to_date' => new sfValidatorDateTime(array('required' => false)))),
      'anonyme'     => new sfValidatorChoice(array('required' => false, 'choices' => array('', 1, 0))),
      'utilisateur' => new sfValidatorDoctrineChoice(array('required' => false, 'model' => $this->getRelatedModelName('sfGuardUser'), 'column' => 'id')),
      'updated_at'  => new sfValidatorDateRange(array('required' => false, 'from_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 00:00:00')), 'to_date' => new sfValidatorDateTime(array('required' => false, 'datetime_output' => 'Y-m-d 23:59:59')))),
    ));

    $this->widgetSchema->setNameFormat('gesseh_etudiant_filters[%s]');

    $this->errorSchema = new sfValidatorErrorSchema($this->validatorSchema);

    $this->setupInheritance();

    parent::setup();
  }

  public function getModelName()
  {
    return 'GessehEtudiant';
  }

  public function getFields()
  {
    return array(
      'id'          => 'Number',
      'promo_id'    => 'ForeignKey',
      'annee_promo' => 'Text',
      'classement'  => 'Number',
      'tel'         => 'Text',
      'naissance'   => 'Date',
      'anonyme'     => 'Boolean',
      'utilisateur' => 'ForeignKey',
      'updated_at'  => 'Date',
    );
  }
}
