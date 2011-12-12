<?php

/**
 * transaction actions.
 *
 * @package    zahler
 * @subpackage transaction
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class transaccionActions extends sfActions {
	public function executeEliminar(sfWebRequest $request) {
		try {
			$transactionId = $request -> getParameter('id');
			$criteria = new Criteria();
			$criteria -> add(TransaccionPeer::TRA_ID, $transactionId);
			TransaccionPeer::doDelete($criteria);
		} catch(Exception $e) {
			return $this -> renderText($e);
		}
		return $this -> renderText('ok');
	}

	public function executeCrear(sfWebRequest $request) {
		try {
			$transaction = null;
			if ($request -> getParameter('transaction_id') != '') {
				$transaction = TransaccionPeer::retrieveByPK($request -> getParameter('transaction_id'));
			} else {
				$transaction = new Transaccion();
			}
			$transaction -> setTraFecha($request -> getParameter('date'));
			$transaction -> setTraReferencia($request -> getParameter('reference'));
			$transaction -> setTraDescripcion($request -> getParameter('description'));
			$transaction -> setTraValor($request -> getParameter('value'));
			$transaction -> setTraCueIdDebito($request -> getParameter('to_account_id'));
			$transaction -> setTraCueIdCredito($request -> getParameter('from_account_id'));
			$transaction -> save();
		} catch(Exception $e) {
			return $this -> renderText($e);
		}
		return $this -> renderText('ok');
	}

	public function executeConsultarListaRegistros(sfWebRequest $request) {
		$accountId = $request -> getParameter('account_id');

		$criteria = new Criteria();

		$criterion1 = $criteria -> getNewCriterion(TransaccionPeer::TRA_CUE_ID_DEBITO, $accountId, Criteria::EQUAL);
		$criterion2 = $criteria -> getNewCriterion(TransaccionPeer::TRA_CUE_ID_CREDITO, $accountId, Criteria::EQUAL);
		$criterion1 -> addOr($criterion2);
		$criteria -> add($criterion1);
		$criteria -> addAscendingOrderByColumn(TransaccionPeer::TRA_FECHA);

		$transactions = TransaccionPeer::doSelect($criteria);

		$criteria = new Criteria();
		$criteria -> add(CuentaPeer::CUE_ID, $accountId);
		$account = CuentaPeer::doSelectOne($criteria);
		// $account = new Cuenta();
		$accountType = $account -> getCueTipo();

		$result = array();
		$data = array();

		$debit = (double)0;
		$credit = (double)0;

		foreach ($transactions as $transaction) {
			// $transaction = new Transaccion();

			if ($transaction -> getTraCueIdDebito() == $accountId) {
				$debit = $debit + (double)$transaction -> getTraValor();

				$fields = array();
				$fields['transaction_id'] = $transaction -> getTraId();
				$fields['date'] = $transaction -> getTraFecha('d-m-Y');
				$fields['reference'] = $transaction -> getTraReferencia();
				$fields['description'] = $transaction -> getTraDescripcion();
				$fields['debit'] = round($transaction -> getTraValor(), 2);
				$fields['credit'] = 0;
				$fields['to_from_account_id'] = $transaction -> getTraCueIdCredito();
				$fields['balance'] = round(CuentaPeer::calcularSaldo($debit, $credit, $accountType), 2);
				$data[] = $fields;
			}
			if ($transaction -> getTraCueIdCredito() == $accountId) {
				$credit = $credit + (double)$transaction -> getTraValor();

				$fields = array();
				$fields['transaction_id'] = $transaction -> getTraId();
				$fields['date'] = $transaction -> getTraFecha('d-m-Y');
				$fields['reference'] = $transaction -> getTraReferencia();
				$fields['description'] = $transaction -> getTraDescripcion();
				$fields['debit'] = 0;
				$fields['credit'] = round($transaction -> getTraValor(), 2);
				$fields['to_from_account_id'] = $transaction -> getTraCueIdDebito();
				$fields['balance'] = round(CuentaPeer::calcularSaldo($debit, $credit, $accountType), 2);
				$data[] = $fields;
			}
		}

		$result['data'] = $data;
		return $this -> renderText(json_encode($result));
	}

}
