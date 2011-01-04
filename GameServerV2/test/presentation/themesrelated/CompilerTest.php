<?php
/**
 * This is a compiler test
 */
if(!defined('APP')){
	require_once '../../../pathBuilder.php';
}
require_once APP."presentation/themesrelated/Compiler.php";

class CompilerTest
{
	public static function test($theme)
	{
		$compiler = new Compiler($theme);
		$code = self::returnCode(HOME.'themes/'.$theme.'/php/'.$_GET['p']);
		echo '<textarea rows="36" cols="160" readonly style="background-color:#efffef">'.$code."</textarea>";
	}
	public static function returnCode($path){
		return file_get_contents($path);		
	}	
}
CompilerTest::test("testtheme1");

?>