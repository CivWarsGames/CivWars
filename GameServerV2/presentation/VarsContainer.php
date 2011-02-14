<?php
require_once APP.'presentation/groups/Player.php';
require_once APP.'presentation/groups/City.php';

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
        }

    }
    public static function loadObject($type, $id){
        if(!is_array(self::$display[$type][$id])){
            switch ($type){
                case 'PLAYER':
                    self::$display[$type][$id] = Player::loadPlayerInfo($id);
                    break;
                case 'CITY':
                    self::$display[$type][$id] = City::loadCityInfo($id);
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
    private static function loadBuildingVars(){
        require_once APP.'presentation/groups/Building.php';
        Buildings::LoadInfo();
        self::$display['BUILDING'] = &Buildings::$buildingsInfo;
    }
}