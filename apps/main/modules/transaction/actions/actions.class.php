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
	public function executeGetTransactionList(sfWebRequest $request) {
		$criteria = new Criteria();
		if($request->hasParameter('account_id')) {
			$accountId = $request->hasParameter('account_id');
			$criterion1 = $criteria->getNewCriterion(TransactionPeer::ATR_ACC_ID_SOURCE, $accountId, Criteria::EQUAL);
			$criteria->addOr($criterion1);
			$criterion2 = $criteria->getNewCriterion(TransactionPeer::ATR_ACC_ID_TARGET, $accountId, Criteria::EQUAL);
			$criteria->addOr($criterion2);
		}
		$transactions = TransactionPeer::doSelect($criteria);

		$result = array();
		$data = array();

		foreach($transactions as $transaction) {
			$fields = array();

			$fields['id'] = $transaction->setTraId();
			$fields['description'] = $transaction->setTraDescription();

			$data[] = $fields;
		}

		$result['data'] = $data;
		return $this->renderText(json_encode($result));
	}
}
