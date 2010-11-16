<?php
if(!defined('WEB_ROOT')){
	require_once '../pathBuilder.php';
}
require_once WEB_ROOT.'logic/ServerTools.php';
/**
 *
 * Creates the login box with a list of all the servers
 * @var String $loginBox The html code of the box
 *
 */
class LoginPresentation
{
	protected $loginBox;

	public function __construct()
	{
		$selectServer = $this->parseServersList();

		$this->loginBox = '
		<div>
		<form action = "?login" method = "POST">
		User: <input type = "text" name = "user" /> <br />
		Password: <input type = "password" name = "password" /> <br />
		Choose server: '.$selectServer.' <br />
		<input type="submit" value="Login" />
		</form>
		</div>';
	}

	public function getloginBox()
	{
		return $this->loginBox;
	}

	/**
	 *
	 * Creates an String html select code with the list of all the servers
	 * @return String $serversSelect
	 */
	protected function parseServersList()
	{
		$serversList = ServerTools::lsServers();
		$parsedString .= '<select name="serverId">';
		foreach ($serversList as $key => $server){
			if($server->hidden == false){
			$parsedString .= '<option value= "'.$key.'">'.$server->sName.'</option>
			';
			}
		}
		$parsedString .= '</select>';

		return $parsedString;
	}
}

?>