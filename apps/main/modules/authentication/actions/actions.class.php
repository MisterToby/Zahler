<?php

/**
 * authentication actions.
 *
 * @package    zahler
 * @subpackage authentication
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class authenticationActions extends sfActions
{
	/**
	 * Executes index action
	 *
	 * @param sfRequest $request A request object
	 */
	public function executeIndex(sfWebRequest $request)
	{
			
	}
	public function executeAuthenticate(sfWebRequest $request) {
		$user = $this->getUser();
		$user->setAuthenticated(true);

		$response = array();

		$username = $request->getParameter('user_name');
		$password = sha1($request->getParameter('password'));
		$criteria = new Criteria();
		$criteria->add(UserPeer::SUS_USER_NAME, $username);
		$criteria->add(UserPeer::SUS_PASSWORD, $password);
		$count = UserPeer::doCount($criteria);

		if($count>0) {
			$response['success'] = true;
			$response['msg'] = 'Successful authentication';
		}
		else {
			$response['success'] = false;
			$response['msg'] = 'The username and password do not match';
		}
		return $this->renderText(json_encode($response));
	}
	public function executeLogOut() {
		$user = $this->getUser();
		$user->setAuthenticated(false);

		$response = array();
		$response['success'] = true;
		$response['msg'] = 'Successful logout';
		return $this->renderText(json_encode($response));
	}
}
