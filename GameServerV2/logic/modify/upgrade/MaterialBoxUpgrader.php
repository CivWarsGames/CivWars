<?php
require_once APP.'logic/modify/MaterialSubstractor.php';
require_once APP.'logic/tools/Timer.php';
class MaterialBoxUpgrader extends MaterialSubstractor
{
    private $mapInfo = array();
    private $materialsInfo = array();
    
    public function MaterialBoxUpgrader()
    {
        $this->mapInfo = DataBaseManager::fetchArray(DataBaseManager::query("SELECT box_level, current_type, owner_city_id, sieger_city_id
         FROM {map} WHERE box_id = ".$_GET['material']));
        $this->materialsInfo = DataBaseManager::fetchArray(DataBaseManager::query("SELECT mat_box_id_cur_improvement
         FROM {materials} WHERE city_id = ".User::get_currentCityId()));
        if($this->materialsInfo['mat_box_id_cur_improvement'] == 0 && $this->mapInfo['owner_city_id'] == User::get_currentCityId()
         && $this->mapInfo['sieger_city_id'] == 0 && $this->mapInfo['current_type'] > 1 && $this->mapInfo['current_type'] <= 5){
            $this->readCosts();
            $this->loadSubstractor();
            if($this->lookIfPossible()){
                $timeCost = $this->costs[5] * BuildingsUtils::getTimeBonus(User::get_faction());
                DataBaseManager::query("UPDATE {materials} SET mat_box_id_cur_improvement = ".$_GET['material']. ",
                mat_box_finish_time = ".Timer::addUNIXTime($timeCost)." WHERE city_id = ".User::get_idUser());
                $this->substractMaterials();
            }
        }
    }
    private function readCosts()
    {
        LoadMaterialsCosts::getmaterialNames(User::get_faction());
        $names = &LoadMaterialsCosts::$materialNames;
        LoadMaterialsCosts::getmaterialCosts(User::get_faction());
        $this->costs = &LoadMaterialsCosts::$materialCosts[$names[$this->mapInfo['current_type']-2]][$this->mapInfo['box_level']];
    }
}