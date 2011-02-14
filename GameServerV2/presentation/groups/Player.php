<?php
require_once APP.'logic/tools/Parser.php';
require_once APP.'datamanager/DataBaseManager.php';
class Player
{  
    public static function loadPlayerInfo($id){
        if(Parser::onlyNumbersString($id)){
            self::loadFromId($id);
        }else{
            self::loadFromName($id);
        }
    }
    private static function loadFromId($id){
        self::updatePuntuation($id);
        $player = array();
        $result = DataBaseManager::fetchArray(DataBaseManager::query("SELECT name,aly_id,puntuation,cities_number,description,language,faction,theme 
        FROM profile WHERE id = '$id' JOIN users ON profile.user_id = users.id_user JOIN aly_players ON profile.user_id = aly_players.user_id"));
        $player['NAME'] = $result['name'];
        $player['PUNTUATION'] = $result['puntuation'];
        $player['ALLY']['ID'] = $result['aly_id'];
        $player['CITIES_NUMBER'] = $result['cities_number'];
        $player['PROFILE']['DESCRIPTION'] = $result['description'];
        $player['PROFILE']['LANGUAGE'] = $result['language'];
        $player['PROFILE']['THEME_NAME'] = $result['theme'];
        $player['PROFILE']['FACTION'] = $result['faction'];
        
        $citiesIdsRes = DataBaseManager::query("SELECT city_id FROM buildings WHERE user_id = $id");
        while($citiesIds = DataBaseManager::fetchArray($citiesIdsRes)){
            $player['CITY_ID'][] = $citiesIds[0];
        }
        return $player;
    }
    private static function loadFromName($id){
        $resultn = DataBaseManager::fetchArray(DataBaseManager::query("SELECT id_user FROM users WHERE name = '$id'"));
        $result['ID'] = $resultn[0];
        return $result;
    }
    private static function updatePuntuation($id){
        
    }
}