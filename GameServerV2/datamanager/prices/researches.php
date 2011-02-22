<?php

class LoadResearchesCosts
{
	private static $faction;
	public static $researchesNames = array();
	private static $loadedResearchesNames = false;
	public static $researchesCosts = array();
	private static $loadedResearchesCosts = false;
	public static $researchesProperties = array();
	private static $loadedResearchesProperties = false;
	
	public static function getResearchesProperties($faction)
	{
		if(self::$loadedResearchesProperties && self::$faction == $faction)
		{
			return self::$researchesProperties;
		}else{
			self::$faction = $faction;
			self::$loadedResearchesProperties = true;
			self::$researchesProperties = self::setResearchesProperties($faction);
			//return self::$researchesProperties;
		}
	}
	public static function getResearchesCosts($faction)
	{
	    
	}
	private static function setResearchesCosts($faction)
	{
	    
	    
	}
	private static function setResearchesCosts($faction)
	{
	    //metal,oil,gold
	    return $c;
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