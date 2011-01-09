<?php
if(!defined('APP')){
	require_once '../../../pathBuilder.php';
}
require_once APP.'datamanager/DataBaseManager.php';
require_once APP.'logic/tools/BuildingsUtils.php';
require_once APP.'datamanager/prices/researches.php';
require_once APP.'datamanager/prices/buildings.php';
require_once APP.'datamanager/prices/materials.php';
require_once APP.'presentation/VarsContainer.php';
require_once APP.'logic/User.php';

/**
 *
 * This class updates the materials of a city and stores some results that the theme will need
 *
 */
class UpdateMaterials
{
	private $_cityId;
	private $_materialsBoxesInfo = array();
	private $_materialsInfo = array();
	private $_research = array();
	private $_buildingsInfo = array();
	private $_besiegedBoxesInfo = array();
	private $_quantity = array();
	private $_faction;
	private $_finalTime;
	private $_initialTime;

	public function __construct($cityId, $finalTime = "NOW")
	{
		//set vars
		$this->_cityId = $cityId;
		$this->_finalTime = $finalTime;
		$this->_initialTime =  "LastUpdate";

		//do work
		$this->selectData();
		$this->calculateData();
		$this->updateData();

	}

	private function selectData()
	{
		$idUser = DataBaseManager::fetchArray(DataBaseManager::query("SELECT owner_user_id FROM {map} WHERE id =".$this->_cityId));
		$this->faction = DataBaseManager::fetchArray(DataBaseManager::query("SELECT faction FROM {profile} WHERE user_id = ".$idUser));
		
		$this->_materialsInfo = DataBaseManager::fetchArray(DataBaseManager::query("SELECT * FROM {materials}
		 WHERE city_id = ".$this->_cityId));

		$this->_initialTime = $this->_initialTime == "LastUpdate" ? $this->_materialsInfo['update_time'] : $this->_initialTime;
		$this->_finalTime = ($this->_finalTime == "NOW") ? time() : $this->_finalTime;
		$this->_finalTime = $this->_finalTime >= $this->_initialTime ? $this->_finalTime : 0;

		$this->_research = DataBaseManager::fetchArray(DataBaseManager::query("SELECT improved_metal_extraction,
		improved_oil_extraction,investments,thermal_energy,solar_energy,fission_nuclear_energy,fusion_nuclear_energy
		 FROM {research} WHERE user_id = ".$this->_materialsInfo['user_id'] ));

		$this->_buildingsInfo = DataBaseManager::fetchArray(DataBaseManager::query("SELECT * FROM {buildings} WHERE city_id = ".$this->_cityId));

		//Normal material boxes
		$materialsBoxesSelect= DataBaseManager::query("SELECT box_id, box_level,
		 current_type FROM {map} WHERE owner_city_id = ".$this->_cityId." AND current_type <> 1 AND sieger_city_id = 0");

		while ($materialsBoxesInfo = DataBaseManager::fetchArray($materialsBoxesSelect)){
			$this->_materialsBoxesInfo[] = $materialsBoxesInfo;
		}

		//Besieged boxes selection
		$besiegedBoxesSelect = DataBaseManager::query("SELECT box_id, box_level,
		 current_type FROM {map} WHERE current_type <> 1 AND sieger_city_id = ".$this->_cityId."");

		while ($besiegedBoxesInfo = DataBaseManager::fetchArray($besiegedBoxesSelect)){
			$this->_besiegedBoxesInfo[] = $besiegedBoxesInfo;
		}
	}

	private function calculateData()
	{
		//research bonuses
		$researchesProperties = &LoadResearchesCosts::getResearchesProperties($this->faction);

		$bonus['METAL'] = $this->_research['improved_metal_extraction'] == 1 ? $researchesProperties['IMPROVED_METAL_EXTRACTION'] : 1;
		$bonus['OIL'] = $this->_research['improved_oil_extraction'] == 1 ? $researchesProperties['IMPROVED_OIL_EXTRACTION'] : 1;
		$bonus['GOLD'] = $this->_research['investments'] == 1 ? $researchesProperties['INVESTMENTS'] : 1;
		$bonus['ENERGY'] = ($this->_research['thermal_energy'] == 1 ? $researchesProperties['THERMAL_ENERGY'] : 1)*($this->_research['solar_energy'] == 1 ? $researchesProperties['SOLAR_ENERGY'] : 1)*($this->_research['fission_nuclear_energy'] == 1 ? $researchesProperties['FISSION_NUCLEAR_ENERGY'] : 1)*($this->_research['fusion_nuclear_energy'] == 1 ? $researchesProperties['FUSION_NUCLEAR_ENERGY'] : 1);

		//building bonuses
		//level
		$level['STEEL_FACTORY'] = BuildingsUtils::getLevel('STEEL_FACTORY', $this->_buildingsInfo);
		$level['STEEL_FACTORY'] = isset($level['STEEL_FACTORY'][0]) ? $level['STEEL_FACTORY'][0] : 0;
		$level['REFINERY'] = BuildingsUtils::getLevel('REFINERY', $this->_buildingsInfo);
		$level['REFINERY'] = isset($level['REFINERY'][0]) ? $level['REFINERY'][0] : 0;
		$level['STOCK_EXCHANGE'] = BuildingsUtils::getLevel('STOCK_EXCHANGE', $this->_buildingsInfo);
		$level['STOCK_EXCHANGE'] = isset($level['STOCK_EXCHANGE'][0]) ? $level['STOCK_EXCHANGE'][0] : 0;
		//bonus
		$buildingsProperties = &LoadBuildingsCosts::getbuildingProperties($this->faction);	
		
		$bonus['METAL'] *= $buildingsProperties['STEEL_FACTORY'][$level['STEEL_FACTORY']];
		$bonus['OIL'] *= $buildingsProperties['REFINERY'][$level['REFINERY']];
		$bonus['GOLD'] *= $buildingsProperties['STOCK_EXCHANGE'][$level['STOCK_EXCHANGE']];

		//Material Boxes and production
		$materialsProduction = &LoadMaterialsCosts::getmaterialProduction($this->faction);
		$materialsName = &LoadMaterialsCosts::getmaterialNames($this->faction);

		//Normal material boxes loop
		foreach ($this->_materialsBoxesInfo as $key => $materialBoxInfo){
			$type = $materialBoxInfo['current_type']+2;
			$materialName = $materialsName[$type];
			$level = $materialBoxInfo['box_level'];
			$hourProduction[$materialName] += $materialsProduction[$materialName][$level];
		}

		//Besieged material boxes loop they only produce the 25% but their maintenace cost is 50%
		foreach ($this->_besiegedBoxesInfo as $key => $materialBoxInfo){
			$type = $materialBoxInfo['current_type']+2;
			$materialName = $materialsName[$type];
			$level = $materialBoxInfo['box_level'];

			//ONLY 25% of the normal production
			$hourProduction[$materialName] += $materialsProduction[$materialName][$level]/4;
		}

		//Add bonuses + SERVER_SPEED_RATE
		$hourProduction['METAL'] *= SERVER_SPEED_RATE * $bonus['METAL']* $this->_materialsInfo['metal_percentage']/100;
		$hourProduction['OIL'] *= SERVER_SPEED_RATE * $bonus['OIL']* $this->_materialsInfo['oil_percentage']/100;
		$hourProduction['GOLD'] *= SERVER_SPEED_RATE * $bonus['GOLD']* $this->_materialsInfo['gold_percentage']/100;
		$hourProduction['ENERGY'] *= $bonus['ENERGY'];

		//maintenances over 100
		$maintenance['METAL'] = $this->_materialsInfo['metal_maintenance_cost'] * 100 / $this->_materialsInfo['metal_percentage'];
		$maintenance['OIL'] = $this->_materialsInfo['oil_maintenance_cost']* 100 / $this->_materialsInfo['oil_percentage'];
		$maintenance['GOLD'] = $this->_materialsInfo['gold_maintenance_cost']* 100 / $this->_materialsInfo['gold_percentage'];

		$realMaintenanceCost = $this->_materialsInfo['buildings_maintenance'] + $this->_materialsInfo['troops_maintenance'] - $maintenance['METAL'] + $this->_materialsInfo['metal_maintenance_cost']	- $maintenance['OIL'] + $this->_materialsInfo['oil_maintenance_cost'] - $maintenance['GOLD'] +  $this->_materialsInfo['gold_maintenance_cost'];

		//energies cost over 100
		$energy['METAL'] = $this->_materialsInfo['metal_energy_cost'] * 100 / $this->_materialsInfo['metal_percentage'];
		$energy['OIL'] = $this->_materialsInfo['oil_energy_cost'] * 100 / $this->_materialsInfo['oil_percentage'];
		$energy['GOLD'] = $this->_materialsInfo['gold_energy_cost'] * 100 / $this->_materialsInfo['gold_percentage'];

		$realEnergyCost = $this->_materialsInfo['energy_cost'] - $energy['METAL'] + $this->_materialsInfo['metal_energy_cost']	- $energy['OIL'] + $this->_materialsInfo['oil_energy_cost'] - $energy['GOLD'] +  $this->_materialsInfo['gold_energy_cost'];

		//In seconds
		$timeDiff = $this->_finalTime - $this->_initialTime;

		$production['METAL'] = $hourProduction['METAL'] * $timeDiff/3600;
		$production['OIL'] = $hourProduction['OIL'] * $timeDiff/3600;
		$production['GOLD'] = ($hourProduction['GOLD'] - $realMaintenanceCost) * $timeDiff/3600;
		$production['ENERGY'] = $hourProduction['ENERGY'] - $realEnergyCost ;

		//capacites
		//levels
		$level = array();
		$level['WAREHOUSE'] = BuildingsUtils::getLevel('WAREHOUSE', $this->_buildingsInfo);

		$level['BANK'] = BuildingsUtils::getLevel('BANK', $this->_buildingsInfo);

		//basic capacity
		$capacity['WAREHOUSE'] = 500;
		$capacity['BANK'] = 500;

		foreach ($level['WAREHOUSE'] as $key => $value){
			$capacity['WAREHOUSE'] += $buildingsProperties['WAREHOUSE'][$value];
		}
		foreach ($level['BANK'] as $key => $value){
			$capacity['BANK'] += $buildingsProperties['BANK'][$value];
		}

		$quantity['METAL'] = ($production['METAL']+$this->_materialsInfo['metal']) <= $capacity['WAREHOUSE'] ? ($production['METAL']+$this->_materialsInfo['metal']) : $capacity['WAREHOUSE'];
		$quantity['OIL'] = ($production['OIL']+$this->_materialsInfo['oil']) <= $capacity['WAREHOUSE'] ? ($production['OIL']+$this->_materialsInfo['oil']) : $capacity['WAREHOUSE'];
		$quantity['GOLD'] = ($production['GOLD']+$this->_materialsInfo['gold']) <= $capacity['BANK'] ? ($production['GOLD']+$this->_materialsInfo['gold']) : $capacity['BANK'];

		$this->_quantity = $quantity;

		//store the data that the theme will need
		if(User::get_currentCityId() == $this->_cityId){
			require_once APP.'logic/VarCollector.php';
			$quantities['METAL'] = floor($quantity['METAL']);
			$quantities['OIL'] = floor($quantity['OIL']);
			$quantities['GOLD'] = floor($quantity['GOLD']);
			
			$capacities['WAREHOUSE'] = floor($capacity['WAREHOUSE']);
			$capacities['BANK'] = floor($capacity['BANK']);
			
			$hourProductions['METAL'] = floor($hourProduction['METAL']);
			$hourProductions['OIL'] = floor($hourProduction['OIL']);
			$hourProductions['GOLD_DIRTY']	= floor($hourProduction['GOLD']);	
			$hourProductions['GOLD']	= floor($hourProduction['GOLD'] - $realMaintenanceCost);
			$hourProductions['ENERGY_DIRTY'] = floor($hourProduction['ENERGY']);						
			$hourProductions['ENERGY'] = floor($hourProduction['ENERGY'] - $realEnergyCost);			
			
			VarCollector::setMaterialVars('QUANTITY', $quantities);
			VarCollector::setMaterialVars('CAPACITY', $capacities);
			VarCollector::setMaterialVars('HOUR_PRODUCTION', $hourProductions);		
		}
	}

	private function updateData()
	{
		DataBaseManager::query("UPDATE {materials} SET update_time = ".$this->_finalTime.",
		 metal = ".$this->_quantity['METAL'].", oil = ".$this->_quantity['OIL'].", 
		 gold= ".$this->_quantity['GOLD']." WHERE city_id =".$this->_cityId);
	}
}

?>