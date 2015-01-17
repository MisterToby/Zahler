<?php

namespace Zahler\ZahlerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class DebtPaymentType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', 'date', array('input' => 'datetime'))
            ->add('depDeb')
            ->add('sourceAccount', 'entity', array('class' => 'ZahlerBundle:Account'))
            ->add('amount', 'money', array('currency' => 'COP'))
            ->add('interest', 'money', array('currency' => 'COP'))
        ;
            // ->add('depTraInterest')
            // ->add('depTra')
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Zahler\ZahlerBundle\Entity\DebtPayment'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'zahler_zahlerbundle_debtpayment';
    }
}
