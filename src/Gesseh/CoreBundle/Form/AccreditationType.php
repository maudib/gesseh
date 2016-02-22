<?php

/**
 * This file is part of GESSEH project
 *
 * @author: Pierre-François ANGRAND <gesseh@medlibre.fr>
 * @copyright: Copyright 2016 Pierre-François Angrand
 * @license: GPLv3
 * See LICENSE file or http://www.gnu.org/licenses/gpl.html
 */

namespace Gesseh\CoreBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

/**
 * Accreditation Type
 */
class AccreditationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('begin')
                ->add('end')
                ->add('sector')
                ->add('supervisor')
                ->add('user')
                ->add('comment')
                ->add('Enregister', 'submit')
        ;
    }

    public function getName()
    {
        return 'gesseh_corebundle_accreditationtype';
    }

    public function configureOptions(\Symfony\Component\OptionsResolver\OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Gesseh\CoreBundle\Entity\Accreditation',
        ));

        $resolver->setAllowedValues(array(
        ));
    }
}
