<?php

namespace Zahler\ZahlerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Loan
 */
class Loan {
    const LOAN_ACCOUNT = 515;
    /**
     * @var integer
     */
    private $id;

    /**
     * @var \Zahler\ZahlerBundle\Entity\Transaction
     */
    private $loaTra;

    /**
     * @var \Zahler\ZahlerBundle\Entity\Person
     */
    private $loaPer;

    private $date;

    private $sourceAccount;

    private $amount;

    public function getDate() {
        return $this -> date;
    }

    public function setDate($date) {
        $this -> date = $date;

        return $this;
    }

    public function getSourceAccount() {
        return $this -> sourceAccount;
    }

    public function setSourceAccount($sourceAccount) {
        $this -> sourceAccount = $sourceAccount;

        return $this;
    }

    public function getAmount() {
        return $this -> amount;
    }

    public function setAmount($amount) {
        $this -> amount = $amount;

        return $this;
    }

    public function __toString() {
        return $this -> getId() . ' - ' . $this -> getLoaPer() -> getPerName();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this -> id;
    }

    /**
     * Set loaTra
     *
     * @param \Zahler\ZahlerBundle\Entity\Transaction $loaTra
     * @return Loan
     */
    public function setLoaTra(\Zahler\ZahlerBundle\Entity\Transaction $loaTra = null) {
        $this -> loaTra = $loaTra;

        return $this;
    }

    /**
     * Get loaTra
     *
     * @return \Zahler\ZahlerBundle\Entity\Transaction
     */
    public function getLoaTra() {
        return $this -> loaTra;
    }

    /**
     * Set loaPer
     *
     * @param \Zahler\ZahlerBundle\Entity\Person $loaPer
     * @return Loan
     */
    public function setLoaPer(\Zahler\ZahlerBundle\Entity\Person $loaPer = null) {
        $this -> loaPer = $loaPer;

        return $this;
    }

    /**
     * Get loaPer
     *
     * @return \Zahler\ZahlerBundle\Entity\Person
     */
    public function getLoaPer() {
        return $this -> loaPer;
    }

}
