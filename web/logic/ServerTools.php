<?php
if(!defined('WEB_ROOT')){
	require_once '../pathBuilder.php';
}
class ServerTools
{
	/**
	 * 
	 * Loops over the servers directory, includes all the sX.php files and puts the $s in a array
	 */
	public static function lsServers()
	{
		if ($handle = opendir(HOME.'servers')) {
			while (false !== ($file = readdir($handle))) {
				if($file != 'Server.php' && $file!= '.' && $file!='..'){
					require_once HOME.'servers/'.$file;					
				}
			}
			closedir($handle);
		}
		return $s;
	}
}
?>