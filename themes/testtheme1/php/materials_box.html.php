<?php require_once(APP.'presentation/VarsContainer.php');
			 VarsContainer::load('MATERIAL'); ?> 
<div>
<span title="<?php echo VarsContainer::$display['MATERIAL']['HOUR_PRODUCTION']['METAL'];?>"><?php echo VarsContainer::$display['MATERIAL']['QUANTITY']['METAL'];?>/<?php echo VarsContainer::$display['MATERIAL']['CAPACITY']['WAREHOUSE'];?></span>
<span title="<?php echo VarsContainer::$display['MATERIAL']['HOUR_PRODUCTION']['OIL'];?>"><?php echo VarsContainer::$display['MATERIAL']['QUANTITY']['OIL'];?>/<?php echo VarsContainer::$display['MATERIAL']['CAPACITY']['WAREHOUSE'];?></span>
<span title="<?php echo VarsContainer::$display['MATERIAL']['HOUR_PRODUCTION']['GOLD'];?>">
 <?php if ((VarsContainer::$display['MATERIAL']['HOUR_PRODUCTION']['GOLD']) >= 0) {  ?> <span><?php echo VarsContainer::$display['MATERIAL']['QUANTITY']['GOLD'];?></span><?php } else { ?> <span style="color:red;"><?php echo VarsContainer::$display['MATERIAL']['QUANTITY']['GOLD'];?></span><?php } ?> /<?php echo VarsContainer::$display['MATERIAL']['CAPACITY']['BANK'];?></span>
<span> <?php if ((VarsContainer::$display['MATERIAL']['HOUR_PRODUCTION']['ENERGY']) >= 0) {  ?> <span><?php echo VarsContainer::$display['MATERIAL']['HOUR_PRODUCTION']['ENERGY'];?></span><?php } else { ?> <span style="color:red;"><?php echo VarsContainer::$display['MATERIAL']['HOUR_PRODUCTION']['ENERGY'];?></span><?php } ?> /<?php echo VarsContainer::$display['MATERIAL']['HOUR_PRODUCTION']['ENERGY_DIRTY'];?></span>
</div>