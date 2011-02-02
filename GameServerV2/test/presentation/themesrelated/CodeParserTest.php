<?php
if(!defined('APP')){
    require_once '../../../pathBuilder.php';
}
require_once APP."presentation/themesrelated/CodeParser.php";

class CodeParserTest extends CodeParser
{
    public function CodeParserTest($filename){
        if(!isset($_GET['t'])) $_GET['t'] = 'testtheme1';
        $this->tplRoot = HOME."themes/testtheme1/";

        $code = $this->getFileContent($filename);
        echo '<textarea rows="36" cols="160" readonly style="background-color:#efffef">'.
        $this->parseContent($code).'</textarea>';
    }
}


$ok = new CodeParserTest($_GET['f']);
?>