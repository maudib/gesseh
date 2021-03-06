<?php

/**
 * This file is part of GESSEH project
 *
 * @author: Pierre-François ANGRAND <gesseh@medlibre.fr>
 * @copyright: Copyright 2013 Pierre-François Angrand
 * @license: GPLv3
 * See LICENSE file or http://www.gnu.org/licenses/gpl.html
 */

namespace Gesseh\SimulationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * SimulPeriodType
 */
class SimulPeriodType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder->add('begin')
            ->add('end')
            ->add('Enregistrer', 'submit')
    ;
  }

  public function getName()
  {
    return 'gesseh_simulationbundle_simulperiodtype';
  }

  public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
  {
    $resolver->setDefaults(array(
        'data_class' => 'Gesseh\SimulationBundle\Entity\SimulPeriod',
    ));

    $resolver->setAllowedValues(array(
    ));
  }
}
