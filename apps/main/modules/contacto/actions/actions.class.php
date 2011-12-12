<?php

/**
 * contact actions.
 *
 * @package    zahler
 * @subpackage contact
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class contactoActions extends sfActions {
	public function executeEliminar(sfWebRequest $request) {
		try {
			$idContacto = $request -> getParameter('id');
			$criteria = new Criteria();
			$criteria -> add(ContactoPeer::CON_ID, $idContacto);
			ContactoPeer::doDelete($criteria);
		} catch(Exception $e) {
			return $this -> renderText($e);
		}
		return $this -> renderText('ok');
	}

	public function executeConsultarListaNombreCompleto(sfWebRequest $request) {
		$criteria = new Criteria();
		$criteria -> addAscendingOrderByColumn(ContactoPeer::CON_APELLIDOS);
		$contacts = ContactoPeer::doSelect($criteria);

		$result = array();
		$data = array();

		foreach ($contacts as $contact) {
			$fields = array();

			// $contact = new Contacto();

			$fields['contact_id'] = $contact -> getConId();
			$fields['contact_name'] = $contact -> getConApellidos() . ' ' . $contact -> getConNombres();

			$data[] = $fields;
		}

		$result['data'] = $data;
		return $this -> renderText(json_encode($result));
	}

	public function executeCrear(sfWebRequest $request) {
		try {
			$contact = null;
			if ($request -> getParameter('contact_id') != '') {
				$contact = ContactoPeer::retrieveByPK($request -> getParameter('contact_id'));
			} else {
				$contact = new Contacto();
			}
			// $contact = new Contacto();
			$contact -> setConNombres($request -> getParameter('first_name'));
			$contact -> setConApellidos($request -> getParameter('last_name'));
			$contact -> setConEmail($request -> getParameter('email'));
			$contact -> setConDireccion($request -> getParameter('address'));
			$contact -> setConTelefono($request -> getParameter('phone_number'));
			$contact -> save();
		} catch(Exception $e) {
			return $this -> renderText($e);
		}
		return $this -> renderText('ok');
	}

	public function executeConsultarLista(sfWebRequest $request) {
		$contacts = ContactoPeer::doSelect(new Criteria());

		$result = array();
		$data = array();

		foreach ($contacts as $contact) {
			$fields = array();

			// $contact = new Contacto();

			$fields['contact_id'] = $contact -> getConId();
			$fields['first_name'] = $contact -> getConNombres();
			$fields['last_name'] = $contact -> getConApellidos();
			$fields['email'] = $contact -> getConEmail();
			$fields['address'] = $contact -> getConDireccion();
			$fields['phone_number'] = $contact -> getConTelefono();

			$data[] = $fields;
		}

		$result['data'] = $data;
		return $this -> renderText(json_encode($result));
	}

}
