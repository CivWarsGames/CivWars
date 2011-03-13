<?php
require_once APP.'logic/modify/MaterialSubstractor.php';
require_once APP.'logic/tools/Timer.php';

class BuildingsUpgrader extends MaterialSubstractor
{
    private $level;
    private $type;

    public function BuildingsUpgrader()
    {
        $plotNumber = $_GET['building'];
        if(Parser::onlyNumbersString($plotNumber)){
            $result = DataBaseManager::fetchArray(DataBaseManager::query("SELECT * FROM {buildings} WHERE city_id =".User::get_currentCityId()));
            if($plotNumber == 16){
                $this->type = 16;
                $this->level = $result['defences'];
            }else{
                $this->level = $result['level_'.$plotNumber];
                $this->type = $result['type_'.$plotNumber];
            }
            if($this->type == 0){
                $plot = new Plot();
                $possible = array_search($_GET['building_name'], $plot->availableBuildingsList);
            }
            if(($this->type != 0 || $possible === false) && $result['current_improvement'] == 0){
                $this->loadSubstractor();
                $this->readCosts($plotNumber);
                if($this->lookIfPossible()){
                    //time bonus
                    $timeBonus = BuildingsUtils::getTimeBonus($this->faction,$result);
                    $timeCost = $this->costs[5]*$timeBonus/SERVER_SPEED_RATE;
                    if($this->type == 0){
                        DataBaseManager::query("UPDATE {buildings} SET current_improvement = '$plotNumber',
                         finish_time = ".Timer::addUNIXTime($timeCost).", type_".$plotNumber." = ".array_search($_GET['building_name'], $buildingNames)." 
                         WHERE city_id = ".User::get_currentCityId());
                    }else{
                        DataBaseManager::query("UPDATE {buildings} SET current_improvement = '$plotNumber',
                         finish_time = ".Timer::addUNIXTime($timeCost)." WHERE city_id = ".User::get_currentCityId());
                    }
                    $this->substractMaterials();
                }
            }
        }
    }
    private  function readCosts($plotNumber)
    {
        require_once APP.'datamanager/prices/buildings.php';
        LoadBuildingsCosts::getbuildingNames($this->faction);
        $buildingNames = &LoadBuildingsCosts::$buildingNames;
        LoadBuildingsCosts::getbuildingCosts($this->faction);
        $buildingCosts = &LoadBuildingsCosts::$buildingCosts;
        if($this->type != 0){
            //max upgraded
            if($this->level >= count($buildingCosts[$buildingNames[$this->type]])){
                $this->costs[0] = $this->materialsStock['METAL'] + 1;
            }else{
                $this->costs = &$buildingCosts[$buildingNames[$this->type]][$this->level];
            }
        }else{
            $this->costs = &$buildingCosts[$_GET['building_name']][$this->level];
        }       
        
    }
}