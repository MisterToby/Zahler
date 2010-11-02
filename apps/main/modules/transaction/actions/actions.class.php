<?php

/**
 * transaction actions.
 *
 * @package    zahler
 * @subpackage transaction
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class transactionActions extends sfActions
{
	public function executeDelete(sfWebRequest $request) {
		try {
			$transactionId = $request->getParameter('id');
			$criteria = new Criteria();
			$criteria->add(TransactionPeer::ATR_ID, $transactionId);
			TransactionPeer::doDelete($criteria);
		} catch(Exception $e) {
			return $this->renderText($e);
		}
		return $this->renderText('ok');
	}
	public function executeCreate(sfWebRequest $request) {
		try {
			$transaction = new Transaction();
			$transaction->setAtrDate($request->getParameter('date'));
			$transaction->setAtrReference($request->getParameter('reference'));
			$transaction->setAtrDescription($request->getParameter('description'));
			$transaction->setAtrValue($request->getParameter('value'));
			$transaction->setAtrAccIdDebit($request->getParameter('to_account_id'));
			$transaction->setAtrAccIdCredit($request->getParameter('from_account_id'));
			$transaction->save();
		} catch(Exception $e) {
			return $this->renderText($e);
		}
		return $this->renderText('ok');
	}
	public function executeGetTransactionList(sfWebRequest $request) {
		$accountId = $request->getParameter('account_id');

		$criteria = new Criteria();

		$criterion1 = $criteria->getNewCriterion(TransactionPeer::ATR_ACC_ID_DEBIT, $accountId, Criteria::EQUAL);
		$criterion2 = $criteria->getNewCriterion(TransactionPeer::ATR_ACC_ID_CREDIT, $accountId, Criteria::EQUAL);
		$criterion1->addOr($criterion2);
		$criteria->add($criterion1);

		//		$criteria->add(TransactionPeer::ATR_ACC_ID_DEBIT, $accountId);
		//		$criteria->addOr(TransactionPeer::ATR_ACC_ID_DEBIT, $accountId);
		//		$criteria->add(TransactionPeer::ATR_ACC_ID_CREDIT, $accountId);
		//		$criteria->addOr(TransactionPeer::ATR_ACC_ID_CREDIT, $accountId);

		$transactions = TransactionPeer::doSelect($criteria);

		$result = array();
		$data = array();

		foreach($transactions as $transaction) {
			$fields = array();

			//						$transaction = new Transaction();

			$fields['id'] = $transaction->getAtrId();
			$fields['date'] = $transaction->getAtrDate('d-m-Y');
			$fields['reference'] = $transaction->getAtrReference();
			$fields['description'] = $transaction->getAtrDescription();
			if($transaction->getAtrAccIdDebit()==$accountId) {
				$fields['debit'] = $transaction->getAtrValue();
				$fields['credit'] = 0;
				$fields['to_from_account_id'] = $transaction->getAtrAccIdCredit();
			}
			else {
				$fields['credit'] = $transaction->getAtrValue();
				$fields['debit'] = 0;
				$fields['to_from_account_id'] = $transaction->getAtrAccIdDebit();
			}

			$data[] = $fields;
		}

		$result['data'] = $data;
		return $this->renderText(json_encode($result));
	}
}
