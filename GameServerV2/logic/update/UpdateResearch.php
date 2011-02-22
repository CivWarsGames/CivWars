<?php

class UpdateResearch
{
    public function UpdateResearch()
    {
        $results = DataBaseManager::query("SELECT user_id, current_research FROM {research} WHERE current_research<>0
         AND finish_time <= NOW()");
        while($result = DataBaseManager::fetchArray($results)){
	        DataBaseManager::query("UPDATE {research} SET current_research = 0, $result[current_research] = 1 WHERE
	         user_id = $result[user_id]");
        }
    }
}