<?php 
if(!defined('WEB_ROOT')){
require_once 'pathBuilder.php';
}
//Not finished
class Web
{
	public static function printWeb()
	{
		require_once WEB_ROOT.'presentation/LoginPresentation.php';
		$loginBox = new LoginPresentation();
		echo $loginBox->getloginBox();
	}
}

?>