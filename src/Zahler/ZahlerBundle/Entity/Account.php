<?php

namespace Zahler\ZahlerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Account
 */
class Account
{
    /**
     * @var string
     */
    private $accName;

    /**
     * @var integer
     */
    private $accId;

    /**
     * @var \Zahler\ZahlerBundle\Entity\AccountType
     */
    private $accActType;

    public function __toString() {
	return $this->accName;
    }

    /**
     * Set accName
     *
     * @param string $accName
     * @return Account
     */
    public function setAccName($accName)
    {
        $this->accName = $accName;

        return $this;
    }

    /**
     * Get accName
     *
     * @return string 
     */
    public function getAccName()
    {
        return $this->accName;
    }

    /**
     * Get accId
     *
     * @return integer 
     */
    public function getAccId()
    {
        return $this->accId;
    }

    /**
     * Set accActType
     *
     * @param \Zahler\ZahlerBundle\Entity\AccountType $accActType
     * @return Account
     */
    public function setAccActType(\Zahler\ZahlerBundle\Entity\AccountType $accActType = null)
    {
        $this->accActType = $accActType;

        return $this;
    }

    /**
     * Get accActType
     *
     * @return \Zahler\ZahlerBundle\Entity\AccountType 
     */
    public function getAccActType()
    {
        return $this->accActType;
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
