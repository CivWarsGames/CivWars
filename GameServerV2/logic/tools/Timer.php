<?php
/**
 * Provides the time treatment functions
 * @package logic_tools
 */
class Timer
{
    
    /**
     * This function returns the result of adding time to the $startTime
     * @param String $startTime initial UNIX time it can be NOW to set the current time
     * @param int $timeToAdd 
     * @return int $finalTime in UNIX
     */    
    public static function addUNIXTime($timeToAdd, $startTime = "NOW" )
    {
        if($startTime == "NOW"){
            $startTime = time();
        }
        $finalTime = $startTime + $timeToAdd;
        return $finalTime;        
    }
    
    /**
     * This function returns the result of subtracting time to the $startTime
     * @param String $startTime String initial UNIX time it can be NOW to set the current time
     * @param int $timeToSubtract
     * @return int $finalTime in UNIX
     */    
    public static function subtractUNIXTime($timeToSubtract, $startTime = "NOW")
    {
        if($startTime == "NOW"){
            $startTime = time();
        }
        $finalTime = $startTime - $timeToSubtract;
        return $finalTime;       
    }
    
    /**
     * This function returns the result of adding time to the $startTime
     * @param String $startTime  initial UNIX time it can be NOW to set the current time
     * @param int $seconds the time that you add
     * @return int $finalUNIXTime
     */
    public static function addTime($startTime = "NOW", $seconds = 0, $minutes = 0, $hours = 0, $days = 0)
    {
               
        $secondsToAdd = $seconds + $minutes * 60 + $hours * 3600 + $days * 3600 * 24;
        
        $finalUNIXTime = self::addUNIXTime($secondsToAdd,$startTime);
        
        return $finalUNIXTime;
    }
    
    /**
     * This function returns the result of subtracting time to the $startTime
     * @param String $startTime initial UNIX time it can be NOW to set the current time
     * @param int $seconds the time that you subtract
     * @return int $finalUNIXTime
     */
    public static function subtractTime($startTime = "NOW", $seconds = 0, $minutes = 0, $hours = 0, $days = 0)
    {
               
        $secondsToSubtract = $seconds + $minutes * 60 + $hours * 3600 + $days * 3600 * 24;
        
        $finalUNIXTime = self::subtractUNIXTime($secondsToSubtract,$startTime);
        
        return $finalUNIXTime;
    }
    
    /**
     * This function returns the result of adding time with an specific time value 
     * @param String $uNIXTime initial UNIX time it can be NOW to set the current time
     * @param int $number time that you add
     * @param String timeUnit
     * @return int $finalUNIXTime
     */
    public static function addUnitTime($uNIXTime,$number,$timeUnit = "SECOND")
    {
        if($timeUnit == "SECOND"){
            $finalUNIXTime = self::addUNIXTime($number,$uNIXTime);
        }elseif($timeUnit == "MINUTE"){
            $number*=60;
            $finalUNIXTime = self::addUNIXTime($number,$uNIXTime);
        }elseif($timeUnit == "HOUR"){
            $number*=3600;
            $finalUNIXTime = self::addUNIXTime($number,$uNIXTime);
        }elseif($timeUnit == "DAY"){
            $number*=3600*24;
            $finalUNIXTime = self::addUNIXTime($number,$uNIXTime);
        }
        return $finalUNIXTime;
    }
    
    /**
     * Transforms a UNIX time to a readable date using date() function (dummy function)
     * @param String $format the output format
     * @param int $uNIXtime
     * @return String $time
     */
    public static function uNIXToDate(  $uNIXtime,$format = 'Y-m-d H:i:s')
    {
        
        $time = date($format, $uNIXtime);
        return $time;
    }
    
    /**
     * Tranforms a date String to a UNIX Time recognises:
     * Y (year type: yyyy)
     * 
     * @param String $date
     * @param String $format
     * @return Int $uNIXTime
     */
    public static function timeToUNIX($date, $format = 'Y-m-d H:i:s')
    {
    	/*FIXME The format can't be changed and can't be added new types of var */
    	$pos = array(0 =>strrpos($format, 'Y'), 1 =>strrpos($format, 'm'),2 =>strrpos($format, 'd'),
    	3 =>strrpos($format, 'H'),4 =>strrpos($format, 'i'),5 =>strrpos($format, 's'));
 
    	sort($pos);
    	$currentposition = 0;
    	$t = 0;
    	$currentposition += $pos[0];

		foreach ($pos as $key => $vaule){
			if($value != -1){
				if($key == 0){
					$diff = 4;
				}else{
					$diff = 2;
				}
				if($key == 0){
					$year = substr($date, $currentposition, 4);
				}

				if($key == 1){
					$month = substr($date, $currentposition, 2);					
				}
				if($key == 2){
					$day = substr($date, $currentposition, 2);							
				}
				if($key == 3){
					$hour = substr($date, $currentposition, 2);				
				}
				if($key == 4){
					$minute = substr($date, $currentposition, 2);					
				}
				if($key == 5){
					$second = substr($date, $currentposition, 2);				
				}
				$a = $t+1;
				
				$currentposition += $diff+$pos[$a]-$pos[$t]-1;			
			}
			$t++;
		}
		
        if(!isset($year)){
			$year = date('Y');
		}
        if(!isset($month)){
			$month = date('m');
		}
        if(!isset($day)){
			$day = date('d');
		}
		if(!isset($hour)){
			$hour = date('H');
		}
    	if(!isset($minute)){
			$minute = date('i');
		}
        if(!isset($second)){
			$second = date('s');
		}		
    	$uNIXTime = mktime($hour,$minute,$second,$month,$day,$year);
    	
    	
        return $uNIXTime;
        
    }
    
    /**
     * 
     * This function transforms a second date to a X(days)d H:i:s
     * @param Int $seconds
     * @return String
     */
    public static function secondsToDate($seconds)
    {
    	$days = floor($seconds/(24*3600));
    	$seconds -= $days * 24 * 3600;
    	$hours = floor($seconds/3600);
    	$seconds -= $hours * 3600;
    	$minutes = floor($seconds/60);
    	$seconds -= $minutes * 60;
    	
    	$hours = $hours < 10 ? '0'.$hours : $hours;
    	$minutes = $minutes < 10 ? '0'.$minutes : $minutes;
    	$seconds = $seconds < 10 ? '0'.$seconds : $seconds;
    	
    	if($days > 0){
    		return $days.'d '.$hours.':'.$minutes.':'.$seconds;
    	}else{
    		return $hours.':'.$minutes.':'.$seconds;
    	}
    }

    
}

?>