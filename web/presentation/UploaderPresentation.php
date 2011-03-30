<?php 
require_once WEB_ROOT.'logic/tools/Parser.php';
require_once WEB_ROOT.'logic/ServerTools.php';

class UploaderPresentation
{
    private $text = "";
    //handler
    public function UploaderPresentation()
    {
        $this->uploaderFirstMenu();
        if(isset($_GET['ntm'])){
            $this->newThemeMenu();
        }else if(isset($_GET['etm'])){
            $this->editThemeMenu();
        }else if(isset($_GET['ttm'])){
            $this->testThemeMenu();
        }
        $this->text .= '<br />';
        $this->uploadForm();
    }
    private function uploaderFirstMenu(){
        $this->text .= '<a href="?'.Parser::saveGet('ntm','etm,ttm').'">Upload a new Theme</a> 
        <a href="?'.Parser::saveGet('etm','ntm,ttm').'">Edit a theme</a> 
        <a href="?'.Parser::saveGet('ttm','ntm,etm').'">Use the test theme</a><br/>';
    }
    private function newThemeMenu()
    {
        
    }
    private function editThemeMenu()
    {
        
    }
    private function testThemeMenu()
    {
        $path = 'themes/testtheme1/src';
        $files = ServerTools::lsFiles(HOME.$path);
        $this->prepareFolderTree($files,$path.'/');
    }
    private function prepareFolderTree($array,$url)
    {
        foreach ($array as $key => $val){
            if(is_array($val)){
                $this->text .= '<a href= "'.$url.$key.'">'.$key.'/</a><br><div style="margin-left:40px;">';
                $this->prepareFolderTree($val,$url.$key.'/');
                $this->text .= '</div>
                ';
            }else{
                $this->text .= '<a href="'.$url.$val.'">'.$val.'</a><br>
                ';
            }
           
        }    
        
    }
    private function uploadForm()
    {
        $this->text .= '<div><form method="post">
        Select file: <input type="file" name="origin"/><br />
        File path (relative to src/): <input type="text" /><br />
        <input type="submit" value="Send" name="send" />
        </form></div>';    
    }
    public function get_text()
    {
        return $this->text;
    }
}

?>