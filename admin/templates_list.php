
		<div id="col-left">

		<div class="wrap fixed">    
	    	<div id="rerss-adminicon" class="icon32"><br/></div>
	        <h2><?php _e('Templates',REAL_ESTATE_RSS); ?></h2>
			<form id="rss-templates-form" action="admin.php?page=<?php echo REAL_ESTATE_RSS_ADMINPAGE;?>" method="POST">

			<div class="tablenav">
				<div class="alignleft actions">
					<label class="hidden" for="bulkactions"><?php _e('Actions:',REAL_ESTATE_RSS); ?></label>
					<select name="bulkactions" id="bulkactions">
						<option value=""><?php _e('Bulk Actions',REAL_ESTATE_RSS);?></option>
						<option value="delete"><?php _e('Delete',REAL_ESTATE_RSS);?></option>
					</select>
					<input value="<?php _e('Apply',REAL_ESTATE_RSS); ?>" class="button dobulkaction" name="dobulkaction" type="submit" />
				</div>
				<div class="clear"></div>
			</div>
			<div class="clear"></div>
			
			
	        <table class="widefat" style="margin-top:4px"> 
				<thead>
					<tr>
					<th scope="col" class="check-column"><input type="checkbox" name="check_all" id="check_all" class="checkbox" /></th>
					<th scope="col"><?php _e('ID',REAL_ESTATE_RSS); ?></th>
					<th scope="col"><?php _e('Template Name',REAL_ESTATE_RSS); ?></a></th>
					</tr>
				</thead>						
				<?php $templates = $wpdb->get_results("SELECT template_id,name FROM $rerss_db"); ?>
				<tbody id="rerss-the-list">
					<?php include_once('templates_listview.php');?>
				</tbody>

				</table>
				<script type="text/javascript">
				/* <![CDATA[ */
					jQuery('#check_all').click(function(){
						jQuery('.check').attr('checked', jQuery(this).is(':checked'));
					});
					jQuery('#check_all, .check').attr('checked',false);

					// Confirm
					jQuery('.dobulkaction').click(function(){

						if ( jQuery('select[name=bulkactions]', jQuery(this).parent() ).val() == 'delete' ) {
							if ( confirm('<?php echo js_escape(__("You are about to delete the selected items.\n  'Cancel' to stop, 'OK' to delete.")); ?>') ) 
							{
								return true;
							}
							return false;
						}				
					});
				/* ]]> */
				</script>
	    </form>
	    </div>
	
	</div>