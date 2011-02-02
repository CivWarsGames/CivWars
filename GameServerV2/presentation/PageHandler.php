<?php
class PageHandler
{
	protected $theme;

	public function PageHandler($page, $trial = false)
	{
		$trial = true;//This should be removed ;)
		
		if($trial){
			$this->trialMode($page);
		}else{
			$this->compilatedMode($page);
		}

	}

	private function getThemeName()
	{
		if($this->theme){
			return $this->theme;
		}
		$userId = User::get_idUser();
		$theme = DataBaseManager::fetchArray(DataBaseManager::query("SELECT theme FROM
		{profile} WHERE user_id = $userId"));
		$this->theme = $theme[0];
		return $this->theme;
	}

	private function trialMode($page)
	{
		require_once APP.'presentation/themesrelated/Interpreter.php';
		$theme = $this->getThemeName();
		$url = $page.'.html';
		$interpreter = new Interpreter($url, $theme);
	}

	private function compilatedMode($page)
	{
		$theme = $this->getThemeName();
		$url = HOME.'themes/'.$theme.'/php/'.$page.'.html.php';
		require_once $url;

	}

}
?>