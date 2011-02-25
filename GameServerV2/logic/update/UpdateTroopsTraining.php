<?php
class UpdateTroopsTraining
{
    private $info = array();
    private $costs;
    private $troopCityInfo = array();
    private $troopMaintenance;

    public function UpdateTroopsTraining()
    {
        $result = DataBaseManager::query("SELECT * FROM {troops_training} WHERE next_finish_time <= NOW()");
        while($this->info = DataBaseManager::fetchArray($result)){
            $this->selectInfos();
            $x = $this->updateTraining();
            $this->updateCity($x);
        }
    }
    private function selectInfos()
    {
        LoadTroopCosts::getTroopCostsProperties(User::get_faction());
        $this->costs = &LoadTroopCosts::$troopCostsProperties['Tr'.$this->info['troop_type']][4];
        $this->troopCityInfo = DataBaseManager::fetchArray(DataBaseManager::query("SELECT * FROM {troop_movements} WHERE
         owner_city_id = ".$this->info['city_id']." AND movement_type = 0"));
        //movement type = 0 indicates that this are the troops created in the city
        $result = DataBaseManager::fetchArray(DataBaseManager::query("SELECT troops_maintenance FROM {materials} WHERE
         city_id = ".$this->info['city_id']));
        $this->troopMaintenance = $result[0];
    }
    private function updateTraining()
    {
        if($this->info['all_finish_time'] <= time()){
            //train all the queue
            DataBaseManager::query("DELETE FROM {troops_training} WHERE training_id = ".$this->info['city_id']);
            $x = $this->info['queue'];
        }else{
            //train x
            $timeExtra = time() - $this->info['next_finish_time'];
            $x = floor($timeExtra/$this->info['one_troop_time_cost']) + 1; //this last +1 is the one that was finished
            $nextFinishTime = $this->info['one_troop_time_cost'] - ($timeExtra % $this->info['one_troop_time_cost'])+time();
            DataBaseManager::query("UPDATE {troops_training} SET next_finish_time = $nextFinishTime, queue = ".($x - $this->info['queue']).
            " WHERE training_id = ".$this->info['city_id']);
        }
        return $x;
    }
    private function updateCity($troops)
    {
        $maintenance = $this->troopMaintenance + $troops*$this->costs;
        DataBaseManager::query("UPDATE {materials} SET troops_maintenance = $maintenance WHERE city_id = ".$this->info['city_id']);
        $troops += $this->troopCityInfo['tr'.$this->info['troop_type']];
        DataBaseManager::query("UPDATE {troop_movements} SET tr".$this->info['troop_type']." = $troops WHERE
         owner_city_id = ".$this->info['city_id']." AND movement_type = 0");
    }
}

?>