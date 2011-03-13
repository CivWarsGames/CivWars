<?php
if(!defined('APP')){
	require_once '../../pathBuilder.php';
}
require_once APP.'datamanager/prices/buildings.php';
require_once APP.'logic/User.php';

/**
 * 
 * This class has some functions that allow to do repetitive tasks related with the buldings
 *  like look the level of a building or it's properties.
 *
 */
class BuildingsUtils
{
	private static $_buildingNames;
	private static $_buildingNamesSet;
	private static $_faction;
	private static $_buildingProperties;
	private static $_researchProperties;
	/**
	 * 
	 * @param String/Int $buildingName
	 * @param Int[] $buildingsDBArray
	 * @return Int[] The level of every building that has it's name if there isn't it returns an empty array
	 */
	public static function getLevel($buildingName,$buildingsDBArray,$faction)
	{
		if($faction == self::$_faction && self::$_buildingNamesSet){
			$buildings = &self::$_buildingNames;
		}else{
			LoadBuildingsCosts::getbuildingNames($faction);
			self::$_buildingNames = &LoadBuildingsCosts::$buildingNames;
			
			$buildings = &self::$_buildingNames;
			self::$_faction = $faction;
			self::$_buildingNamesSet = true;		
		}
		$level = array();
		$buildingNumber = array_search($buildingName, $buildings);
		for($f=1;$f<=15;$f++){
			if($buildingNumber  == $buildingsDBArray['type_'.$f]){
				$level[] = $buildingsDBArray['level_'.$f];
			}
		}
		return $level;
	}
	
	/**
	 * 
	 * Gets the time bonus of a city
	 * @return Int The time bonus
	 */
	public static function getTimeBonus($faction = NULL, $buildingsDBArray = NULL,$researchDBArray = NULL)
	{
		if(is_array(self::$_buildingProperties) && self::$_faction == $faction){
			$buildings = &self::$_buildingProperties;			
		}else{
			self::$_faction = $faction;
			self::$_buildingProperties = &LoadBuildingsCosts::getbuildingProperties($faction);
			$buildings = &self::$_buildingProperties;
			
		}
		if($faction == NULL){
		    $faction = User::get_faction();
		}
		if($buildingsDBArray == NULL){
			$buildingsDBArray = DataBaseManager::fetchArray(DataBaseManager::query("SELECT * FROM {buildings} WHERE city_id =".User::get_currentCityId()));
		}
		if($researchDBArray == NULL){
			$researchDBArray = DataBaseManager::fetchArray(DataBaseManager::query("SELECT reinforced_concrete, robots FROM {research}
			WHERE user_id = ".User::get_idUser()));
		}
		$bonus = 1;
		$level = self::getLevel('COMMAND_CENTER', $buildingsDBArray,self::$_faction);
		if(!isset($level[0])){
			$level[0] = 0;
		}
		$bonus *= $buildings['COMMAND_CENTER'][$level[0]]/100;
		require_once APP.'datamanager/prices/researches.php';
		$bonus *= $researchDBArray['reinforced_concrete'] == 1 ? $buildings['REINFORCED_CONCRETE'] : 1;
		$bonus *= $researchDBArray['robots'] == 1 ? $buildings['ROBOTS'] : 1;
		
		return $bonus;
	}
	
}
?>