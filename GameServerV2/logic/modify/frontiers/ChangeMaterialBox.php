<?php
class ChangeMaterialBox
{
    public function ChangeMaterialBox()
    {
        if(Parser::onlyNumbersString($_GET['id']) && Parser::onlyNumbersString($_GET['to'])){
            $result = DataBaseManager::query("SELECT current_type,natural_type FROM {map} WHERE  box_id = $_GET[id]
            AND owner_city_id = ".User::get_currentCityId()." AND sieger_city_id = 0");
            if(DataBaseManager::numRows($result) > 0){
                $type = DataBaseManager::fetchArray($result);
                if($_GET['to'] != $type['current_type']){
                    if($_GET['to'] == 1 && $type['natural_type'] == 1){
                        DataBaseManager::query("UPDATE {map} SET current_type = $_GET[to] WHERE box_id = $_GET[id]");
                    }else if($_GET['to'] != 2 && $type['natural_type'] == 2){
                        DataBaseManager::query("UPDATE {map} SET current_type = $_GET[to] WHERE box_id = $_GET[id]");
                    }else if($_GET['to'] == 3 || $_GET['to'] == 4){
                        DataBaseManager::query("UPDATE {map} SET current_type = $_GET[to] WHERE box_id = $_GET[id]");
                    }
                }
            }
        }
    }
}