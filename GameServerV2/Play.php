<?php
if(!defined('APP')){
require_once 'pathBuilder.php';
}
require_once APP.'logic/Session.php';
require_once APP.'datamanager/serverSettings.php';
require_once APP.'logic/GetPost.php';
/**
 * 
 * Launches all the proces and selects the data to show
 *
 */
class Play
{
	private  static $_session;
	
	public static function get_session()
	{
		return self::$_session;
	}
	
	public static function  launch()
	{
		self::$_session = new Session();
		if(self::$_session->get_sessionState() == true){
			$user = self::$_session->user;
			$user->update();
		}else{
			echo self::$_session->loginFailMessage;
			//TODO redirect a non logged user
		}
		
		GetPost::handleGetPostParams();
	}
}

?>