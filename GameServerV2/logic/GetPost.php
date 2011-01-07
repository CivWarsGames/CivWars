<?php
/**
 * 
 * This class handles all the params obtained by get method
 *
 */
class GetPost
{
	public static function handleGetPostParams()
	{
		$_GET = array_merge($_GET,$_POST);
		foreach ($_GET as $key => $value){
			$got = self::getPostSwitcher($key);
		}
	}

	private static function getPostSwitcher($key)
	{
		switch ($key){
			
			case 'ajax':
				require_once APP.'presentation/AJAXHandler.php';
				$got = new AJAXHandler($_GET[$key]);
				break;
			
			case 'page':
				require_once APP.'presentation/PageHandler.php';
				$got = new PageHandler($_GET[$key]);	
				break;
				
			case 'upgrade':
				require_once APP.'logic/modify/upgrade/UpgradeHandler.php';	
				$got = new Upgrade();
				break;
			
			case 'logout':
				require_once APP.'Play.php';
				if($session = Play::get_session()){
					$session->logout("bye!");
					$got = true;
				}
				break;
				
		//add more cases here
			return $got;	
		}
	}
}
?>