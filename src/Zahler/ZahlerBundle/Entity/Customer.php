<?php

namespace Zahler\ZahlerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Customer
 */
class Customer {
    /**
     * @var string
     */
    private $cusName;

    /**
     * @var integer
     */
    private $id;

    public function __toString() {
        return $this -> cusName;
    }

    /**
     * Set cusName
     *
     * @param string $cusName
     * @return Customer
     */
    public function setCusName($cusName) {
        $this -> cusName = $cusName;

        return $this;
    }

    /**
     * Get cusName
     *
     * @return string
     */
    public function getCusName() {
        return $this -> cusName;
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this -> id;
    }

}
