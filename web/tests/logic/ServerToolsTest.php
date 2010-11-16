<?php
if(!defined('WEB_ROOT')){
	require_once '../../pathBuilder.php';
}
require_once WEB_ROOT.'logic/ServerTools.php';
class ServerToolsTest
{
	public static function lsServersTest()
	{
		$serversArray = ServerTools::lsServers();
		print_r($serversArray);
	}
}
ServerToolsTest::lsServersTest();
?>