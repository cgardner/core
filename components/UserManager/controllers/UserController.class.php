<?php
namespace Cumula;

class UserController extends BaseMVCController {
	public function __construct($component) {
		parent::__construct($component);
	}
	
	public function startup() {
		$this->registerRoute('/user/register');
		$this->registerRoute('/user/login');
		$this->registerRoute('/user/logout');
		$this->registerRoute('/user/process');
		$this->registerRoute('/user/save');
	}
	
	public function login($route, $router, $args) {
		$args['ref'] = (isset($args['ref']) ? $args['ref'] : '/index.php');
		$session = Session::getInstance();
		$session->setValue('login_referer', $args['ref']);
		$this->form = FormHelper::getInstance();
		$this->render();
	}
	
	public function process($route, $router, $args) {
		$password = md5($args['password']);
		$user = User::find(array('email' => $args['email'], 'password' => $password));
		if(!$user) {
			$this->addWarning('Username or password is invalid!');
			$this->form = FormHelper::getInstance();
			$this->render('login');
			$this->dispatch(USER_MANAGER_LOGIN_FAILED, array($user));
		} else {
			$this->addMessage('Login Successful!');
			$session = Session::getInstance();
			$session->setValue('user', $user);
			$ref = $session->getValue('login_referer', '/');
			$session->unsetValue('login_referer');
			$this->dispatch(USER_MANAGER_LOGIN_SUCCEEDED, array($user));
			$this->redirectTo($ref);
		}
	}
	
	public function logout() {
		$session = Session::getInstance();
		$session->unsetValue('user');
		$this->redirectTo('/');
	}
	
	public function register() {
		$this->form = FormHelper::getInstance();
		$this->render();
	}
	
	public function save($route, $router, $args) {
		if($args['password'] === $args['confirm-password']) {
			$args['password'] =  md5($args['password']);
			$user = new User($args);
			$user->save();
			$this->addMessage('Account Created!');
			$this->dispatch(USER_MANAGER_USER_REGISTERED, array($user));
			$session = Session::getInstance();
			$session->setValue('user', $user);
			$ref = $session->getValue('login_referer', '/');
			$this->redirectTo($ref);
			return;
		} else {
			$this->addWarning('Passwords do not match');
			$this->render('register');
		}
	}
}