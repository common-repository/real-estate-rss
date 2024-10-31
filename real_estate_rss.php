<?php
/*
Plugin Name: Real Estate RSS
Plugin URI: http://ibad.bebasbelanja.com
Description: Insert RSS into posts/pages with love.
Author: Abdul Ibad
Version: 1.0
Author URI: http://ibad.bebasbelanja.com
*/


define('REAL_ESTATE_RSS','real-estate-rss');
define('REAL_ESTATE_RSS_ADMINPAGE','real-estate-rss/real_estate_rss.php');

$rerss_db = $table_prefix."rerss";


/* Don't touch this, or you will get headache*/
$dir = str_replace('\\','/',dirname(__FILE__));
$home = get_option('siteurl');
$start = strpos($dir,'/wp-content/');
$end = strlen($dir);
$plugin_url = $home.substr($dir,$start,$end);
define('REAL_ESTATE_RSS_PLUGINURL',$plugin_url);

// Include Library
include_once($dir.'/library/simplepie.php');


load_plugin_textdomain(REAL_ESTATE_RSS);
################################################################################
// HANDLE ACTIVATE
################################################################################
function real_estate_rss_init(){
	global $wpdb,$wp_roles,$rerss_db;
	
	$wp_roles->add_cap( 'administrator', 'user_can_edit_templates_rss' );
	$wp_roles->add_cap( 'administrator', 'user_can_config_rss' );
	
		// Get Collation
	$collate = "";
	if($wpdb->supports_collation()) {
		if(!empty($wpdb->charset)) $collate = "DEFAULT CHARACTER SET $wpdb->charset";
		if(!empty($wpdb->collate)) $collate .= " COLLATE $wpdb->collate";
	} 
	
	// Create tables 	
	$structure = "CREATE TABLE IF NOT EXISTS `$rerss_db` (
  			`template_id` int unsigned NOT NULL AUTO_INCREMENT,
  			`name` varchar(200) NOT NULL,
  			`header` longtext NOT NULL,
  			`body` longtext NOT NULL,
  			`footer` longtext NOT NULL,
 			 PRIMARY KEY (`template_id`)) $collate; ";

	$wpdb->query($structure);

	$q = $wpdb->get_results("select * from $rerss_db");
	if ( !empty( $q ) ) {
		$wpdb->query("TRUNCATE table $rerss_db");
	}
	
	$header = "<a href=\"{link}\" title=\"{title}\"><h3>{title}</h3></a><br />{description}<ul>";
	$body = "<li><a href=\"{link}\" title=\"{title}\">{title}</a><br />{description}</li>";
	$footer = "</ul>";
	
	$insert = sprintf("INSERT INTO %s (name,header,body,footer) VALUES ('Default Template','%s','%s','%s')",$rerss_db,$header,$body,$footer);
	
	$wpdb->query($insert);
	
	rerss_default_options();

}

function rerss_default_options(){
	$dir = str_replace('\\','/',dirname(__FILE__));
	$abspath = str_replace('\\','/',ABSPATH);
	// Delete The Absolute path, change to path the wordpress
	$relpath = str_replace($abspath,'',$dir);
	//Cache directory
	$cachedir = $dir.'/cache';
	$options['template'] = '1';
	$options['cache']			 = 'true';
	$options['cachetime']		 = '3600';
	$options['cachedir']		 = $cachedir;
	
	// Delete the old options
	delete_option('real_estate_rss');
	
	// Add new options
	add_option('real_estate_rss',$options);
	
}

  
function rerss_activate(){

      real_estate_rss_init();
	//add_action('activate_','real_estate_rss_init');		
}

register_activation_hook( __FILE__, 'rerss_activate' );

################################################################################
// Set up menus within the wordpress admin sections
################################################################################
function rerss_menu() { 	
	
// Add a new top-level menu:
    add_menu_page(__('Real-Estate RSS',REAL_ESTATE_RSS), __('RE RSS',REAL_ESTATE_RSS), 'user_can_edit_templates_rss', __FILE__ , 'rerss_admin', REAL_ESTATE_RSS_PLUGINURL.'/images/rss_sl.png');
// Add submenus to the custom top-level menu:
	add_submenu_page(__FILE__, __('RSS Templates',REAL_ESTATE_RSS),  __('Templates',REAL_ESTATE_RSS) , 'user_can_edit_templates_rss', __FILE__ , 'rerss_admin');
    add_submenu_page(__FILE__, __('RSS Configuration',REAL_ESTATE_RSS) , __('Configuration',REAL_ESTATE_RSS) , 'user_can_config_rss', 'rerss_config', 'rerss_config');
    add_submenu_page(__FILE__, __('RSS Helpers',REAL_ESTATE_RSS) , __('Help',REAL_ESTATE_RSS) , 'user_can_config_rss', 'rerss_help', 'rerss_help');
}

