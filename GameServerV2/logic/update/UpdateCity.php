<?php
if(!defined('APP')){
require_once '../../pathBuilder.php';
}
require_once APP.'logic/User.php';

require_once 'UpdateBuildings.php';
require_once 'UpdateMaterialBoxes.php';

require_once 'UpdateMaterials.php';


class UpdateCity
{
	public function UpdateCity()
	{
		 new UpdateBuildings();
		 new UpdateMaterialBoxes();
		//$updateResearch = new UpdateResearch();
		
		//This must be the last one
		$updateMateral = new UpdateMaterials(User::get_currentCityId());
		
		
	}
}

?>