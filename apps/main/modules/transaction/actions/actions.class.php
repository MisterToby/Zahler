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
			$transaction = null;
			if($request->getParameter('transaction_id')!='') {
				$transaction = TransactionPeer::retrieveByPK($request->getParameter('transaction_id'));
			}
			else {
				$transaction = new Transaction();
			}
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
	public function executeGetListOfEntries(sfWebRequest $request) {
		$accountId = $request->getParameter('account_id');

		$criteria = new Criteria();

		$criterion1 = $criteria->getNewCriterion(TransactionPeer::ATR_ACC_ID_DEBIT, $accountId, Criteria::EQUAL);
		$criterion2 = $criteria->getNewCriterion(TransactionPeer::ATR_ACC_ID_CREDIT, $accountId, Criteria::EQUAL);
		$criterion1->addOr($criterion2);
		$criteria->add($criterion1);
		$criteria->addAscendingOrderByColumn(TransactionPeer::ATR_DATE);

		$transactions = TransactionPeer::doSelect($criteria);

		$criteria = new Criteria();
		$criteria->add(AccountPeer::ACC_ID, $accountId);
		$account = AccountPeer::doSelectOne($criteria);
		$accountType = $account->getAccType();

		$result = array();
		$data = array();

		$debit = (double) 0;
		$credit = (double) 0;

		foreach($transactions as $transaction) {
			//			$transaction = new Transaction();

			if($transaction->getAtrAccIdDebit()==$accountId) {
				$debit = $debit + (double) $transaction->getAtrValue();

				$fields = array();
				$fields['transaction_id'] = $transaction->getAtrId();
				$fields['date'] = $transaction->getAtrDate('d-m-Y');
				$fields['reference'] = $transaction->getAtrReference();
				$fields['description'] = $transaction->getAtrDescription();
				$fields['debit'] = round($transaction->getAtrValue(),2);
				$fields['credit'] = 0;
				$fields['to_from_account_id'] = $transaction->getAtrAccIdCredit();
				$fields['balance'] = round(AccountPeer::calculateBalance($debit, $credit, $accountType),2);
				$data[] = $fields;
			}
			if($transaction->getAtrAccIdCredit()==$accountId) {
				$credit = $credit + (double) $transaction->getAtrValue();

				$fields = array();
				$fields['transaction_id'] = $transaction->getAtrId();
				$fields['date'] = $transaction->getAtrDate('d-m-Y');
				$fields['reference'] = $transaction->getAtrReference();
				$fields['description'] = $transaction->getAtrDescription();
				$fields['debit'] = 0;
				$fields['credit'] = round($transaction->getAtrValue(),2);
				$fields['to_from_account_id'] = $transaction->getAtrAccIdDebit();
				$fields['balance'] = round(AccountPeer::calculateBalance($debit, $credit, $accountType),2);
				$data[] = $fields;
			}
		}

		$result['data'] = $data;
		return $this->renderText(json_encode($result));
	}
}