add_action('admin_menu', 'rerss_menu');

function rerss_admin(){
	global $wpdb,$rerss_db;
	
	
	if(strtolower($_POST['bulkactions'])=="delete"){
		$checked = $_POST['check'];
		$bulk_ids = array();
		if ($checked && is_array($checked)) foreach ($checked as $key=>$value){
			if (key($value) && key($value)>0) $bulk_ids[] = key($value);
		} elseif ($checked) {
			$bulk_ids = explode(',',$checked);
		}
		
		 wp_cache_flush();
		
		 foreach ($bulk_ids as $bid) {
			if (is_numeric($bid) && $bid>0) {
						$sql = sprintf("DELETE FROM %s WHERE template_id='%s';",$wpdb->escape($rerss_db),$wpdb->escape($bid));
						$wpdb->query($sql);
					
			}
		
		}
		
		echo '<div id="message" class="updated fade"><p><strong>'.__('Selected Templates deleted Successfully',REAL_ESTATE_RSS).'</strong></p></div>';
		
		// Truncate table if empty
		$q	=	$wpdb->get_results("SELECT * FROM $rerss_db");

		if ( empty( $q ) ) {
			$wpdb->query("TRUNCATE table $rerss_db");
		}
	} // End Bulk actions delete
	
	$template_id = '';
	$template_name = '';
	$template_header = '';
	$template_body = '';
	$template_footer = '';
	$template_action = 'add';
	$success_action = "New template has added";
	$button_template_action = 'Add Template';
	
	?>
	
	<div id="col-container">
		
		<?php include_once ('admin/add_template.php'); ?>
		
		<?php include_once ('admin/templates_list.php'); ?>
		

</div> <!-- End Container -->
	<?php rerss_footer(); ?>
	<?php
}

