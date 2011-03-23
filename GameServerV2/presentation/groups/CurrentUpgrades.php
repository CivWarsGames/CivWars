<?php
require_once APP.'logic/User.php';
require_once APP.'logic/tools/Timer.php';

class CurrentUpgrades
{
    public static $upgrades = array();

    public static function loadUpgradesInfo()
    {
        self::buildingsInfo();
        self::materialsInfo();
        self::researchInfo();
    }
    private static function buildingsInfo()
    {
        $info = DataBaseManager::fetchArray(DataBaseManager::query("SELECT * FROM {buildings} WHERE city_id = ".User::get_currentCityId()));

        LoadBuildingsCosts::getbuildingNames(User::get_faction());
        $names = &LoadBuildingsCosts::$buildingNames;

        if($info['current_improvement'] == "defences") $buildings['NAME'] = $names[16];
        else $buildings['NAME'] = $names[$info['type_'.$info['current_improvement']]];
        if($buildings['NAME'] == "") $buildings['NAME'] = 0;
        else{
            $buildings['TO_LEVEL'] = $info['level_'.$info['current_improvement']]+1;
            $buildings['FINISH_TIME'][0] = $info['finish_time'];
            $buildings['FINISH_TIME'][1] = Timer::uNIXToDate($buildings['FINISH_TIME'][0]);
            $buildings['TIME_TO_FINISH'][0] = $buildings['FINISH_TIME'][0] - time();
            $buildings['TIME_TO_FINISH'][1]  =  Timer::secondsToDate($buildings['TIME_TO_FINISH'][0]);
            
        }
        
        self::$upgrades['BUILDINGS'] = $buildings;
    }
    private static function materialsInfo()
    {
        $info1 = DataBaseManager::fetchArray(DataBaseManager::query("SELECT mat_box_finish_time, mat_box_id_cur_improvement FROM {materials}
        WHERE city_id = ".User::get_currentCityId()));
        $info2 = DataBaseManager::fetchArray(DataBaseManager::query("SELECT box_level,current_type FROM {map} WHERE box_id = ".$info1['mat_box_id_cur_improvement']));
        
        LoadMaterialsCosts::getmaterialNames(User::get_faction());
        $names = &LoadMaterialsCosts::$materialNames;
        if($info1['mat_box_id_cur_improvement'] == 0) $materials['NAME'] = 0;
        else{
            $materials['NAME'] = $names[$info2['current_type']-2];
            $materials['TO_LEVEL'] = $info2['box_level']+1;
            $materials['FINISH_TIME'][0] = $info1['finish_time'];
            $materials['FINISH_TIME'][1] = Timer::uNIXToDate($materials['FINISH_TIME'][0]);
            $materials['TIME_TO_FINISH'][0] = $materials['FINISH_TIME'][0]-time();
            $materials['TIME_TO_FINISH'][1]  =  Timer::secondsToDate($materials['TIME_TO_FINISH'][0]);
        }
        
        self::$upgrades['MATERIALS'] = $materials;
    }
    private static function researchInfo()
    {
        $info = DataBaseManager::fetchArray(DataBaseManager::query("SELECT finish_time, current_research FROM {research} WHERE user_id = ".User::get_idUser()));

        $research['NAME'] = strtoupper($info['current_research']);
        if($research['NAME'] != 0){
            $research['FINISH_TIME'][0] = $info['finish_time'];
            $research['FINISH_TIME'][1] = Timer::uNIXToDate($research['FINISH_TIME'][0]);
            $research['TIME_TO_FINISH'][0] = $research['FINISH_TIME'][0] -time();
            $research['TIME_TO_FINISH'][1] = Timer::secondsToDate($research['TIME_TO_FINISH'][0]);
        }
        
        self::$upgrades['RESEARCH'] = $research;
    }
}