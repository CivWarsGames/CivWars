<?php
class Box
{
    public $coords = array(); //x,y
    public $ownerCity = 0; //id
    public $ownerUser = 0; //id
    public $boxLevel = 0;
    //public $naturalType;
    public $currentType; //1 = city, 2 = metal, 3 = oil, 4 = gold, 5 = energy
    public $siegerCityId = 0;
    public $ownerPercentage;
    public $secondaryCity;
    public $thirdCity;
    public $secondaryPercentage;
    public $thirdPercentage;
    
    public function Box($x,$y)
    {
        
    }
    public function Box($info)
    {
        
    }
    public function getInfo()
    {
        
    }
}