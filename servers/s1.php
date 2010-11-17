<?php
require_once 'Server.php';
$s['s1'] = new Server();

/*Server Settings*/
$s['s1']->dbUser = 'cw';
$s['s1']->dbPass = 'test';
$s['s1']->appRoot = 'GameServerV2/';
$s['s1']->sName = 'Server 1';
?>