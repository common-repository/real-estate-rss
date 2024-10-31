<?php
$options = get_option('real_estate_rss');

$default_template = $options['template'];
$cache = $options['cache'];
$cachetime = $options['cachetime'];
$cachedir = $options['cachedir'];

if($cache == "true"){
	$checked = "checked=\"checked\" ";
}else{
	$checked = "";
}
?>
<table class="widefat fixed">
	<tr scope="row">
	<td style="width: 15%">
<label for="rerss-defaul-template"><?php _e('Default Template',REAL_ESTATE_RSS);?></label>
</td>
<td>
<select name="default-template" id="rerss-default-template">
<?php
$templates = rerss_get_templates();
foreach($templates as $template):

	if($template->template_id==$default_template){
		$selected = " selected=\"selected\"";
	}else{
		$selected = "";
	}
?>
<option value="<?php echo $template->template_id;?>"<?php echo $selected;?>><?php echo $template->name;?></option>
<?php endforeach; ?>
</select>
</td>
</tr>
<tr scope="row">
<td>
<label for="rerss-cache"><?php _e('Cache Enable',REAL_ESTATE_RSS);?></label>
</td>
<td>
	<input type="checkbox" id="rerss-cache" value="1" name="cache" <?php echo $checked;?>/>
	</td>
	</tr>
	<tr scope="row">
	<td>
<label for="rerss-cache-time"><?php _e('Cache Time',REAL_ESTATE_RSS);?></label>
</td>
<td>
<input type="text" id="rerss-cache-time" name="cache-time" value="<?php echo $cachetime;?>" />
</td>
</tr>

<tr scope="row">
<td>
<label for="rerss-cache-directory"><?php _e('Cache Directory',REAL_ESTATE_RSS);?></label>
</td>
<td>
<input type="text" id="rerss-cache-directory" name="cache-directory" size="50" value="<?php echo $cachedir;?>" />
</td>
</tr>
</table>