<?php
if(!defined('WEB_ROOT')){
	require_once '../pathBuilder.php';
}
require_once WEB_ROOT.'logic/ServerTools.php';
require_once WEB_ROOT.'datamanager/language/'.LANGUAGE.'/loginPresentationLang.php';
?>
<div>
<form action="?login" method="POST"><?php echo $lang['USER']?>: <input
	type="text" name="user" /> <br />
<?php echo $lang['PASSWORD']?>: <input type="password" name="password" /> <br />
<?php echo $lang['CHOOSE SERVER']?>: <select name="serverId">

<?php
foreach (ServerTools::lsServers() as $key => $server){
	if($server->hidden == false){
		echo '<option value= "'.$key.'">'.$server->sName.'</option>
			';
	}
}
?>

</select> <br />
<input type="submit" value="<?php echo $lang['LOGIN']?>" /></form>
</div>
