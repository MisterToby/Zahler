<?php

namespace Zahler\ZahlerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Transaction
 */
class Transaction
{
    /**
     * @var \DateTime
     */
    private $traDate;

    /**
     * @var string
     */
    private $traDescription;

    /**
     * @var float
     */
    private $traAmount;

    /**
     * @var integer
     */
    private $traId;

    /**
     * @var \Zahler\ZahlerBundle\Entity\Account
     */
    private $traAccCredit;

    /**
     * @var \Zahler\ZahlerBundle\Entity\Account
     */
    private $traAccDebit;


    /**
     * Set traDate
     *
     * @param \DateTime $traDate
     * @return Transaction
     */
    public function setTraDate($traDate)
    {
        $this->traDate = $traDate;

        return $this;
    }

    /**
     * Get traDate
     *
     * @return \DateTime 
     */
    public function getTraDate()
    {
        return $this->traDate;
    }

    /**
     * Set traDescription
     *
     * @param string $traDescription
     * @return Transaction
     */
    public function setTraDescription($traDescription)
    {
        $this->traDescription = $traDescription;

        return $this;
    }

    /**
     * Get traDescription
     *
     * @return string 
     */
    public function getTraDescription()
    {
        return $this->traDescription;
    }

    /**
     * Set traAmount
     *
     * @param float $traAmount
     * @return Transaction
     */
    public function setTraAmount($traAmount)
    {
        $this->traAmount = $traAmount;

        return $this;
    }

    /**
     * Get traAmount
     *
     * @return float 
     */
    public function getTraAmount()
    {
        return $this->traAmount;
    }

    /**
     * Get traId
     *
     * @return integer 
     */
    public function getTraId()
    {
        return $this->traId;
    }

    /**
     * Set traAccCredit
     *
     * @param \Zahler\ZahlerBundle\Entity\Account $traAccCredit
     * @return Transaction
     */
    public function setTraAccCredit(\Zahler\ZahlerBundle\Entity\Account $traAccCredit = null)
    {
        $this->traAccCredit = $traAccCredit;

        return $this;
    }

    /**
     * Get traAccCredit
     *
     * @return \Zahler\ZahlerBundle\Entity\Account 
     */
    public function getTraAccCredit()
    {
        return $this->traAccCredit;
    }

    /**
     * Set traAccDebit
     *
     * @param \Zahler\ZahlerBundle\Entity\Account $traAccDebit
     * @return Transaction
     */
    public function setTraAccDebit(\Zahler\ZahlerBundle\Entity\Account $traAccDebit = null)
    {
        $this->traAccDebit = $traAccDebit;

        return $this;
    }

    /**
     * Get traAccDebit
     *
     * @return \Zahler\ZahlerBundle\Entity\Account 
     */
    public function getTraAccDebit()
    {
        return $this->traAccDebit;
    }
    /**
     * @var integer
     */
    private $id;


    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
}