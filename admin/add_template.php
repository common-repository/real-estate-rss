		<div id="col-right">
		<div class="col-wrap">


		<div class="form-wrap">
		<h3><?php _e('Add Custom Template',REAL_ESTATE_RSS);?></h3>
		<div id="ajax-response"></div>
		<div class="form-field form-required">
			<label for="template_name"><?php _e('Template Name',REAL_ESTATE_RSS);?></label>
			<input name="template_name" id="template_name" value="<?php echo $template_name;?>" size="40" aria-required="true" type="text">
		   <p><?php _e('The name is used to identify the template.',REAL_ESTATE_RSS);?></p>

		</div>

		<div class="form-field">
			<label for="template_header"><?php _e('Template Header',REAL_ESTATE_RSS);?></label>
			<textarea name="template_header" id="template_header" rows="6" cols="30"><?php echo $template_header;?></textarea>
		</div>

		<div class="form-field">
			<label for="template_body"><?php _e('Template Body',REAL_ESTATE_RSS);?></label>
			<textarea name="template_body" id="template_body" rows="6" cols="30"><?php echo $template_body;?></textarea>
		    <p><?php _e('Template Body is used by items (looping)',REAL_ESTATE_RSS);?></p>
		</div>

		<div class="form-field">
			<label for="template_footer"><?php _e('Template Footer',REAL_ESTATE_RSS);?></label>
			<textarea name="template_footer" id="template_footer" rows="6" cols="30"><?php echo $template_footer;?></textarea>
		</div>
		<input type="hidden" id="template_id" name="template_id" value="<?php echo $template_id;?>" />
		<p class="submit"><input id="rerss-addtemplate"class="button" name="submit" value="<?php echo $button_template_action;?>" type="button" /></p>
		</div>

		</div> 

		</div> <!-- End col-right-->
