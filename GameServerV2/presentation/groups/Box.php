<?php

class Box
{
    public static function getInfoFromId($id)
    {
        $result = DataBaseManager::fetchArray(DataBaseManager::query("SELECT * FROM {map} WHERE box_id = '$id'"));
        return self::presentInfo($result);
    }
    public static function getInfoFromCoords($x, $y)
    {
        $result = DataBaseManager::fetchArray(DataBaseManager::query("SELECT * FROM {map} WHERE x_coord = '$x' and y_coord = '$y"));
        return self::presentInfo($result);
    }
    private static function presentInfo($result)
    {
        $box = array();
        switch ($result['current_type']){
            case 1: $box['TYPE'] = "CITY";
            break;
            case 2: $box['TYPE'] = "METAL";
            break;
            case 3: $box['TYPE'] = "OIL";
            break;
            case 4: $box['TYPE'] = "INDUSTRY";
            break;
            case 5: $box['TYPE'] = "POWER PLANT";
            break;
        }
        $box['ID'] = $result['box_id'];
        $box['COORD']['X'] = $result['x_coord'];
        $box['COORD']['Y'] = $result['y_coord'];
        $box['OWNER_USER'] = $result['owner_user_id'];
        $box['OWNER'][0] = $result['owner_city_id'];
        $box['OWNER'][1] = $result['secondary_city_id'];
        $box['OWNER'][2] = $result['third_city_id'];
        $box['PERCENTAGE'][1] = $result['secondary_city_percentage'];
        $box['PERCENTAGE'][2] = $result['third_city_percentage'];
        $box['PERCENTAGE'][0] = 100-$box['PERCENTAGE'][1]-$box['PERCENTAGE'][2];
        $box['SIEGER'] = $result['sieger_city_id'];
        $box['LEVEL'] = $result['box_level'];
        return $box;
    }
}
?>