################################################################################
// Admin Header
################################################################################
function rerss_adminhead(){
	
	global $wp_db_version;
	
	wp_enqueue_script('jquery');
	echo '<link rel="stylesheet" type="text/css" href="'.REAL_ESTATE_RSS_PLUGINURL.'/css/real_estate_rss.css" />';
	if ($_REQUEST['page'] == REAL_ESTATE_RSS_ADMINPAGE) {
		// Style 2.7
		
		
		// Write the scripts here
?>
		<script type="text/javascript">
		jQuery(document).ready(function($) {
		
		jQuery("#rerss-addtemplate").click( function($) {
			var ajax_url = '<?php echo admin_url("admin-ajax.php"); ?>';
			
		    /*$(this).unbind('click').bind('click', function(){return false;});*/
			var data = {
				action			: 'rerss_ajaxtemplatelist',
				cookie			: encodeURIComponent(document.cookie),
				templatename	: jQuery('#template_name').val(),
				templateheader	: jQuery('#template_header').val(),
				templatebody	: jQuery('#template_body').val(),
				templatefooter	: jQuery('#template_footer').val(),
				doaction		: 'add'
			};
			
			jQuery.post(ajax_url, data, function(response) {
				if(response != ''){
					jQuery('#template_name').val('');
					jQuery('#template_header').val('');
					jQuery('#template_body').val('');
					jQuery('#template_footer').val('');
					alert('<?php _e('New template added',REAL_ESTATE_RSS);?>');
					
					jQuery('#rerss-the-list').html(response);
				}else{
					alert('<?php _e('No Response from server',REAL_ESTATE_RSS);?>');
				}
			});
			jQuery('#template_name').text('');
			jQuery('#template_header').text('');
			jQuery('#template_body').text('');
			jQuery('#template_footer').text('');
		});
		
	});
		
		updateTemplate = function(tid){
				var ajax_url = '<?php echo admin_url("admin-ajax.php"); ?>';
			    /*$(this).unbind('click').bind('click', function(){return false;});*/
	
				var data = {
					action			: 'rerss_ajaxtemplatelist',
					cookie			: encodeURIComponent(document.cookie),
					templateid		: jQuery('#edit-template-id-'+tid).val(),	
					templatename	: jQuery('#edit-template-name-'+tid).val(),
					templateheader	: jQuery('#edit-template-header-'+tid).val(),
					templatebody	: jQuery('#edit-template-body-'+tid).val(),
					templatefooter	: jQuery('#edit-template-footer-'+tid).val(),
					doaction		: 'update'
				};

				jQuery.post(ajax_url, data, function(response) {
					if(response != ''){
						alert('<?php _e('Template updated',REAL_ESTATE_RSS);?>');
						jQuery('#rerss-the-list').html(response);
					}else{
						alert('<?php _e('No Response from server',REAL_ESTATE_RSS);?>');
					}
				});
		};
		
		deleteTemplate = function(id,tid){
		
			var ajax_url = '<?php echo admin_url("admin-ajax.php"); ?>';
			
			var ask = confirm("<?php _e("Are you sure want to delete this?",REAL_ESTATE_RSS);?>");
			
			if(ask == false){
				return false;
			}
		    /*$(this).unbind('click').bind('click', function(){return false;});*/
		
			var data = {
				action			: 'rerss_ajaxtemplatelist',
				cookie			: encodeURIComponent(document.cookie),
				templateid		: tid,
				doaction		: 'delete'
			};

			jQuery.post(ajax_url, data, function(response) {
				if(response != ''){
					jQuery('#rerss-the-list').html(response);
				}else{
						alert('<?php _e('No Response from server',REAL_ESTATE_RSS);?>');
					}
			});
	};
		</script>
<?php
			
	}elseif($_REQUEST['page'] == "rerss_config"){
		
	?>	
	<script type="text/javascript">
	
	jQuery(document).ready(function($) {
		jQuery("#rerss-recreate-database").click( function($) {
			var ajax_url = '<?php echo admin_url("admin-ajax.php"); ?>';
			
			var ask = confirm("<?php _e("This will truncate Real Estate RSS Database and Reset Options ?",REAL_ESTATE_RSS);?>");
			
			if(ask == false){
				return false;
			}
			
			var data = {
				action			: 'rerss_config_action',
				cookie			: encodeURIComponent(document.cookie),
				doaction		: 'recreate'
			};
			
			
			jQuery.post(ajax_url, data, function(response) {
				if(response != ''){
					jQuery('#response-display').html(response);
				}else{
					alert('<?php _e('No Response from server',REAL_ESTATE_RSS);?>');
				}
			});
		
		});
			
		jQuery('#rerss-save-config').click(function($){
			
				var ajax_url = '<?php echo admin_url("admin-ajax.php"); ?>';
			
				var data = {
					action			: 'rerss_config_action',
					cookie			: encodeURIComponent(document.cookie),
					template		: jQuery('#rerss-default-template').val(),
					cache			: jQuery('#rerss-cache').attr('checked'),
					cachetime		: jQuery('#rerss-cache-time').val(),
					cachedir		: jQuery('#rerss-cache-directory').val(),
					doaction		: 'saveconfig'
				};


				jQuery.post(ajax_url, data, function(response) {
					if(response != ''){
						jQuery('#response-display').html(response);
					}else{
						alert('<?php _e('No Response from server',REAL_ESTATE_RSS);?>');
					}
				});
			
		});
		
	});
	
	</script>

		
<?php
		
	}elseif($_REQUEST['page'] == "rerss_help"){
?>		
	<script type="text/javascript">
	jQuery(document).ready( function($){
		jQuery('#compatibility-test').click( function($){
			jQuery('#iframe-display').attr('src','<?php echo REAL_ESTATE_RSS_PLUGINURL."/test/sp_compatibility_test.php";?>');	
		});
		
		jQuery('#documentation-display').click( function($){
			jQuery('#iframe-display').attr('src','<?php echo REAL_ESTATE_RSS_PLUGINURL."/docs/documentation.html";?>');	
		});
	});

	</script>	
		
<?php		
	}
	
	
}

add_action('admin_head','rerss_adminhead');

################################################################################
// Templates Functions
################################################################################
function rerss_get_templates(){
	global $wpdb,$rerss_db;
	$sql = sprintf("SELECT template_id,name,header,body,footer FROM %s ORDER BY template_id ASC",$wpdb->escape($rerss_db));
	$templates = $wpdb->get_results($sql);
	return $templates;
}

function rerss_get_template($id){
	global $wpdb,$rerss_db;
	$sql = sprintf("SELECT template_id,name,header,body,footer FROM %s WHERE template_id='%s'",$wpdb->escape($rerss_db),$wpdb->escape($id));
	$template = $wpdb->get_row($sql);
	return $template;
}

