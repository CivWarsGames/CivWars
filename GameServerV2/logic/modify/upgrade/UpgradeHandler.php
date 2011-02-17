<?php
class UpgradeHandler
{
    public function UpgradeHandler()
    {
        if(isset($_GET['material'])){
            require_once 'MaterialBoxUpgrader.php';
            new MaterialBoxUpgrader();
        }
        if(isset($_GET['building'])){
            require_once 'BuildingsUpgrader.php';
            new BuildingsUpgrader();
        }
        if(isset($_GET['research'])){
            require_once 'ResearchUpgrader.php';
            new ResearchUpgrader();
        }
    }
    
}