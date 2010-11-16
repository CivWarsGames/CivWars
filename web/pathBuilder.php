<?php
/**
 * This file creates paths the app root it must be included for all the files 
 * that include some other file.
 * And requires some constant files
 */

if(!defined('HOME')){
define('HOME',$_SERVER[DOCUMENT_ROOT]."/documents/CivWars2/");
}
define('WEB_ROOT',HOME."web/");
//This has to be changed
define('LANGUAGE',"en");
?>