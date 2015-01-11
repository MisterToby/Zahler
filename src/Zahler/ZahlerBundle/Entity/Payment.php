<?php

namespace Zahler\ZahlerBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Payment
 */
class Payment {
    const INTEREST_ACCOUNT = 527;
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
     * @var \Zahler\ZahlerBundle\Entity\Loan
     */
    private $payLoa;

    private $amount;

    private $interest;

    private $date;

    private $destinationAccount;

    public function getAmount() {
        return $this -> amount;
    }

    public function setAmount($amount) {
        $this -> amount = $amount;

        return $this;
    }

    public function getInterest() {
        return $this -> interest;
    }

    public function setInterest($interest) {
        $this -> interest = $interest;

        return $this;
    }

    public function getDate() {
        return $this -> date;
    }

    public function setDate($date) {
        $this -> date = $date;

        return $this;
    }

    public function getDestinationAccount() {
        return $this -> destinationAccount;
    }

    public function setDestinationAccount($destinationAccount) {
        $this -> destinationAccount = $destinationAccount;

        return $destinationAccount;
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
     * Set payTraInterest
     *
     * @param \Zahler\ZahlerBundle\Entity\Transaction $payTraInterest
     * @return Payment
     */
    public function setPayTraInterest(\Zahler\ZahlerBundle\Entity\Transaction $payTraInterest = null) {
        $this -> payTraInterest = $payTraInterest;

        return $this;
    }

    /**
     * Get payTraInterest
     *
     * @return \Zahler\ZahlerBundle\Entity\Transaction
     */
    public function getPayTraInterest() {
        return $this -> payTraInterest;
    }

    /**
     * Set payTra
     *
     * @param \Zahler\ZahlerBundle\Entity\Transaction $payTra
     * @return Payment
     */
    public function setPayTra(\Zahler\ZahlerBundle\Entity\Transaction $payTra = null) {
        $this -> payTra = $payTra;

        return $this;
    }

    /**
     * Get payTra
     *
     * @return \Zahler\ZahlerBundle\Entity\Transaction
     */
    public function getPayTra() {
        return $this -> payTra;
    }

    /**
     * Set payLoa
     *
     * @param \Zahler\ZahlerBundle\Entity\Loan $payLoa
     * @return Payment
     */
    public function setPayLoa(\Zahler\ZahlerBundle\Entity\Loan $payLoa = null) {
        $this -> payLoa = $payLoa;

        return $this;
    }

    /**
     * Get payLoa
     *
     * @return \Zahler\ZahlerBundle\Entity\Loan
     */
    public function getPayLoa() {
        return $this -> payLoa;
    }

}
