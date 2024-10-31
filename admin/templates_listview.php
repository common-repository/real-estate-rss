<?php	
					foreach($templates as $template){
			?>		
					<tr class="iedit alternate">
										<td scope="col">
										<?php if($template->template_id != 1): ?>
											<input type="checkbox" name="check[][<?php echo $template->template_id;?>]" id="check_<?php echo $template->template_id;?>" class="checkbox check" />
										<?php endif;?>
											</td>
										<td scope="col"><?php echo $template->template_id;?></td>
										<td scope="col">
										<?php if($template->template_id == 1): ?>
										<?php echo $template->name;?><br />
										<small><em><?php _e("This is default template, you can't delete this.",REAL_ESTATE_RSS);?></em></small>
										<?php else: ?>
										<a id="title-edit<?php echo $template->template_id;?>" title="<?php _e('Edit this template',REAL_ESTATE_RSS);?>" href="#title-edit<?php echo $template->template_id;?>"><?php echo $template->name;?></a>
										<?php endif;?>
										<?php if($template->template_id != 1): ?>
											<div class="row-actions rss-template-action">
												<span class="edit" id="linkeditemplate<?php echo $template->template_id;?>"><?php _e('Edit',REAL_ESTATE_RSS);?></span> | <span class="delete"><a class="submitdelete" href="#<?php echo $template->template_id;?>" title="<?php _e('Delete this template',REAL_ESTATE_RSS);?>" onClick="javascript:deleteTemplate(this,<?php echo $template->template_id;?>);"><?php _e('Delete',REAL_ESTATE_RSS);?></a></span>
											</div>
											<script type="text/javascript">
											jQuery(document).ready( function() { 
					jQuery("#linkeditemplate<?php echo $template->template_id;?>,#title-edit<?php echo $template->template_id;?>,#rerss-hideedittemplate<?php echo $template->template_id;?>").click( function(){ 
													jQuery("#toggle<?php echo $template->template_id;?>").slideToggle(400);
												});
											});
											</script>
											<div style="display:none;" id="toggle<?php echo $template->template_id;?>">
											<?php rerss_edit_template($template->template_id);?>
											</div>
											<?php endif;?>
										</td>
										</tr>
		<?php } ?>