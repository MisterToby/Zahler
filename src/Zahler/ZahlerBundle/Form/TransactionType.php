<?php

namespace Zahler\ZahlerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class TransactionType extends AbstractType
{
        /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('traDate', NULL, array('label' => 'Date: '))
            ->add('traDescription', NULL, array('label' => 'Description: '))
            ->add('traAccCredit')
            ->add('traAccDebit')
            ->add('traAmount')
        ;
            // ->add('account', 'entity', array('label' => 'Account: ', 'class' => 'ZahlerBundle:Account'))
            // ->add('debit_amount', 'text', array('label' => 'Debit amount: '))
            // ->add('credit_amount', 'text', array('label' => 'Credit amount: '))
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Zahler\ZahlerBundle\Entity\Transaction',
            'csrf_protection'   => false
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'zahler_zahlerbundle_transaction';
    }
}
