<?php

namespace Zahler\ZahlerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Loan
 */
class Loan
{
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Zahler\ZahlerBundle\Entity\Transaction
     */
    private $loaTra;

    /**
     * @var \Zahler\ZahlerBundle\Entity\Customer
     */
    private $loaCus;


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
     * Set loaTra
     *
     * @param \Zahler\ZahlerBundle\Entity\Transaction $loaTra
     * @return Loan
     */
    public function setLoaTra(\Zahler\ZahlerBundle\Entity\Transaction $loaTra = null)
    {
        $this->loaTra = $loaTra;

        return $this;
    }

    /**
     * Get loaTra
     *
     * @return \Zahler\ZahlerBundle\Entity\Transaction 
     */
    public function getLoaTra()
    {
        return $this->loaTra;
    }

    /**
     * Set loaCus
     *
     * @param \Zahler\ZahlerBundle\Entity\Customer $loaCus
     * @return Loan
     */
    public function setLoaCus(\Zahler\ZahlerBundle\Entity\Customer $loaCus = null)
    {
        $this->loaCus = $loaCus;

        return $this;
    }

    /**
     * Get loaCus
     *
     * @return \Zahler\ZahlerBundle\Entity\Customer 
     */
    public function getLoaCus()
    {
        return $this->loaCus;
    }
}
