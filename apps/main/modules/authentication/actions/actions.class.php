<?php

/**
 * authentication actions.
 *
 * @package    zahler
 * @subpackage authentication
 * @author     Your name here
 * @version    SVN: $Id: actions.class.php 23810 2009-11-12 11:07:44Z Kris.Wallsmith $
 */
class authenticationActions extends sfActions {
	/**
	 * Executes index action
	 *
	 * @param sfRequest $request A request object
	 */
	public function executeIndex(sfWebRequest $request) {

	}

	public function executeAuthenticate(sfWebRequest $request) {
		$user = $this -> getUser();

		$response = array();

		$username = $request -> getParameter('user_name');
		$password = sha1($request -> getParameter('password'));
		$criteria = new Criteria();
		$criteria -> add(UsuarioPeer::USU_LOGIN, $username);
		$criteria -> add(UsuarioPeer::USU_PASSWORD, $password);
		$count = UsuarioPeer::doCount($criteria);

		if ($count > 0) {
			$user -> setAuthenticated(true);
			$response['success'] = true;
			$response['msg'] = 'Autenticación exitosa';
		} else {
			$user -> setAuthenticated(false);
			$response['success'] = false;
			$response['msg'] = 'Login y/o contraseña inválidos';
		}
		return $this -> renderText(json_encode($response));
	}

	public function executeLogOut() {
		$user = $this -> getUser();
		$user -> setAuthenticated(false);

		$response = array();
		$response['success'] = true;
		$response['msg'] = 'Successful logout';
		return $this -> renderText(json_encode($response));
	}

}
