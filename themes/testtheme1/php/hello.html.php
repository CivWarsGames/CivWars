 <!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<title>Insert title here</title>
</head>
<body>
<?php require_once(APP.'presentation/VarsContainer.php');
			 VarsContainer::load('PLAYER'); VarsContainer::loadObject('PLAYER','des','NAME');?><?php VarsContainer::loadObject('PLAYER',VarsContainer::$display['PLAYER']['des']['NAME']['556']);?><?php VarsContainer::loadObject('PLAYER',VarsContainer::$display['PLAYER']['des']['NAME']['556'],'NAME');?> 
<?php echo VarsContainer::$display['PLAYER'][VarsContainer::$display['PLAYER']['des']['NAME']['556']]['NAME']['555'];?>


 <?php VarsContainer::loadObject('PLAYER','ses');?><?php if ((VarsContainer::$display['PLAYER']['ses']['Sas']) == (VarsContainer::$display['COOL']['POS']['NAM'])) {  ?> 
<?php echo VarsContainer::$display['GAS']['TROP']['Sas'];?>

<?php } ?> 
SUCK


 <?php VarsContainer::loadObject('PLAYER','ses');?><?php if (isset( VarsContainer::$display['PLAYER']['ses']['Sas'])) {for ($_i = 0; $_i <  VarsContainer::$display['PLAYER']['ses']['Sas']; $_i++){ VarsContainer::loadObject('PLAYER',$_i);?> 
<?php echo VarsContainer::$display['PLAYER'][$_i]['NAME'];?>

<?php }} ?> 
</body>
</html>