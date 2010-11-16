<?php
if(!defined('WEB_ROOT')){
	require_once '../../pathBuilder.php';
}
require_once WEB_ROOT.'presentation/LoginPresentation.php';
class LoginPresentationTest
{
	public static function showLoginBox()
	{
		$loginBox = new LoginPresentation();
		echo $loginBox->getloginBox();
		
	}
}
LoginPresentationTest::showLoginBox();
?>