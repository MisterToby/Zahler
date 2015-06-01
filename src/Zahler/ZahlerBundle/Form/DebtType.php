<?php

namespace Zahler\ZahlerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DebtType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', 'date', array('input' => 'datetime'))
            ->add('destinationAccount', 'entity', array('class' => 'ZahlerBundle:Account'))
            ->add('debPer')
            ->add('amount', 'money', array('currency' => 'COP'))
            ->add('debInterestRate')
            ->add('debDescription')
        ;
            // ->add('debTra')
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Zahler\ZahlerBundle\Entity\Debt',
            'csrf_protection'   => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'zahler_zahlerbundle_debt';
    }
}
