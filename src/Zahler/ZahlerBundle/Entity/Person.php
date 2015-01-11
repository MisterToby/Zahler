<?php

namespace Zahler\ZahlerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Person
 */
class Person {
    /**
     * @var string
     */
    private $perName;

    /**
     * @var integer
     */
    private $id;

    public function __toString() {
        return $this -> perName;
    }

    /**
     * Set perName
     *
     * @param string $perName
     * @return Person
     */
    public function setPerName($perName) {
        $this -> perName = $perName;

        return $this;
    }

    /**
     * Get perName
     *
     * @return string
     */
    public function getPerName() {
        return $this -> perName;
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
