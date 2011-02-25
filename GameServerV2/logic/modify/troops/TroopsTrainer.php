<?php
class TroopsTrainer extends MaterialSubstractor
{
    private $buildingsInfo = array();
    private $researchInfo = array();
    private $info = array();
    private $allCosts = array();

    public function TroopsTrainer()
    {
        $this->selectInfo();
        for($i=1; $i<=12; $i++){
            if(isset($_GET['tr'.$i])){
                if($this->isPossibleTechonlogically($i)){
                    $this->calculateCosts($type);
                    $this->loadSubstractor();
                    if($this->lookIfPossible()){
                        $this->insertTraining();
                        $this->substractMaterials();
                    }
                }
            }
        }
    }
    private function selectInfo()
    {
         LoadTroopCosts::getTroopCostsProperties(User::get_faction());
         $this->allCosts = &LoadTroopCosts::$troopCostsProperties;
         $this->buildingsInfo = DataBaseManager::fetchArray(DataBaseManager::query("SELECT * FROM {buildings} WHERE city_id = ".User::get_currentCityId()));
         $this->researchInfo = DataBaseManager::fetchArray(DataBaseManager::query("SELECT * FROM {research} WHERE user_id = ".User::get_idUser()));
    }
    private function calculateCosts($type)
    {
        $quantity = $_GET['tr'.$type];
        $this->costs[0] = $this->allCosts['Tr'.$type][0]*$quantity;
        $this->costs[1] = $this->allCosts['Tr'.$type][0]*$quantity;
        $this->costs[2] = $this->allCosts['Tr'.$type][0]*$quantity;
        $this->info['startTime'] = $this->startTime($type);
        $this->info['timeCost'] = $this->allCosts['Tr'.$type][3]*$this->calculateTimeBonus($type);
        $this->info['type'] = $type;
        $this->info['queue'] = $quantity;
        
    }
    private function insertTraining()
    {
        DataBaseManager::query("INSERT INTO {troops_training} (city_id,all_finish_time,next_finish_time,one_troop_time_cost,troop_type,queue) VALUES 
        (".User::get_currentCityId().",".Timer::addUNIXTime(($this->info['timeCost']*$this->info['queue'])).",
        ".Timer::addUNIXTime($this->info['timeCost'].",".$this->info['timeCost'].",".$this->info['type'].",".$this->info['queue'].")"));
    }
    private function isPossibleTechonlogically($type){
        switch ($type){
            //TODO technologies conditions in troops
        }
        return false;
        //return bool
    }
    private function startTime($type)
    {

        //barraks
        if($type<=4){
            $result = DataBaseManager::query("SELECT all_finish_time FROM {troops_training} WHERE city_id = ".User::get_currentCityId()." AND
            troop_type >= 1 AND troop_type <= 4 ORDER BY all_finish_time DESC");
        }
        //factory
        else if($type<=7){
            $result = DataBaseManager::query("SELECT all_finish_time FROM {troops_training} WHERE city_id = ".User::get_currentCityId()." AND
            troop_type >= 5 AND troop_type <= 7 ORDER BY all_finish_time DESC");
        }
        //airport
        else if($type<=10){
            $result = DataBaseManager::query("SELECT all_finish_time FROM {troops_training} WHERE city_id = ".User::get_currentCityId()." AND
            troop_type >= 8 AND troop_type <= 10 ORDER BY all_finish_time DESC");            
        }
        //headquarters
        else if($type <= 12){
            $result = DataBaseManager::query("SELECT all_finish_time FROM {troops_training} WHERE city_id = ".User::get_currentCityId()." AND
            troop_type >= 11 AND troop_type <= 12 ORDER BY all_finish_time DESC");
        }
        $startTime = DataBaseManager::fetchArray($result);
        //return unix time
        return $startTime[0];
    }
    private function calculateTimeBonus($type)
    {
        //TODO calculate troops time bonus
        return $bonus;
    }
}