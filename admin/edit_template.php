	<div class="form-field form-required">
			<label for="edit-template-name-<?php echo $template_id;?>"><?php _e('Template name',REAL_ESTATE_RSS);?></label>
			<input name="edit-template-name" id="edit-template-name-<?php echo $template_id;?>" value="<?php echo $template_name;?>" size="40" aria-required="true" type="text">
		    <p><?php _e('The name is used to identify the template.',REAL_ESTATE_RSS);?></p>

		</div>

		<div class="form-field">
			<label for="edit-template-header-<?php echo $template_id;?>"><?php _e('Template Header',REAL_ESTATE_RSS);?></label>
			<textarea name="edit-template-header" id="edit-template-header-<?php echo $template_id;?>" rows="3" cols="15"><?php echo $template_header;?></textarea>
		</div>

		<div class="form-field">
			<label for="edit-template-body-<?php echo $template_id;?>"><?php _e('Template Body',REAL_ESTATE_RSS);?></label>
			<textarea name="edit-template-body" id="edit-template-body-<?php echo $template_id;?>" rows="3" cols="15"><?php echo $template_body;?></textarea>
		    <p><?php _e('Template Body is used by items (looping)',REAL_ESTATE_RSS);?></p>
		</div>

		<div class="form-field">
			<label for="edit-template-footer-<?php echo $template_id;?>"><?php _e('Template Footer',REAL_ESTATE_RSS);?></label>
			<textarea name="edit-template-footer" id="edit-template-footer-<?php echo $template_id;?>" rows="3" cols="15"><?php echo $template_footer;?></textarea>
		</div>
		<input type="hidden" id="edit-template-id-<?php echo $template_id;?>" name="edit-template-id-<?php echo $template_id;?>" value="<?php echo $template_id;?>" />
		<p class="submit"><input id="rerss-edittemplate-<?php echo $template_id;?>" class="button" name="submit" value="<?php _e('Save',REAL_ESTATE_RSS);?>" type="button" onClick="javascript:updateTemplate(<?php echo $template_id;?>);" /> <input id="rerss-hideedittemplate<?php echo $template_id;?>"class="button" name="submit" value="<?php _e('Hide',REAL_ESTATE_RSS);?>" type="button" /></p>
