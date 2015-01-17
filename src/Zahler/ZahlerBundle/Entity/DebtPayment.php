<?php

namespace Zahler\ZahlerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * DebtPayment
 */
class DebtPayment
{
    const INTEREST_ACCOUNT = 570;
    
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Zahler\ZahlerBundle\Entity\Transaction
     */
    private $depTraInterest;

    /**
     * @var \Zahler\ZahlerBundle\Entity\Transaction
     */
    private $depTra;

    /**
     * @var \Zahler\ZahlerBundle\Entity\Loan
     */
    private $depLoa;
    
    private $date;
    
    private $sourceAccount;
    
    private $amount;
    
    private $interest;
    
    public function getInterest() {
        return $this -> interest;
    }

    public function setInterest($interest) {
        $this -> interest = $interest;

        return $this;
    }
    
    public function getAmount() {
        return $this -> amount;
    }

    public function setAmount($amount) {
        $this -> amount = $amount;

        return $this;
    }
    
    public function setSourceAccount($sourceAccount)
    {
        $this->sourceAccount = $sourceAccount;
        
        return $this->sourceAccount;
    }
    
    public function getSourceAccount()
    {
        return $this->sourceAccount;
    }
    
    public function setDate($date)
    {
        $this->date = $date;
        
        return $date;
    }
    
    public function getDate()
    {
        return $this->date;
    }


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
     * Set depTraInterest
     *
     * @param \Zahler\ZahlerBundle\Entity\Transaction $depTraInterest
     * @return DebtPayment
     */
    public function setDepTraInterest(\Zahler\ZahlerBundle\Entity\Transaction $depTraInterest = null)
    {
        $this->depTraInterest = $depTraInterest;

        return $this;
    }

    /**
     * Get depTraInterest
     *
     * @return \Zahler\ZahlerBundle\Entity\Transaction 
     */
    public function getDepTraInterest()
    {
        return $this->depTraInterest;
    }

    /**
     * Set depTra
     *
     * @param \Zahler\ZahlerBundle\Entity\Transaction $depTra
     * @return DebtPayment
     */
    public function setDepTra(\Zahler\ZahlerBundle\Entity\Transaction $depTra = null)
    {
        $this->depTra = $depTra;

        return $this;
    }

    /**
     * Get depTra
     *
     * @return \Zahler\ZahlerBundle\Entity\Transaction 
     */
    public function getDepTra()
    {
        return $this->depTra;
    }

    /**
     * Set depLoa
     *
     * @param \Zahler\ZahlerBundle\Entity\Loan $depLoa
     * @return DebtPayment
     */
    public function setDepLoa(\Zahler\ZahlerBundle\Entity\Loan $depLoa = null)
    {
        $this->depLoa = $depLoa;

        return $this;
    }

    /**
     * Get depLoa
     *
     * @return \Zahler\ZahlerBundle\Entity\Loan 
     */
    public function getDepLoa()
    {
        return $this->depLoa;
    }
    /**
     * @var \Zahler\ZahlerBundle\Entity\Debt
     */
    private $depDeb;


    /**
     * Set depDeb
     *
     * @param \Zahler\ZahlerBundle\Entity\Debt $depDeb
     * @return DebtPayment
     */
    public function setDepDeb(\Zahler\ZahlerBundle\Entity\Debt $depDeb = null)
    {
        $this->depDeb = $depDeb;

        return $this;
    }

    /**
     * Get depDeb
     *
     * @return \Zahler\ZahlerBundle\Entity\Debt 
     */
    public function getDepDeb()
    {
        return $this->depDeb;
    }
}
