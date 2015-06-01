<?php

namespace Zahler\ZahlerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class LoanType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date', 'date', array('input' => 'datetime'))
            ->add('sourceAccount', 'entity', array('class' => 'ZahlerBundle:Account'))
            ->add('loaPer')
            ->add('amount', 'money', array('currency' => 'COP'))
            ->add('loaInterestRate')
            ->add('loaDescription')
        ;
            // ->add('loaTra')
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Zahler\ZahlerBundle\Entity\Loan',
            'csrf_protection'   => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'zahler_zahlerbundle_loan';
    }
}
