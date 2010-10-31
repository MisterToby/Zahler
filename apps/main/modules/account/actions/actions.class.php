<?php

/**
 * account actions.
 *
 * @package    zahler
 * @subpackage account
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class accountActions extends sfActions
{
	public function executeGetAccountList(sfWebRequest $request) {
		$criteria = new Criteria();
		if($request->hasParameter('account_type')) {
			$criteria->add(AccountPeer::ACC_ACT_ID, $request->getParameter('account_type'));
		}
		$accounts = AccountPeer::doSelect($criteria);

		$result = array();
		$data = array();

		foreach($accounts as $account) {
			$fields = array();
				
			$fields['account_id'] = $account->getAccId();
			$fields['account_name'] = $account->getAccName();

			$data[] = $fields;
		}

		$result['data'] = $data;
		return $this->renderText(json_encode($result));
	}
}