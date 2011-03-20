<?php
require_once APP.'logic/update/UpdateFrontiers.php';
class FrontiersUpdater
{
    private $info = array();
    private $buildingsInfo = array();
    private $universityLevel;

    public function FrontiersUpdater()
    {
        if(isset($_GET['box']) && Parser::onlyNumbersString($_GET['box'])){
            $result = DataBaseManager::query("SELECT * FROM {map} WHERE box_id = $_GET[box]");
            if(DataBaseManager::numRows($result) > 0){
                $this->info = DataBaseManager::fetchArray($result);
            }
        }else if(isset($_GET['cx']) && isset($_GET['cy']) &&
        Parser::onlyNumbersString($_GET['cx']) && Parser::onlyNumbersString($_GET['cy'])){
            $result = DataBaseManager::query("SELECT box_id FROM {map} WHERE x_coord = $_GET[cx] AND y_coord = $_GET[cy]");
            $this->info['x_coord'] = $_GET['cx'];
            $this->info['y_coord'] = $_GET['cy'];
            if(DataBaseManager::numRows($result) == 0 && $this->seeIfPossible()){
                $this->setNewBox($_GET[cx], $_GET[cy]);
            }
        }
    }
    private function seeIfPossible()
    {
        //get the level
        $this->buildingsInfo = DataBaseManager::fetchArray(DataBaseManager::query("SELECT * FROM {buildings} WHERE
         city_id = ".User::get_currentCityId()));
        $university = BuildingsUtils::getLevel($buildingName, $this->buildingsInfo, User::get_faction());
        $this->universityLevel = $university[0];
        $b = true;
        //count the number of controlled boxes
        $result = DataBaseManager::query("SELECT box_id FROM {map} WHERE owner_city_id = ".User::get_currentCityId());
        if(DataBaseManager::numRows($result >= $this->universityLevel)){
            $b = false;
        }
        //see if the box is adjacent (only cross)
        $x = $this->info['x_coord'];
        $y = $this->info['y_coord'];

        DataBaseManager::query("SELECT box_id FROM {map} WHERE ((x_coord = $x + 1 AND  y_coord = $y) OR
             (x_coord = $x - 1 AND  y_coord = $y) OR (x_coord = $x AND  y_coord = $y + 1) OR (x_coord = $x AND  y_coord = $y - 1))
             AND owner_city_id = ".User::get_currentCityId());
        if(DataBaseManager::query($result) == 0) $b = false;

        return b;
    }

    private function setNewBox($cx,$cy)
    {
        //insert
        $ownerCity  = User::get_currentCityId();
        $ownerUser = User::get_idUser();
        //x+y == pair type = metal else type = oil
        $type = ($cx + $cy)%2 == 2 ? 1 : 2;
        DataBaseManager::query("INSERT INTO {map} (x_coord,y_coord,owner_city_id,owner_user_id,natural_type,current_type)
         VALUES ($cx, $cy,$ownerCity,$ownerUser,$type,$type)");
    }
    private function createAFrontiersConfilct($boxId)
    {
        //update the 2nd/3rd box_owner
        if($this->info['secondary_city_id'] != 0){
            DataBaseManager::query("UPDATE {map} SET secondary_city_id = ".User::get_currentCityId()." WHERE box_id = $boxId");
        }else{
            DataBaseManager::query("UPDATE {map} SET third_city_id = ".User::get_currentCityId()." WHERE box_id = $boxId");
        }
        //new UpdateFrontiers
        new UpdateFrontiers($this->buildingsInfo, User::get_faction());
    }
}
?>