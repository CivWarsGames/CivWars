<?php
/**
 * 
 * This class handles all the params obtained by get method
 *
 */
class Got
{
    private $text = "";
	public function Got()
	{
		//$_GET = array_merge($_GET,$_POST);
		foreach ($_GET as $key => $value){
			$this->getPostSwitcher($key);
		}
	}

	private function getPostSwitcher($key)
	{
		switch ($key){
			//theme upload
		    case 'themeupload':
		        require_once WEB_ROOT.'logic/themesadm/Uploader.php';
		        require_once WEB_ROOT.'presentation/UploaderPresentation.php';
		        new Uploader();
		        $up = new UploaderPresentation();
		        $this->text .= $up->get_text();
		        break;
				
		//add more cases here
				
		}
	}
    public function get_text()
    {
        return $this->text;
    }
}
?>