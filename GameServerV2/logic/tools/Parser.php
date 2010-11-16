<?php
if(!defined('APP')){
	require_once '../../pathBuilder.php';
}
require_once 'CustomException.php';
/**
 *
 * This class contains some security methods to parse Strings
 *
 */
class Parser
{
	/**
	 *
	 * Translates the String to a non dangerous String for the DB
	 * @param String $initialString
	 * @return String $finalString
	 */
	public static function characterTraductor($initialString)
	{
		$traductor=array("\r\n"=>"<br />" ,  "'"=>"&#39;" ,"<" => "[" , ">" => "]" ,
		 "[separation_line]"=>"<hr />", "ç" => "&ccedil;", "·"=>"&middot;", "ü"=>"&uuml;" ,
		 "ï"=>"&iuml;" , "á"=>"&aacute;" , "é"=>"&eacute;" , "í"=>"&iacute;" , "ó"=>"&oacute;" ,
		 "ú"=>"&uacute;" , "à"=>"&agrave;", "è"=>"&egrave;" , "ì"=>"&igrave;" , "ò"=>"&ograve;" ,
		 "ù"=>"&ugrave;", "ñ"=>"&ntilde;", "\\"=>"?" , "#"=>"¿" );

		$finalString = strtr($initialString, $traductor);

		return $finalString;

	}

	/**
	 *
	 * Untranslates some characters from the DB
	 * @param String $initialString
	 * @return String $finalString
	 */
	public static function characterUntraductor($initialString)
	{
		$tradspi=array("<br />" =>"\r\n",  "&#39;"=>"'", "["=>"<"  , "]" => ">" ,
		 "<hr />"=>"[separation_line]" ,"&ccedil;" => "ç", "&middot;" =>"·",
		 "&uuml;"=>"ü", "&iuml;"=>"ï" , "&aacute;"=>"á" , "&eacute;"=>"é" ,
		 "&iacute;"=>"í" , "&oacute;"=>"ó" , "&uacute;"=>"ú" , "&agrave;"=>"à",
		 "&egrave;"=>"è" , "&igrave;"=>"ì" , "&ograve;" =>"ò" ,"&ugrave;"=>"ù",
		 "&ntilde;"=>"ñ" );


		$finalString = strtr($initialString, $traductor);

		return $finalString;
	}

	/**
	 *
	 * Checks if a String contains only numbers.
	 * @param String $evaluableString
	 * @return Boolean
	 */
	public static function onlyNumbersString($evaluableString)
	{
		$pattern="^[0-9\-]{1,7}$";
		try{
			if(mb_ereg($pattern, $evaluableString)){
				return true;
			}else{
				throw new CustomException (sprintf ("The string: %s, doesn't contains only numbers!",
			 $evaluableString),INVALID_ONLY_NUMBERS_STRING);
			 return false;
			}
		}
		catch (CustomException $e)
		{
			$e->errorLog(false);
		}
	}

	/**
	 *
	 * Checks if a email is valid
	 * something@something.som
	 * @param String $email
	 * @return Boolean
	 */
	public static function emailValidation($email)
	{
		$patern = '^[0-9a-zA-Z0-9_\.\-]+@[a-zA-Z0-9_]+([.][a-zA-Z0-9_]+)*[.][a-zA-Z]{2,3}$';
		try{
			if(mb_ereg($pattern, $email)){
				return true;
			}else{
				throw new CustomException (sprintf ("The string: %s, isn't a valid email!",
			 $evaluableString),INVALID_EMAIL);
			 return false;
			}
		}
		catch (CustomException $e)
		{
			$e->errorLog(false);
		}
	}
	
	/**
	 * 
	 * Crypt a password
	 * @param String $password
	 * @return String $cryptedPassword
	 */
	public static function passwordCrypt($password)
	{
		$cryptedPassword=base64_encode(pack('H*', sha1(utf8_encode($password))));
		
		return $cryptedPassword;		
	}
}
?>