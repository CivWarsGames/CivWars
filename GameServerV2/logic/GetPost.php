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
			case 'upgrade':
				require_once APP.'logic/modify/upgrade/UpgradeHandler.php';	
				$got = new UpgradeHandler();
				break;
			case 'send':
			    require_once APP.'logic/modify/sendtroops/TroopsSender.php';
			    $got = new TroopsSender();
			    break;
			case 'profile':
			    require_once APP.'logic/modify/profile/ProfilesHandler.php';
			    $got = new ProfilesHandler;
			    break;
			case 'messages':
			    require_once APP.'logic/modify/messages/Messenger.php';
			    $got = new Messenger();
			    break;
			    //add here
			case 'json':
                require_once APP.'presentation/JsonHandler.php';
                new JsonHandler();
			    break;
			    
            case 'page':
                require_once APP.'presentation/PageHandler.php';
                $got = new PageHandler($_GET[$key]);    
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