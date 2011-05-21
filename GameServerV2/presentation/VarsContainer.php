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
    public static $material = array();//TODO reupdate material box are set $_GET: upgrade or training or sth that costs materials

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
            break;
            case 'CURRENT_UPGRADES': self::loadCurrentUpgradesVars();
            break;
            case "WAREHOUSE":
                LoadBuildingsCosts::getbuildingProperties(User::get_faction());
                self::$display['WAREHOUSE'] = &LoadBuildingsCosts::$buildingProperties['WAREHOUSE'];
                break;
            case "BANK":
                LoadBuildingsCosts::getbuildingProperties(User::get_faction());
                self::$display['BANK'] = &LoadBuildingsCosts::$buildingProperties['BANK'];
                break;
            case "UNIVERSITY":
                LoadBuildingsCosts::getbuildingProperties(User::get_faction());
                self::$display['UNIVERSITY'] = &LoadBuildingsCosts::$buildingProperties['UNIVERSITY'];
                break;
            case "LAB":
                LoadBuildingsCosts::getbuildingProperties(User::get_faction());
                self::$display['LAB'] = &LoadBuildingsCosts::$buildingProperties['LAB'];
                break;
            case "TRAINING_CAMP":
                LoadBuildingsCosts::getbuildingProperties(User::get_faction());
                self::$display['TRAINING_CAMP'] = &LoadBuildingsCosts::$buildingProperties['TRAINING_CAMP'];
                break;
            case "ADV_LAB":
                LoadBuildingsCosts::getbuildingProperties(User::get_faction());
                self::$display['ADV_LAB'] = &LoadBuildingsCosts::$buildingProperties['ADV_LAB'];
                break;
            case "STEEL_FACTORY":
                LoadBuildingsCosts::getbuildingProperties(User::get_faction());
                self::$display['STEEL_FACTORY'] = &LoadBuildingsCosts::$buildingProperties['STEEL_FACTORY'];
                break;
            case "REFINERY":
                LoadBuildingsCosts::getbuildingProperties(User::get_faction());
                self::$display['REFINERY'] = &LoadBuildingsCosts::$buildingProperties['REFINERY'];
                break;
            case "STOCK_EXCHANGE":
                LoadBuildingsCosts::getbuildingProperties(User::get_faction());
                self::$display['STOCK_EXCHANGE'] = &LoadBuildingsCosts::$buildingProperties['STOCK_EXCHANGE'];
                break;
            case "BASE_DEFENCES":
                LoadBuildingsCosts::getbuildingProperties(User::get_faction());
                self::$display['BASE_DEFENCES'] = &LoadBuildingsCosts::$buildingProperties['BASE_DEFENCES'];
                break;
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
                    break;
                case 'MESSAGES':
                    if($t3 != "IN" || $t3 != "OUT" || $id >= 100){
                        require_once APP.'presentation/groups/Messages.php';
                        self::$display[$type][$id] = Messages::getMessages($id,$t3);
                    }
                    break;
                case 'BOX':
                    require_once APP.'presentation/groups/Box.php';
                    self::$display[$type][$id] = Box::getInfoFromId($id);
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
        require_once APP.'presentation/groups/Game.php';
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
    private static function loadCurrentUpgradesVars(){
        require_once APP.'presentation/groups/CurrentUpgrades.php';
        if(!is_array(self::$display['CURRENT_UPGRADES'])){
            CurrentUpgrades::loadUpgradesInfo();
            self::$display['CURRENT_UPGRADES'] = &CurrentUpgrades::$upgrades;
        }
    }
}