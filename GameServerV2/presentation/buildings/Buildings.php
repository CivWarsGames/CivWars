<?php
require_once APP.'datamanager/prices/buildings.php';
require_once APP.'datamanager/DataBaseManager.php';
require_once APP.'logic/User.php';
require_once APP.'logic/tools/Timer.php';

class Buildings
{
    public static $buildingsInfo = array();

    public static function loadInfo(){
        LoadBuildingsCosts::getbuildingNames(User::get_faction());
        LoadBuildingsCosts::getbuildingCosts(User::get_faction());
        $buildingCosts = &LoadBuildingsCosts::$buildingCosts;
        $buildingNames = &LoadBuildingsCosts::$buildingNames;
        $city_buildings = DataBaseManager::fetchArray(DataBaseManager::query("SELECT * FROM {buildings} WHERE city_id = ".User::get_currentCityId()));

        for($i=1;$i<=16;$i++){
            if($i == 16){
                self::$buildingsInfo[$i]['NAME'] = $buildingNames[16];
                self::$buildingsInfo[$i]['LEVEL'] = $city_buildings['defences'];
            }else{
                self::$buildingsInfo[$i]['NAME'] = $buildingNames[$city_buildings['type_'.$i]];
                self::$buildingsInfo[$i]['LEVEL'] = $city_buildings['level_'.$i];
            }
            self::$buildingsInfo[$i]['NEXT_LEVEL_COSTS']['METAL'] = $buildingCosts[self::$buildingsInfo[$i]['NAME']][ self::$buildingsInfo[$i]['LEVEL']][0];
            self::$buildingsInfo[$i]['NEXT_LEVEL_COSTS']['OIL'] = $buildingCosts[self::$buildingsInfo[$i]['NAME']][ self::$buildingsInfo[$i]['LEVEL']][1];
            self::$buildingsInfo[$i]['NEXT_LEVEL_COSTS']['GOLD'] = $buildingCosts[self::$buildingsInfo[$i]['NAME']][ self::$buildingsInfo[$i]['LEVEL']][2];
            self::$buildingsInfo[$i]['NEXT_LEVEL_COSTS']['ENERGY'] = $buildingCosts[self::$buildingsInfo[$i]['NAME']][ self::$buildingsInfo[$i]['LEVEL']][3];
            self::$buildingsInfo[$i]['NEXT_LEVEL_COSTS']['MAINTENANCE'] = $buildingCosts[self::$buildingsInfo[$i]['NAME']][ self::$buildingsInfo[$i]['LEVEL']][4];
            self::$buildingsInfo[$i]['NEXT_LEVEL_COSTS']['TIME'][0] = $buildingCosts[self::$buildingsInfo[$i]['NAME']][ self::$buildingsInfo[$i]['LEVEL']][5];
            self::$buildingsInfo[$i]['NEXT_LEVEL_COSTS']['TIME'][1] = Timer::secondsToDate($buildingCosts[self::$buildingsInfo[$i]['NAME']][ self::$buildingsInfo[$i]['LEVEL']][5]);
            $current_upgrade = DataBaseManager::fetchArray(DataBaseManager::query("SELECT current_improvement FROM {buildings} WHERE city_id =".User::get_currentCityId()));
            if($current_upgrade[0] == 0){
                if(self::$buildingsInfo[$i]['NEXT_LEVEL_COSTS']['METAL'] != NULL){
                    if(self::$buildingsInfo[$i]['NEXT_LEVEL_COSTS']['METAL'] <= VarsContainer::$material['QUANTITY']['METAL'] &&
                    self::$buildingsInfo[$i]['NEXT_LEVEL_COSTS']['OIL'] <= VarsContainer::$material['QUANTITY']['OIL'] &&
                    self::$buildingsInfo[$i]['NEXT_LEVEL_COSTS']['GOLD'] <= VarsContainer::$material['QUANTITY']['GOLD']){
                        self::$buildingsInfo[$i]['IMPROVING_POSSIBLE']  = "YES";
                    }else{
                        self::$buildingsInfo[$i]['IMPROVING_POSSIBLE']  = "THERE_ARENT_MATERIALS";
                    }
                }else{
                    self::$buildingsInfo[$i]['IMPROVING_POSSIBLE']  = "MAX_UPGRADED";
                }
            }else{
                self::$buildingsInfo[$i]['IMPROVING_POSSIBLE']  = "THERE_IS_AN_UPGRADE_IN_PROGRESS" ;

            }

        }

    }
}