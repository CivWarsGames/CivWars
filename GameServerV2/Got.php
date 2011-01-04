<?php
/**
 * 
 * This class handles all the params obtained by get method
 *
 */
class Got
{
	public static function handleGetParams()
	{
		foreach ($_GET as $key => $value){
			$got = self::getSwitcher($key);
		}
	}

	private static function getSwitcher($key)
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