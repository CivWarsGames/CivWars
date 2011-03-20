<?php

/**
 *
 * This class stores the information about materials and returns it in arrays
 *
 */
class LoadMaterialsCosts
{
	public static $materialNames = array();
	private static $loadedMaterialNames = false;
	public static $materialCosts = array();
	private static $loadedMaterialCosts = false;
	public static $materialProduction = array();
	private static $loadedMaterialProduction = false;
	private static $faction;

	public static function getmaterialNames($faction)
	{
		if(self::$loadedMaterialNames && self::$faction == $faction)
		{
			return self::$materialNames;
		}else{
			self::$materialNames = self::setmaterialNames($faction);
			self::$faction = $faction;
			self::$loadedMaterialNames = true;
			//return self::$materialNames;
		}
	}

	public static function getmaterialCosts($faction)
	{
	    
		if(self::$loadedMaterialCosts && self::$faction == $faction)
		{
			return self::$materialCosts;
		}else{
			self::$materialCosts = self::setmaterialCosts($faction);
			self::$faction = $faction;
			self::$loadedMaterialCosts = true;		
			//return self::$materialCosts;
		}
	}

	public static function getmaterialProduction($faction)
	{
		if(self::$loadedMaterialProduction && self::$faction == $faction)
		{
			return self::$materialProduction;
		}else{
			self::$materialProduction = self::setmaterialProduction($faction);
			self::$loadedMaterialProduction = true;
			self::$faction = $faction;
			//return self::$materialProduction;
		}
	}

	private static function setmaterialNames($faction)
	{
		$mat = array( "METAL_MINE", "GUSHER", "INDUSTRY", "POWER_PLANT", "METAL", "OIL", "GOLD", "ENERGY");
		return $mat;
	}

