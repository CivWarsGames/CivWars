<?php
class MaterialSubstractor
{
    private $costs = array();
    private $materialsStock = array();
    private $faction;

    protected function loadSubstractor(){
        $this->faction = User::get_faction();
        $result = DataBaseManager::fetchArray(DataBaseManager::query("SELECT metal,oil,gold FROM {materials} WHERE city_id = ".User::get_currentCityId()));
        $this->materialsStock['METAL'] = $result['metal'];
        $this->materialsStock['OIL'] = $result['oil'];
        $this->materialsStock['GOLD'] = $result['gold'];
    }
    protected function substractMaterials()
    {
        if($this->lookIfPossible()){
            $this->materialsStock['METAL'] -= $this->costs[0];
            $this->materialsStock['OIL'] -= $this->costs[1];
            $this->materialsStock['GOLD'] -= $this->costs[2];
            DataBaseManager::query("UPDATE {materials} SET metal = ".$this->materialsStock['METAL'].", oil = ".$this->materialsStock['oil'].",
            gold = ".$this->materialsStock['GOLD']." WHERE city_id = ".User::get_currentCityId());
        }
    }
    protected function lookIfPossible(){
        return $this->costs[0] <= $this->materialsStock['METAL'] && $this->costs[1] <= $this->materialsStock['OIL'] && $this->costs[2] <= $this->materialsStock['GOLD'];
    }
}