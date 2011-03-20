<?php
/**
 *
 * This class updates all the finished buildings from all the players
 *
 */
class UpdateBuildings
{
    private $info = array();
    private $buildingCosts = array();
    private $maintenanceCosts = array();
    private $buildingNames = array();
    private $faction;
    public function UpdateBuildings()
    {
        $result = DataBaseManager::query("SELECT * FROM {buildings} WHERE current_improvement <> 0 and finish_time <= ".time()." ORDER BY finish_time ASC");
        while($data = DataBaseManager::fetchArray($result)){
            $this->info = &$data;
            $this->selectInfo();
            $this->calculateCosts();
            $this->updateUser();
        }
    }
    private function selectInfo()
    {
        $faction = DataBaseManager::fetchArray(DataBaseManager::query("SELECT faction FROM {profile} WHERE
        user_id = ".$this->info['user_id']));
        $faction = $faction[0];
        $this->faction = $faction;
        LoadBuildingsCosts::getbuildingCosts($faction);
        $this->buildingCosts = &LoadBuildingsCosts::$buildingCosts;
        LoadBuildingsCosts::getbuildingNames($faction);
        $this->buildingNames = &LoadBuildingsCosts::$buildingNames;
        $this->maintenanceCosts = DataBaseManager::fetchArray(DataBaseManager::query("SELECT buildings_maintenance, energy_cost FROM
        {materials} WHERE city_id = ".$this->info['city_id']));
    }
    private function calculateCosts()
    {
        $this->maintenanceCosts['buildings_maintenance'] += $this->buildingCosts[$this->buildingNames[$this->info['current_improvement']]]
        [$this->info['level_'.$this->info['current_improvement']]][4];
        $this->maintenanceCosts['energy_cost'] += $this->buildingCosts[$this->buildingNames[$this->info['current_improvement']]]
        [$this->info['level_'.$this->info['current_improvement']]][3];
    }
    private function updateUser()
    {
        //update frontiers only when university is upgraded
        if($this->buildingNames[$this->info['current_improvement']] == 'UNIVERSITY'){
            new UpdateFrontiers($this->info, $this->faction);
        }
        //update materials
        new UpdateMaterials($this->info['city_id'],$this->info['finish_time']);

        //update maintenance costs
        DataBaseManager::query("UPDATE {materials} SET buildings_maintenance = ".$this->maintenanceCosts['buildings_maintenance'].",
         energy_cost = ".$this->maintenanceCosts['energy_cost']." WHERE city_id =".$this->info['city_id']);
         
        //update buildings queue
        if($this->info['current_improvement'] == 16){
            $level = $this->info['defences']+1;
            DataBaseManager::query("UPDATE {buildings} SET current_improvement = 0, defences = $level
         WHERE city_id = ".$this->info['city_id']);
        }else{
            $level = $this->info['level_'.$this->info['current_improvement']]+1;
            DataBaseManager::query("UPDATE {buildings} SET current_improvement = 0, level_".$this->info['current_improvement']." = $level
         WHERE city_id = ".$this->info['city_id']);
        }
    }
}
?>