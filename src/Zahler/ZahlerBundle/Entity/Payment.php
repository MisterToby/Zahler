<?php

namespace Zahler\ZahlerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payment
 */
class Payment
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Zahler\ZahlerBundle\Entity\Transaction
     */
    private $payTraInterest;

    /**
     * @var \Zahler\ZahlerBundle\Entity\Transaction
     */
    private $payTra;

    /**
     * @var \Zahler\ZahlerBundle\Entity\Customer
     */
    private $payLoa;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set payTraInterest
     *
     * @param \Zahler\ZahlerBundle\Entity\Transaction $payTraInterest
     * @return Payment
     */
    public function setPayTraInterest(\Zahler\ZahlerBundle\Entity\Transaction $payTraInterest = null)
    {
        $this->payTraInterest = $payTraInterest;

        return $this;
    }

    /**
     * Get payTraInterest
     *
     * @return \Zahler\ZahlerBundle\Entity\Transaction 
     */
    public function getPayTraInterest()
    {
        return $this->payTraInterest;
    }

    /**
     * Set payTra
     *
     * @param \Zahler\ZahlerBundle\Entity\Transaction $payTra
     * @return Payment
     */
    public function setPayTra(\Zahler\ZahlerBundle\Entity\Transaction $payTra = null)
    {
        $this->payTra = $payTra;

        return $this;
    }

    /**
     * Get payTra
     *
     * @return \Zahler\ZahlerBundle\Entity\Transaction 
     */
    public function getPayTra()
    {
        return $this->payTra;
    }

    /**
     * Set payLoa
     *
     * @param \Zahler\ZahlerBundle\Entity\Customer $payLoa
     * @return Payment
     */
    public function setPayLoa(\Zahler\ZahlerBundle\Entity\Customer $payLoa = null)
    {
        $this->payLoa = $payLoa;

        return $this;
    }

    /**
     * Get payLoa
     *
     * @return \Zahler\ZahlerBundle\Entity\Customer 
     */
    public function getPayLoa()
    {
        return $this->payLoa;
    }
}
