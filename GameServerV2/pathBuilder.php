<?php
/**
 * This file creates paths the app root it must be included for all the files 
 * that include some other file.
 * And requires some constant files
 */
if(!defined('HOME')){
define('HOME',$_SERVER[DOCUMENT_ROOT]."/documents/CivWars/");//The project root relative to the server
	}
//This should be changed
define('APP',HOME."GameServerV2/");

require_once APP.'datamanager/errorCodeConstants.php';
?>