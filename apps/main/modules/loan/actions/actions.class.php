<?php

/**
 * annuity actions.
 *
 * @package    zahler
 * @subpackage annuity
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class loanActions extends sfActions
{
	public function executeDelete(sfWebRequest $request) {
		try {
			$loanId = $request->getParameter('id');
			$loan = LoanPeer::retrieveByPK($loanId);
			$transaction = $loan->getTransaction();
			$transaction->delete();
			$loan->delete();
		} catch(Exception $e) {
			return $this->renderText($e);
		}
		return $this->renderText('ok');
	}
	public function executeCreate(sfWebRequest $request) {
		try {
			$loan = null;
			$transaction = null;
			if($request->getParameter('loan_id')!='') {
				$loan = LoanPeer::retrieveByPK($request->getParameter('loan_id'));
				$transaction = $loan->getTransaction();
			}
			else {
				$loan = new Loan();
				$transaction = new Transaction();
				$transaction->setAtrReference('');
				$transaction->setAtrDescription('Loan');
			}

			$transaction->setAtrDate($request->getParameter('date'));
			$transaction->setAtrAccIdDebit($request->getParameter('loans_account_id'));
			$transaction->setAtrAccIdCredit($request->getParameter('source_account_id'));
			$transaction->setAtrValue($request->getParameter('amount'));

			$loan->setLoaConId($request->getParameter('contact_id'));
			$loan->setTransaction($transaction);
			$loan->setLoaInterestRate($request->getParameter('interest_rate'));
			$loan->save();
		} catch(Exception $e) {
			return $this->renderText($e);
		}
		return $this->renderText('ok');
	}
	public function executeGetList(sfWebRequest $request) {
		$loans = LoanPeer::doSelect(new Criteria());

		$result = array();
		$data = array();

		foreach($loans as $loan) {
			$fields = array();

			//			$loan = new Loan();

			$fields['loan_id'] = $loan->getLoaId();
			$fields['contact_id'] = $loan->getLoaConId();
			$fields['transaction_id'] = $loan->getLoaAtrId();
			$fields['interest_rate'] = $loan->getLoaInterestRate();

			$transaction = $loan->getTransaction();

			$fields['date'] = $transaction->getAtrDate('d-m-Y');
			$fields['amount'] = $transaction->getAtrValue();
			$fields['source_account_id'] = $transaction->getAtrAccIdCredit();
			$fields['loans_account_id'] = $transaction->getAtrAccIdDebit();

			$data[] = $fields;
		}

		$result['data'] = $data;
		return $this->renderText(json_encode($result));
	}
}
