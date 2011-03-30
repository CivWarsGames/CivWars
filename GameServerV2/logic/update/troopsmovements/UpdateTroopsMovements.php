<?php
/**
 *
 * This class decodes troop movements types
 * 0 => Base troops
 * 1 => Reinforcements (incoming)
 * 2 => Attack target: destroy
 * 3 => Attack target: materials
 * 4 => Attack target: Spy
 * 5 => Attack target: siege only material boxes (incoming)
 * 6 => Engineer target: New city
 * 7 => General (?? not used)
 * //UI developers only can use until here in &t="mov_type" param
 * 12 => Reinforcements (staged)
 * 11 => Siege(staged)
 * 10 => Attack returning (all)
 *
 */
class UpdateTroopsMovements
{
    public function UpdateTroopsMovements()
    {
        $troopMovements = DataBaseManager::query("SELECT * FROM {troops_movements} WHERE movement_type < 11 AND movement_type > 0
         AND arrival_time <= ".time());
        while($info = DataBaseManager::fetchArray($troopMovements)){
            switch ($info['movement_type']){
                case 1:
                    require_once APP.'Reinforcements.php';
                    new Reinforcement($info);
                    break;
                case 2:
                    require_once APP.'AttackDestroy.php';
                    new AttackDestroy($info);
                    break;
                    //etc
            }
        }
    }
}