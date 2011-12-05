<?php

/**
 * annuity actions.
 *
 * @package    zahler
 * @subpackage annuity
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class loanActions extends sfActions {
	public function executeGetLoanDetails(sfWebRequest $request) {
		$loanId = $request -> getParameter('loan_id');
		$loan = LoanPeer::retrieveByPK($loanId);
		// $loan = new Loan();
		$loanTransaction = $loan -> getTransaction();
		// $loanTransaction = new Transaction();
		$value = $loanTransaction -> getAtrValue();

		$criteria = new Criteria();
		$criteria -> add(LoanPaymentPeer::LPA_LOA_ID, $loanId);
		$criteria -> addJoin(LoanPaymentPeer::LPA_ATR_ID, TransactionPeer::ATR_ID);
		$criteria -> addDescendingOrderByColumn(TransactionPeer::ATR_DATE);
		$payments = LoanPaymentPeer::doSelect($criteria);

		$totalPayments = 0;
		foreach ($payments as $payment) {
			// $payment = new LoanPayment();
			$paymentTransaction = $payment -> getTransaction();
			// $paymentTransaction = new Transaction();
			$totalPayments += $paymentTransaction -> getAtrValue();
		}

		$currentBalance = $value - $totalPayments;

		$dateTimeCurrent = new DateTime('now');
		$interval = null;
		if (count($payments) > 0) {
			$lastPayment = $payments[0];
			// $lastPayment = new LoanPayment();
			$lastPaymentTransaction = $lastPayment -> getTransaction();
			// $lastPaymentTransaction = new Transaction();
			$dateTimeLastPayment = new DateTime($lastPaymentTransaction -> getAtrDate('Y-m-d'));
			$interval = $dateTimeCurrent -> diff($dateTimeLastPayment, true);
		} else {
			$loanDateTime = new DateTime($loanTransaction -> getAtrDate('Y-m-d'));
			$interval = $dateTimeCurrent -> diff($loanDateTime, true);
		}
		$months = $interval -> m;
		$interests = $currentBalance * (pow(($loan -> getLoaInterestRate() / 100) + 1, $months / 12) - 1);
		$interests = number_format($interests, 2, '.', '');

		$fullPayment = $currentBalance + $interests;

		$result = array();
		$data = array();
		$fields = array();
		$fields['current_balance'] = $currentBalance;
		$fields['interests'] = $interests;
		$fields['full_payment'] = $fullPayment;
		$data[] = $fields;
		$result['data'] = $data;
		return $this -> renderText(json_encode($result));
	}

	public function executeDeletePayment(sfWebRequest $request) {
		$payment = LoanPaymentPeer::retrieveByPK($request -> getParameter('id'));
		$transaction = $payment -> getTransaction();

		// $payment = new LoanPayment();

		$criteria = new Criteria();
		$criteria -> add(LoanPaymentPeer::LPA_LOA_ID, $payment -> getLpaLoaId());
		$criteria -> addDescendingOrderByColumn(LoanPaymentPeer::LPA_ID);
		$lastPayment = LoanPaymentPeer::doSelectOne($criteria);

		// $lastPayment = new LoanPayment();

		if ($payment -> getLpaId() === $lastPayment -> getLpaId()) {
			$payment -> delete();
			$transaction -> delete();
			return $this -> renderText('Ok');
		} else {
			return $this -> renderText('You can delete last payment only');
		}
	}

	public function executeRegisterPayment(sfWebRequest $request) {
		$payment = null;
		$transaction = null;
		if ($request -> getParameter('payment_id') != '') {
			$payment = LoanPaymentPeer::retrieveByPK($request -> getParameter('payment_id'));
			$transaction = $payment -> getTransaction();
		} else {
			$payment = new LoanPayment();
			$transaction = new Transaction();
			$transaction -> setAtrReference('');
			$transaction -> setAtrDescription('Loan payment');
		}

		$loan = LoanPeer::retrieveByPK($request -> getParameter('loan_id'));

		$payment -> setLoan($loan);

		$transaction -> setAtrDate($request -> getParameter('date'));
		$transaction -> setAtrValue($request -> getParameter('amount'));
		$transaction -> setAtrAccIdCredit($loan -> getTransaction() -> getAtrAccIdDebit());
		$transaction -> setAtrAccIdDebit($request -> getParameter('payments_account_id'));

		$transaction -> save();

		$payment -> setTransaction($transaction);

		$payment -> save();

		return sfView::NONE;
	}

	public function executeGetPaymentsList(sfWebRequest $request) {
		$criteria = new Criteria();
		if ($request -> hasParameter('loan_id')) {
			$criteria -> add(LoanPaymentPeer::LPA_LOA_ID, $request -> getParameter('loan_id'));
		}
		$payments = LoanPaymentPeer::doSelect($criteria);

		$result = array();
		$data = array();

		foreach ($payments as $payment) {
			$fields = array();

			//			$payment = new LoanPayment();

			$fields['payment_id'] = $payment -> getLpaId();
			$fields['loan_id'] = $payment -> getLpaLoaId();
			$fields['transaction_id'] = $payment -> getLpaAtrId();

			$transaction = $payment -> getTransaction();
			$loan = $payment -> getLoan();

			$fields['date'] = $transaction -> getAtrDate('d-m-Y');
			$fields['amount'] = $transaction -> getAtrValue();
			$fields['payments_account_id'] = $transaction -> getAtrAccIdDebit();

			$data[] = $fields;
		}

		$result['data'] = $data;
		return $this -> renderText(json_encode($result));
	}

	public function executeDelete(sfWebRequest $request) {
		try {
			$loanId = $request -> getParameter('id');
			$loan = LoanPeer::retrieveByPK($loanId);
			$transaction = $loan -> getTransaction();
			$transaction -> delete();
			$loan -> delete();
		} catch(Exception $e) {
			return $this -> renderText($e);
		}
		return $this -> renderText('ok');
	}

	public function executeCreate(sfWebRequest $request) {
		try {
			$loan = null;
			$transaction = null;
			if ($request -> getParameter('loan_id') != '') {
				$loan = LoanPeer::retrieveByPK($request -> getParameter('loan_id'));
				$transaction = $loan -> getTransaction();
			} else {
				$loan = new Loan();
				$transaction = new Transaction();
				$transaction -> setAtrReference('');
				$transaction -> setAtrDescription('Loan');
			}

			$transaction -> setAtrDate($request -> getParameter('date'));
			$transaction -> setAtrAccIdDebit($request -> getParameter('loans_account_id'));
			$transaction -> setAtrAccIdCredit($request -> getParameter('source_account_id'));
			$transaction -> setAtrValue($request -> getParameter('amount'));

			$loan -> setLoaConId($request -> getParameter('contact_id'));
			$loan -> setTransaction($transaction);
			$loan -> setLoaInterestRate($request -> getParameter('interest_rate'));
			$loan -> save();
		} catch(Exception $e) {
			return $this -> renderText($e);
		}
		return $this -> renderText('ok');
	}

	public function executeGetList(sfWebRequest $request) {
		$loans = LoanPeer::doSelect(new Criteria());

		$result = array();
		$data = array();

		foreach ($loans as $loan) {
			$fields = array();

			//			$loan = new Loan();

			$fields['loan_id'] = $loan -> getLoaId();
			$fields['contact_id'] = $loan -> getLoaConId();
			$fields['transaction_id'] = $loan -> getLoaAtrId();
			$fields['interest_rate'] = $loan -> getLoaInterestRate();

			$transaction = $loan -> getTransaction();

			$fields['date'] = $transaction -> getAtrDate('d-m-Y');
			$fields['amount'] = $transaction -> getAtrValue();
			$fields['source_account_id'] = $transaction -> getAtrAccIdCredit();
			$fields['loans_account_id'] = $transaction -> getAtrAccIdDebit();

			$data[] = $fields;
		}

		$result['data'] = $data;
		return $this -> renderText(json_encode($result));
	}

}
