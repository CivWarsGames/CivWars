<?php
if(!defined('APP')){
	require_once '../pathBuilder.php';
}
require_once 'User.php';
require_once APP.'datamanager/DataBaseManager.php';

/**
 * Session class, mantains the session and stores the user ID in a $_SESSION var
 * @package logic
 * @var $_isLogged default false
 * @var String $loginFailMessage why the user is not logged
 * @var User $user
 */
class Session
{
	private $_sessionState = false;
	public $loginFailMessage = '';
	public $user;

	public function __construct()
	{
		if(isset($_SESSION['userId'])){
			$this->_sessionState = true;
			$selectUserCId = DataBaseManager::query("SELECT current_city_id FROM {users} WHERE id_user = $_SESSION[userId]");
			$selectUserCId = DataBaseManager::fetchArray($selectUserCId);
			$user = new User($_SESSION['userId'], $selectUserCId[0]);
			$this->user = $user;
		}else{
			require_once APP.'logic/tools/Parser.php';
			//Avoid SQL injection
			$name = Parser::characterTraductor($_POST['user']);
			$serverId = Parser::characterTraductor($_POST['serverId']);
			$password = $_POST['password'];

			if(strlen($name) > 0 && strlen($password) > 0 ){
				$this->login($name, $password,$serverId);
			}else{
				$this->logout("Your name/password are incorrect");
			}
		}
	}

	public function get_sessionState()
	{
		return $this->_sessionState;
	}

	/**
	 * The user is logged if it wasn't and a user instance is created and stored the ID
	 * in a $_SESSION var
	 */
	protected function login($name, $password, $serverId)
	{
		try
		{
			require_once APP.'logic/tools/Parser.php';

			$password = Parser::passwordCrypt($password);
			$selectUserIds = DataBaseManager::query("SELECT id_user, current_city_id FROM {users} WHERE name = '$name' AND
			password = '$password'");

			if(DataBaseManager::numRows($selectUserIds)>0){
				$userIds = DataBaseManager::fetchArray($selectUserIds);
				$user = new User($userIds['id_user'], $userIds['current_city_id']);
				$_SESSION['server'] = $serverId;
				if($userIds['current_city_id'] > 0){
					$_SESSION['userId'] = User::get_idUser();
					$this->_sessionState = true;
					$this->user = $user;
				}else{
					//non activated user
					$this->logout("User acount isn't activated yet");
				}
			}else{
				throw new CustomException('Bad login attempt for the user:'.$name, BAD_LOGIN_ATTEMPT);
				$this->logout("Your name/password are incorrect");
			}
		}
		catch(CustomException $e)
		{
			$e->errorLog();
		}
	}

	/**
	 * Destroys the user's session
	 */
	public function logout($message = '')
	{
		$this->loginFailMessage = $message;
		$this->_sessionState = false;
		session_destroy();
	}
}
?>