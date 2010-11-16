<?php
class Index
{
	public static function main()
	{
		if(isset($_SESSION['server']) || isset($_GET['login'])){

			if(isset($_SESSION['server'])){
				$serverId = $_SESSION['server'];
			}else{
				if(isset($_POST['serverId'])){
					$serverId = $_POST['serverId'];
				}else{
					require_once 'web/Web.php';
					Web::printWeb();
					return;
				}
			}
			require_once 'servers/'.$serverId.'.php';
			$approot = $s[$serverId]->appRoot;
				
			require_once $approot.'Play.php';
			Play::launch();
		}else{
			require_once 'web/Web.php';
			Web::printWeb();
		}
	}
}
$start = microtime(true); 
session_start();
Index::main();
$finish = microtime(true); 
$total = $finish - $start;
//echo $total;
?>