	private static function setmaterialCosts($faction)
	{
		//Metal,Oil,Money,Energy,Maintenance/hour,Construction Time(seconds),Destruction Time(seconds)
		//Level 1,2,3 ex: $buildings['costs']['command_center'][0][1] = The oil cost to upgrade the command center to level 1
		
		$c['METAL_MINE']= array(array(16,21,18,2,1,150,15), array(23,31,26,1,1,240,24), array(33,44,37,1,1,382,38), array(47,63,53,2,1,603,60), array(69,92,78,2,2,947,95), array(100,133,113,3,2,1477,148), array(145,193,164,4,2,2289,229), array(210,280,238,5,2,3525,353), array(304,405,345,7,2,5393,539), array(444,592,503,9,3,8197,820), array(653,871,740,12,3,12377,1238), array(967,1289,1096,17,3,18566,1857), array(1440,1920,1632,21,3,27663,2766), array(2160,2880,2448,24,3,40941,4094), array(3262,4349,3697,28,4,60183,6018), array(4958,6611,5619,32,4,87867,8787), array(7586,10115,8597,37,4,127408,12741), array(11683,15577,13241,42,4,183467,18347), array(18109,24145,20524,47,4,262358,26236), array(28250,37667,32017,53,5,372548,37255), array(44352,59136,50266,58,5,525293,52529), array(70076,93435,79419,64,5,735410,73541), array(111421,148561,126277,69,5,1022220,102222), array(178274,237699,202044,74,5,1410664,141066), array(287022,382696,325292,80,6,1932609,193261));

		$c['GUSHER']= array(array(21,16,18,2,1,150,15), array(31,23,26,1,1,240,24), array(44,33,37,1,1,382,38), array(63,47,53,2,1,603,60), array(92,69,78,2,2,947,95), array(133,100,113,3,2,1477,148), array(193,145,164,4,2,2289,229), array(280,210,238,5,2,3525,353), array(405,304,345,7,2,5393,539), array(592,444,503,9,3,8197,820), array(871,653,740,12,3,12377,1238), array(1289,967,1096,17,3,18566,1857), array(1920,1440,1632,21,3,27663,2766), array(2880,2160,2448,24,3,40941,4094), array(4349,3262,3697,28,4,60183,6018), array(6611,4958,5619,32,4,87867,8787), array(10115,7586,8597,37,4,127408,12741), array(15577,11683,13241,42,4,183467,18347), array(24145,18109,20524,47,4,262358,26236), array(37667,28250,32017,53,5,372548,37255), array(59136,44352,50266,58,5,525293,52529), array(93435,70076,79419,64,5,735410,73541), array(148561,111421,126277,69,5,1022220,102222), array(237699,178274,202044,74,5,1410664,141066), array(382696,287022,325292,80,6,1932609,193261));

		$c['INDUSTRY']= array(array(16,16,11,2,1,150,15), array(23,23,15,1,1,240,24), array(33,33,22,1,1,382,38), array(47,47,31,2,1,603,60), array(69,69,46,2,2,947,95), array(100,100,67,3,2,1477,148), array(145,145,97,4,2,2289,229), array(210,210,140,5,2,3525,353), array(304,304,203,7,2,5393,539), array(444,444,296,9,3,8197,820), array(653,653,435,12,3,12377,1238), array(967,967,645,17,3,18566,1857), array(1440,1440,960,21,3,27663,2766), array(2160,2160,1440,24,3,40941,4094), array(3262,3262,2175,28,4,60183,6018), array(4958,4958,3305,32,4,87867,8787), array(7586,7586,5057,37,4,127408,12741), array(11683,11683,7789,42,4,183467,18347), array(18109,18109,12073,47,4,262358,26236), array(28250,28250,18833,53,5,372548,37255), array(44352,44352,29568,58,5,525293,52529), array(70076,70076,46717,64,5,735410,73541), array(111421,111421,74281,69,5,1022220,102222), array(178274,178274,118849,74,5,1410664,141066), array(287022,287022,191348,80,6,1932609,193261));

		$c['POWER_PLANT']= array(array(16,16,16,0,1,150,15), array(23,23,23,0,1,240,24), array(33,33,33,0,1,382,38), array(47,47,47,0,1,603,60), array(69,69,69,0,2,947,95), array(100,100,100,0,2,1477,148), array(145,145,145,0,2,2289,229), array(210,210,210,0,2,3525,353), array(304,304,304,0,2,5393,539), array(444,444,444,0,3,8197,820), array(653,653,653,0,3,12377,1238), array(967,967,967,0,3,18566,1857), array(1440,1440,1440,0,3,27663,2766), array(2160,2160,2160,0,3,40941,4094), array(3262,3262,3262,0,4,60183,6018), array(4958,4958,4958,0,4,87867,8787), array(7586,7586,7586,0,4,127408,12741), array(11683,11683,11683,0,4,183467,18347), array(18109,18109,18109,0,4,262358,26236), array(28250,28250,28250,0,5,372548,37255), array(44352,44352,44352,0,5,525293,52529), array(70076,70076,70076,0,5,735410,73541), array(111421,111421,111421,0,5,1022220,102222), array(178274,178274,178274,0,5,1410664,141066), array(287022,287022,287022,0,6,1932609,193261));

		return $c;
	}

	/**
	 *
	 * Material production/hour started by level 0!
	 */
	private static function setmaterialProduction($faction)
	{
		$pr['METAL'] = array(8, 12, 18, 26, 37, 51, 71, 96, 129, 170, 221, 283, 356, 442, 539, 652, 783, 932, 1099, 1286, 1492, 1716, 1956, 2210, 2475, 2748);
		$pr['OIL'] = array(8,12, 18, 26, 37, 51, 71, 96, 129, 170, 221, 283, 356, 442, 539, 652, 783, 932, 1099, 1286, 1492, 1716, 1956, 2210, 2475, 2748);
		$pr['ENERGY'] = array(8,12, 18, 26, 37, 51, 71, 96, 129, 170, 221, 283, 356, 442, 539, 652, 783, 932, 1099, 1286, 1492, 1716, 1956, 2210, 2475, 2748);
		$pr['GOLD'] = array(8,16,24,34,49,68,94,128,172,227,295,377,475,589,719,870,1044,1242,1466,1715,1989,2288,2608,2947,3300,3663);

		return $pr;
	}
}

?>