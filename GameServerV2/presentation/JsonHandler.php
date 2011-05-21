<?php
require_once APP.'presentation/groups/Map.php';
require_once 'VarsContainer.php';

class JsonHandler
{
    private $json = array();

    public function JsonHandler()
    {

        /* data is an array of arrays ex:
         * $data = Array ( [0] => Array ( [0] => PLAYER [1] => 3 [2] => NAME )
         *               [1] => Array ( [0] => CITY [1] => 4 [2] => ALLY )
         *               ...
         *               )
         */
        $data = json_decode($_GET['data'],true);
        foreach ($data as $query){
            switch ($query[0]){
                case 'MAP':                   
                    $map = new Map();
                    
                    if($query[2] === "MATERIALS_MAP"){
                        $map->setInfoById($query[1], $query[3]);
                    }else{
                        $map->setInfoByCoords($query[1], $query[2], $query[3], $query[4]);
                    }
                    $this->json = $map->getInfo();
                    break;

                case 'PLAYER':
                case 'CITY':
                case 'MESSAGES':
                case 'BOX':
                    if(!isset($query[3])) $query[3] = NULL;
                    VarsContainer::loadObject($query[0], $query[1], $query[3]);
                    break;
                default:
                    VarsContainer::load($query[0]);
                    break;

            }
            if(isset($query[3])){
                $this->json[] = VarsContainer::$display[$query[0]][$query[1]][$query[2]][$query[3]];
            }else{
                $this->json[] = VarsContainer::$display[$query[0]][$query[1]][$query[2]];
            }
        }
    }

    public function getjson(){
        return json_encode($this->json);
    }
}