<?php


/**
 * Skeleton subclass for representing a row from the 'account' table.
 *
 *
 *
 * This class was autogenerated by Propel 1.4.2 on:
 *
 * Sun Oct 24 11:48:20 2010
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 * @package    lib.model
 */
class Account extends BaseAccount {
	public function __toString() {
		return $this->getAccName();
	}
} // Account