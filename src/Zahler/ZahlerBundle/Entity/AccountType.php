<?php

namespace Zahler\ZahlerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * AccountType
 */
class AccountType
{
    /**
     * @var string
     */
    private $actName;

    /**
     * @var integer
     */
    private $actId;
	
    public function __toString() {
	return $this->actName;
    }

    /**
     * Set actName
     *
     * @param string $actName
     * @return AccountType
     */
    public function setActName($actName)
    {
        $this->actName = $actName;

        return $this;
    }

    /**
     * Get actName
     *
     * @return string 
     */
    public function getActName()
    {
        return $this->actName;
    }

    /**
     * Get actId
     *
     * @return integer 
     */
    public function getActId()
    {
        return $this->actId;
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
