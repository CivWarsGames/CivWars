<?php
if(!defined('APP')){
	require_once '../../../pathBuilder.php';
}
require_once APP."presentation/themesrelated/Interpreter.php";
class InterpreterTest{
	public static function test($file,$themename)
	{
		new Interpreter($file, $themeName);
		
	}
}



?>