function rerss_edit_template($tid){
	
	$template = rerss_get_template($tid);
	
	$template_id = $template->template_id;
	$template_name = $template->name;
	$template_header = $template->header;
	$template_body = $template->body;
	$template_footer = $template->footer;
	
	
	include('admin/edit_template.php');
	
}


################################################################################
// Templates Handle
################################################################################
function rerss_ajaxtemplatelist(){
	global $wpdb,$rerss_db;
	
	$doaction = strtolower($_REQUEST['doaction']);

	switch($doaction){
		case 'add':
	
		$templatename = strip_tags($_REQUEST['templatename']);
		$templateheader = stripslashes($_REQUEST['templateheader']);
		$templatebody = stripslashes($_REQUEST['templatebody']);
		$templatefooter = stripslashes($_REQUEST['templatefooter']);
		$sql = sprintf("INSERT INTO %s (name,header,body,footer) VALUES ('%s','%s','%s','%s')",
						$wpdb->escape($rerss_db),
						$wpdb->escape($templatename),
						$wpdb->escape($templateheader),
						$wpdb->escape($templatebody),
						$wpdb->escape($templatefooter));

		$result = $wpdb->query($sql);
		
	break;
	case 'update':
	$templateid = strip_tags($_REQUEST['templateid']);
	$templatename = stripslashes($_REQUEST['templatename']);
	$templateheader = stripslashes($_REQUEST['templateheader']);
	$templatebody = stripslashes($_REQUEST['templatebody']);
	$templatefooter = stripslashes($_REQUEST['templatefooter']);
	$sql = sprintf("UPDATE %s SET name='%s',header='%s',body='%s',footer='%s' WHERE template_id='%s'",
		$wpdb->escape($rerss_db),
		$wpdb->escape($templatename),
		$wpdb->escape($templateheader),
		$wpdb->escape($templatebody),
		$wpdb->escape($templatefooter),
		$wpdb->escape($templateid));

	$result = $wpdb->query($sql);
	if(empty($result)){
		$result = 1;
	}
	break;
	case 'delete':
	$tid = $_REQUEST['templateid'];
	$sql = sprintf("DELETE FROM %s WHERE template_id='%s'",$wpdb->escape($rerss_db),$wpdb->escape($tid));
	$result = $wpdb->query($sql);
	break;
}

	if($result){
		$templates = rerss_get_templates();
		include_once('admin/templates_listview.php');
 	}
	die();
}

################################################################################
// Configuration Handle
################################################################################
function rerss_config_action(){
	$doaction = strtolower($_REQUEST['doaction']);

	switch($doaction){
		case 'recreate':
		real_estate_rss_init();
		echo '<div id="message" class="updated fade"><p><strong>'.__('Re-Create Database',REAL_ESTATE_RSS).'</strong></p></div>';
		include('admin/config.php');
		?>
		<script type="text/javascript">
		jQuery(document).ready( function($){
			jQuery('#message').slideUp(5000);
		});
		
		</script>
		<?php
		break;
		case 'saveconfig':
		
		$options = get_option('real_estate_rss');
		$newoptions['template'] = $_REQUEST['template'];
		$newoptions['cache'] = $_REQUEST['cache'];
		$newoptions['cachetime'] = $_REQUEST['cachetime'];
		$newoptions['cachedir']  = $_REQUEST['cachedir'];
		if($newoptions != $options){
			update_option('real_estate_rss',$newoptions);
			echo '<div id="message" class="updated fade"><p><strong>'.__('Options updated',REAL_ESTATE_RSS).'</strong></p></div>';
			include('admin/config.php');
			?>
			<script type="text/javascript">
			jQuery(document).ready( function($){
				jQuery('#message').slideUp(5000);
			});
			
			</script>
			<?php
		}else{
			echo '<div id="message" class="updated fade"><p><strong>'.__('Options update failed',REAL_ESTATE_RSS).'</strong></p></div>';
			include('admin/config.php');
			?>
			<script type="text/javascript">
			jQuery(document).ready( function($){
				jQuery('#message').slideUp(5000);
			});
			
			</script>
			<?php
		}
		break;
	}
	
	die();
}

add_action('wp_ajax_rerss_ajaxtemplatelist', 'rerss_ajaxtemplatelist' );
add_action('wp_ajax_rerss_config_action','rerss_config_action');


