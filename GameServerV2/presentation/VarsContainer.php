<?php

class VarsContainer
{
	public static $display = array();
	/**
	 * @var Int[][]
	 * @return
	 * The mateirals var is an array that contains information related with the materials and
	 * its production. Its structure is:
	 * $materials[QUANTITY/CAPACITY/HOUR_PRODUCTION]
	 * $materials[QUANTITY][METAL/OIL/GOLD]
	 * $materials[CAPACITY][WAREHOUSE/BANK]
	 * $materials[HOUR_PRODUCTION][METAL/OIL/GOLD_DIRTY/GOLD/ENERGY_DIRTY/ENERGY] (dirty is
	 * the production before substract the mantenainces and energy costs
	 */
	public static $material = array();

	public static function load($vars)
	{
		switch ($vars){
			case "MATERIAL": self::loadMaterialVars();
			break;
		}
	}
	private static function loadMaterialVars()
	{
		self::$display['MATERIAL'] = &self::$material;
	}
	public static function setMaterialVars($key,$value)
	{
		self::$materials[$key] = $value;
	}
}