<?php
if(!defined('APP')){
	require_once '../../pathBuilder.php';
}
require_once APP.'datamanager/DataBaseManager.php';
/**
 *
 * Some methods to handle exceptions
 * @package logic_tools
 * @var private String $_dBTable the table where errors are stored
 * @var private String $_email an email to send the errors
 *
 */
class CustomException extends Exception
{
	protected $_dBtable;
	protected $_email;

	/**
	 *
	 * Handles the error logs
	 * an email if it is possible.
	 * @param boolean $show if the parsed log has to be shown
	 */
	public function errorLog($show = true, $insert = true)
	{
		if($insert == true){
			$this -> insertInDB();
		}
		$parsedLog = $this -> parseError();
		if($show == true){
			//show the log
			echo $parsedLog;
		}
		//TODO look if send an email is possible

	}

	/**
	 *
	 * Parses an error message to show
	 * @return String $parsedLog
	 */
	protected function parseError()
	{
		$backtrace = debug_backtrace();
		$trad=array(' ' => '&nbsp;');
		$parsedLog .= '<h3><strong>Error:</strong></h3>';
		$parsedLog .= nl2br(strtr(print_r($backtrace, true),$trad));

		$parsedLog.= 'IP ->'.$_SERVER['REMOTE_ADDR'].'<br>
		Date ->'.date(" Y-m-d H:i:s").'<br><br>';

		return $parsedLog;
	}

	/**
	 *
	 * Inserts the log in the dataBase
	 */
	protected function insertInDB()
	{
		$backtrace = print_r(debug_backtrace(),true);
		$ip = $_SERVER['REMOTE_ADDR'];
		$date = date("Y-m-d H:i:s");
		DataBaseManager::query('INSERT INTO error_logs (back_trace,ip) VALUES
		("'.$backtrace.'","'.$ip.'")');
		//TODO a new column that shows the error code
	}

	/**
	 *
	 * Sends an email with all the logs if it is not a short time since last send.
	 */
	protected function sendEmail()
	{
		//TODO this :)
	}

}
?>