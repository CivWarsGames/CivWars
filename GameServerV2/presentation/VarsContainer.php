<?php


class VarsContainer
{
    public static $display = array();
    /**
     * @var Int[][]
     * @return
     * The mateirals var is an array that contains information related with the materials and
     * its production. Its structure is:
     * $materials[QUANTITY/CAPACITY/HOUR_PRODUCTION]
     * $materials[QUANTITY][METAL/OIL/GOLD]
     * $materials[CAPACITY][WAREHOUSE/BANK]
     * $materials[HOUR_PRODUCTION][METAL/OIL/GOLD_DIRTY/GOLD/ENERGY_DIRTY/ENERGY] (dirty is
     * the production before substract the mantenainces and energy costs
     */
    public static $material = array();

    public static function load($vars)
    {
        switch ($vars){
            case "MATERIAL": self::loadMaterialVars();
            break;
            case "BUILDING": self::loadBuildingVars();
            break;
            case "GAME": self::loadGameVars();
            break;
            case "GET_POST": self::loadGetPostVars();
        }

    }
    public static function loadObject($type, $id, $t3 = NULL)
    {
        if(!is_array(self::$display[$type][$id])){
            switch ($type){
                case 'PLAYER':
                    require_once APP.'presentation/groups/Player.php';
                    self::$display[$type][$id] = Player::loadPlayerInfo($id);
                    break;
                case 'CITY':
                    require_once APP.'presentation/groups/City.php';
                    self::$display[$type][$id] = City::loadCityInfo($id);
                case 'MESSAGES':
                    if($t3 != "IN" || $t3 != "OUT" || $id >= 100){
                        require_once APP.'presentation/groups/Messages.php';
                        self::$display[$type][$id] = Messages::getMessages($id,$t3);
                    }

            }
        }
    }
    private static function loadMaterialVars()
    {
        self::$display['MATERIAL'] = &self::$material;
    }
    public static function setMaterialVars($key,$value)
    {
        self::$material[$key] = $value;

    }
    private static function loadBuildingVars()
    {
        require_once APP.'presentation/groups/Building.php';
        Buildings::LoadInfo();
        self::$display['BUILDING'] = &Buildings::$buildingsInfo;
    }
    private static function loadGameVars()
    {
        require_once 'presentation/groups/Game';
        if(!is_array(self::$display['GAME'])){
            Game::loadGameInfo();
            self::$display['GAME'] = &Game::$gameInfo;
        }
    }
    private static function loadGetPostVars(){
        if(!is_array(self::$display['GET'])){
            self::$display['GET'] = &$_GET;
            self::$display['POST'] = &$_POST;
        }
    }
}