<?php

/**
 * annuity actions.
 *
 * @package    zahler
 * @subpackage annuity
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class annuityActions extends sfActions
{
	public function executeDelete(sfWebRequest $request) {
		try {
			$annuityId = $request->getParameter('id');
			$annuity = AnnuityPeer::retrieveByPK($annuityId);
			$transaction = $annuity->getTransaction();
			$transaction->delete();
			$annuity->delete();
		} catch(Exception $e) {
			return $this->renderText($e);
		}
		return $this->renderText('ok');
	}
	public function executeCreate(sfWebRequest $request) {
		try {
			$annuity = null;
			$transaction = null;
			if($request->getParameter('annuity_id')!='') {
				$annuity = AnnuityPeer::retrieveByPK($request->getParameter('annuity_id'));
				$transaction = $annuity->getTransaction();
			}
			else {
				$annuity = new Annuity();
				$transaction = new Transaction();
				$transaction->setAtrReference('');
				$transaction->setAtrDescription('Loan');
			}

			$transaction->setAtrDate($request->getParameter('date'));
			$transaction->setAtrAccIdDebit($request->getParameter('loans_account_id'));
			$transaction->setAtrAccIdCredit($request->getParameter('source_account_id'));
			$transaction->setAtrValue($request->getParameter('loan_amount'));

			$annuity->setAnnConId($request->getParameter('contact_id'));
			$annuity->setTransaction($transaction);
			$annuity->setAnnInterestRate($request->getParameter('interest_rate'));
			$annuity->setAnnLoanTerm($request->getParameter('loan_term'));
			$annuity->save();
		} catch(Exception $e) {
			return $this->renderText($e);
		}
		return $this->renderText('ok');
	}
	public function executeGetList(sfWebRequest $request) {
		$annuities = AnnuityPeer::doSelect(new Criteria());

		$result = array();
		$data = array();

		foreach($annuities as $annuity) {
			$fields = array();

			$fields['annuity_id'] = $annuity->getAnnId();
			$fields['contact_id'] = $annuity->getAnnConId();
			$fields['transaction_id'] = $annuity->getAnnAtrId();
			$fields['interest_rate'] = $annuity->getAnnInterestRate();
			$fields['loan_term'] = $annuity->getAnnLoanTerm();

			$transaction = $annuity->getTransaction();

			$fields['date'] = $transaction->getAtrDate('d-m-Y');
			$fields['loan_amount'] = $transaction->getAtrValue();
			$fields['source_account_id'] = $transaction->getAtrAccIdCredit();
			$fields['loans_account_id'] = $transaction->getAtrAccIdDebit();

			$data[] = $fields;
		}

		$result['data'] = $data;
		return $this->renderText(json_encode($result));
	}
}
