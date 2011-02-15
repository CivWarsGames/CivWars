<?php
require_once APP.'logic/tools/Parser.php';
require_once APP.'datamanager/DataBaseManager.php';
class City
{
    public static function loadCityInfo($id){
        if(Parser::onlyNumbersString($id)){
            return self::loadFromId($id);
        }else{
            return self::loadFromName($id);
        }
    }
    private static function loadFromId($id){
        $city = array();
        $result = DataBaseManager::fetchArray(DataBaseManager::query("SELECT current_type, owner_user_id, city_name, x_coord, y_coord,
         buildings_maintenance FROM {map} JOIN {materials} ON box_id = city_id WHERE box_id = '$id'"));
        if($result['current_type'] == 1){
            $city['OWNER']['ID'] = $result['owner_user_id'];
            $city['NAME'] = $result['city_name'];
            $city['PUNTUATION'] = $result['buildings_maintenance'];
            $city['COORD']['X'] = $result['x_coord'];
            $city['COORD']['Y'] = $result['y_coord'];
        }else{
            $city[0] = 0;
        }
        return $city;

    }
    private static function loadFromName($id){
        $id = Parser::characterTraductor($id);
        $resultn = DataBaseManager::fetchArray(DataBaseManager::query("SELECT owner_city_id FROM {map} WHERE city_name = '$id'"));
        $result['ID'] = $resultn[0];
        return $result;
    }
}