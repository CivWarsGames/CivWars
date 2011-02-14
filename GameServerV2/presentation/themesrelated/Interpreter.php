<?php
if(!defined('APP')){
    require_once '../../../pathBuilder.php';
}
require_once 'CodeParser.php';
require_once 'SecondParser.php';

class Interpreter extends CodeParser
{
    /**
     *
     * Interprets the comment html code
     * @param String $file
     */
    public function __construct($file, $themeName)
    {
        if($themeName!=""){
            $themeName .= "/";
        }
        $this->tplRoot = HOME."themes/".$themeName;
        $this->tplInclude($file);

    }

    protected function compileTagInclude($tagArgs)
    {
        // Process var includes
        if (mb_ereg("^[A-Z0-9\-_]+$", $tagArgs))
        {
            return "require_once(APP.'presentation/VarsContainer.php');
			 VarsContainer::load('$tagArgs');";
        }

        return "\$this->tplInclude('$tagArgs');";
    }

    /**
     * Include a separate template
     * @access private
     */
    protected function tplInclude($filename)
    {
        $handle = $filename;
        $this->filename[$handle] = $filename;

        $code =mb_ereg("^[A-Z0-9\-_]+$", $handle) ? false : $this->getFileContent($handle);

        if ($code)
        {
            //echo $this->parseContent($filename);
            eval(' ?>' . $this->parseContent($code) . '<?php ');
        }
    }


}


?>