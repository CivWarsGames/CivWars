<?php
if(!defined('APP')){
require_once '../pathBuilder.php';
}
require_once APP.'logic/update/UpdateCity.php';
/**
 * User's main class, it mantains the user vars.
 * @package logic
 * @var $_idUser int
 * @var $_currentBaseId int
 */
class User
{
    protected static  $_idUser;
    protected static  $_currentCityId;

    public function __construct($idUser, $currentCityId)
    {
        self::$_idUser = $idUser;
        self::$_currentCityId = $currentCityId;
    }
    
    public static function get_idUser()
    {
        return self::$_idUser;
    }
    
    public static function get_currentCityId()
    {
        return self::$_currentCityId;
    }
    
    
    /**
     * This class changes the base of the user safely
     * @param $newBaseId int The new base
     */
    public static function change_currentCityId($newCityId)
    {
        /*
         * TODO ensure that the user is the owner of that base
         */
    }
    
    /**
     * This method updates the user and the base
     */
    public function update()
    {
    	$updateCity = new UpdateCity();
    }
    
}
?>