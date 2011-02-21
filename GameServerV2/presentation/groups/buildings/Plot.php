<?php
require_once APP.'logic/VarCollector.php';
require_once APP.'logic/tools/BuildingsUtils.php';
require_once APP.'logic/User.php';
require_once APP.'datamanager/prices/buildings.php';
require_once APP.'logic/tools/Timer.php';

/**
 *
 * When there's an empty plot, it's loaded a list with all the available buildings
 *
 */
class Plot
{
    public $availableBuildingsList = array();
    private $buildingsInfo = array();
    private $researchInfo = array();
    private $timeBonus;
    private $currentImprovement;


    public function Plot()
    {
        $this->createAvalableBuildingsList();
    }

    private function createAvalableBuildingsList()
    {
        $faction = User::get_faction();
        LoadBuildingsCosts::getbuildingCosts($faction);
        $prices = &LoadBuildingsCosts::$buildingCosts;

        $this->buildingsInfo = DataBaseManager::fetchArray(DataBaseManager::query("SELECT * FROM {buildings}
		WHERE city_id =".User::get_currentCityId()));

        $buildingsInfo = &$this->buildingsInfo;

        $this->currentImprovement = $buildingsInfo['current_improvement'];

        $this->researchInfo = DataBaseManager::fetchArray(DataBaseManager::query("SELECT * FROM
		{research} WHERE user_id =".User::get_idUser()));

        $researchInfo = &$this->researchInfo;
        $this->timeBonus = BuildingsUtils::getTimeBonus($faction, $buildingsInfo, $researchInfo);

        //Command Center
        $level['COMMAND_CENTER'] = BuildingsUtils::getLevel('COMMAND_CENTER', $buildingsInfo);
        if(!isset($level['COMMAND_CENTER'][0])){
            $this->availableBuildingsList[]['NAME'] = 'COMMAND_CENTER';
            $this->lookMaterials('COMMAND_CENTER');
        }


        //Warehouse
        $level['WAREHOUSE'] = BuildingsUtils::getLevel('WAREHOUSE', $buildingsInfo);
        $maxLevel = 0;
        foreach ($level['WAREHOUSE'] as $key => $value){
            $maxLevel = max($value, $maxLevel);
        }
        //If a warehouse is higher of 20 it can be builded a second one
        if($maxLevel >= 20){
            $this->availableBuildingsList[]['NAME'] = 'WAREHOUSE';
            $this->lookMaterials('WAREHOUSE');

        }elseif($maxLevel == 0 && $buildingsInfo['current_improvement'] != 1 && $level['COMMAND_CENTER'][0] >= 1){
            $this->availableBuildingsList[]['NAME'] = 'WAREHOUSE';
            $this->lookMaterials('WAREHOUSE');

        }
        $level['WAREHOUSE'][0] = $maxLevel;


        //Bank (same as warehouse)
        $level['BANK'] = BuildingsUtils::getLevel('BANK', $buildingsInfo);
        $maxLevel = 0;
        foreach ($level['BANK'] as $key => $value){
            $maxLevel = max($value, $maxLevel);
        }
        if($maxLevel >= 20){
            $this->availableBuildingsList[]['NAME'] = 'BANK';
            $this->lookMaterials('BANK');
        }elseif($maxLevel == 0 && $buildingsInfo['current_improvement'] != 1 && $level['COMMAND_CENTER'][0] >= 1){
            $this->availableBuildingsList[]['NAME'] = 'BANK';
            $this->lookMaterials('BANK');
        }
        $level['BANK'][0] = $maxLevel;


        //Barraks
        $level['BARRAKS'] = BuildingsUtils::getLevel('BARRAKS', $buildingsInfo);
        if(!isset($level['BARRAKS'][0]) && $level['COMMAND_CENTER'][0] >= 3){
            $this->availableBuildingsList[]['NAME'] = 'BARRAKS';
            $this->lookMaterials('BARRAKS');
        }

        //Market
        $level['MARKET'] = BuildingsUtils::getLevel('MARKET', $buildingsInfo);
        if(!isset($level['MARKET'][0]) && $level['COMMAND_CENTER'][0] >= 4 &&
        $level['WAREHOUSE'][0] >= 3 && $level['BANK'][0] >= 3){
            $this->availableBuildingsList[]['NAME'] = 'MARKET';
            $this->lookMaterials('MARKET');
        }

        //Research lab
        $level['RESEARCH_LAB'] = BuildingsUtils::getLevel('RESEARCH_LAB', $buildingsInfo);
        if(!isset($level['RESEARCH_LAB'][0]) && $level['COMMAND_CENTER'][0] >= 5){
            $this->availableBuildingsList[]['NAME'] = 'RESEARCH_LAB';
            $this->lookMaterials('RESEARCH_LAB');
        }

        //Factory
        $level['FACTORY'] = BuildingsUtils::getLevel('FACTORY', $buildingsInfo);
        if(!isset($level['FACTORY'][0]) && $level['RESEARCH_LAB'][0] >= 2 &&
        $researchInfo['reciprocating_engine'] == 1){
            $this->availableBuildingsList[]['NAME'] = 'FACTORY';
            $this->lookMaterials('FACTORY');
        }

        //Airport
        $level['AIRPORT'] = BuildingsUtils::getLevel('AIRPORT', $buildingsInfo);
        if(!isset($level['AIRPORT'][0]) && $level['RESEARCH_LAB'][0] >= 7
        && $level['COMMAND_CENTER'][0] >= 10 &&	 $researchInfo['fly'] == 1){
            $this->availableBuildingsList[]['NAME'] = 'AIRPORT';
            $this->lookMaterials('AIRPORT');
        }

        //Headquarters
        $level['HEADQUARTERS'] = BuildingsUtils::getLevel('HEADQUARTERS', $buildingsInfo);
        if(!isset($level['HEADQUARTERS'][0]) 	&& $level['COMMAND_CENTER'][0] >= 12){
            $this->availableBuildingsList[]['NAME'] = 'HEADQUARTERS';
            $this->lookMaterials('HEADQUARTERS');
        }

        //Consulate
        $level['UNIVERSITY'] = BuildingsUtils::getLevel('UNIVERSITY', $buildingsInfo);
        if(!isset($level['UNIVERSITY'][0]) 	&& $level['COMMAND_CENTER'][0] >= 2){
            $this->availableBuildingsList[]['NAME'] = 'UNIVERSITY';
            $this->lookMaterials('UNIVERSITY');
        }

        //Training Camp
        $level['TRAINING_CAMP'] = BuildingsUtils::getLevel('TRAINING_CAMP', $buildingsInfo);
        if(!isset($level['TRAINING_CAMP'][0]) 	&& $level['AIRPORT'][0] >= 5 &&
        $level['FACTORY'][0] >= 10 && $level['BARRAKS'][0] >= 15){
            $this->availableBuildingsList[]['NAME'] = 'TRAINING_CAMP';
            $this->lookMaterials('TRAINING_CAMP');
        }

        //Advanced lab
        $level['ADVANCED_LAB'] = BuildingsUtils::getLevel('ADVANCED_LAB', $buildingsInfo);
        if(!isset($level['ADVANCED_LAB'][0]) 	&& $level['COMMAND_CENTER'][0] >= 15
        && $level['RESEARCH_LAB'][0] >= 15){
            $this->availableBuildingsList[]['NAME'] = 'ADVANCED_LAB';
            $this->lookMaterials('ADVANCED_LAB');
        }

        //STEEL_FACTORY
        $level['STEEL_FACTORY'] = BuildingsUtils::getLevel('STEEL_FACTORY', $buildingsInfo);
        if(!isset($level['STEEL_FACTORY'][0]) 	&& $level['MARKET'][0] >= 10
        && $researchInfo['improved_metal_extraction'] == 1){
            $select = DataBaseManager::query("SELECT box_id FROM {map} WHERE owner_city_id = ".User::get_currentCityId()."
			AND box_level >=10 AND current_type = 2 AND sieger_city_id = 0");
            if(DataBaseManager::numRows($select) > 0){
                $this->availableBuildingsList[]['NAME'] = 'STEEL_FACTORY';
                $this->lookMaterials('STEEL_FACTORY');
            }
        }

        //REFINERY
        $level['REFINERY'] = BuildingsUtils::getLevel('REFINERY', $buildingsInfo);
        if(!isset($level['REFINERY'][0]) 	&& $level['MARKET'][0] >= 10
        && $researchInfo['improved_oil_extraction'] == 1){
            $select = DataBaseManager::query("SELECT box_id FROM {map} WHERE owner_city_id = ".User::get_currentCityId()."
			AND box_level >=10 AND current_type = 3 AND sieger_city_id = 0");
            if(DataBaseManager::numRows($select) > 0){
                $this->availableBuildingsList[]['NAME'] = 'REFINERY';
                $this->lookMaterials('REFINERY');
            }
        }

        //STOCK_EXCHANGE
        $level['STOCK_EXCHANGE'] = BuildingsUtils::getLevel('STOCK_EXCHANGE', $buildingsInfo);
        if(!isset($level['STOCK_EXCHANGE'][0]) 	&& $level['MARKET'][0] >= 10
        && $researchInfo['investments'] == 1){
            $select = DataBaseManager::query("SELECT box_id FROM {map} WHERE owner_city_id = ".User::get_currentCityId()."
			AND box_level >=10 AND current_type = 4 AND sieger_city_id = 0");
            if(DataBaseManager::numRows($select) > 0){
                $this->availableBuildingsList[]['NAME'] = 'STOCK_EXCHANGE';
                $this->lookMaterials('STOCK_EXCHANGE');
            }
        }
    }

    private function lookMaterials($buildingName)
    {
        //this function is only useful if its behind UpdateMaterials
        $materials = &VarCollector::$materials['QUANTITY'];
        if($materials['METAL']>= $this->availableBuildingsList[$buildingName]['COSTS'][0] &&
        $materials['OIL']>= $this->availableBuildingsList[$buildingName]['COSTS'][1] &&
        $materials['GOLD']>= $this->availableBuildingsList[$buildingName]['COSTS'][2]){
            if($this->currentImprovement == 0){
                $this->availableBuildingsList[(count($this->availableBuildingsList)-1)]['IMPROVING_POSSIBLE'] = 'YES';
            }else{
                $this->availableBuildingsList[(count($this->availableBuildingsList)-1)]['IMPROVING_POSSIBLE'] = 'THERE_IS_AN_UPGRADE_IN_PROGRESS';
            }
        }else{
            $this->availableBuildingsList[(count($this->availableBuildingsList)-1)]['IMPROVING_POSSIBLE'] = 'THERE_ARENT_MATERIALS';
        }
    }


}