<?php

namespace Zahler\ZahlerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Debt
 */
class Debt
{
    const DEBT_ACCOUNT = 521;
    /**
     * @var string
     */
    private $debDescription;

    /**
     * @var float
     */
    private $debInterestRate;

    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Zahler\ZahlerBundle\Entity\Transaction
     */
    private $debTra;

    /**
     * @var \Zahler\ZahlerBundle\Entity\Person
     */
    private $debPer;
    
    private $date;
    
    private $destinationAccount;
    
    private $amount;
    
    public function __toString() {
        return $this -> getId() . ' - ' . $this -> getDebPer() -> getPerName();
    }
    
    public function setAmount($amount)
    {
        $this->amount = $amount;
        
        return $this;
    }
    
    public function getAmount()
    {
        return $this->amount;
    }
    
    public function setDestinationAccount($destinationAccount)
    {
        $this->destinationAccount = $destinationAccount;
        
        return $destinationAccount;
    }
    
    public function getDestinationAccount()
    {
        return $this->destinationAccount;
    }
    
    public function setDate($date)
    {
        $this->date = $date;
        
        return $this;
    }
    
    public function getDate()
    {
        return $this->date;
    }


    /**
     * Set debDescription
     *
     * @param string $debDescription
     * @return Debt
     */
    public function setDebDescription($debDescription)
    {
        $this->debDescription = $debDescription;

        return $this;
    }

    /**
     * Get debDescription
     *
     * @return string 
     */
    public function getDebDescription()
    {
        return $this->debDescription;
    }

    /**
     * Set debInterestRate
     *
     * @param float $debInterestRate
     * @return Debt
     */
    public function setDebInterestRate($debInterestRate)
    {
        $this->debInterestRate = $debInterestRate;

        return $this;
    }

    /**
     * Get debInterestRate
     *
     * @return float 
     */
    public function getDebInterestRate()
    {
        return $this->debInterestRate;
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
     * Set debTra
     *
     * @param \Zahler\ZahlerBundle\Entity\Transaction $debTra
     * @return Debt
     */
    public function setDebTra(\Zahler\ZahlerBundle\Entity\Transaction $debTra = null)
    {
        $this->debTra = $debTra;

        return $this;
    }

    /**
     * Get debTra
     *
     * @return \Zahler\ZahlerBundle\Entity\Transaction 
     */
    public function getDebTra()
    {
        return $this->debTra;
    }

    /**
     * Set debPer
     *
     * @param \Zahler\ZahlerBundle\Entity\Person $debPer
     * @return Debt
     */
    public function setDebPer(\Zahler\ZahlerBundle\Entity\Person $debPer = null)
    {
        $this->debPer = $debPer;

        return $this;
    }

    /**
     * Get debPer
     *
     * @return \Zahler\ZahlerBundle\Entity\Person 
     */
    public function getDebPer()
    {
        return $this->debPer;
    }
}
