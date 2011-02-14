<?php
require_once APP.'logic/User.php';
require_once APP.'logic/tools/Timer.php';
class Game
{
    public static $gameInfo;
    
    public static function loadGameInfo()
    {
        self::$gameInfo['CURRENT_CITY']['ID'] = User::get_currentCityId();
        self::$gameInfo['USER']['ID'] = User::get_idUser();
        self::$gameInfo['USER']['FACTION'] = User::get_faction();
        self::$gameInfo['SERVER']['NAME'] = SERVER_NAME;
        self::$gameInfo['SERVER']['TIME'][0] = time();
        self::$gameInfo['SERVER']['TIME'][1] = Timer::uNIXToDate(time());
    }    
}

?>