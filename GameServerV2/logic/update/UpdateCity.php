<?php
if(!defined('APP')){
require_once '../../pathBuilder.php';
}
/*require_once APP.'logic/update/updateall/UpdateBuildings.php';
require_once APP.'logic/update/updateall/UpdateMaterialBoxes.php';
require_once APP.'logic/update/updateall/UpdateResearch.php';*/
require_once APP.'logic/update/UpdateMaterials.php';
require_once APP.'logic/User.php';


class UpdateCity
{
	public function UpdateCity()
	{
		//$updateBuildings = new UpdateBuildings();
		//$updateMateralBoxes = new UpdateMaterialBoxes();
		//$updateResearch = new UpdateResearch();
		
		//This must be the last one
		$updateMateral = new UpdateMaterials(User::get_currentCityId());
		
		
	}
}

?>