################################################################################
// Parser
################################################################################
function rerss_parse( $atts , $subs , $data ){
	$data = str_replace( $atts , $subs, $data );
	return $data;
}
function rerss_parse_image($template,$feed){

	if(preg_match('/{image\s(.*?)}/',$template,$matches) ){
			$value = "";
			$code = $matches[1];
			$pattern = $matches[0];
			$code = strtoupper(str_replace(" ", "", $code));
			switch($code){
				case "WIDTH":
					$value = $feed->get_image_width();
					break;
				case "HEIGHT":
					$value = $feed->get_image_height();
					break;
				case "LINK":
					$value = $feed->get_image_link();
					break;
				case "TITLE":
					$value = $feed->get_image_title();
					break;
				case "URL":
				default:
					$value = $feed->get_image_url();
					break;
				}
	}
	
	return array($pattern,$value);
}
################################################################################
/*
Parse with custom template rerss_parse_customtemplate($feed,$customtemplate,$numberitems)
$feed: means of url of the feed
$customtemplate: ID of the custom templates
$numberitems : The number of items to show
*/
################################################################################
function rerss_parse_customtemplate($feed,$customtemplate="",$numberitems=""){
	global $wpdb,$rerss_db;

	if(strstr("$feed",',')){
		$feed = explode(",",$feed);
	}
	
	// Call SimplePie
	$feed = new SimplePie($feed);

	// Get Options
	$options = get_option('real_estate_rss');
	$defaulttemplate = $options['template'];
	$cache   = $options['cache'];
	$cachetime = $options['cachetime'];
	
	// Windows directory handle
	$abspath = str_replace('\\','/',ABSPATH);	
	
	// make to absolute path
	$cachedir  = $abspath.$options['cachedir'];

	// Cache action
	if($cache == "true"){
		$feed->enable_cache('true');
		$feed->set_cache_location($cachedir);
		$cachetime = (intval($cachetime) / 60); //convert from seconds to minutes
		$feed->set_cache_duration($cachetime);
	}else{
		$feed->enable_cache('false');
	}
	
	// Init feed
	$feed->init();
	
	// Assign to default template
	if(empty($customtemplate) || ($customtemplate == 0)){
		$customtemplate = $defaulttemplate;
	}
	
	// Get the template
	$template = rerss_get_template($customtemplate);

	$fpatts = array('{title}','{link}','{description}','{language}','{copyright}','{category}','{image}','{favicon}');

	$fsubs = array($feed->get_title(),$feed->get_link(),$feed->get_description(),$feed->get_language(),$feed->get_copyright(),$feed->get_category(),$feed->get_image_url(),$feed->get_favicon());	
			
	$itematts = array('{title}','{link}','{description}','{author}','{category}','{source}','{date}','{content}');
	
	$start = 0;
	$length = $numberitems;
	
	$items = $feed->get_items($start,$length);
	$itemstext = array();
	
	// Number items must have, if == 0 or < 1 then number items = all
	if(($numberitems == 0) || ($numberitems < 1)){
		$numberitems = count($feed->get_items());
	}
		

	// Replace the attributes
	foreach($items as $item){

		$itemsubs = array($item->get_title(),$item->get_permalink(),$item->get_description(),$item->get_author(),$item->get_category(),$item->get_source(),$item->get_date(),$item->get_content());
		
			if(preg_match('/{date\s\"(.*?)\"}/',$template->body,$match) ){
				$itematts[] = $match[0];
				$itemsubs[] = $item->get_date($match[1]);
			}
		
		$itemstext[] = rerss_parse($itematts,$itemsubs,$template->body);
		$count++;
	}
	
	
	// Parse the feed logo
	$image = rerss_parse_image($template->header,$feed);

	$fpatts[] = $image[0];
	$fsubs[]  = $image[1];
	
	
	// Parse the header and footer template
	$header = rerss_parse($fpatts,$fsubs,$template->header);
	$footer = rerss_parse($fpatts,$fsubs,$template->footer);
	
	// Assign the result to $data for return
	
	// Copyright intact - Donate to remove this or cat will not free again ;)
	// Support this plugin
	$copyright = '<div style="text-align:center;font-size: .8em;"><a href="http://ibad.bebasbelanja.com/real-estate-rss.html" rel="follow" style="color: #f4f4f4">Generate By Real Estate RSS</a></div>';
	
	$data = $header;
	
	foreach($itemstext as $itemtext){
		$data .= $itemtext;
	}
	$data .= $footer;

	///$data .= $copyright;

	return $data;
}

