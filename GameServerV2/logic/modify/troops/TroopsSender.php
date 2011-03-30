<?php
require_once APP.'logic/tools/Parser.php';
require_once APP.'logic/tools/Timer.php';
require_once APP.'datamanager/prices/troops.php';

/**
 *
 * This class encodes troop movements types
 *
 */
class TroopsSender
{
    private $distance;
    private $troopCostsProperties = array();
    private $cityTroopsInfo = array();
    private $finalOil;
    private $reciverUserId;

    public function TroopsSender()
    {
        if($this->seeIfPossible()){
            LoadTroopCosts::getTroopCostsProperties(User::get_faction());
            $this->troopCostsProperties = &LoadTroopCosts::$troopCostsProperties;
            $this->sendTroops();
        }
    }
    /**
     * Looks if the values are valid and correct
     */
    private function seeIfPossible()
    {
        $correct = true;
        if(Parser::onlyNumbersString($_GET['o'])){
            if($_GET['o'] == User::get_currentCityId()){
                $this->cityTroopsInfo = DataBaseManager::fetchArray(DataBaseManager::query("SELECT * FROM {troops_movements} WHERE
             reciver_id = $_GET[o] and movement_type = 0"));
            }else{
                $this->cityTroopsInfo = DataBaseManager::fetchArray(DataBaseManager::query("SELECT * FROM {troops_movements} WHERE
             reciver_city_id = $_GET[o] and owner_city_id = ".User::get_currentCityId()." and movement_type = 10"));
            }
            $correct = $correct && (!(Parser::onlyNumbersString($_GET['t']) && $_GET['t'] >= 0 && $_GET['t'] <= 6));
            $i = 1;
            $ttrops = 0;
            while($correct && $i<12){
                if(!(isset($_GET['tr'.$i]) && Parser::onlyNumbersString($_GET['tr'.$i]) && $_GET['tr'.$i] >= 0 && $this->cityTroopsInfo['tr'.$i] >= $_GET['tr'.$i] )){
                    $correct = false;
                }else{
                    $ttrops += $_GET['tr'.$i];
                }
                $i++;
            }
            $correct = $correct && ($ttrops > 0) && Parser::onlyNumbersString($_GET['d'] && $_GET['d'] > 0);
            if($correct){
                $exsistsd = DataBaseManager::query("SELECT owner_user_id FROM {map} WHERE box_id = $_GET[d]");
                $this->calculateDistance();
                $oil = DataBaseManager::fetchArray(DataBaseManager::query("SELECT oil FROM {materials} WHERE city_id = ".User::get_currentCityId()));
                $oil = $oil[0];
                $this->finalOil = $oil - $this->calculateOil();
                $correct = ($this->finalOil >= 0 && DataBaseManager::numRows($exsistsd));
                $reciverUserId = DataBaseManager::fetchArray($exsistsd);
                $this->reciverUserId = $reciverUserId[0];
            }
        }else{
            $correct = false;
        }
        return $correct;

    }
    private function calculateDistance()
    {
        $o = DataBaseManager::fetchArray(DataBaseManager::query("SELECT coord_x,coord_y FROM {map} WHERE box_id = $_GET[o]"));
        $d = DataBaseManager::fetchArray(DataBaseManager::query("SELECT coord_x,coord_y FROM {map} WHERE box_id = $_GET[d]"));
        $dx = $d['coord_x'] -$o['coord_x'];
        $dy = $d['coord_y'] -$o['coord_y'];
        $this->distance = sqrt($dx*$dx + $dy*$dy);
    }
    private function calculateTime()
    {
        //speed boxes/hour of the slowest used troop
        $slowest = 1000000;
        for($i=1; $i<=12; $i++){
            if($_GET['Tr'.$i] > 0 && $slowest > $this->troopCostsProperties['Tr'.$i][5] ){
                $slowest = $this->troopCostsProperties['Tr'.$i][5];
            }
        }
        $time = $this->distance/($slowest*SERVER_SPEED_RATE);
        return $time;
    }
    private function calculateOil()
    {
        //oil cost
        $oil = 0;
        for($i=1; $i<12; $i++){
            if($_GET['Tr'.$i] > 0 ){
                $oil += $this->troopCostsProperties['Tr'.$i][11];
            }
        }
        return $oil*$this->distance;
    }
    private function sendTroops()
    {
        $time = Timer::addUNIXTime($this->calculateTime());
        DataBaseManager::query("UPDATE {materials} SET oil = ".$this->finalOil." WHERE city_id = ".User::get_currentCityId());
        DataBaseManager::query("INSERT INTO {troops_movements} (movement_type,owner_city_id,owner_user_id,reciver_city_id,
        reciver_user_id,sender_city_id,arrival_time,tr1,tr2,tr3,tr4,tr5,tr6,tr7,tr8,tr9,tr10,tr11,tr12)
         VALUES ($_GET[t],".$this->cityTroopsInfo['owner_city_id'].",".$this->cityTroopsInfo['owner_user_id'].",$_GET[d],
         ".$this->reciverUserId.",".$this->cityTroopsInfo['reciver_city_id'].",$time,$_GET[tr1],$_GET[tr2],$_GET[tr3],$_GET[tr4]
         ,$_GET[tr5],$_GET[tr6],$_GET[tr7],$_GET[tr8],$_GET[tr9],$_GET[tr10],$_GET[tr11],$_GET[tr12])");
        //final troops
        $t = array();
        $ttrops = 0;
        for($i=1; $i<=12; $i++){
            $t[$i] = $this->cityTroopsInfo['tr'.$i] - $_GET['tr'.$i];
            $ttrops += $t[$i];
        }
        //if there left no troops in the city or there isn't the troop origin city => delete
        if($ttrops > 0 || $_GET['o'] == User::get_currentCityId()){
            DataBaseManager::query("UPDATE {troops_movements} SET tr1 = $t[1],tr2 = $t[2],tr3 = $t[3],tr4 = $t[4],tr5 = $t[5],
            tr6 = $t[6],tr7 = $t[7],tr8 = $t[8],tr9 = $t[9],tr10 = $t[10],tr11 = $t[11],tr12 = $t[12]
            WHERE mov_id = ".$this->cityTroopsInfo['mov_id']);
        }else{
            DataBaseManager::query("DELETE FROM {troops_movements} WHERE mov_id = ".$this->cityTroopsInfo['mov_id']);
        }
    }
}