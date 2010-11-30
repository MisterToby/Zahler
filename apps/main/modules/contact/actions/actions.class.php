<?php

/**
 * contact actions.
 *
 * @package    zahler
 * @subpackage contact
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class contactActions extends sfActions
{
	public function executeGetListWithFullName(sfWebRequest $request) {
		$criteria = new Criteria();
		$criteria->addAscendingOrderByColumn(ContactPeer::CON_LAST_NAME);
		$contacts = ContactPeer::doSelect($criteria);

		$result = array();
		$data = array();

		foreach($contacts as $contact) {
			$fields = array();

			$fields['contact_id'] = $contact->getConId();
			$fields['contact_name'] = $contact->getConLastName().' '.$contact->getConFirstName();

			$data[] = $fields;
		}

		$result['data'] = $data;
		return $this->renderText(json_encode($result));
	}
	public function executeCreate(sfWebRequest $request) {
		try {
			$contact = null;
			if($request->getParameter('contact_id')!='') {
				$contact = ContactPeer::retrieveByPK($request->getParameter('contact_id'));
			}
			else {
				$contact = new Contact();
			}
			$contact->setConFirstName($request->getParameter('first_name'));
			$contact->setConLastName($request->getParameter('last_name'));
			$contact->setConEmail($request->getParameter('email'));
			$contact->setConAddress($request->getParameter('address'));
			$contact->setConPhoneNumber($request->getParameter('phone_number'));
			$contact->save();
		} catch(Exception $e) {
			return $this->renderText($e);
		}
		return $this->renderText('ok');
	}
	public function executeGetList(sfWebRequest $request) {
		$contacts = ContactPeer::doSelect(new Criteria());

		$result = array();
		$data = array();

		foreach($contacts as $contact) {
			$fields = array();

			$fields['contact_id'] = $contact->getConId();
			$fields['first_name'] = $contact->getConFirstName();
			$fields['last_name'] = $contact->getConLastName();
			$fields['email'] = $contact->getConEmail();
			$fields['address'] = $contact->getConAddress();
			$fields['phone_number'] = $contact->getConPhoneNumber();

			$data[] = $fields;
		}

		$result['data'] = $data;
		return $this->renderText(json_encode($result));
	}
}
