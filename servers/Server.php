<?php

/**
 * 
 * The vars of a server with its defaults
 *
 */
class Server
{
	public $dbHost = 'localhost';
	public $dbUser = 'root';
	public $dbPass = 'root';
	public $dbName = 'test';
	public $dbPrefix = '';
	public $sName = 'sX';
	public $maxUsers = -1; // -1 = unlimited
	public $appRoot = '';//The root of the aplication ex: GameServer
	public $serverSpeedRate = 1; //The speed of the server It must be > 0!!!
	public $allowedGameType = 'ALL';// A String of the allowed themes separated by commas if 'ALL' is the first the following are blacklist
	public $hidden = false; //if true the server is not showed to the users
	
	public function __construct(){}
}

?>