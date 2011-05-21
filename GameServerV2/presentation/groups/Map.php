<?php
class Map
{
    public $mapCoords = array();//associative array x0 y0 dx dy
    public $boxes = array(); // Box[]


    public function Map()
    {

    }
    //(x0,y0) = top-left corner
    public function setInfoByCoords($x0,$y0,$dx,$dy)
    {
        //get boxes info
        $info = DataBaseManager::query("SELECT * FROM {map} WHERE x_coord >= $x0 AND x_coord <= ".($x0+$dx)." AND y_coord <= $y0 AND
        y_coord >= ".($y0-$dy)." ORDER BY y_coord DESC, x_coord ASC");
        $this->mapCoords = array("x0" => $x0, "y0" => $y0, "dx" => $dx, "dy" => $dy);
        //fill map
        $this->boxes = $this->fillMap($info,$x0,$y0,$dx,$dy);
        
    }
    public function setInfoById($boxId,$size)
    {
        $size = (!($size <= 5 && $size >= 1)) ? 5 : $size;
        //get central coords
        $query = DataBaseManager::query("SELECT x_coord, y_coord FROM {map} WHERE box_id = $boxId");
        if(DataBaseManager::numRows($query) == 0){
            $query = DataBaseManager::query("SELECT x_coord, y_coord FROM {map} WHERE box_id = ".User::get_currentCityId());
        }
        $centralCoords = DataBaseManager::fetchArray($query);
        //get top-left coords
        $x0 =$centralCoords['x_coord'] - $size + 1;
        $y0 = $centralCoords['y_coord'] + $size - 1;
        $dx = 2*$size - 1;
        $dy = $dx;

        $this->setInfoByCoords($x0, $y0, $dx, $dy);

    }
    public function getInfo()
    {
        return $this->boxes;
    }
    private function fillMap($info,$x0,$y0,$dx,$dy)
    {
        $dbinfo = array();
        $res = array();
        while($infx = DataBaseManager::fetchArray($info)){
            $dbinfo[] = $infx;
        }
        
        $xi = $x0;
        $yi = $y0;
        $bi = 0;
        $k = 0;
        while($yi >= $y0- $dy){
            while($xi <= $x0 + $dx){
                if($dbinfo[$bi]['x_coord'] == $xi && $dbinfo[$bi]['y_coord'] == $yi){
                    $res[$k]['BOX']['TYPE'] = $dbinfo[$bi]['current_type'];
                    $res[$k]['BOX']['ORIGINAL_TYPE'] = $dbinfo[$bi]['natural_type'];
                    $res[$k]['BOX']['COORD']['X'] = $dbinfo[$bi]['x_coord'];
                    $res[$k]['BOX']['COORD']['Y'] = $dbinfo[$bi]['y_coord'];
                    $res[$k]['BOX']['OWNER'][0] = $dbinfo[$bi]['owner_city_id'];
                    $res[$k]['BOX']['OWNER'][1] = $dbinfo[$bi]['secondary_city_id'];
                    $res[$k]['BOX']['OWNER'][2] = $dbinfo[$bi]['third_city_id'];
                    $res[$k]['BOX']['PERCENTAGE'][0]= "".(100 - $dbinfo[$bi]['secondary_city_percentage'] - $dbinfo[$bi]['third_city_percentage'])."";
                    $res[$k]['BOX']['PERCENTAGE'][1] = $dbinfo[$bi]['secondary_city_percentage'];
                    $res[$k]['BOX']['PERCENTAGE'][2] = $dbinfo[$bi]['third_city_percentage'];
                    $res[$k]['BOX']['SIEGER'] = $dbinfo[$bi]['sieger_city_id'];
                    $res[$k]['BOX']['LEVEL'] = $dbinfo[$bi]['box_level'];
                    $res[$k]['BOX']['OWNER_USER'] = $dbinfo[$bi]['owner_user_id'];
                    if($dbinfo[$bi]['current_type'] == 1){
                        $res[$k]['BOX']['NAME'] = $dbinfo[$bi]['city_name'];
                        $puntuation = DataBaseManager::fetchArray(DataBaseManager::query("SELECT buildings_maintenance FROM {materials}
                         WHERE city_id = ".$dbinfo[$bi]['box_id']));
                        $res[$k]['BOX']['PUNTUATION'] = $puntuation[0];
                    }
                    $bi++;
                }else{
                    $res[$k]['BOX']['TYPE'] = ($xi + $yi)%2 == 0 ? "2" : "3";
                    $res[$k]['BOX']['ORIGINAL_TYPE'] = ($xi + $yi)%2 == 0 ? "2" : "3";
                    $res[$k]['BOX']['COORD']['X'] = "$xi";
                    $res[$k]['BOX']['COORD']['Y'] = "$yi";
                    $res[$k]['BOX']['OWNER'][0] = "0";
                    $res[$k]['BOX']['OWNER'][1] = "0";
                    $res[$k]['BOX']['OWNER'][2] = "0";
                    $res[$k]['BOX']['PERCENTAGE'][0]= "0";
                    $res[$k]['BOX']['PERCENTAGE'][1] = "0";
                    $res[$k]['BOX']['PERCENTAGE'][2] = "0";
                    $res[$k]['BOX']['SIEGER'] = "0";
                    $res[$k]['BOX']['LEVEL'] = "0";
                    $res[$k]['BOX']['OWNER_USER'] = "0";

                }
                $xi++;
                $k++;
            }
            $xi = $x0;
            $yi--;
        }
        return $res;
    }
}