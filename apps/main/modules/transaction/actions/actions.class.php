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
	public function executeCreate(sfWebRequest $request) {
		try {
			$transaction = new Transaction();
			$transaction->setAtrDate($request->getParameter('date'));
			$transaction->setAtrReference($request->getParameter('reference'));
			$transaction->setAtrDescription($request->getParameter('description'));

			$entry1 = new Entry();
			$entry1->setAceAccId($request->getParameter('account1_id'));
			$entry1->setAceDebit($request->getParameter('debit'));
			$entry1->setAceCredit($request->getParameter('credit'));

			$entry2 = new Entry();
			$entry2->setAceAccId($request->getParameter('account2_id'));
			$entry2->setAceDebit($request->getParameter('debit'));
			$entry2->setAceCredit($request->getParameter('credit'));

			$transaction->addEntry($entry1);
			$transaction->addEntry($entry2);

			$transaction->save();
		} catch(Exception $e) {
			return $this->renderText($e);
		}
		return $this->renderText('ok');
	}
	public function executeGetTransactionList(sfWebRequest $request) {
		$criteria = new Criteria();
		if($request->hasParameter('account_id')) {
			$criteria->addJoin(TransactionPeer::ATR_ID, EntryPeer::ACE_ATR_ID);
			$criteria->add(EntryPeer::ACE_ACC_ID, $request->getParameter('account_id'));
		}
		$transactions = TransactionPeer::doSelect($criteria);

		$result = array();
		$data = array();

		foreach($transactions as $transaction) {
			$fields = array();

			//			$transaction = new Transaction();

			$fields['id'] = $transaction->getAtrId();
			$fields['date'] = $transaction->getAtrDate();
			$fields['reference'] = $transaction->getAtrReference();
			$fields['description'] = $transaction->getAtrDescription();

			$data[] = $fields;
		}

		$result['data'] = $data;
		return $this->renderText(json_encode($result));
	}
}
