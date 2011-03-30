<?php
    class Reinforcements extends TroopMovement
    {
        public function Reinforcements($info)
        {
            $faction = DataBaseManager::fetchArray(DataBaseManager::query("SELECT faction FROM {profile} WHERE user_id = ".$info['owner_user_id']));
            $faction = $faction[0];
            $this->mInfo = &$info;
            $cost = $this->calculateTroopsMaintenances($info, $faction);
            $this->updateMaintenance(-$cost, $info['sender_city_id']);
            $this->updateMaintenance($cost, $info['reciver_city_id']);
            $this->updateTroops($info['mov_id'], 12); //Stage the reinforcement troops
        }
    }
?>