<?php
/**
 * This class is only used when the univesity is updated or there's a frontiers conflict
 */
class UpdateFrontiers
{
    private $info = array();
    
    public function UpdateFrontiers($buildingsArray, $faction)
    {
        $this->selectInfo($faction);
        //select conflicted boxes
        $cityId = $buildingsArray['city_id'];
        $result = DataBaseManager::query("SELECT * FROM {map} WHERE (owner_city_id = $cityId AND secondary_city_id <> 0)
         OR secondary_owner_id = $city_id OR third_city_id = $cityId");
        while($info = DataBaseManager::fetchArray($result)){
            $this->info = &$info;
        }
    }
    private function selectBonus($faction,$universityLevel)
    {
        
        return $bonus;
    }
    private function recalculateFrontiers($boxId,$firstOwnerInfo,$secondInfo,$thirdInfo)
    {
        //Es fa amb el (nivell_universitat * punts_ciutat)/distancia
        
    }
    private function getAfectedCityInfo($cityId)
    {
        //return array(puntuation,university bonus)
    }
}