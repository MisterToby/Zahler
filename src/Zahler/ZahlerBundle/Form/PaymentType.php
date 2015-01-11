<?php

namespace Zahler\ZahlerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class PaymentType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', 'date', array('input' => 'datetime'))
            ->add('payLoa')
            ->add('destinationAccount', 'entity', array('class' => 'ZahlerBundle:Account'))
            ->add('amount', 'money', array('currency' => 'COP'))
            ->add('interest', 'money', array('currency' => 'COP'))
        ;
            // ->add('payTraInterest')
            // ->add('payTra')
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Zahler\ZahlerBundle\Entity\Payment'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'zahler_zahlerbundle_payment';
    }
}
