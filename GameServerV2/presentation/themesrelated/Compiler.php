<?php
if(!defined('APP')){
	require_once '../../../pathBuilder.php';
}
require_once 'CodeParser.php';

/**
 *
 * This class "compiles"/ transforms the html comment code to php and saves it to the
 *  $themeroot/php folder
 *
 */
class Compiler extends CodeParser
{
	/**
	 *
	 * Parses the html code
	 * @param String $file
	 */
	public function __construct($themeName)
	{
		if($themeName!=""){
			$themeName .= "/";
		}

		$this->tplRoot = HOME."themes/".$themeName;
		if(!@opendir($this->tplRoot."php")){
			//CustomException no mkdir handler
			mkdir($this->tplRoot."php");
		}
		$srcRoot = $this->tplRoot."src/";
		$this->compileFolder($srcRoot);

	}

	protected function compileTagInclude($tagArgs)
	{
		// Process var includes
		if (mb_ereg("^[A-Z0-9\-_]+$", $tagArgs))
		{
			return "require_once(APP.'presentation/VarsContainer.php');
			 VarsContainer::load('$tagArgs');";
		}

		return " require_once('$tagArgs'.php);";
	}

	protected function compileFolder($folder)
	{
		if ($handle = opendir($folder)) {
			while (false !== ($file = readdir($handle))) {
				if($file!= '.' && $file!='..'){
					$this->filename[] =$file;
					$this->compileFile($file);
				}
			}
			closedir($handle);
		}
	}

	protected function compileFile($file)
	{
		$code = $this->getFileContent($file);
		$compilatedCode = $this->parseContent($code);
		$this->compileWrite($file, $compilatedCode);
	}

	/**
	 * Write compiled file to cache directory
	 */
	private function compileWrite($handle, $data)
	{
		$filename = $this->tplRoot."php/".$handle.".php";

		if ($fp = @fopen($filename, 'wb'))
		{
			@flock($fp, LOCK_EX);
			@fwrite ($fp, $data);
			@flock($fp, LOCK_UN);
			@fclose($fp);

			chmod($filename, 0777);		//TODO look chmods

		}

		return;
	}

	/**
	 * We only declare this function (dirty?) ;)
	 */
	protected function tplInclude($filename){

	}



}


?>