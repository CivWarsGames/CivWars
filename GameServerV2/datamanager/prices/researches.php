<?php

class LoadResearchesCosts
{
	private static $faction;
	private static $_researchesNames = array();
	private static $loadedResearchesNames = false;
	private static $_researchesCosts = array();
	private static $loadedResearchesCosts = false;
	private static $_researchesProperties = array();
	private static $loadedResearchesProperties = false;
	
	public static function getResearchesProperties($faction)
	{
		if(self::$loadedResearchesProperties && self::$faction == $faction)
		{
			return self::$_researchesProperties;
		}else{
			self::$faction = $faction;
			self::$loadedResearchesProperties = true;
			self::$_researchesProperties = self::setResearchesProperties();
			return self::$_researchesProperties;
		}
	}
	
	private static function setResearchesProperties($faction)
	{
		//Factor that multiplies the production 
		
		$p['IMPROVED_METAL_EXTRACTION'] = 1.1;
		$p['IMPROVED_OIL_EXTRACTION'] = 1.1;
		$p['INVESTMENTS'] = 1.1;
		$p['THERMAL_ENERGY'] = 1.05;
		$p['SOLAR_ENERGY'] = 1.06;
		$p['FISSION_NUCLEAR_ENERGY'] = 1.07;
		$p['FUSION_NUCLEAR_ENERGY'] = 1.09;
		
		// % that reduces building time
		$p['REINFORCED_CONCRETE'] = 0.9;
		$p['ROBOTS'] = 0.9;
				
		return $p;
	}
	
		
	
}
?>