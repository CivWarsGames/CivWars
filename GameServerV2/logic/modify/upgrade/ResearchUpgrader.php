<?php
require_once APP.'logic/modify/MaterialSubstractor.php';

class ResearchUpgrader extends MaterialSubstractor
{
    private $research;
    public function ResearchUpgrader(){
        $researchName = $_GET['research'];
        $this->research = DataBaseManager::fetchArray(DataBaseManager::query("SELECT current_research, $researchName FROM {research} WHERE
         user_id =".User::get_idUser()));
        if($this->research['current_search'] == 0 && $this->research[$researchName] == 0 && 
        $this->isItAvailableTechnologicaly($researchName)){
            $this->loadSubstractor();
            $this->readCosts($researchName);
            if($this->lookIfPossible()){
                $timeCost = $this->costs[3] * $this->calculateTimeBonus()/SERVER_SPEED_RATE;
                DataBaseManager::query("UPDATE {research} SET current_research = '$researchName',
                 finish_time = ".Timer::addUNIXTime($timeCost)." WHERE user_id = ".User::get_idUser());
                $this->substractMaterials();
            }
        }
    }
    private function readCosts($researchName)
    {
        LoadResearchesCosts::setResearchesCosts(User::get_faction());
        $this->costs = &LoadResearchesCosts::$researchesCosts[$researchName];
    }
    private function calculateTimeBonus()
    {
        LoadBuildingsCosts::getbuildingProperties($faction);
        return LoadBuildingsCosts::$buildingProperties['RESEARCH_LAB'];
    }
    private function isItAvailableTechnologicaly($researchName)
    {
        $researchName = strtolower($researchName);
        $buildingsInfo = DataBaseManager::fetchArray(DataBaseManager::query("SELECT * FROM {buildings} WHERE city_id = ".User::get_currentCityId()));
        switch ($researchName){
            case 'steel':
                if(BuildingsUtils::getLevel('RESEARCH_LAB', $buildingsInfo) >= 1){
                    $select = DataBaseManager::query("SELECT box_id FROM {map} WHERE owner_city_id = ".User::get_currentCityId()."
                 AND box_level >=2 AND current_type = 2 AND sieger_city_id = 0");
                    if(DataBaseManager::numRows($select) > 0){
                        return true;
                    }
                }
                break;
            case 'electricity':
                if(BuildingsUtils::getLevel('RESEARCH_LAB', $buildingsInfo) >= 1){
                    $select = DataBaseManager::query("SELECT box_id FROM {map} WHERE owner_city_id = ".User::get_currentCityId()."
                 AND box_level >=2 AND current_type = 5 AND sieger_city_id = 0");
                    if(DataBaseManager::numRows($select) > 0){
                        return true;
                    }
                }
                break;
            case 'assembly_line':
                if(BuildingsUtils::getLevel('RESEARCH_LAB', $buildingsInfo) >= 2 && $this->research['electricity'] == 1){
                    return true;
                }
                break;
            case 'reinforced_concrete':
                if(BuildingsUtils::getLevel('RESEARCH_LAB', $buildingsInfo) >= 2 && $this->research['steel'] == 1){
                    return true;
                }
                break;
            case 'radio':
                if(BuildingsUtils::getLevel('RESEARCH_LAB', $buildingsInfo) >= 3 && $this->research['electricity'] == 1){
                    return true;
                }
                break;
            case 'reciprocating_engine':
                if(BuildingsUtils::getLevel('RESEARCH_LAB', $buildingsInfo) >= 3 && $this->research['electricity'] == 1 && $this->research['steel'] == 1){
                    return true;
                }
                break;
            case 'armor':
                if(BuildingsUtils::getLevel('RESEARCH_LAB', $buildingsInfo) >= 5 && $this->research['steel'] == 1){
                    return true;
                }
                break;
            case 'artillery':
                if(BuildingsUtils::getLevel('RESEARCH_LAB', $buildingsInfo) >= 6 && $this->research['armor'] == 1 && $this->research['steel'] == 1){
                    return true;
                }
                break;
            case 'fly':
                if(BuildingsUtils::getLevel('RESEARCH_LAB', $buildingsInfo) >= 10 && $this->research['reciprocating_engine'] == 1 && $this->research['radio'] == 1 && $this->research['armor'] == 1){
                    return true;
                }
                break;
            case 'espionage':
                if(BuildingsUtils::getLevel('RESEARCH_LAB', $buildingsInfo) >= 4){
                    return true;
                }
                break;
            case 'computers_I':
                if(BuildingsUtils::getLevel('RESEARCH_LAB', $buildingsInfo) >= 5 && $this->research['electricity'] == 1){
                    return true;
                }
                break;
            case 'computers_II':
                if(BuildingsUtils::getLevel('RESEARCH_LAB', $buildingsInfo) >= 12  &&  $this->research['plastic'] == 1 &&  $this->research['computers_I'] == 1){
                    return true;
                }
                break;
            case 'improved_metal_extraction':
                $select = DataBaseManager::query("SELECT box_id FROM {map} WHERE owner_city_id = ".User::get_currentCityId()."
                 AND box_level >=10 AND current_type = 2 AND sieger_city_id = 0");
                if(DataBaseManager::numRows($select) > 0){
                    return true;
                }
                break;
            case 'improved_oil_extraction':
                $select = DataBaseManager::query("SELECT box_id FROM {map} WHERE owner_city_id = ".User::get_currentCityId()."
                 AND box_level >=10 AND current_type = 3 AND sieger_city_id = 0");
                if(DataBaseManager::numRows($select) > 0){
                    return true;
                }
                break;
            case 'investments':
                $select = DataBaseManager::query("SELECT box_id FROM {map} WHERE owner_city_id = ".User::get_currentCityId()."
                 AND box_level >=10 AND current_type = 4 AND sieger_city_id = 0");
                if(DataBaseManager::numRows($select) > 0){
                    return true;
                }
                break;
            case 'titanium':
                if(BuildingsUtils::getLevel('RESEARCH_LAB', $buildingsInfo) >= 7  &&  $this->research['improved_metal_extraction'] == 1){
                    return true;
                }
                break;
            case 'plastic':
                if(BuildingsUtils::getLevel('RESEARCH_LAB', $buildingsInfo) >= 7  &&  $this->research['improved_oil_extraction'] == 1){
                    return true;
                }
                break;
            case 'jet_engine':
                if(BuildingsUtils::getLevel('RESEARCH_LAB', $buildingsInfo) >= 9  &&  $this->research['titanium'] == 1){
                    return true;
                }
                break;
            case 'robots':
                if(BuildingsUtils::getLevel('ADVANCED_LAB', $buildingsInfo) >= 3   &&  $this->research['computers_II'] == 1 &&
                $this->research['reciprocating_engine'] == 1 &&  $this->research['plastic'] == 1 && $this->research['titanium'] == 1 ){
                    return true;
                }
                break;
            case 'optic_fiber':
                if(BuildingsUtils::getLevel('ADVANCED_LAB', $buildingsInfo) >= 5   &&  $this->research['computers_II'] == 1 &&
                $this->research['plastic'] == 1 && $this->research['titanium'] == 1 ){
                    return true;
                }
                break;
            case 'satellites':
                if(BuildingsUtils::getLevel('ADVANCED_LAB', $buildingsInfo) >= 7   &&  $this->research['computers_II'] == 1 &&
                $this->research['plastic'] == 1 && $this->research['titanium'] == 1 && $this->research['optic_fiber'] == 1
                && $this->research['fission_nuclear_energy'] == 1 ){
                    return true;
                }
                break;
            case 'genetics':
                if(BuildingsUtils::getLevel('ADVANCED_LAB', $buildingsInfo) >= 9 ){
                    return true;
                }
                break;
            case 'thermal_energy':
                if(BuildingsUtils::getLevel('RESEARCH_LAB', $buildingsInfo) >= 1){
                    $select = DataBaseManager::query("SELECT box_id FROM {map} WHERE owner_city_id = ".User::get_currentCityId()."
                 AND box_level >=4 AND current_type = 5 AND sieger_city_id = 0");
                    if(DataBaseManager::numRows($select) > 0){
                        $select = DataBaseManager::query("SELECT box_id FROM {map} WHERE owner_city_id = ".User::get_currentCityId()."
                 AND box_level >=5 AND current_type = 3 AND sieger_city_id = 0");
                        if(DataBaseManager::numRows($select) > 0){
                            return true;
                        }
                    }
                }
                break;
            case 'solar_energy':
                if(BuildingsUtils::getLevel('RESEARCH_LAB', $buildingsInfo) >= 3 &&  $this->research['electricity'] == 1 &&  $this->research['steel'] == 1){
                    $select = DataBaseManager::query("SELECT box_id FROM {map} WHERE owner_city_id = ".User::get_currentCityId()."
                 AND box_level >=2 AND current_type = 5 AND sieger_city_id = 0");
                    if(DataBaseManager::numRows($select) > 0){
                        return true;
                    }
                }
                break;
            case 'fission_nuclear_energy':
                if(BuildingsUtils::getLevel('ADVANCED_LAB', $buildingsInfo) >= 1){
                    $select = DataBaseManager::query("SELECT box_id FROM {map} WHERE owner_city_id = ".User::get_currentCityId()."
                 AND box_level >= 16 AND current_type = 5 AND sieger_city_id = 0");
                    if(DataBaseManager::numRows($select) > 0){
                        return true;
                    }
                }
                break;
            case 'fusion_nuclear_energy':
                if(BuildingsUtils::getLevel('ADVANCED_LAB', $buildingsInfo) >= 15){
                    $select = DataBaseManager::query("SELECT box_id FROM {map} WHERE owner_city_id = ".User::get_currentCityId()."
                 AND box_level >= 22 AND current_type = 5 AND sieger_city_id = 0");
                    if(DataBaseManager::numRows($select) > 0){
                        return true;
                    }
                }
                break;
        }
        return false;
    }

}

?>