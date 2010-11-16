<?php
if(!defined('APP')){
require_once '../pathBuilder.php';
}


/**
 * Set the default settings for the server
 */
if(isset($_SESSION['server'])){
	$serverId = $_SESSION['server'];
	require_once HOME.'servers/'.$serverId.'.php';
	$server = $s[$serverId];
}elseif(isset($_GET['login'])){
	$serverId = $_POST['serverId'];
	require_once HOME.'servers/'.$serverId.'.php';
	$server = $s[$serverId];
}
/*Database related*/
if(!defined('DB_HOST')){
	define('DB_HOST',$server->dbHost);
}
if(!defined('DB_USER')){
	define('DB_USER',$server->dbUser);
}
if(!defined('DB_PASSWORD')){
	define('DB_PASSWORD',$server->dbPass);
}
if(!defined('DB_NAME')){
	define('DB_NAME',$server->dbName);
}
if(!defined('DB_TABLE_PREFIX')){
	define('DB_PREFIX',$server->dbPrefix);
}

/*Game server related*/
//-1 = unlimited
if(!defined('MAX_USERS')){
	define('MAX_USERS',$server->maxUsers);
}
//For changing the game version should always be declared
if(!defined('APP_ROOT')){
	define('APP_ROOT',$server->appRoot);
}
if(!defined('ALLOWED_GAME_TYPE')){
	define('ALLOWED_GAME_TYPE',$server->allowedGameType);
}
//Hidden server only for allowed players
if(!defined('HIDDEN_SERVER')){
	define('HIDDEN_SERVER',$server->hidden);
}
if(!defined('SERVER_NAME')){
	define('SERVER_NAME',$server->sName);
}
if(!defined('SERVER_SPEED_RATE')){
	define('SERVER_SPEED_RATE',$server->serverSpeedRate);
}
?>