<?php
if(!defined('APP')){
	require_once '../pathBuilder.php';
}
require_once APP.'logic/tools/CustomException.php';
/**
 *
 * This is the class that manages the data base the only one.
 * Only uses one connection to the database (singleton)
 * @package datamanager
 * @var private _connection
 *
 */
class DataBaseManager
{
	static private $_connection = NULL;


	public function __construct(){}

	/**
	 *
	 * Creates a database connection
	 * @param String $server
	 * @param String $user
	 * @param String $password
	 * @param String $dataBaseName
	 */
	static protected  function set_connection($server = "localhost", $user = "root" ,$password = "root" ,$dataBaseName = "test")
	{
		try
		{
			self::$_connection=mysql_connect("$server","$user","$password");
			if(self::$_connection == false){
				throw new CustomException("Error during the connection in the DB. mysql_error(): ". mysql_error(), MYSQL_CONNECTION_ERROR);
			}
		}
		catch (CustomException $e)
		{
			$e -> errorLog(false,false);
		}
		try
		{
			$db = mysql_select_db("$dataBaseName", self::$_connection);

			if($db == false){
				throw new CustomException("Database change failed. mysql_error(): ". mysql_error(), MYSQL_SELECT_DB_ERROR);
			}
		}
		catch (CustomException $e2)
		{
			$e2 -> errorLog(false,false);
		}
	}

	/**
	 *
	 * A getter method that returns the connection or creates a new one if not exists
	 */
	static protected function get_connection()
	{
		if(self::$_connection == NULL){
			self::set_connection(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
		}
		return self::$_connection;
	}

	/**
	 *
	 * changes the data base to a new given database name
	 * @param String $newDataBase
	 */
	//static public function changeDataBase($newDataBase){//TODO this :)	}

	/**
	 *
	 * Queries in the database
	 * @param String $query
	 * @return $result
	 */
	static public function query($query)
	{
		try{
			$query = DataBaseManager::dbPrefixTables($query);
			$result = mysql_query($query, self::get_connection());
			if ($result == false){
				throw new CustomException (sprintf ("MySQL.Error(%d): %s. Original query: %s", mysql_errno (), mysql_error (), $query), MYSQL_QUERY_ERROR);
			}
		}
		catch (CustomException $e)
		{
			$e->errorLog();
		}
		return $result;
	}

	/**
	 *
	 * Fetches an array from a MySQL query
	 * @param String $query
	 */
	public static function fetchArray($queryResult){
		if($queryResult){
		return mysql_fetch_array($queryResult);
		}
	}

	/**
	 *
	 * Append a database prefix to all tables in a query.
	 * @param String $query
	 * @return String The properly-prefixed string.
	 */
	protected static function dbPrefixTables($query) 
	{
		return strtr($query, array('{' => DB_PREFIX, '}' => ''));
	}
	
	/**
	 * 
	 * @param $queryResult
	 * @return int The number of selected rows
	 */
	public static function numRows($queryResult)
	{
		return mysql_num_rows($queryResult);
	}
	

}

?>