function rerss_parse_shortcode($shortcode){
		
		// Get the "url=" value
		if(preg_match_all("/url=\"(.+?)\"/",$shortcode,$matches,PREG_SET_ORDER)){
			$url = $matches[0][1];
			
		}
		
		// Get the "template=" value
		if(preg_match_all("/template=([0-9]+)/",$shortcode,$matches,PREG_SET_ORDER)){
			$template = $matches[0][1];
		}
		
		// Get the "items=" value
		if(preg_match_all("/items=([0-9]+)/",$shortcode,$matches,PREG_SET_ORDER)){
			$items = $matches[0][1];
		}
		
		$data = rerss_parse_customtemplate($url,$template,$items);
		return $data;
}

function rerss_parse_content( $content ){

	// if not found [rss, then return the content 
	if(substr_count($content,'[rss') == 0) return $content;

	if(preg_match_all("/\[rss.+?\]/", $content, $matches,PREG_SET_ORDER)){
		foreach($matches as $match => $shortcode){
			
			// Check if have URL(feed)
			if(preg_match_all("/url=\"(.+?)\"/",$shortcode[0],$matches,PREG_SET_ORDER)){
				$url = $matches[0][1];
				if(empty($url)){
						continue;
				}
			}
			
			$content = str_replace($shortcode,rerss_parse_shortcode($shortcode[0]),$content);
		}
	}
	return $content;	
}

add_filter('the_content','rerss_parse_content',1);


################################################################################
/*
Configuration page
*/
################################################################################
function rerss_config(){
	
?>
<div class="wrap">
	<div id="rerss-adminicon" class="icon32"><br></div>

<h2>RSS Configuration</h2>
<div id="response-display">
<?php include('admin/config.php'); ?>
</div>
<br />
<input type="button" name="save" id="rerss-save-config" class="button-secondary" value="<?php _e("Save Changes",REAL_ESTATE_RSS);?>" /> &nbsp; <input type="button" name="recreate" id="rerss-recreate-database" class="button-secondary" value="<?php _e("Re-Create Database",REAL_ESTATE_RSS);?>" />
</div>
<?php rerss_footer(); ?>
<?php
}

################################################################################
/*
Documentation Page
*/
################################################################################
function rerss_help(){
?>

	<div class="wrap">
		<div id="rerss-adminicon" class="icon32"><br></div>

	<div class="form-wrap">
	<h2><?php _e('RSS Help',REAL_ESTATE_RSS);?></h2>
	<table class="widefat">
		<tr>
			<td>
				<a id="documentation-display" href="#documentation"><?php _e('Documentation',REAL_ESTATE_RSS);?></a> &nbsp; |
				&nbsp;
	<a id="compatibility-test" href="#compatibility"><?php _e('Compatibility Test',REAL_ESTATE_RSS);?></a>
	<iframe frameborder="0" id="iframe-display" src="<?php echo REAL_ESTATE_RSS_PLUGINURL."/docs/documentation.html";?>" height="400" style="width:100%" />
	</td>
	</tr>
	</table>
	</div>
	</div>
<?php
}

################################################################################
/*
Footer
*/
################################################################################
function rerss_footer(){
	?>
	<br />
	<hr style="border:0px;height:1px;font-size:1px;margin-bottom:5px;background:#dddddd;color:#dddddd" />
	<small style="color:#999999">
	<a href="?page=rerss_help"><?php _e('Documentation',REAL_ESTATE_RSS);?></a> &nbsp; | &nbsp; <a target="_blank" href="#home"><?php _e('Visit plugin home',REAL_ESTATE_RSS);?></a> &nbsp; | &nbsp; <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&amp;business=3ZM74BGUXB7EE&amp;lc=ID&amp;item_name=Real%20Estate%20RSS&amp;currency_code=USD&amp;bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted" title="Click to donate" target="_blank"><?php _e('Donate',REAL_ESTATE_RSS);?></a> &nbsp; |
	</small>
	<?php
}

function real_estate_rss($args){
	
	$defaults = array(
			'url' => '', 
			'template' => '1',
			'items' => -1,
			'echo' => 1
		);

	$args = str_replace('&amp;','&',$args);
	
	$r = wp_parse_args( $args, $defaults );
	
	if($r['echo']){
		
			 echo rerss_parse_customtemplate($r['url'],$r['template'],$r['items']);
	}else{
			return rerss_parse_customtemplate($r['url'],$r['template'],$r['items']);
	}

}

include_once($dir.'/widget.php');

add_action( 'widgets_init', 'rerss_load_widgets' );
?>