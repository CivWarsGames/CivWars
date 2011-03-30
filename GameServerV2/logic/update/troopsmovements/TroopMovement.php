<?php
abstract class TroopMovement
{
    protected $troopsMaintenanceCosts;
    protected $mInfo = array();

    /**
     *
     * @param $troops array tr1 => nº troops type 1 , tr2 => etc
     * @return The maintenance cost of all troops
     */
    protected function calculateTroopsMaintenances($troops,$faction)
    {
        LoadTroopCosts::getTroopCostsProperties($faction);
        $trCosts = &LoadTroopCosts::$troopCostsProperties;
        $cost = 0;
        for($i=1; i<=12; $i++){
            $cost += $trCosts['Tr'.$i][4]*$troops['tr'.$i];
        }
        return $cost;
    }
    /**
     *
     * Adds the $cost to the maintenance cost(if we want to substract pass $cost < 0)
     * @param $cost
     * @param $cityId
     * @param $isLoaded Says if costs are loaded in $this->troopsMaintenanceCosts
     */
    protected function updateMaintenance($cost,$cityId,$isLoaded = false)
    {
        if(!isLoaded){
            $res = DataBaseManager::fetchArray(DataBaseManager::query("SELECT troops_maintenance FROM {materials} WHERE city_id = $cityId"));
            $this->troopsMaintenanceCosts = $res[0];
        }
        $finalCost = $res + $cost;
        DataBaseManager::query("UPDATE {materials} SET troops_maintenance = $finalCost WHERE city_id = $cityId");
    }
    /**
     *
     * Updates a troop movement
     * Pre: The movement, is a finished movement
     * @param $info (new m info)
     * @param $movId the id of the movement to update
     * @param $newMovementType The type the movement that is going to be
     */
    protected function updateTroops($movId, $newMovementType,$info = false)
    {
        //if troops arrive at home ($newMovementType = 0) add to the current troops
        if($this->mInfo['owner_city_id'] == $this->mInfo['reciver_city_id']){
            $baseInfo = DataBaseManager::fetchArray(DataBaseManager::query("SELECT * FROM {troops_movements} WHERE movement_type = 0 AND 
            owner_city_id = ".$this->mInfo['owner_city_id']));
            for($i=1; $i<=12; $i++){
                $baseInfo['tr'.$i] += $this->mInfo['tr'.$i]; 
            }
            //update native city troops
            DataBaseManager::query("UPDATE {troops_movements} SET tr1 = $baseInfo[tr1], tr2 = $baseInfo[tr2], tr3 = $baseInfo[tr3], tr4 = $baseInfo[tr4],
             tr5 = $baseInfo[tr5], tr6 = $baseInfo[tr6], tr7 = $baseInfo[tr7], tr8 = $baseInfo[tr8], tr9 = $baseInfo[tr9], 
             tr10 = $baseInfo[tr10], tr11 = $baseInfo[tr11], tr12 = $baseInfo[tr12] WHERE mov_id = ".$baseInfo['mov_id']);
            //delete movement
            DataBaseManager::query("DELETE FROM {troops_movements} WHERE mov_id = ".$this->mInfo['mov_id']);
        }else{
            //If new troop data is passed
            if(is_array($troops)){
                DataBaseManager::query("UPDATE {troops_movements} SET movement_type = $newMovementType,
                reciver_city_id = $info[reciver_city_id] ,reciver_user_id = $info[reciver_user_id],
                 sender_city_id = $info[sender_city_id], arrival_time tr1 = $troops[tr1],
                 tr2 = $troops[tr2], tr3 = $troops[tr3], tr4 = $troops[tr4], tr5 = $troops[tr5],
                  tr6 = $troops[tr6], tr7 = $troops[tr7], tr8 = $troops[tr8], tr9 = $troops[tr9],
                   tr10 = $troops[tr10], tr11 = $troops[tr11], tr12 = $troops[tr12] WHERE mov_id = $movId");

            }else{
                DataBaseManager::query("UPDATE {troops_movements} SET movement_type = $newMovementType WHERE mov_id = $movId");
            }
        }
    }
}