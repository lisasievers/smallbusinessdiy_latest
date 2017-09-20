<?php 

?>
<select autocomplete="off" name="<?php echo $select_id; ?>"  id="<?php echo $select_id; ?>" style="width:120px;margin-top: 30px;">
	<?php 
	foreach($language_info as $val=>$language) 
	{ 
		if($select_lan==$val) $is_selected="selected='selected'";
		else $is_selected="";

		?>
		<option <?php echo $is_selected;?> value="<?php echo $val;?>" data-image="<?php echo base_url('assets/site/plugins/ms-dropdown/images/icons/blank.gif');?>" data-imagecss="flag <?php echo $language['country_code'];?>" data-title="Bengali"><?php echo $language['label'];?></option>
		<?php
		}
	?>
</select>