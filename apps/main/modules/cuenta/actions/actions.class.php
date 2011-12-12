<?php

/**
 * account actions.
 *
 * @package    zahler
 * @subpackage account
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class cuentaActions extends sfActions {
	public function executeEliminar(sfWebRequest $request) {
		try {
			$accountId = $request -> getParameter('id');
			$criteria = new Criteria();
			$criteria -> add(CuentaPeer::CUE_ID, $accountId);
			CuentaPeer::doDelete($criteria);
		} catch(Exception $e) {
			return $this -> renderText($e);
		}
		return $this -> renderText('ok');
	}

	public function executeConsultarListaCategorizadaCuentas(sfWebRequest $request) {
		$criteria = new Criteria();
		if ($request -> hasParameter('account_type')) {
			$criteria -> add(CuentaPeer::CUE_TIPO, $request -> getParameter('account_type'));
		}
		$accounts = CuentaPeer::doSelect($criteria);

		$result = array();
		$data = array();

		foreach ($accounts as $account) {
			$fields = array();

			// $account = new Cuenta();

			$fields['account_id'] = $account -> getCueId();
			$prefix = '';
			switch($account->getCueTipo()) {
				case CuentaPeer::ACTIVO :
					$prefix = 'Activo';
					break;
				case CuentaPeer::PATRIMONIO :
					$prefix = 'Patrimonio';
					break;
				case CuentaPeer::EGRESO :
					$prefix = 'Egreso';
					break;
				case CuentaPeer::INGRESO :
					$prefix = 'Ingreso';
					break;
				case CuentaPeer::PASIVO :
					$prefix = 'Pasivo';
					break;
			}
			$fields['account_name'] = $prefix . ':' . $account -> getCueNombre();

			$data[] = $fields;
		}

		$result['data'] = $data;
		return $this -> renderText(json_encode($result));
	}

	public function executeCrear(sfWebRequest $request) {
		try {
			$account = new Cuenta();
			$account -> setCueNombre($request -> getParameter('nombre'));
			$account -> setCueTipo($request -> getParameter('tipo'));
			$account -> save();
		} catch(Exception $e) {
			return $this -> renderText($e);
		}
		return $this -> renderText('ok');
	}

	public function executeConsultarListaCuentas(sfWebRequest $request) {
		$criteria = new Criteria();
		if ($request -> hasParameter('tipo_cuenta')) {
			$criteria -> add(CuentaPeer::CUE_TIPO, $request -> getParameter('tipo_cuenta'));
		}
		$cuentas = CuentaPeer::doSelect($criteria);

		$result = array();
		$data = array();

		foreach ($cuentas as $cuenta) {
			$fields = array();

			// $cuenta = new Cuenta();

			$fields['id'] = $cuenta -> getCueId();
			$fields['nombre'] = $cuenta -> getCueNombre();

			$data[] = $fields;
		}

		$result['data'] = $data;
		return $this -> renderText(json_encode($result));
	}

}
