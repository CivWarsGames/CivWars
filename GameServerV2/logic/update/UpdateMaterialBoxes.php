<?php
//There can be problems if someone sieges a box while upgrading it!
class UpdateMaterialBoxes
{
    private $info = array();
    private $boxInfo = array();
    private $costs = array();
    private $maintenances = array();
    private $type;

    public function  UpdateMaterialBoxes()
    {
        $results = DataBaseManager::query("SELECT * FROM {materials} WHERE mat_box_id_cur_improvement <> 0 and
         mat_box_finish_time <= NOW ");
        while($result = DataBaseManager::fetchArray($results)){
            $this->info = &$result;
            $this->selectInfo();
            $this->calculateCosts();
            $this->updateCity();
        }
    }
    private function selectInfo()
    {
        $this->boxInfo = DataBaseManager::fetchArray(DataBaseManager::query("SELECT current_type, box_level, sieger_city_id FROM {map} WHERE
        box_id = ".$this->info['mat_box_id_cur_improvement']));
        LoadMaterialsCosts::getmaterialCosts(User::get_faction());
        LoadMaterialsCosts::getmaterialNames(User::get_faction());
        $names = &LoadMaterialsCosts::$materialNames;
        $this->costs = &LoadMaterialsCosts::$materialCosts[$this->boxInfo['current_type'-2]][$this->boxInfo['box_level']];
    }
    private function calculateCosts()
    {
        if($this->boxInfo['current_type'] == 2){
            $this->type = 'metal';
        }else if($this->boxInfo['current_type'] == 3){
            $this->type = 'oil';
        }else if($this->boxInfo['current_type'] == 4){
            $this->type = 'gold';
        }
        if($this->boxInfo['current_type'] != 5){
            //only the 25% of maintenance cost is reduced when you reduce the production to 50%
            $this->maintenances['maintenance'] = $this->costs[4]/2*(1+$this->info[$this->type.'_percentage']/100) + $this->info[$this->type.'_maintenance_cost'];
            //when you reduce 50% your production 50% of energy cost is reduced
            $this->maintenances['energy'] = $this->costs[3]*($this->info[$this->type.'_percentage']/100) + $this->info[$this->type.'_energy_cost'];
        }else{
            $this->maintenances['maintenance'] =  $this->costs[4] + $this->info['buildings_maintenance'];
        }
    }
    private function updateCity()
    {
        new UpdateMaterials($this->info['city_id'],$this->info['mat_box_finish_time']);
        $level = $this->boxInfo['box_level']+1;
        DataBaseManager::query("UPDATE {map} SET box_level = $level WHERE box_id = ".$this->boxInfo['box_id']);
        //power plants maintenance cost is added to buildings maintenance
        if($this->boxInfo['current_type'] == 5){
            DataBaseManager::query("UPDATE {materials} SET buildings_maintenance = ".$this->maintenances['maintenance'].",
             WHERE city_id = ".$this->info['city_id']);    
        }else{
            DataBaseManager::query("UPDATE {materials} SET ".$this->type."_maintenance_cost = ".$this->maintenances['maintenance'].",
         ".$this->type."_energy_cost = ".$this->maintenances['energy']." WHERE city_id = ".$this->info['city_id']);           
        }

    }
}