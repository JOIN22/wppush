<?php
/*
Plugin Name: ReviewAZON - The Amazon Product Review Plug-in for Wordpress
Plugin URI: http://www.reviewazon.com
Description: The Amazon Product Review Plug-in for Wordpress
Author: Brad Hanson, Niche Web Strategies, LLC
Version: 1.6.0
Author URI: http://www.reviewazon.com
*/
#########################################################################
#                                                                       #
#                   ReviewAZON 1.x Wordpress Plugin                     #
#                        www.reviewazon.com                             #
#                                                                       #
#########################################################################
# COPYRIGHT NOTICE                                                      #
# Copyright 2009 Nichew Web Strategies LLC.  All Rights Reserved.       #
#                                                                       #
# This script may be only used and modified in accordance to the        #
# license agreement attached (eula.txt) except where expressly          #
# noted within commented areas of the code body. This copyright notice  #
# and the comments above and below must remain intact at all times.     #
# By using this code you agree to indemnify Niche Web Strategies LLC,   #
# its corporate agents and affiliates from any liability that might     #
# arise from its use.                                                   #
#                                                                       #
# Selling the code for this program without prior written consent is    #
# expressly forbidden and in violation of Domestic and International    #
# copyright laws.                                                       #
#########################################################################

#ReviewAZON AWS Settings
#============================================================================================================================================
define('KEYID', ReviewAZON_get_affiliate_setting("AWS_Webservice_Key"));
define('SECRETKEY', ReviewAZON_get_affiliate_setting("AWS_Secret_Key"));
define('AssocTag', ReviewAZON_get_affiliate_setting("AWS_Associate_ID"));
define('AWSVERSION','2010-09-01');
define('AWSUrl','http://ecs.amazonaws.'. ReviewAZON_get_affiliate_setting("AWS_Affiliate_Country") .'/onca/xml?');
define('AFFILIATECOUNTRY',ReviewAZON_get_affiliate_setting("AWS_Affiliate_Country"));
define('REVIEWAZONVER','1.6.0');
define('REVIEWAZONDBVER','1.1.1');
#============================================================================================================================================

# ReviewAZON paths
#============================================================================================================================================
define('WP_PLUGIN_DIR', WP_CONTENT_DIR . '\plugins');
define('WP_PLUGIN_URL', WP_CONTENT_URL . '/plugins');
define('ReviewAZON_ROOT', dirname(__FILE__) . '/');   
define('ReviewAZON_ADMIN_FOLDER', dirname(__FILE__) . '/admin/'); 
define('ReviewAZON_URL_ROOT', WP_PLUGIN_URL . '/ReviewAZON/');  
define('ReviewAZON_IMAGES_FOLDER', WP_PLUGIN_URL . '/ReviewAZON/images/');   
define('ReviewAZON_IMAGES_FOLDER_NOSLASH', WP_PLUGIN_URL . '/ReviewAZON/images');         
define('ReviewAZON_ADMIN', ReviewAZON_URL_ROOT . 'admin/');
define('ReviewAZON_ADMIN_IMAGES', ReviewAZON_URL_ROOT . 'admin/images/');
define('ReviewAZON_PROCESS_REQUEST_FILE', ReviewAZON_URL_ROOT . 'processRequest.php');
#define('ReviewAZON_PROCESS_REQUEST_FILE', 'http://'.$_SERVER[ 'HTTP_HOST' ].'/wp-content/plugins/ReviewAZON/processRequest.php');
define('ReviewAZON_VIDEO_SEARCH_FILE', ReviewAZON_URL_ROOT . 'video_search_request.php');
define('AWSERRORHEADER','<p><img src="'.ReviewAZON_ADMIN_IMAGES.'error.gif" style="padding-right:5px;vertical-align:sub;" /><b>Amazon Web Service Configuration Error!</b></p>');
#============================================================================================================================================

#Create Admin Menus
#============================================================================================================================================
add_action('admin_menu', 'ReviewAZON_add_admin_pages');
add_action('admin_menu', 'ReviewAZON_add_custom_box');
add_action('save_post', 'ReviewAZON_save_postdata');
add_action('delete_post', 'ReviewAZON_delete_postdata');

require_once("simple_html_dom.php");

function ReviewAZON_add_admin_pages() 
{
    add_menu_page('ReviewAZON Report', 'ReviewAZON', 8, __FILE__, 'ReviewAZON_toplevel_page', '/wp-content/plugins/ReviewAZON/admin/images/menuicon.png');
   
    add_submenu_page('ReviewAZON/ReviewAZON.php', 'Affiliate Settings', 'Affiliate Settings', 8, 'Affiliate_Settings', 'ReviewAZON_affiliate_settings');
    
    add_submenu_page('ReviewAZON/ReviewAZON.php', 'Advanced Settings', 'Advanced Settings', 8, 'Advanced_Settings', 'ReviewAZON_advanced_settings');
    
    add_submenu_page('ReviewAZON/ReviewAZON.php', 'Bulk Product Posts', 'Bulk Product Posts', 8, 'Bulk_Product_Posts', 'ReviewAZON_bulk_reviews');
    
    add_submenu_page('ReviewAZON/ReviewAZON.php', 'ReviewAZON Click Reporting', 'Click Reporting', 8, 'Click_Reporting', 'ReviewAZON_detail_reports');
    
    add_submenu_page('ReviewAZON/ReviewAZON.php', 'Manage Templates', 'Manage Templates', 8, 'Manage_Templates', 'ReviewAZON_manage_templates');
   
}

function ReviewAZON_affiliate_settings() 
{
	include('affiliate_settings.php');
}

function ReviewAZON_bulk_reviews() 
{
	include('manage_bulk_reviews.php');
}

function ReviewAZON_manage_templates() 
{
	include('manage_templates.php');
}

function ReviewAZON_advanced_settings() 
{
	include('advanced_settings.php');
}

function ReviewAZON_detail_reports()
{
	global $wpdb;

	$nonce= wp_create_nonce ('my-nonce');

		if(isset($_POST['ReviewAZON_truncate_product_settings']))
		{
			if(check_admin_referer( 'my-nonce'))
			{
				if($_POST['ReviewAZON_report_column'] == "All")
				{
					$reportQuery = "SELECT count(*) as Clicks, ASIN, Click_Date, Tracking_ID, Click_ID FROM ".$wpdb->prefix."reviewazon_reports Group By ASIN Order By Clicks DESC";
				}
				else
				{
					$reportQuery = "SELECT count(*) as Clicks, ASIN, Click_Date, Tracking_ID, Click_ID FROM ".$wpdb->prefix."reviewazon_reports WHERE {$_POST['ReviewAZON_report_column']} LIKE '%{$_POST['ReviewAZON_report_filter_text']}%' Group By ASIN Order By Clicks DESC";
				}
			}
			else
			{
				die('');
			}
		}
		else
		{
			$reportQuery = "SELECT count(*) as Clicks, ASIN, Click_Date, Tracking_ID, Click_ID FROM ".$wpdb->prefix."reviewazon_reports Group By ASIN Order By Clicks DESC";
		}

?>
<link type="text/css" href="<?php echo ReviewAZON_ADMIN ?>css/admin.css" rel="Stylesheet" />
<link type="text/css" href="<?php echo ReviewAZON_ADMIN ?>theme/ui.all.css" rel="Stylesheet" />
<script type="text/javascript" src="<?php echo ReviewAZON_ADMIN ?>js/jquery-1.3.1.js"></script>
<script type="text/javascript" src="<?php echo ReviewAZON_ADMIN ?>js/jquery-ui-personalized-1.6rc6.js"></script>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#detail_dialog').dialog({
		autoOpen: false,
		resizable: true,
		buttons: {
			"Close": function() { 
				jQuery(this).dialog("close"); 
			} 
		},
		modal: true,
		overlay:{ background: "gray", opacity: 0.9 },
		width: 700,
		height: 550
	});
});

function getReportDetails(id,title)
{
	jQuery('#ui-dialog-title-detail_dialog').html('Report Details for '+title);
	jQuery('#dialog-list').html('<tr class="iedit alternate"><td class="name column-name" style="text-align:center" colspan="5"><img src="<?php echo ReviewAZON_ADMIN_IMAGES ?>ajax-loader_old2.gif" style="vertical-align:middle;margin:5px;" /><strong>Report Data.............</strong></td></tr>');
	jQuery('#detail_dialog').dialog('open');
	jQuery.post("<?php echo ReviewAZON_PROCESS_REQUEST_FILE ?>", { action: 'getreportdetails', id: id , _ajax_nonce:'<?php echo $nonce ?>'}, function(data){
		jQuery('#dialog-list').html(data);
	});
}

</script>
<form method="post">
<div class="wrap">
	<h2><img src="<?php echo ReviewAZON_ADMIN_IMAGES ?>largestar.gif" style="vertical-align:middle;margin:5px;" />ReviewAZON Click Report - ReviewAZON Wordpress Plugin Version <?php echo REVIEWAZONVER ?></h2>
	<br />
	<table>
	<tr>
		<td>Report Filter:</td>
		<td><input type="text" name="ReviewAZON_report_filter_text" value="<?php echo $_POST['ReviewAZON_report_filter_text']; ?>"/></td>
		<td>
			<select name="ReviewAZON_report_column">
				<option value="All">Show All Records</option>
				<option value="ASIN" <?php if($_POST['ReviewAZON_report_column'] == "ASIN") { echo 'selected'; }?>>ASIN</option>
				<option value="Title" <?php if($_POST['ReviewAZON_report_column'] == "Title") { echo 'selected'; }?>>Title</option>
				<option value="Tracking_ID" <?php if($_POST['ReviewAZON_report_column'] == "Tracking_ID") { echo 'selected'; }?>>Tracking ID</option>
			</select>
		</td>
		<td>
    		<input class="button-primary" name="ReviewAZON_truncate_product_settings" type="submit" value="Search" />
		</td>
	</tr>
	<tr><td colspan="4">&nbsp;</td></tr>
	</table>
	<table cellspacing="0" class="widefat fixed">
	<thead>
	<tr>
	<th style="text-align:center;width:75px;" class="manage-column" scope="col">No. Clicks</th>
	<th style="text-align:center;width:150px;" class="manage-column" scope="col">Product</th>
	<th style="" class="manage-column" scope="col">Title</th>
	<th style="" class="manage-column" scope="col">ASIN</th>
	<th style="" class="manage-column"  scope="col">Tracking ID</th>
	<th style="" class="manage-column" scope="col">Click Details</th>
	</tr>
	</thead>
	<tfoot>
	<tr>
	<th style="text-align:center;width:75px;" class="manage-column" scope="col">No. Clicks</th>
	<th style="text-align:center;width:150px;" class="manage-column" scope="col">Product</th>
	<th style="" class="manage-column" scope="col">Title</th>
	<th style="" class="manage-column" scope="col">ASIN</th>
	<th style="" class="manage-column"  scope="col">Tracking ID</th>
	<th style="" class="manage-column" scope="col">Click Details</th>
	</tr>
	</tfoot>
	<tbody class="list:cat" id="the-list">
<?php 
$reportRows = $wpdb->get_results($reportQuery);
$reportImage = ReviewAZON_ADMIN_IMAGES.'/report.gif';
if(count($reportRows) > 0)
{
	foreach($reportRows as $reportRow)
	{
		$current = ReviewAZON_get_amazon_current_node($reportRow->ASIN,null,null,null,null);
		
		echo '<tr class="iedit alternate"><td class="name column-name" style="text-align:center">'.$reportRow->Clicks.'</td><td class="column-name" style="width:10px;text-align:center;"><img src="'.$current->SmallImage->URL.'" style="margin:5px;" /></td><td class="name column-name">'.$current->ItemAttributes->Title.'</td><td class="name column-name">'.$reportRow->ASIN.'</td><td class="name column-name" nowrap>'.$reportRow->Tracking_ID.'</td><td class="name column-name" nowrap><a href="#" onclick="getReportDetails(\''.$reportRow->ASIN.'\',\''.$current->ItemAttributes->Title.'\')"><img src="'.$reportImage.'" style="vertical-align:middle;margin:5px;" />View Details</a></td></tr>';
	}
}
else
{
	echo '<tr class="iedit alternate"><td class="column-name" colspan="6">There are no clicks to report.</td></tr>';	
}
?>
</tbody>
</table>
<div class="ReviewAZON" id="detail_dialog" title="Report Details" style="display:none;overflow;auto;">
	<table cellspacing="0" class="widefat">
	<thead>
	<tr>
	<th style="text-align:center;width:150px;" class="manage-column" scope="col">Product</th>
	<th style="" class="manage-column" scope="col">ASIN</th>
	<th style="" class="manage-column" scope="col">Title</th>
	<th style="" class="manage-column"  scope="col">Tracking ID</th>
	<th style="" class="manage-column" scope="col">IP Address</th>
	<th style="" class="manage-column" scope="col">Click Date</th>
	</tr>
	</thead>
	<tfoot>
	<tr>
	<th style="text-align:center;width:150px;" class="manage-column" scope="col">Product</th>
	<th style="" class="manage-column" scope="col">ASIN</th>
	<th style="" class="manage-column" scope="col">Title</th>
	<th style="" class="manage-column"  scope="col">Tracking ID</th>
	<th style="" class="manage-column" scope="col">IP Address</th>
	<th style="" class="manage-column" scope="col">Click Date</th>
	</tr>
	</tfoot>
	<tbody class="list:cat" id="dialog-list">
	<tr class="iedit alternate"><td class="name column-name" style="text-align:center" colspan="6"><img src="<?php echo ReviewAZON_ADMIN_IMAGES ?>ajax-loader_old2.gif" style="vertical-align:middle;margin:5px;" /><strong>Report Data.............</strong></td></tr>
	</tbody>
</table>
</div>
</div>
<?php wp_nonce_field('my-nonce'); ?>
</form>
<?php	
}
#============================================================================================================================================

#Register short code parser to get product reviews
add_shortcode('ReviewAZON', 'reviewAZON_func');
add_filter('the_content_rss','do_shortcode', 11);

#Register activation script to create tables and populate default data
register_activation_hook(__FILE__, 'ReviewAZON_activate');

#Add some stuff to the document head
add_action('wp_head', 'ReviewAZON_add_stuff_head');

function ReviewAZON_add_stuff_head()
{
	if(ReviewAZON_get_affiliate_setting("Css_Location") == 'default')
	{
		echo '<link rel="stylesheet" type="text/css" href="'.ReviewAZON_URL_ROOT.'templates/'.ReviewAZON_get_affiliate_setting("Default_Template_Group").'/css/default.css" media="screen" />';
	}
	else
	{
		echo '<link rel="stylesheet" type="text/css" href="'.ReviewAZON_get_affiliate_setting("Css_Location").'/default.css" media="screen" />';
	}
}


#Register widget hooks
#=========================================================================================================
add_action("plugins_loaded", "auction_init");
add_action("plugins_loaded", "amazon_product_accessories_init");
add_action("plugins_loaded", "amazon_similar_products_init");
add_action("plugins_loaded", "featuredProducts_init");
add_action("plugins_loaded", "brands_init");
add_action("plugins_loaded", "priceRange_init");
add_action('wp_dashboard_setup', 'ReviewAZON_reports_init' );

#=========================================================================================================

#Widget initialize functions
#=========================================================================================================
function ReviewAZON_reports_init()
{
	wp_add_dashboard_widget('reviewazon_reporting', 'ReviewAZON Top 5 Performing Products', 'ReviewAZON_Reports');
}

function ReviewAZON_Reports()
{
	$nonce= wp_create_nonce ('my-nonce');
?>
<script type="text/javascript">
jQuery(document).ready(function(){
	getDashboardReportDetails();
});

function getDashboardReportDetails()
{
	jQuery.post("<?php echo ReviewAZON_PROCESS_REQUEST_FILE ?>", { action: 'getdashboardreport', _ajax_nonce:'<?php echo $nonce ?>' }, function(data){
		jQuery('#dashboard-list').html(data);
	});
}
</script>
<p>These are the top 5 performing products based on the number of clicks to the Amazon web site.</p>
<table cellspacing="0" class="widefat fixed">
	<thead>
	<tr>
	<th style="width:50px;text-align:center;" class="manage-column" scope="col">Clicks</th>
	<th style="" class="manage-column" scope="col">Product</th>
	<th style="" class="manage-column" scope="col">Title</th>
	<th style="" class="manage-column" scope="col">ASIN</th>
	<th style="" class="manage-column" scope="col">Tracking ID</th>
	</tr>
	</thead>
	<tbody class="list:cat" id="dashboard-list">
		<tr class="iedit alternate"><td class="name column-name" style="text-align:center" colspan="4"><img src="<?php echo ReviewAZON_ADMIN_IMAGES ?>ajax-loader_old2.gif" style="vertical-align:middle;margin:5px;" /><strong>Report Data.............</strong></td></tr>
	</tbody>
</table>
<div style="text-align:right;padding:8px;padding-top:10px;"><img src="<?php echo ReviewAZON_ADMIN_IMAGES.'/report.gif';?>" style="vertical-align:middle;margin:5px;" /><a href="/wp-admin/admin.php?page=Click_Reporting">View Full Product Report</a></div>
<?php	
}

function amazon_product_accessories_init()
{
	register_sidebar_widget(__('Amazon Product Accessories'), 'widget_amazon_product_accessories');
	register_widget_control(   'Amazon Product Accessories', 'product_accessories_control', 300, 200 );	
}

function amazon_similar_products_init()
{
	register_sidebar_widget(__('Amazon Similar Products'), 'widget_amazon_similar_products');
	register_widget_control(   'Amazon Similar Products', 'similar_products_control', 300, 200 );
}

function auction_init()
{
  register_sidebar_widget(__('eBay Auctions Widget'), 'widget_auction');
  register_widget_control(   'eBay Auctions Widget', 'auction_control', 300, 200 );  
}

function featuredProducts_init()
{
  register_sidebar_widget(__('Featured Product Widget'), 'widget_featuredProducts');
  register_widget_control(   'Featured Product Widget', 'featuredProducts_control', 300, 200 );  
}


function brands_init()
{
  register_sidebar_widget(__('Brands Widget'), 'widget_brands');
  register_widget_control(   'Brands Widget', 'brands_control', 300, 200 );  
}


function priceRange_init()
{
  register_sidebar_widget(__('Price Range Widget'), 'widget_priceRange');
  register_widget_control(   'Price Range Widget', 'priceRange_control', 300, 200 );  
}

#===========================================================================================================


#Plugin Activation Function
#===========================================================================================================
function ReviewAZON_activate()
{
	global $wpdb;	
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
    update_option( "ReviewAZON_db_state", "" );
    
    

    

	if(ReviewAZON_get_mysql_version() >= 5)
	{	
			$settings_table_name = $wpdb->prefix.'reviewazon_settings';
			$post_setting_table_name = $wpdb->prefix.'reviewazon_post_settings';
			$tabs_table_name = $wpdb->prefix.'reviewazon_tabs';
			$templates_table_name = $wpdb->prefix.'reviewazon_templates';
			$video_reviews_table_name = $wpdb->prefix.'reviewazon_video_reviews';
			$bulk_reviews_table_Name = $wpdb->prefix.'reviewazon_bulk_reviews';
			$reports_table_Name = $wpdb->prefix.'reviewazon_reports';
			$tracking_table_Name = $wpdb->prefix.'reviewazon_tracking_id';
			$installed_ver = get_option("ReviewAZON_db_version");	
			
			//Rename ReviewAZON tables with Wordpress Prefix for multiple table use
			if($wpdb->get_var("SHOW TABLES LIKE 'reviewazon_settings'") == 'reviewazon_settings')
			{
				$settingsNameChangeQuery = "RENAME TABLE reviewazon_settings TO {$settings_table_name}";
				$wpdb->query($settingsNameChangeQuery);
			}
			if($wpdb->get_var("SHOW TABLES LIKE 'reviewazon_post_settings'") == 'reviewazon_post_settings')
			{
				$settingsNameChangeQuery = "RENAME TABLE reviewazon_post_settings TO {$post_setting_table_name}";
				$wpdb->query($settingsNameChangeQuery);
			}
			if($wpdb->get_var("SHOW TABLES LIKE 'reviewazon_tabs'") == 'reviewazon_tabs')
			{
				$settingsNameChangeQuery = "RENAME TABLE reviewazon_tabs TO {$tabs_table_name}";
				$wpdb->query($settingsNameChangeQuery);
			}
			if($wpdb->get_var("SHOW TABLES LIKE 'reviewazon_templates'") == 'reviewazon_templates')
			{
				$settingsNameChangeQuery = "RENAME TABLE reviewazon_templates TO {$templates_table_name}";
				$wpdb->query($settingsNameChangeQuery);
			}
			if($wpdb->get_var("SHOW TABLES LIKE 'reviewazon_video_reviews'") == 'reviewazon_video_reviews')
			{
				$settingsNameChangeQuery = "RENAME TABLE reviewazon_video_reviews TO {$video_reviews_table_name}";
				$wpdb->query($settingsNameChangeQuery);
			}
			if($wpdb->get_var("SHOW TABLES LIKE 'reviewazon_bulk_reviews'") == 'reviewazon_bulk_reviews')
			{
				$settingsNameChangeQuery = "RENAME TABLE reviewazon_bulk_reviews TO {$bulk_reviews_table_Name}";
				$wpdb->query($settingsNameChangeQuery);
			}
			if($wpdb->get_var("SHOW TABLES LIKE 'reviewazon_reports'") == 'reviewazon_reports')
			{
				$settingsNameChangeQuery = "RENAME TABLE reviewazon_reports TO {$reports_table_Name}";
				$wpdb->query($settingsNameChangeQuery);
			}			
			if($wpdb->get_var("SHOW TABLES LIKE 'reviewazon_tracking_id'") == 'reviewazon_tracking_id')
			{
				$settingsNameChangeQuery = "RENAME TABLE reviewazon_tracking_id TO {$tracking_table_Name}";
				$wpdb->query($settingsNameChangeQuery);
			}			
			
			$createSettingsTableSQL = "CREATE TABLE {$settings_table_name} (
		  						  ID int(11) NOT NULL AUTO_INCREMENT,
		  						  AWS_Webservice_Key varchar(255) NOT NULL,
		  						  AWS_Secret_Key varchar(255) NOT NULL,
		  						  AWS_Associate_ID varchar(255) NOT NULL,
		  						  AWS_URL varchar(255) NOT NULL,
		  						  Default_Template_Group varchar(255) NOT NULL DEFAULT 'Default',
		  						  Post_Content_Type varchar(255) NOT NULL,
		  						  Excerpt_Layout_Mode varchar(255) NOT NULL DEFAULT 'Normal',
								  Show_Excerpts_Pages varchar(255) NOT NULL,
								  Show_Ebay_Auctions tinyint(1) NOT NULL DEFAULT '0',
								  Show_Overstock_Items tinyint(1) NOT NULL DEFAULT '0',
								  No_Reviews_To_Display tinyint(1) NOT NULL DEFAULT '5',
								  Min_Review_Score_To_Display varchar(100) NOT NULL DEFAULT '-OverallRating',
								  AWS_Affiliate_Country varchar(50) NOT NULL,
								  Cache_Description tinyint(1) NOT NULL DEFAULT '0',
								  Cache_Customer_Reviews tinyint(1) NOT NULL DEFAULT '1',
								  Cache_Similar_Products tinyint(1) NOT NULL DEFAULT '1',
								  Cache_Product_Accessories tinyint(1) NOT NULL DEFAULT '1',
								  Page_Cache_Length int(11) NOT NULL DEFAULT '0',
								  Save_Last_Search_Results tinyint(1) NOT NULL DEFAULT '0',
								  Invalidate_Page_Cache tinyint(1) NOT NULL DEFAULT '1',
								  Free_Shipping_Text text,
								  Empty_Product_Accessories_Text text,
								  Allow_Expanded_Tokens tinyint(1) NOT NULL DEFAULT '1',
								  Empty_Related_Products_Text text,
								  Empty_Ebay_Auctions_Text text,
								  Empty_PhpOStock_Text text NOT NULL,
								  Empty_Video_Reviews_Text text,
								  Empty_Product_Details_Text text,
								  Empty_Customer_Reviews_Text text,
								  Truncate_Excerpt int(11) NOT NULL DEFAULT '350',
								  Truncate_Excerpt_Text text NOT NULL,
								  Allow_Link_Cloaking tinyint(1) NOT NULL DEFAULT '0',
								  Allow_Image_Link_Cloaking tinyint(1) NOT NULL DEFAULT '0',
								  SEO_Link_Prefix varchar(100) NOT NULL DEFAULT 'review',
								  Enable_Debugging tinyint(1) NOT NULL DEFAULT '0',
								  Css_Location varchar(255) NOT NULL DEFAULT 'default',
								  Create_Brand_Category tinyint(1) NOT NULL DEFAULT '0',
  								  Create_Tags tinyint(1) NOT NULL DEFAULT '1',
  								  Tag_Filter text,
								  PRIMARY KEY (ID)
								) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;";
			
				$createPostSettingsTableSQL = "CREATE TABLE {$post_setting_table_name} (
											  ID int(11) NOT NULL AUTO_INCREMENT,
											  Post_ID int(11) NOT NULL,
											  ASIN varchar(100) NOT NULL,
											  Customer_Review_Count int(11) DEFAULT NULL,
											  Product_Review_Rating decimal(10,0) DEFAULT NULL,
											  Ebay_Search_Query varchar(500) DEFAULT NULL,
											  Overstock_Search_Query varchar(500) NOT NULL,
											  Review_Excerpt text,
											  Review_Description text,
											  Review_Title varchar(255) DEFAULT NULL,
											  Ebay_Widget_Title varchar(255) DEFAULT NULL,
											  Product_Image_Small varchar(255) DEFAULT NULL,
											  Product_Image_Medium varchar(255) DEFAULT NULL,
											  Product_Image_Large varchar(255) DEFAULT NULL,
											  Customer_Reviews text,
											  Similar_Products text,
											  Product_Accessories text,
											  Page_Cache text,
											  Last_Cache_Date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
											  PRIMARY KEY (ID),
											  UNIQUE KEY Post_ID (Post_ID)
											) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;";
				
				$createReportingTableSQL = "CREATE TABLE {$reports_table_Name} (
										  Click_ID int(11) NOT NULL AUTO_INCREMENT,
										  ASIN varchar(200) NOT NULL,
										  Tracking_ID varchar(500) NOT NULL,
										  Title varchar(255) NOT NULL,
										  Country varchar(100) NOT NULL,
										  IP_Address varchar(100) NOT NULL,
										  Click_Date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
										  PRIMARY KEY (Click_ID)
										) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
				
				$createTrackingIdTableSQL = "CREATE TABLE {$tracking_table_Name} (
											  ID int(11) NOT NULL AUTO_INCREMENT,
											  Affiliate_Country varchar(100) NOT NULL,
											  Tracking_ID varchar(500) NOT NULL,
											  Display_Order int(11) NOT NULL,
											  PRIMARY KEY (ID)
											) ENGINE=MyISAM  DEFAULT CHARSET=utf8;";
			
			if($wpdb->get_var("SHOW TABLES LIKE '$settings_table_name'") != $settings_table_name) 
			{
		
		       dbDelta($createSettingsTableSQL);
		       $query = "INSERT INTO {$settings_table_name} (ID, Free_Shipping_Text, Empty_Product_Accessories_Text, Empty_Related_Products_Text, Empty_Ebay_Auctions_Text, Empty_PhpOStock_Text, Empty_Video_Reviews_Text, Empty_Product_Details_Text, Empty_Customer_Reviews_Text, Truncate_Excerpt_Text,Show_Excerpts_Pages) VALUES
						(1, '<img class=\"RZIcon\" src=\"/wp-content/plugins/ReviewAZON/images/package.gif\" /><span class=\"RZFreeShipping\">Free Shipping Available</span>', '<img class=\"RZIcon\" src=\"/wp-content/plugins/ReviewAZON/images/information.gif\" /><span>No product accessories were found for this product.</span>', '<img class=\"RZIcon\" src=\"/wp-content/plugins/ReviewAZON/images/information.gif\" /><span>No related products were found for this product.</span>', 'No auction listings were available.', 'No Overstock.com listings were available.', '<img class=\"RZIcon\" src=\"/wp-content/plugins/ReviewAZON/images/television.gif\" /><span>No video reviews found for this product.</span>', '<img class=\"RZIcon\" src=\"/wp-content/plugins/ReviewAZON/images/page.gif\" /><span>No details are available for this product</span>', '<img class=\"RZIcon\" src=\"/wp-content/plugins/ReviewAZON/images/user_comments.gif\" /><span>No customer reviews were found for this product.</span>', '<a rel=\"nofollow\" href=\"%%PERMALINK%%\">[Read More]</a>','a:6:{i:0;s:4:\"home\";i:1;s:9:\"frontpage\";i:2;s:8:\"category\";i:3;s:6:\"search\";i:4;s:3:\"tag\";i:5;s:6:\"sticky\";}')";
		       
			   $wpdb->query($query);
				
			}
			else
			{
				if($installed_ver != REVIEWAZONDBVER)
				{
					dbDelta($createSettingsTableSQL);
					$query = "UPDATE {$settings_table_name} SET Empty_PhpOStock_Text = 'No Overstock.com listings were available.', Truncate_Excerpt_Text = '... <a rel=\"nofollow\" href=\"%%PERMALINK%%\">[Read More]</a>', Show_Excerpts_Pages = 'a:6:{i:0;s:4:\"home\";i:1;s:9:\"frontpage\";i:2;s:8:\"category\";i:3;s:6:\"search\";i:4;s:3:\"tag\";i:5;s:6:\"sticky\";}'";
					$wpdb->query($query);
				}
			}
			
			if($wpdb->get_var("SHOW TABLES LIKE '$post_setting_table_name'") != $post_setting_table_name) 
			{
				dbDelta($createPostSettingsTableSQL);			
			}
			else
			{
				if($installed_ver != REVIEWAZONDBVER)
				{
					dbDelta($createPostSettingsTableSQL);
				}	
			}
			
			if($wpdb->get_var("SHOW TABLES LIKE '$tabs_table_name'") != $tabs_table_name) 
			{
				$tabTableSQL = "CREATE TABLE IF NOT EXISTS {$tabs_table_name} (
		  						Tab_ID int(11) NOT NULL AUTO_INCREMENT,
								Tab_Title varchar(100) NOT NULL,
								Tab_Body text NOT NULL,
								Show_Tab tinyint(1) NOT NULL,
								Display_Order int(11) NOT NULL,
								PRIMARY KEY (Tab_ID)
								) ENGINE=MyISAM  DEFAULT CHARSET=utf8		
							    ";
				      
		       dbDelta($tabTableSQL);	
		       
		       $query = "INSERT INTO {$tabs_table_name} (Tab_ID, Tab_Title, Tab_Body, Show_Tab, Display_Order) VALUES
						(1, 'Description', '%%DESCRIPTION%%', 1, 1),
						(2, 'Additional Details', '%%PRODUCTDETAILS%%', 1, 2),
						(3, 'Customer Reviews', '%%CUSTOMERREVIEWS%%', 1, 3)";
		       $wpdb->query($query);      
				
			}
			if($wpdb->get_var("SHOW TABLES LIKE '$templates_table_name'") != $templates_table_name) 
			{
				$templateTableSQL = "CREATE TABLE IF NOT EXISTS {$templates_table_name} (
		  							Template_ID int(11) NOT NULL AUTO_INCREMENT,
									Template_Group varchar(255) CHARACTER SET utf8 NOT NULL,
									Template_Name varchar(255) CHARACTER SET utf8 NOT NULL,
									Template_Html text CHARACTER SET utf8 NOT NULL,
									PRIMARY KEY (Template_ID)
									) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC	
							    ";
				      
		       dbDelta($templateTableSQL);
		           
		       $query = "INSERT INTO {$templates_table_name} (Template_Group,Template_Name) 
		       	         VALUES ('Custom','Main'),
		       				    ('Custom','Excerpt'),
		                        ('Custom','Excerpt_Flow'),
		       				    ('Custom','Customer_Review'),
		       				    ('Custom','Description'),
		       				    ('Custom','Similar_Products'),
		       				    ('Custom','Product_Accessories'),
		       				    ('Custom','Video_Reviews'),
		       				    ('Custom','Inline_Post'),
		       				    ('Custom','Multi_Inline_Post')";
		 
		       $wpdb->query($query);
		       
		       ReviewAZON_load_default_templates();
		       
		              
			}
			if($wpdb->get_var("SHOW TABLES LIKE '$video_reviews_table_name'") != $video_reviews_table_name) 
			{
				$videoReviewTableSQL = "CREATE TABLE IF NOT EXISTS {$video_reviews_table_name} (
									   Video_ID int(11) NOT NULL AUTO_INCREMENT,
									   Post_ID int(11) NOT NULL,
									   Video_Title varchar(255) CHARACTER SET utf8 NOT NULL,
									   Video_Image_Url varchar(255) CHARACTER SET utf8 NOT NULL,
									   Video_Watch_Url varchar(255) CHARACTER SET utf8 NOT NULL,
									   PRIMARY KEY (Video_ID)
									   ) ENGINE=MyISAM  DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC			
									  ";
				      
		       dbDelta($videoReviewTableSQL);		
				
			}
			
			if($wpdb->get_var("SHOW TABLES LIKE '$bulk_reviews_table_Name'") != $bulk_reviews_table_Name) 
			{
				$bulkReviewTableSQL = "CREATE TABLE {$bulk_reviews_table_Name} (
									  ID int(11) NOT NULL AUTO_INCREMENT,
									  ASIN varchar(100) NOT NULL,
									  Product_Image varchar(200) DEFAULT NULL,
									  Title varchar(255) DEFAULT NULL,
									  Display_Order int(11) NOT NULL,
									  PRIMARY KEY (ID)
									  ) ENGINE=MyISAM DEFAULT CHARSET=utf8		
									  ";
				      
		       dbDelta($bulkReviewTableSQL);	
				
			}
			
			if($wpdb->get_var("SHOW TABLES LIKE '$reports_table_Name'") != $reports_table_Name) 
			{
		      	dbDelta($createReportingTableSQL);
			}
			
			if($wpdb->get_var("SHOW TABLES LIKE '$tracking_table_Name'") != $tracking_table_Name) 
			{
		      	dbDelta($createTrackingIdTableSQL);
			}
				
			if($installed_ver != REVIEWAZONDBVER)
			{
				//Add existing tracking id
				$addTrackingQuery = "SELECT AWS_Associate_ID, AWS_Affiliate_Country FROM {$settings_table_name}";
				$trackingIDToAdd = $wpdb->get_row($addTrackingQuery);
				
				$trackingIDCheckQuery = "SELECT Tracking_ID FROM {$tracking_table_Name} WHERE Tracking_ID = '{$trackingIDToAdd->AWS_Associate_ID}'";
				$trackidtest = $wpdb->get_row($trackingIDCheckQuery);
				
				if(empty($trackidtest))
				{				
					$insertTrackingQuery = "INSERT INTO {$tracking_table_Name} (ID,Affiliate_Country,Tracking_ID,Display_Order) VALUES (id,'{$trackingIDToAdd->AWS_Affiliate_Country}','{$trackingIDToAdd->AWS_Associate_ID}',0)";
					$wpdb->query($insertTrackingQuery); 
					
					global $post;
				 	$myposts = get_posts('numberposts=-1&post_status=blank');
				 	$asscID = $wpdb->get_var("SELECT AWS_Associate_ID FROM {$settings_table_name}");
				 	foreach($myposts as $post)
				 	{
				 		$currentTrackingID = get_post_meta($post->ID, 'ReviewAZON_Tracking_ID',true);
				 		if(empty($currentTrackingID))
				 		{				 		
					 		update_post_meta($post->ID, 'ReviewAZON_Tracking_ID', $asscID);
					 		$overstockSearchQuery = '[phpostock]'.$post->post_title.', 10[/phpostock]';
					 		$updateOverstockQuery = "UPDATE {$post_setting_table_name} SET Overstock_Search_Query = '{$overstockSearchQuery}' WHERE Post_ID = '{$post->ID}'";
					 		$wpdb->query($updateOverstockQuery); 
				 		}
				 	}
				}
			}
			
			update_option( "ReviewAZON_db_version", REVIEWAZONDBVER );
	}
	else
	{
			$settings_table_name = $wpdb->prefix.'reviewazon_settings';
			$post_setting_table_name = $wpdb->prefix.'reviewazon_post_settings';
			$tabs_table_name = $wpdb->prefix.'reviewazon_tabs';
			$templates_table_name = $wpdb->prefix.'reviewazon_templates';
			$video_reviews_table_name = $wpdb->prefix.'reviewazon_video_reviews';
			$bulk_reviews_table_Name = $wpdb->prefix.'reviewazon_bulk_reviews';
			$reports_table_Name = $wpdb->prefix.'reviewazon_reports';
			$tracking_table_Name = $wpdb->prefix.'reviewazon_tracking_id';
			$installed_ver = get_option("ReviewAZON_db_version");	
			
			//Rename ReviewAZON tables with Wordpress Prefix for multiple table use
			if($wpdb->get_var("SHOW TABLES LIKE 'reviewazon_settings'") == 'reviewazon_settings')
			{
				$settingsNameChangeQuery = "RENAME TABLE reviewazon_settings TO {$settings_table_name}";
				$wpdb->query($settingsNameChangeQuery);
			}
			if($wpdb->get_var("SHOW TABLES LIKE 'reviewazon_post_settings'") == 'reviewazon_post_settings')
			{
				$settingsNameChangeQuery = "RENAME TABLE reviewazon_post_settings TO {$post_setting_table_name}";
				$wpdb->query($settingsNameChangeQuery);
			}
			if($wpdb->get_var("SHOW TABLES LIKE 'reviewazon_tabs'") == 'reviewazon_tabs')
			{
				$settingsNameChangeQuery = "RENAME TABLE reviewazon_tabs TO {$tabs_table_name}";
				$wpdb->query($settingsNameChangeQuery);
			}
			if($wpdb->get_var("SHOW TABLES LIKE 'reviewazon_templates'") == 'reviewazon_templates')
			{
				$settingsNameChangeQuery = "RENAME TABLE reviewazon_templates TO {$templates_table_name}";
				$wpdb->query($settingsNameChangeQuery);
			}
			if($wpdb->get_var("SHOW TABLES LIKE 'reviewazon_video_reviews'") == 'reviewazon_video_reviews')
			{
				$settingsNameChangeQuery = "RENAME TABLE reviewazon_video_reviews TO {$video_reviews_table_name}";
				$wpdb->query($settingsNameChangeQuery);
			}
			if($wpdb->get_var("SHOW TABLES LIKE 'reviewazon_bulk_reviews'") == 'reviewazon_bulk_reviews')
			{
				$settingsNameChangeQuery = "RENAME TABLE reviewazon_bulk_reviews TO {$bulk_reviews_table_Name}";
				$wpdb->query($settingsNameChangeQuery);
			}
			if($wpdb->get_var("SHOW TABLES LIKE 'reviewazon_reports'") == 'reviewazon_reports')
			{
				$settingsNameChangeQuery = "RENAME TABLE reviewazon_reports TO {$reports_table_Name}";
				$wpdb->query($settingsNameChangeQuery);
			}			
			if($wpdb->get_var("SHOW TABLES LIKE 'reviewazon_tracking_id'") == 'reviewazon_tracking_id')
			{
				$settingsNameChangeQuery = "RENAME TABLE reviewazon_tracking_id TO {$tracking_table_Name}";
				$wpdb->query($settingsNameChangeQuery);
			}
			
	
			$createSettingsTableSQL = "CREATE TABLE {$settings_table_name} (
		  						  ID int(11) NOT NULL AUTO_INCREMENT,
		  						  AWS_Webservice_Key varchar(255) NOT NULL,
		  						  AWS_Secret_Key varchar(255) NOT NULL,
		  						  AWS_Associate_ID varchar(255) NOT NULL,
		  						  AWS_URL varchar(255) NOT NULL,
		  						  Default_Template_Group varchar(255) NOT NULL DEFAULT 'Default',
		  						  Post_Content_Type varchar(255) NOT NULL,
		  						  Excerpt_Layout_Mode varchar(255) NOT NULL DEFAULT 'Normal',
								  Show_Excerpts_Pages varchar(255) NOT NULL,
								  Show_Ebay_Auctions tinyint(1) NOT NULL DEFAULT '0',
								  Show_Overstock_Items tinyint(1) NOT NULL DEFAULT '0',
								  No_Reviews_To_Display tinyint(1) NOT NULL DEFAULT '5',
								  Min_Review_Score_To_Display varchar(100) NOT NULL DEFAULT '-OverallRating',
								  AWS_Affiliate_Country varchar(50) NOT NULL,
								  Cache_Description tinyint(1) NOT NULL DEFAULT '0',
								  Cache_Customer_Reviews tinyint(1) NOT NULL DEFAULT '1',
								  Cache_Similar_Products tinyint(1) NOT NULL DEFAULT '1',
								  Cache_Product_Accessories tinyint(1) NOT NULL DEFAULT '1',
								  Page_Cache_Length int(11) NOT NULL DEFAULT '0',
								  Save_Last_Search_Results tinyint(1) NOT NULL DEFAULT '0',
								  Invalidate_Page_Cache tinyint(1) NOT NULL DEFAULT '1',
								  Free_Shipping_Text text,
								  Empty_Product_Accessories_Text text,
								  Allow_Expanded_Tokens tinyint(1) NOT NULL DEFAULT '1',
								  Empty_Related_Products_Text text,
								  Empty_Ebay_Auctions_Text text,
								  Empty_PhpOStock_Text text NOT NULL,
								  Empty_Video_Reviews_Text text,
								  Empty_Product_Details_Text text,
								  Empty_Customer_Reviews_Text text,
								  Truncate_Excerpt int(11) NOT NULL DEFAULT '350',
								  Truncate_Excerpt_Text text NOT NULL,
								  Allow_Link_Cloaking tinyint(1) NOT NULL DEFAULT '0',
								  Allow_Image_Link_Cloaking tinyint(1) NOT NULL DEFAULT '0',
								  SEO_Link_Prefix varchar(100) NOT NULL DEFAULT 'review',
								  Enable_Debugging tinyint(1) NOT NULL DEFAULT '0',
								  Css_Location varchar(255) NOT NULL DEFAULT 'default',
								  Create_Brand_Category tinyint(1) NOT NULL DEFAULT '0',
  								  Create_Tags tinyint(1) NOT NULL DEFAULT '1',
  								  Tag_Filter text,
								  PRIMARY KEY (ID)
								) ENGINE=MyISAM ROW_FORMAT=DYNAMIC;";
			
				$createPostSettingsTableSQL = "CREATE TABLE {$post_setting_table_name} (
											  ID int(11) NOT NULL AUTO_INCREMENT,
											  Post_ID int(11) NOT NULL,
											  ASIN varchar(100) NOT NULL,
											  Customer_Review_Count int(11) DEFAULT NULL,
											  Product_Review_Rating decimal(10,0) DEFAULT NULL,
											  Ebay_Search_Query varchar(255) DEFAULT NULL,
											  Overstock_Search_Query varchar(255) NOT NULL,
											  Review_Excerpt text,
											  Review_Description text,
											  Review_Title varchar(255) DEFAULT NULL,
											  Ebay_Widget_Title varchar(255) DEFAULT NULL,
											  Product_Image_Small varchar(255) DEFAULT NULL,
											  Product_Image_Medium varchar(255) DEFAULT NULL,
											  Product_Image_Large varchar(255) DEFAULT NULL,
											  Customer_Reviews text,
											  Similar_Products text,
											  Product_Accessories text,
											  Page_Cache text,
											  Last_Cache_Date timestamp NOT NULL,
											  PRIMARY KEY (ID),
											  UNIQUE KEY Post_ID (Post_ID)
											) ENGINE=MyISAM ROW_FORMAT=DYNAMIC;";
				
				$createReportingTableSQL = "CREATE TABLE {$reports_table_Name} (
										  Click_ID int(11) NOT NULL AUTO_INCREMENT,
										  ASIN varchar(200) NOT NULL,
										  Tracking_ID varchar(255) NOT NULL,
										  Title varchar(255) NOT NULL,
										  Country varchar(100) NOT NULL,
										  IP_Address varchar(100) NOT NULL,
										  Click_Date timestamp NOT NULL,
										  PRIMARY KEY (Click_ID)
										) ENGINE=MyISAM;";
				
				$createTrackingIdTableSQL = "CREATE TABLE {$tracking_table_Name} (
											  ID int(11) NOT NULL AUTO_INCREMENT,
											  Affiliate_Country varchar(100) NOT NULL,
											  Tracking_ID varchar(255) NOT NULL,
											  Display_Order int(11) NOT NULL,
											  PRIMARY KEY (ID)
											) ENGINE=MyISAM;";
			
			if($wpdb->get_var("SHOW TABLES LIKE '$settings_table_name'") != $settings_table_name) 
			{
		
		       dbDelta($createSettingsTableSQL);
		       $query = "INSERT INTO {$settings_table_name} (ID, Free_Shipping_Text, Empty_Product_Accessories_Text, Empty_Related_Products_Text, Empty_Ebay_Auctions_Text, Empty_PhpOStock_Text, Empty_Video_Reviews_Text, Empty_Product_Details_Text, Empty_Customer_Reviews_Text, Truncate_Excerpt_Text,Show_Excerpts_Pages) VALUES
						(1, '<img class=\"RZIcon\" src=\"/wp-content/plugins/ReviewAZON/images/package.gif\" /><span class=\"RZFreeShipping\">Free Shipping Available</span>', '<img class=\"RZIcon\" src=\"/wp-content/plugins/ReviewAZON/images/information.gif\" /><span>No product accessories were found for this product.</span>', '<img class=\"RZIcon\" src=\"/wp-content/plugins/ReviewAZON/images/information.gif\" /><span>No related products were found for this product.</span>', 'No auction listings were available.', 'No Overstock.com listings were available.', '<img class=\"RZIcon\" src=\"/wp-content/plugins/ReviewAZON/images/television.gif\" /><span>No video reviews found for this product.</span>', '<img class=\"RZIcon\" src=\"/wp-content/plugins/ReviewAZON/images/page.gif\" /><span>No details are available for this product</span>', '<img class=\"RZIcon\" src=\"/wp-content/plugins/ReviewAZON/images/user_comments.gif\" /><span>No customer reviews were found for this product.</span>', '<a rel=\"nofollow\" href=\"%%PERMALINK%%\">[Read More]</a>','a:6:{i:0;s:4:\"home\";i:1;s:9:\"frontpage\";i:2;s:8:\"category\";i:3;s:6:\"search\";i:4;s:3:\"tag\";i:5;s:6:\"sticky\";}')";
		       
			   $wpdb->query($query);
				
			}
			else
			{
				if($installed_ver != REVIEWAZONDBVER)
				{
					dbDelta($createSettingsTableSQL);
					$query = "UPDATE {$settings_table_name} SET Empty_PhpOStock_Text = 'No Overstock.com listings were available.', Truncate_Excerpt_Text = '... <a rel=\"nofollow\" href=\"%%PERMALINK%%\">[Read More]</a>', Show_Excerpts_Pages = 'a:6:{i:0;s:4:\"home\";i:1;s:9:\"frontpage\";i:2;s:8:\"category\";i:3;s:6:\"search\";i:4;s:3:\"tag\";i:5;s:6:\"sticky\";}'";
					$wpdb->query($query);
				}
			}
			
			if($wpdb->get_var("SHOW TABLES LIKE '$post_setting_table_name'") != $post_setting_table_name) 
			{
				dbDelta($createPostSettingsTableSQL);			
			}
			else
			{
				if($installed_ver != REVIEWAZONDBVER)
				{
					dbDelta($createPostSettingsTableSQL);
				}	
			}
			
			if($wpdb->get_var("SHOW TABLES LIKE '$tabs_table_name'") != $tabs_table_name) 
			{
				$tabTableSQL = "CREATE TABLE IF NOT EXISTS {$tabs_table_name} (
		  						Tab_ID int(11) NOT NULL AUTO_INCREMENT,
								Tab_Title varchar(100) NOT NULL,
								Tab_Body text NOT NULL,
								Show_Tab tinyint(1) NOT NULL,
								Display_Order int(11) NOT NULL,
								PRIMARY KEY (Tab_ID)
								) ENGINE=MyISAM";
				      
		       dbDelta($tabTableSQL);	
		       
		       $query = "INSERT INTO {$tabs_table_name} (Tab_ID, Tab_Title, Tab_Body, Show_Tab, Display_Order) VALUES
						(1, 'Description', '%%DESCRIPTION%%', 1, 1),
						(2, 'Additional Details', '%%PRODUCTDETAILS%%', 1, 2),
						(3, 'Customer Reviews', '%%CUSTOMERREVIEWS%%', 1, 3)";
		       $wpdb->query($query);      
				
			}
			if($wpdb->get_var("SHOW TABLES LIKE '$templates_table_name'") != $templates_table_name) 
			{
				$templateTableSQL = "CREATE TABLE IF NOT EXISTS {$templates_table_name} (
		  							Template_ID int(11) NOT NULL AUTO_INCREMENT,
									Template_Group varchar(255)NOT NULL,
									Template_Name varchar(255) NOT NULL,
									Template_Html text NOT NULL,
									PRIMARY KEY (Template_ID)
									) ENGINE=MyISAM ROW_FORMAT=DYNAMIC";
				      
		       dbDelta($templateTableSQL);
		           
		       $query = "INSERT INTO {$templates_table_name} (Template_Group,Template_Name) 
		       	         VALUES ('Custom','Main'),
		       				    ('Custom','Excerpt'),
		                        ('Custom','Excerpt_Flow'),
		       				    ('Custom','Customer_Review'),
		       				    ('Custom','Description'),
		       				    ('Custom','Similar_Products'),
		       				    ('Custom','Product_Accessories'),
		       				    ('Custom','Video_Reviews'),
		       				    ('Custom','Inline_Post'),
		       				    ('Custom','Multi_Inline_Post')";
		 
		       $wpdb->query($query);
		       
		       ReviewAZON_load_default_templates();
		       
		              
			}
			if($wpdb->get_var("SHOW TABLES LIKE '$video_reviews_table_name'") != $video_reviews_table_name) 
			{
				$videoReviewTableSQL = "CREATE TABLE IF NOT EXISTS {$video_reviews_table_name} (
									   Video_ID int(11) NOT NULL AUTO_INCREMENT,
									   Post_ID int(11) NOT NULL,
									   Video_Title varchar(255)NOT NULL,
									   Video_Image_Url varchar(255)NOT NULL,
									   Video_Watch_Url varchar(255)NOT NULL,
									   PRIMARY KEY (Video_ID)
									   ) ENGINE=MyISAM ROW_FORMAT=DYNAMIC";
				      
		       dbDelta($videoReviewTableSQL);		
				
			}
			
			if($wpdb->get_var("SHOW TABLES LIKE '$bulk_reviews_table_Name'") != $bulk_reviews_table_Name) 
			{
				$bulkReviewTableSQL = "CREATE TABLE {$bulk_reviews_table_Name} (
									  ID int(11) NOT NULL AUTO_INCREMENT,
									  ASIN varchar(100) NOT NULL,
									  Product_Image varchar(200) DEFAULT NULL,
									  Title varchar(255) DEFAULT NULL,
									  Display_Order int(11) NOT NULL,
									  PRIMARY KEY (ID)
									  ) ENGINE=MyISAM";
				      
		       dbDelta($bulkReviewTableSQL);	
				
			}
			
			if($wpdb->get_var("SHOW TABLES LIKE '$reports_table_Name'") != $reports_table_Name) 
			{
		      	dbDelta($createReportingTableSQL);
			}
			
			if($wpdb->get_var("SHOW TABLES LIKE '$tracking_table_Name'") != $tracking_table_Name) 
			{
		      	dbDelta($createTrackingIdTableSQL);
			}
				
			if($installed_ver != REVIEWAZONDBVER)
			{
				//Add existing tracking id
				$addTrackingQuery = "SELECT AWS_Associate_ID, AWS_Affiliate_Country FROM {$settings_table_name}";
				$trackingIDToAdd = $wpdb->get_row($addTrackingQuery);
				
				$trackingIDCheckQuery = "SELECT Tracking_ID FROM {$tracking_table_Name} WHERE Tracking_ID = '{$trackingIDToAdd->AWS_Associate_ID}'";
				$trackidtest = $wpdb->get_row($trackingIDCheckQuery);
				
				if(empty($trackidtest))
				{
					$insertTrackingQuery = "INSERT INTO {$tracking_table_Name} (ID,Affiliate_Country,Tracking_ID,Display_Order) VALUES (id,'{$trackingIDToAdd->AWS_Affiliate_Country}','{$trackingIDToAdd->AWS_Associate_ID}',0)";
					$wpdb->query($insertTrackingQuery); 
					
					global $post;
				 	$myposts = get_posts('numberposts=-1&post_status=blank');
				 	$asscID = $wpdb->get_var("SELECT AWS_Associate_ID FROM {$settings_table_name}");
				 	foreach($myposts as $post)
				 	{
				 		$currentTrackingID = get_post_meta($post->ID, 'ReviewAZON_Tracking_ID',true);
				 		if(empty($currentTrackingID))
				 		{
					 		update_post_meta($post->ID, 'ReviewAZON_Tracking_ID', $asscID);
					 		$overstockSearchQuery = '[phpostock]'.$post->post_title.', 10[/phpostock]';
					 		$updateOverstockQuery = "UPDATE {$post_setting_table_name} SET Overstock_Search_Query = '{$overstockSearchQuery}' WHERE Post_ID = '{$post->ID}'";
					 		$wpdb->query($updateOverstockQuery); 
				 		}
				 	}
				}
			}
			
			update_option( "ReviewAZON_db_version", REVIEWAZONDBVER );	
	}
	
}
#===========================================================================================================================

#Delete post data hook
#Description: Called when a post is deleted, this function will delete the data associated with that post.
#============================================================================================================================
function ReviewAZON_delete_postdata($post_id)
{
	global $wpdb;
	$queryDeleteNegativePostIDs = "DELETE FROM ".$wpdb->prefix."reviewazon_post_settings WHERE Post_ID LIKE '-%'";
	$queryDeleteNegativeVidoePostIDs = "DELETE FROM ".$wpdb->prefix."reviewazon_video_reviews WHERE Post_ID LIKE '-%'";
	$query = "DELETE FROM ".$wpdb->prefix."reviewazon_post_settings WHERE Post_ID = '{$post_id}'";
	$wpdb->query($query);
	$wpdb->query($queryDeleteNegativePostIDs);
	$wpdb->query($queryDeleteNegativeVidoePostIDs);
	
	//Delete Video Reviews
	$queryDeleteNegativePostIDVideoReviews = "DELETE FROM ".$wpdb->prefix."reviewazon_video_reviews WHERE Post_ID LIKE '-%'";
	$queryDeletePostIDVideoReviews = "DELETE FROM ".$wpdb->prefix."reviewazon_video_reviews WHERE Post_ID = '{$post_id}'";
	$wpdb->query($queryDeleteNegativePostIDVideoReviews);
	$wpdb->query($queryDeletePostIDVideoReviews);
}
#============================================================================================================================


#Save post data hook
#Description: Saves additional data about the product review when the post is saved or updated.
#============================================================================================================================
function ReviewAZON_save_postdata($post_id)
{
	global $wpdb;
	global $flag;

	//Do not process anything unless we have a valid amazon post.
	if(isset($_POST['hdnASIN']))
	{
		if(isset($_POST['tmpID']) && isset($_POST['tmpPostID1']))	
		{	
			$postid = $post_id;
			if(strpos($_POST['tmpPostID1'],'-') === false)
			{
				$postid = $_POST['tmpPostID1'];
			}
	
			$queryVideoReviews = "UPDATE ".$wpdb->prefix."reviewazon_video_reviews SET post_id = '".$postid."' WHERE post_id = '".$_POST['tmpID']."'";
			$wpdb->query($queryVideoReviews);
			
			if(isset($_POST['ReviewAZON_ebay_query']))
			{
				$ebayQuery = addslashes($_POST['ReviewAZON_ebay_query']);
			}
			if(isset($_POST['ReviewAZON_review_description']))
			{
				$description = addslashes($_POST['ReviewAZON_review_description']);
			}
			if(isset($_POST['ReviewAZON_ebay_widget_title']))
			{
				$ebayWidgetTitle = addslashes($_POST['ReviewAZON_ebay_widget_title']);
			}
			if(isset($_POST['ReviewAZON_review_excerpt']))
			{
				$reviewExcerpt = addslashes($_POST['ReviewAZON_review_excerpt']);
			}
			if(isset($_POST['ReviewAZON_overstock_query']))
			{
				$overstockQuery = addslashes($_POST['ReviewAZON_overstock_query']);
			}

			$trackingID = $_POST['ReviewAZON_associate_id'];
			
						
			$queryPostSettings = "UPDATE ".$wpdb->prefix."reviewazon_post_settings SET 
								  post_id = '{$postid}',
								  Ebay_Search_Query = '{$ebayQuery}',
								  Overstock_Search_Query = '{$overstockQuery}',
								  Review_Excerpt = '{$reviewExcerpt}',
								  Review_Description = '{$description}',
								  Review_Title = '{$_POST['ReviewAZON_review_title']}',
								  Ebay_Widget_Title = '{$ebayWidgetTitle}',
								  Page_Cache = null
								  WHERE post_id = '{$_POST['tmpID']}'";
			$wpdb->query($queryPostSettings);	
			update_post_meta($postid,"ReviewAZON_Tracking_ID",$trackingID);
		}
	}
}
#============================================================================================================================

#Helper Functions
#============================================================================================================================
function ReviewAZON_is_from_admin($referer)
{
	if(strpos($referer,'wp-admin') > 0)
	{
		return true;
	}
	return false;
}
	
function aws_signed_request($params)
 {

     $method = "GET";
     $host = "ecs.amazonaws.".AFFILIATECOUNTRY;
     $uri = "/onca/xml";
      
     $params["Service"] = "AWSECommerceService";
     $params["AWSAccessKeyId"] = KEYID;
     $params["Timestamp"] = gmdate("Y-m-d\TH:i:s\Z"); 
     $params["Version"] = AWSVERSION;
     ksort($params);  

     $aws_query = array();
     foreach ($params as $param=>$value)
     {
         $param = str_replace("%7E", "~", rawurlencode($param));
         $value = str_replace("%7E", "~", rawurlencode($value));
         $aws_query[] = $param."=".$value;
     }
     $aws_query = implode("&", $aws_query); 
     $signedString = $method."\n".$host."\n".$uri."\n".$aws_query; 
     $signature = base64_encode(hash_hmac("sha256", $signedString, SECRETKEY, True)); 
     $signature = str_replace("%7E", "~", rawurlencode($signature));  
     $request = "http://".$host.$uri."?".$aws_query."&Signature=".$signature;
  
     return $request;
 }


function ReviewAZON_create_tags($title)
{
	$charsToRemove = array("'","/","\",", ":", "(", ")", "]", "[", "?", "!", ";", "-");
	$extraTagFilter = explode(',',ReviewAZON_get_affiliate_setting("Tag_Filter"));
	
	$sanitizedTitle = str_replace($charsToRemove, "", strtolower($title));		
	$sanitizedTitle = str_replace($extraTagFilter, "", $sanitizedTitle);
		
    $tempTags = explode(' ', $sanitizedTitle);
    $tags = array();		
    for($i = 0, $len = count($tempTags); $i < $len; ++$i)
    {		
		$tagLenth = strlen($tempTags[$i]);
		if ($tagLenth > 3) 
		{
			$tags[] = $tempTags[$i];
		}
	}		
	$comma_separated = implode(",", $tags);
	return $comma_separated;	
}


function ReviewAZON_create_redirect_file()
{
	
	$filename = ABSPATH.'reviewazon.php';
	$Content = '<?php
				require_once("wp-load.php");
				global $wpdb;
				if(isset($_GET["asin"]) && isset($_GET["link"])&& isset($_GET["trackingid"]))
				{
					$awsCountryCode = ReviewAZON_get_affiliate_setting("AWS_Affiliate_Country");
					$keyid = KEYID;
					$assoctag = $_GET["trackingid"];
					$title = str_replace("-"," ",$_GET["title"]);
					$time = current_time(\'mysql\');
					$query = "INSERT INTO ".$wpdb->prefix."reviewazon_reports (ASIN, Tracking_ID, Title, Country, IP_Address, Click_Date) VALUES (\'{$wpdb->escape($_GET["asin"])}\', \'{$wpdb->escape($_GET["trackingid"])}\', \'{$wpdb->escape($title)}\', \'\',\'{$wpdb->escape($_SERVER["REMOTE_ADDR"])}\',\'{$time}\')";
					
					$wpdb->query($query);						
					
					switch($_GET["link"])
					{
						case "techdetails":
							$link = "http://www.amazon.{$awsCountryCode}/dp/tech-data/{$_GET["asin"]}%3FSubscriptionId%3D{$keyid}%26tag%3D{$assoctag}%26linkCode%3Dxm2%26camp%3D2025%26creative%3D386001%26creativeASIN%3D{$_GET["asin"]}";
							break;
						case "baby":
							$link = "http://www.amazon.{$awsCountryCode}/gp/registry/baby/add-item.html%3Fasin.0%3D{$_GET["asin"]}%26SubscriptionId%3D{$keyid}%26tag%3D{$assoctag}%26linkCode%3Dxm2%26camp%3D2025%26creative%3D386001%26creativeASIN%3D{$_GET["asin"]}";
							break;
						case "wedding":
							$link = "http://www.amazon.{$awsCountryCode}/gp/registry/wedding/add-item.html%3Fasin.0%3D{$_GET["asin"]}%26SubscriptionId%3D{$keyid}%26tag%3D{$assoctag}%26linkCode%3Dxm2%26camp%3D2025%26creative%3D386001%26creativeASIN%3D{$_GET["asin"]}";
							break;
						case "wishlist":
							$link = "http://www.amazon.{$awsCountryCode}/gp/registry/wishlist/add-item.html%3Fasin.0%3D{$_GET["asin"]}%26SubscriptionId%3D{$keyid}%26tag%3D{$assoctag}%26linkCode%3Dxm2%26camp%3D2025%26creative%3D386001%26creativeASIN%3D{$_GET["asin"]}";
							break;
						case "taf":
							$link = "http://www.amazon.{$awsCountryCode}/gp/pdp/taf/{$_GET["asin"]}%3FSubscriptionId%3D{$keyid}%26tag%3D{$assoctag}%26linkCode%3Dxm2%26camp%3D2025%26creative%3D386001%26creativeASIN%3D{$_GET["asin"]}";
							break;
						case "custreview":
							$link = "http://www.amazon.{$awsCountryCode}/review/product/{$_GET["asin"]}%3FSubscriptionId%3D{$keyid}%26tag%3D{$assoctag}%26linkCode%3Dxm2%26camp%3D2025%26creative%3D386001%26creativeASIN%3D{$_GET["asin"]}";
							break;
						case "offers":
							$link = "http://www.amazon.{$awsCountryCode}/gp/offer-listing/{$_GET["asin"]}%3FSubscriptionId%3D{$keyid}%26tag%3D{$assoctag}%26linkCode%3Dxm2%26camp%3D2025%26creative%3D386001%26creativeASIN%3D{$_GET["asin"]}";
							break;
						case "product":
							$link = "http://www.amazon.{$awsCountryCode}/dp/{$_GET["asin"]}%3FSubscriptionId%3D{$keyid}%26tag%3D{$assoctag}%26linkCode%3Dxm2%26camp%3D2025%26creative%3D165953%26creativeASIN%3D{$_GET["asin"]}";
							break;
					}
					header( "Location: {$link}");
				
				}
				?>';
	if(file_exists($filename))
	{
		if(!unlink($filename))
		{
			return "error";
		}
		else
		{
			if(!$handle = fopen($filename, 'w'))
			{
				return "error";
			}
			else
			{
				fwrite($handle, $Content);
				fclose($handle);
			}	
		}
	}
	else
	{
		if(!file_exists($filename))
		{
			if(!$handle = fopen($filename, 'w'))
			{
				return "error";
			}
			else
			{
				fwrite($handle, $Content);
				fclose($handle);
			}	
		}
	}
	return "success";
}

function ReviewAZON_link_cloaking_message()
{
	$htacessMessage = "";
	if(ReviewAZON_get_affiliate_setting("Allow_Link_Cloaking"))
	{
		$htacessMessage = '<p><hr style="border:1px solid gainsboro;" /></p><br />';
		$htacessMessage.= '<p style="font-weight:bold;"><img src="'.ReviewAZON_ADMIN_IMAGES.'information.gif" style="vertical-align:middle;padding-left:5px;padding-right:5px" />Link cloaking has been successfully enabled. A file called reviewazon.php has been created in the root directory of your Wordpress installation.</P>';
		$htacessMessage.= '<p style="font-weight:bold;">You will need to add the following code to your .htaccess file located in the root directory of this blog.</P>';
		$htacessMessage.= '<p style="font-weight:bold;">Open the .htaccess file, copy the text below and paste it at the top of the .htaccess page. Save the .htaccess file.</P><br />';
		$htacessMessage.= '<p><div style="width:100%;height:200px;border:1px solid gainsboro;padding:10px;overflow:auto;">';
		$htacessMessage.= '# IMPORTANT: Place this code above and before ANY Wordpress htaccess code in the .htaccess file<br>';
		$htacessMessage.= '# If this Wordpress site is installed anywhere other than the root (http://www.mysite.com/folder/)<br>';
		$htacessMessage.= '# you will need to add the folder name to the RewriteBase line. (RewriteBase /folder/)<br>';
		$htacessMessage.= '#BEGIN ReviewAZON<br>'.htmlentities('<IfModule mod_rewrite.c>').'<br>RewriteEngine On<br>RewriteBase /<br>';
		if(ReviewAZON_get_affiliate_setting("Allow_Image_Link_Cloaking"))
		{
			$htacessMessage.= 'RewriteRule ^images/(.*)/(.*)$ http://ecx.images-amazon.com/images/I/$2 [R,L]<br>';
		}
	    $htacessMessage.= 'RewriteRule ^'.ReviewAZON_get_affiliate_setting("SEO_Link_Prefix").'/(.*)/(.*)/(.*)/(.*)  /reviewazon.php?asin=$2&link=$1&trackingid=$3&title=$4 [R,L]';
	    $htacessMessage.= '<br>'.htmlentities('</IfModule>').'<br>#END ReviewAZON<br></div></P>';
	}
	return $htacessMessage;
	
}


function ReviewAZON_reset_advanced_settings()
{
	global $wpdb;
           $query = "UPDATE ".$wpdb->prefix."reviewazon_settings SET 
           Default_Template_Group = 'Default',
           Post_Content_Type = '',
           Excerpt_Layout_Mode = 'Normal',
           Show_Ebay_Auctions = 0,
           No_Reviews_To_Display = 5,
           Min_Review_Score_To_Display = 'default',
           Cache_Description = 0,
           Cache_Customer_Reviews = 1,
           Cache_Similar_Products = 1,
           Cache_Product_Accessories = 1,
           Page_Cache_Length = 0,
           Save_Last_Search_Results = 0,
           Invalidate_Page_Cache = 1,
           Free_Shipping_Text = '<img class=\"RZIcon\" src=\"/wp-content/plugins/ReviewAZON/images/package.gif\" /><span class=\"RZFreeShipping\">Free Shipping Available</span>',
           Empty_Product_Accessories_Text = '<img class=\"RZIcon\" src=\"/wp-content/plugins/ReviewAZON/images/information.gif\" /><span>No product accessories were found for this product.</span>',
           Allow_Expanded_Tokens = 1,
           Empty_Related_Products_Text = '<img class=\"RZIcon\" src=\"/wp-content/plugins/ReviewAZON/images/information.gif\" /><span>No related products were found for this product.</span>',
           Empty_Ebay_Auctions_Text = 'No auction items were found related to this product.',
           Empty_Video_Reviews_Text = '<img class=\"RZIcon\" src=\"/wp-content/plugins/ReviewAZON/images/television.gif\" /><span>No video reviews found for this product.</span>',
           Empty_Product_Details_Text = '<img class=\"RZIcon\" src=\"/wp-content/plugins/ReviewAZON/images/page.gif\" /><span>No details are available for this product</span>',
           Empty_Customer_Reviews_Text = '<img class=\"RZIcon\" src=\"/wp-content/plugins/ReviewAZON/images/user_comment.gif\" /><span>No customer reviews were found for this product.</span>',
           Truncate_Excerpt = 350,
           Allow_Link_Cloaking = 0,
           Allow_Image_Link_Cloaking = 0,
           SEO_Link_Prefix = 'review',
           Enable_Debugging = 0,
           Css_Location = 'default',
           Empty_PhpOStock_Text = 'No Overstock.com listings were available.', 
           Truncate_Excerpt_Text = '<a rel=\"nofollow\" href=\"%%PERMALINK%%\">[Read More]</a>', 
           Show_Excerpts_Pages = 'a:6:{i:0;s:4:\"home\";i:1;s:9:\"frontpage\";i:2;s:8:\"category\";i:3;s:6:\"search\";i:4;s:3:\"tag\";i:5;s:6:\"sticky\";}',
           Create_Brand_Category = 0,
           Show_Overstock_Items = 0,
           Create_Tags = 1,
           Tag_Filter = ''                      
           ";
      
	  $wpdb->query($query);		
}

function ReviewAZON_get_mysql_version()
{
	$conn   = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	return substr(mysql_get_server_info($conn),0,1);
}

function ReviewAZON_get_mysql_full_version()
{
	$conn   = mysql_connect(DB_HOST, DB_USER, DB_PASSWORD);
	return mysql_get_server_info($conn);
}

function ReviewAZON_isAjax() 
{
	return (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && ($_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest'));
}

function ReviewAZON_clear_page_cache()
{
	global $wpdb;
	$query = "UPDATE ".$wpdb->prefix."reviewazon_post_settings SET Page_Cache = null";
	$wpdb->query($query);
}

function ReviewAZON_get_affiliate_setting($setting)
{
	global $wpdb;
	$query = "SELECT {$setting} FROM ".$wpdb->prefix."reviewazon_settings";
	$affiliateSetting = $wpdb->get_row($query);
	return stripslashes($affiliateSetting->$setting);	
}

function ReviewAZON_get_page_excerpt_setting($pageName)
{
	$showExcerptPages = ReviewAZON_get_affiliate_setting("Show_Excerpts_Pages");
	$pagesToShow = unserialize($showExcerptPages);
	if(in_array($pageName,$pagesToShow))
	{
		return true;
	}
	return false;
}

function ReviewAZON_get_post_setting($postId, $setting)
{
	global $wpdb;
	$query = "SELECT $setting FROM ".$wpdb->prefix."reviewazon_post_settings WHERE Post_ID = '{$postId}'";
	$postSetting = $wpdb->get_row($query);
	return stripslashes($postSetting->$setting);	
}

function isReviewAZONPost($postid)
{
	$settingValue = ReviewAZON_get_post_setting($postid,"ASIN");
	if(!empty($settingValue))
	{
		return true;
	}
	return false;
}

function ReviewAZON_load_default_templates()
{
	global $wpdb;
	$templateNames = array('customer_review','description','excerpt','excerpt_flow','main','product_accessories','similar_products','video_reviews','inline_post','multi_inline_post');
	foreach($templateNames as $templateName)
	{
		if(file_exists(WP_PLUGIN_DIR.'/ReviewAZON/templates/Custom/'.$templateName.'.html')) 
		{
			$templateFile = addslashes(file_get_contents(WP_PLUGIN_DIR.'/ReviewAZON/templates/Custom/'.$templateName.'.html', true));
			$query = "UPDATE ".$wpdb->prefix."reviewazon_templates SET Template_Html = '{$templateFile}' WHERE Template_Name = '{$templateName}'";
			$wpdb->query($query);
		}
		else
		{
			echo('Error: could not open this file:'.WP_PLUGIN_DIR.'/ReviewAZON/templates/Custom/'.$templateName.'.html');
		}
	}	
}

function ReviewAZON_get_template($templateGroup,$templateName)
{
	global $wpdb;
	if($templateGroup == "Custom")
	{
		$query = "SELECT * FROM ".$wpdb->prefix."reviewazon_templates WHERE Template_Group = '{$templateGroup}' AND Template_Name = '{$templateName}'";
		$template = $wpdb->get_row($query);
		return stripslashes($template->Template_Html);
	}
	else
	{
		if(file_exists(WP_PLUGIN_DIR.'/ReviewAZON/templates/'.$templateGroup.'/'.strtolower($templateName).'.html')) 
		{
			$templateFile = file_get_contents(WP_PLUGIN_DIR.'/ReviewAZON/templates/'.$templateGroup.'/'.strtolower($templateName).'.html', true);
			return $templateFile;
		}
		else
		{
			die('Error: could not open this file:'.WP_PLUGIN_DIR.'/ReviewAZON/templates/'.$templateGroup.'/'.$templateName.'.html');
		}
	}
}

function ReviewAZON_get_time_diff( $start, $end )
{
    $ts['start'] = strtotime( $start );
    $ts['end'] = strtotime( $end );
    if( $ts['start']!==-1 && $ts['end']!==-1 )
    {
        if( $ts['end'] >= $ts['start'] )
        {
            $diff = $ts['end'] - $ts['start'];
            if( $days=intval((floor($diff/86400))) )
                $diff = $diff % 86400;
            if( $hours=intval((floor($diff/3600))) )
                $diff = $diff % 3600;
            if( $minutes=intval((floor($diff/60))) )
                $diff = $diff % 60;
            $diff    =    intval( $diff );            
            return( array('days'=>$days, 'hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$diff) );
        }
    }
    return( false );
}


function ReviewAZON_customer_rating($rating, $formatted = false, $text)
{
	$tempRating = $rating;

	if(strlen($tempRating) > 3)
	{
		$rating = substr($tempRating,0,3);
	}
	
	$pos = strpos($rating,'.');
	
	$firstPart = substr($rating,0,1);
	$lastPart = substr($rating,2);
	$ratingPercentage = 0;
	switch ($firstPart)
	{
		case 1:
			$ratingPercentage = 25;
			break;
		case 2:
			$ratingPercentage = 45;
			break;
		case 3:
			$ratingPercentage = 65;
			break;
		case 4:
			$ratingPercentage = 85;
			break;	
		case 5:
			$ratingPercentage = 100;
			break;			
	}
	
	if($pos > 0)
	{
		$ratingPercentage += $lastPart;
	}

	return '<div class="outerStar"><div class="innerStar" style="width:'.$ratingPercentage.'%">&nbsp;</div></div>';
}

function ReviewAZON_get_post_cache_date($postid)
{
	global $wpdb;

	$queryCacheTime = "SELECT Last_Cache_Date FROM ".$wpdb->prefix."reviewazon_post_settings WHERE Post_ID = '{$postid}'"; 
	$cacheTime = $wpdb->get_var($queryCacheTime);
	return $cacheTime;
}

function ReviewAZON_truncate_description($description,$postid="")
{
	if(ReviewAZON_get_affiliate_setting("Truncate_Excerpt") != 0)
	{
		$excerpt = ReviewAZON_get_affiliate_setting("Truncate_Excerpt_Text");
		if(!empty($postid))
		{
			$excerpt = str_replace('%%PERMALINK%%',get_permalink($postid),$excerpt);
		}
		
		if(strlen($description) > ReviewAZON_get_affiliate_setting("Truncate_Excerpt"))
		{
			$trimmedDescription = substr($description,0,ReviewAZON_get_affiliate_setting("Truncate_Excerpt"));
			return $trimmedDescription.$excerpt;	
		}
		else
		{
			return $description;
		}
	}
	return $description;
}

function ReviewAZON_get_page_cache($postid)
{
	global $wpdb;
	
	$query = "SELECT Page_Cache FROM ".$wpdb->prefix."reviewazon_post_settings WHERE Post_ID = '{$postid}'"; 
	$pageCache = $wpdb->get_var($query);
	return stripslashes($pageCache);
}

function ReviewAZON_update_page_cache($asin, $customerReviewCount, $productReviewRating, $smallImage, $mediumImage, $largeImage, $customerReviews, $similarProducts, $productAccessories, $text, $postid)
{
	global $wpdb;
	
	$blogtime = current_time('mysql');
	$pageCache = $text;
	$query = "UPDATE ".$wpdb->prefix."reviewazon_post_settings SET Page_Cache = '{$pageCache}', 
												  ASIN = '{$asin}',
												  Customer_Reviews = '{$customerReviews}',
												  Similar_Products = '{$similarProducts}',
												  Product_Accessories = '{$productAccessories}',
											      Last_Cache_Date = '{$blogtime}' 
											      WHERE Post_ID = '{$postid}'";
	$wpdb->query($query);
}

function ReviewAZON_get_Item_Attribute_Name($key)
{
	$pattern = "/(.)([A-Z])/";
	$replacement = "\\1 \\2";
	return ucfirst(preg_replace($pattern, $replacement, $key)); 
}
#============================================================================================================================



#Creates the content for the admin menu pages
#============================================================================================================================
function ReviewAZON_toplevel_page() {	
	$dbversion = get_option("ReviewAZON_db_version");
	if(empty($dbversion))
	{
		$dbversion = "1.0.6";		
	}
?>
<div class="wrap">
<h2><img src="<?php echo ReviewAZON_ADMIN_IMAGES ?>largestar.gif" style="vertical-align:middle;margin:5px;" />ReviewAZON Wordpress Plugin Version <?php echo REVIEWAZONVER ?></h2>   
<br><br>

<table width="700">
<tr>
	<td width="45%" valign="top">
		<img src="<?php echo ReviewAZON_ADMIN_IMAGES ?>ReviewAZON_Box-copy.gif" />	
	</td>
	<td width="5%">&nbsp;</td>
	<td width="50%" valign="top">
		<table cellspacing="0" class="widefat fixed">
		<thead>
		<tr>
		<th class="manage-column" scope="col">ReviewAZON Release Information</th>
		</tr>
		</thead>
		<tbody class="list:cat" id="the-list">
		<tr><td style="border:0px;"><strong>Product Version:</strong> <?php echo REVIEWAZONVER;?></td></tr>
		<tr><td style="border:0px;"><strong>Database Version:</strong> <?php echo $dbversion;?></td></tr>
		<tr><td style="border:0px;"><strong>PHP Version:</strong> <?php echo phpversion();?></td></tr>
		<tr><td style="border:0px;"><strong>MySql Version:</strong> <?php echo ReviewAZON_get_mysql_full_version();?></td></tr>	
		</tbody>
		</table>
		<br>
		<table cellspacing="0" class="widefat fixed">
		<thead>
		<tr>
		<th class="manage-column" scope="col">ReviewAZON Links</th>
		</tr>
		</thead>
		<tbody class="list:cat" id="the-list">
		<tr><td style="border:0px;"><a href="http://reviewazon.com" target="_blank">ReviewAZON Web Site</a></td></tr>
		<tr><td style="border:0px;"><a href="http://reviewazon.com/forum" target="_blank">ReviewAZON Support Forum</a></td></tr>
		<tr><td style="border:0px;"><a href="http://reviewazon.com/forum/viewforum.php?f=9&sid=fa7523f7d31b3bc1377d4ddb25d1a6b7" target="_blank">ReviewAZON User Guide</a></td></tr>
		<tr><td style="border:0px;"><a href="http://reviewazon.com/forum/viewforum.php?f=9&sid=fa7523f7d31b3bc1377d4ddb25d1a6b7" target="_blank">ReviewAZON Latest Downloads</a></td></tr>
		</tbody>
		</table>
	</td>
</tr>
</table>

</div>

<?php
}
#============================================================================================================================

#Hooks for adding custom Post and Page meta boxes
#============================================================================================================================
function ReviewAZON_add_custom_box()
{
	add_meta_box('ReviewAZON_Meta_Box', 'ReviewAZON Control Panel', 'ReviewAZON_control_output', 'post', 'side', 'high' );
	add_meta_box('ReviewAZON_Meta_Box', 'ReviewAZON Control Panel', 'ReviewAZON_control_output', 'page', 'side', 'high' );

	//ReviewAZON Editable Post Properties
	add_meta_box('ReviewAZON_Post_Properties', 'ReviewAZON Post Properties', 'ReviewAZON_post_properties', 'post', 'normal', 'high' );
	add_meta_box('ReviewAZON_Post_Properties', 'ReviewAZON Post Properties', 'ReviewAZON_post_properties', 'page', 'normal', 'high' );
	
	
	//ReviewAZON Video Review Properties
	add_meta_box('ReviewAZON_Video_Reviews', 'ReviewAZON Video Reviews', 'meta_box_output', 'post', 'normal', 'high' );
	add_meta_box('ReviewAZON_Video_Reviews', 'ReviewAZON Video Reviews', 'meta_box_output', 'page', 'normal', 'high' );

}

function ReviewAZON_post_properties()
{
	global $wpdb;
	global $post;	

	$displayDescription = "display:none";
	$displayEbayAuctions = "display:none";
	$displayOverstockListings = "display:none";
	
	$emptyMessage = '<div id="ReviewAZON_post_properties" style="font-size:1.1em;font-weight:bold;font-family:arial;"><img src="'.ReviewAZON_ADMIN_IMAGES.'error.gif" style="padding-right:5px;vertical-align:sub;" />No Amazon products have been selected. </div>';
	if($post->ID > 0)
	{
		if(ReviewAZON_get_affiliate_setting("Cache_Description"))
		{
			$displayDescription = "display:";	
		}


		if(ReviewAZON_get_affiliate_setting("Show_Ebay_Auctions"))
		{
			$displayEbayAuctions = "display:";		
		}
		
		if(ReviewAZON_get_affiliate_setting("Show_Overstock_Items"))
		{
			$displayOverstockListings = "display:";		
		}
			
		$query = "SELECT * FROM ".$wpdb->prefix."reviewazon_post_settings WHERE post_id='".$post->ID."'";
		$postSettings = $wpdb->get_row($query);
		if(!empty($postSettings))
		{
		 $ebayQuery = stripslashes($postSettings->Ebay_Search_Query);
		 $overstockQuery = stripslashes($postSettings->Overstock_Search_Query);
		 echo '<div style="padding-top:10px;padding-bottom:2px;height:0px;"><b>Review Title</b></div><br /><input style="width:100%" type="text" name="ReviewAZON_review_title" id="ReviewAZON_review_title" value="'.stripslashes($postSettings->Review_Title).'" />';
		 echo "<div style=\"{$displayOverstockListings}\"><div style=\"padding-top:10px;padding-bottom:2px;height:0px;\"><b>Overstock.com Search Query</b></div><br /><textarea style=\"width:100%;height:25px\" name=\"ReviewAZON_overstock_query\" id=\"ReviewAZON_overstock_query\">{$overstockQuery}</textarea></div>";
		 echo "<div style=\"{$displayEbayOptions}\"><div style=\"padding-top:10px;padding-bottom:2px;height:0px;\"><b>Ebay Search Query</b></div><br /><textarea style=\"width:100%;height:25px;\" name=\"ReviewAZON_ebay_query\" id=\"ReviewAZON_ebay_query\" >{$ebayQuery}</textarea></div>";
  		 echo '<div style="'.$displayEbayAuctions.'"><div style="padding-top:10px;padding-bottom:2px;height:0px;"><b>Ebay Widget Title</b></div><br /><input style="width:100%" type="text" name="ReviewAZON_ebay_widget_title" id="ReviewAZON_ebay_widget_title" value="'.stripslashes($postSettings->Ebay_Widget_Title).'" /></div>';
		 echo '<div style="padding-top:10px;padding-bottom:2px;height:0px;"><b>Product Excerpt</b></div><br /><textarea id="ReviewAZON_review_excerpt" name="ReviewAZON_review_excerpt" style="width:100%;height:150px;">'.stripslashes($postSettings->Review_Excerpt).'</textarea>';
		 echo '<div style="'.$displayDescription.'"><div style="padding-top:10px;padding-bottom:2px;height:0px;"><b>Product Description</b></div><br /><textarea id="ReviewAZON_review_description" name="ReviewAZON_review_description" style="width:100%;height:150px;">'.stripslashes($postSettings->Review_Description).'</textarea></div>';
		}
		else
		{
			echo $emptyMessage;
		}
	}
	else
	{
		echo $emptyMessage;	
	}
}

function meta_box_output( ) 
{
	global $wpdb;
	global $post;
	
	if($post->ID > 0)
	{
		$query = "SELECT * FROM ".$wpdb->prefix."reviewazon_video_reviews WHERE post_id='".$post->ID."'";
		$videos = $wpdb->get_results($query);
		if(!empty($videos))
		{
		echo '<div id="review_videos"><ul id="video_list">';
			foreach($videos as $video)
			{
				echo '<li id="review_video_'.$video->Video_ID.'" watchURL="'.$video->Video_Watch_Url.'"><img align="top" src="'.$video->Video_Image_Url.'" />&nbsp;&nbsp;<b>'.$video->Video_Title.'</b>&nbsp;&nbsp;<img  onclick="deleteVideoReview(\'review_video_'.$video->Video_ID.'\',\''.$video->Video_ID.'\',\''.$video->Post_ID.'\')" style="cursor:pointer;" align="absmiddle" src="'.ReviewAZON_ADMIN_IMAGES.'delete.gif" border="0" /></li>';
			}
			echo '</ul></div>';
		}
		else
		{
			echo '<div id="review_videos" style="font-size:1em;font-weight:bold;font-family:arial;"><ul id="video_list"><li id="message"><img src="'.ReviewAZON_ADMIN_IMAGES.'error.gif" style="padding-right:5px;vertical-align:sub;" />No review videos are assigned.</li></ul></div>';		
		}
	}
	else
	{
		echo '<div id="review_videos" style="font-size:1em;font-weight:bold;font-family:arial;"><ul id="video_list"><li id="message"><img src="'.ReviewAZON_ADMIN_IMAGES.'error.gif" style="padding-right:5px;vertical-align:sub;" />No review videos are assigned.</li></ul></div>';
	}
}

function ReviewAZON_control_output()
{
	require_once ( path_join( dirname( __FILE__ ), 'control_panel.php') );
}
#============================================================================================================================================================================================================================


#Product Review Display Functions
#============================================================================================================================================================================================================================

#Build the RSS Feed
function ReviewAZON_print_rss_excerpts($asin,$postID,$trackingID)
{
	$permalink = get_permalink($postID);
		
	$title = ReviewAZON_get_post_setting($postID, "Review_Title", true);
	$customerReviewCount = ReviewAZON_get_post_setting($postID, "Customer_Review_Count", true);
	$description = stripslashes(ReviewAZON_get_post_setting($postID, "Review_Excerpt", true));	
	$customerRating = ReviewAZON_customer_rating(ReviewAZON_get_post_setting($postID, "Product_Review_Rating"),false, "");
	$smallImageUrl = ReviewAZON_get_post_setting($postID, "Product_Image_Small", true);
	$mediumImageUrl = ReviewAZON_get_post_setting($postID, "Product_Image_Medium", true);
	$largeImageUrl = ReviewAZON_get_post_setting($postID, "Product_Image_Large", true);
	
	//Rewrite Images	
	if(ReviewAZON_get_affiliate_setting("Allow_Image_Link_Cloaking"))
	{
		$smallImageUrl = ReviewAZON_get_rewrite_image($smallImageUrl,$asin);
		$mediumImageUrl = ReviewAZON_get_rewrite_image($mediumImageUrl,$asin);
		$largeImageUrl = ReviewAZON_get_rewrite_image($largeImageUrl,$asin);
	}

	
	if(empty($customerReviewCount))
	{
		$customerReviewCount = "0";
	}
	
	$text = '<table cellpadding="0" cellspacing="0" border="0">';
	$text .= '<tr><td><img src="'.$smallImageUrl.'" /><td></tr>';
	$text .= '<tr><td>'.strip_tags($description).'<td></tr>';
	$text .= '</table>';
	
	return htmlentities($text);	
}

	function ReviewAZON_Get_Product_Rating_Customer_Count($url)
	{
		$ret = array();
		if($url != "")
		{
			//$html = file_get_html($url);
			$html = new simple_html_dom();
			
			$session = curl_init($url);
			curl_setopt($session, CURLOPT_HEADER, false);
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($session);
			curl_close($session);		
			
			$html->load($response);
			$img = $html->find("span.asinReviewsSummary a img",0);
			$numReviews = $html->find("span.crAvgStars a",1);
			$averageReview = substr($img->title,0,strpos($img->title," ")+1);
			$reviewCount = substr($numReviews->plaintext,0,strpos($numReviews->plaintext," ")+1);
			$ret["AverageRating"] = trim($averageReview);
			$ret["ReviewCount"] = trim($reviewCount);
			
			$html->clear();
			unset($html);
			return $ret;
		}
		return $ret;
	}

function ReviewAZON_print_inline_post($asin,$postid,$trackingID)
{
	$permalink = get_permalink($postID);
	$templateGroup = ReviewAZON_get_affiliate_setting("Default_Template_Group");		
	$text = ReviewAZON_get_template($templateGroup,'inline_post');
	
	$current = ReviewAZON_get_amazon_current_node($asin,null,null,null,$trackingID);
	
	$iframeUrl = (string)$current->CustomerReviews->IFrameURL;
	$reviewData = ReviewAZON_Get_Product_Rating_Customer_Count($iframeUrl);
	if(isset($reviewData["AverageRating"]))
	{
		$averageRatingNumber = $reviewData["AverageRating"];
	}
	if(isset($reviewData["ReviewCount"]))
	{
		$reviewCount = $reviewData["ReviewCount"];
	}	
	
	//$averageRatingNumber = $current->CustomerReviews->AverageRating;	
	$title = $current->ItemAttributes->Title;	
	$productFeatures = GetProductFeatures($current);
		
	//Links
	$detailPageUrl = $current->DetailPageURL;
	$technicalDetailsPageUrl = GetItemLink($current,"Technical Details");
	$addToBabyRegistryPageUrl = GetItemLink($current,"Add To Baby Registry");
	$addToWeddingRegistryPageUrl = GetItemLink($current,"Add To Wedding Registry");
	$addToWishListPageUrl = GetItemLink($current,"Add To Wishlist");
	$tellAFriendPageUrl = GetItemLink($current,"Tell A Friend");
	$allCustomerReviewsPageUrl = GetItemLink($current,"All Customer Reviews");
	
	//Convert links to friendly url if link cloaking is turned on.
	if(ReviewAZON_get_affiliate_setting("Allow_Link_Cloaking"))
	{
		$sanitizedTitle = sanitize_title_with_dashes($title);
		$detailPageUrl = ReviewAZON_cloak_link('detailpage',$asin,$sanitizedTitle,$trackingID);
		$technicalDetailsPageUrl = ReviewAZON_cloak_link('techdetails',$asin,$sanitizedTitle,$trackingID);
		$addToBabyRegistryPageUrl = ReviewAZON_cloak_link('baby',$asin,$sanitizedTitle,$trackingID);
		$addToWeddingRegistryPageUrl = ReviewAZON_cloak_link('wedding',$asin,$sanitizedTitle,$trackingID);
		$addToWishListPageUrl = ReviewAZON_cloak_link('wish',$asin,$sanitizedTitle,$trackingID);
		$tellAFriendPageUrl = ReviewAZON_cloak_link('taf',$asin,$sanitizedTitle,$trackingID);
		$allCustomerReviewsPageUrl = ReviewAZON_cloak_link('custreview',$asin,$sanitizedTitle,$trackingID);		
	}

	$amazonProdDesc = GetProductEditoralAmazonDotCom($current);
	$normalProdDesc = GetProductEditoralStandard($current);
	
	$description = "";
	if(!empty($amazonProdDesc))
	{
		$description = strip_tags($amazonProdDesc);
	}
	else
	{
		if(!empty($normalProdDesc))
		{
			$description = strip_tags($normalProdDesc);
		}
	}
	
	$descriptionTemplate = ReviewAZON_get_template(ReviewAZON_get_affiliate_setting("Default_Template_Group"),"description");
	$description = str_replace('%%DESCRIPTION%%',$description,$descriptionTemplate);

	
	//Images
	$smallImageUrl = $current->SmallImage->URL;
	$mediumImageUrl = $current->MediumImage->URL;
	$largeImageUrl = $current->LargeImage->URL;
	
	if(empty($smallImageUrl))
	{
		$smallImageUrl = get_option('siteurl').'/images/noimage-75.gif';			
	}
				
	if(empty($mediumImageUrl))
	{
		$mediumImageUrl = get_option('siteurl').'/images/noimage.gif';			
	}
	
	if(empty($largeImageUrl))
	{
		$largeImageUrl = get_option('siteurl').'/images/noimage.gif';			
	}
			
	
	//Rewrite Images	
	if(ReviewAZON_get_affiliate_setting("Allow_Image_Link_Cloaking"))
	{
		$smallImageUrl = ReviewAZON_get_rewrite_image($smallImageUrl,$asin);
		$mediumImageUrl = ReviewAZON_get_rewrite_image($mediumImageUrl,$asin);
		$largeImageUrl = ReviewAZON_get_rewrite_image($largeImageUrl,$asin);
	}
	
	$brand = $current->ItemAttributes->Brand;
	$UPC = '00'.$current->ItemAttributes->UPC;
	$listPrice = $current->ItemAttributes->ListPrice->FormattedPrice;
	$lowestNewPrice = $current->OfferSummary->LowestNewPrice->FormattedPrice;
	
	$amountSaved = $current->Offers->Offer->OfferListing->AmountSaved->FormattedPrice;
	$percentageSaved = $current->Offers->Offer->OfferListing->PercentageSaved;	
	
	if(!empty($percentageSaved))
	{
		$percentageSaved = $percentageSaved.'%';
	}
	
	$manufacturer = $current->ItemAttributes->Manufacturer;
	$model = $current->ItemAttributes->Model;
	
	$freeShipping = $current->Offers->Offer->OfferListing->IsEligibleForSuperSaverShipping;
	
	
	$availibility = $current->Offers->Offer->OfferListing->Availability;
	if(empty($availibility))
	{
		$availibility = '<a href="'.$detailPageUrl.'">View Product Availability</a>';
	}
	
	if(empty($listPrice))
	{
		$listPrice = "Varies based on product options";		
	}
	
	if(empty($lowestNewPrice))
	{
		if(isset($current->OfferSummary->LowestNewPrice->FormattedPrice))
		{
			$lowestNewPrice = $current->OfferSummary->LowestNewPrice->FormattedPrice;
		}
		else
		{
			$lowestNewPrice = "View Sale Price";		
		}
	}
	
	if($freeShipping == 1)
	{
		$freeShipping = stripslashes(ReviewAZON_get_affiliate_setting("Free_Shipping_Text"));
	}
	else
	{
		$freeShipping = "";
	}
	
	$customerRating = ReviewAZON_customer_rating($averageRatingNumber, false, "");
	$customerReviewCount = $reviewCount;
	
	if(empty($customerReviewCount))
	{
		$customerReviewCount = 0;
	}
	
	$text = str_replace("%%IMAGEPATH%%",ReviewAZON_IMAGES_FOLDER_NOSLASH, $text);
	$text = str_replace('%%PLUGINURL%%',WP_PLUGIN_URL.'/ReviewAZON',$text);
	$text = str_replace('%%ASIN%%',$asin,$text);
	
	//Links
	$text = str_replace('%%DETAILPAGELINK%%',$detailPageUrl,$text);
	$text = str_replace('%%TECHNICALDETAILPAGELINK%%',$technicalDetailsPageUrl,$text);
	$text = str_replace('%%ADDTOBABYREGISTRYLINK%%',$addToBabyRegistryPageUrl,$text);
	$text = str_replace('%%ADDTOWEDDINGREGISTRYLINK%%',$addToWeddingRegistryPageUrl,$text);
	$text = str_replace('%%ADDTOWISHLISTLINK%%',$addToWishListPageUrl,$text);
	$text = str_replace('%%TELLAFRIENDLINK%%',$tellAFriendPageUrl,$text);
	$text = str_replace('%%ALLCUSTOMERREVIEWSPAGELINK%%',$allCustomerReviewsPageUrl,$text);	
	
	//Images
	$text = str_replace('%%AMAZONSMALLIMAGE%%',$smallImageUrl,$text);
	$text = str_replace('%%AMAZONMEDIUMIMAGE%%',$mediumImageUrl,$text);
	$text = str_replace('%%AMAZONLARGEIMAGE%%',$largeImageUrl,$text);	
	
	//Product Attributes
	$text = str_replace('%%DESCRIPTION%%',$description,$text);
	$text = str_replace('%%PRODUCTDETAILS%%',$productFeatures,$text);
	$text = str_replace('%%UPCCODE%%',$UPC,$text);
	$text = str_replace('%%MANUFACTURER%%',$manufacturer,$text);
	$text = str_replace('%%TITLE%%',$title,$text);
	$text = str_replace('%%RATINGBAR%%',$customerRating,$text);
	$text = str_replace('%%REVIEWCOUNT%%',$customerReviewCount,$text);
	$text = str_replace('%%LISTPRICE%%',$listPrice,$text);
	$text = str_replace('%%BRAND%%',$brand,$text);
	$text = str_replace('%%MODEL%%',$model,$text);

	$text = str_replace('%%RATINGNUMBER%%',$averageRatingNumber,$text);
	$text = str_replace('%%SALEPRICE%%',$lowestNewPrice,$text);
	
	$text = str_replace('%%FREESHIPPING%%',$freeShipping,$text);
	$text = str_replace('%%SHIPPINGAVAILIBILITY%%',$availibility,$text);
	$text = str_replace('%%AMOUNTSAVED%%',$amountSaved,$text);
	$text = str_replace('%%PERCENTSAVED%%',$percentageSaved,$text);
	
	return $text;
	
}

function ReviewAZON_print_post_excerpts($asin,$postID,$trackingID)
{
	$permalink = get_permalink($postID);
	$templateGroup = ReviewAZON_get_affiliate_setting("Default_Template_Group");
	
	if(ReviewAZON_get_affiliate_setting("Excerpt_Layout_Mode") == "Normal")
	{
		$text = ReviewAZON_get_template($templateGroup,'excerpt');
	}
	else
	{
		$text = ReviewAZON_get_template($templateGroup,'excerpt_flow');
	}
	
	$title = ReviewAZON_get_post_setting($postID, "Review_Title", true);
	$customerReviewCount = ReviewAZON_get_post_setting($postID, "Customer_Review_Count", true);
	$description = stripslashes(ReviewAZON_get_post_setting($postID, "Review_Excerpt", true));	
	$customerRating = ReviewAZON_customer_rating(ReviewAZON_get_post_setting($postID, "Product_Review_Rating"),false, "");
	$smallImageUrl = ReviewAZON_get_post_setting($postID, "Product_Image_Small", true);
	$mediumImageUrl = ReviewAZON_get_post_setting($postID, "Product_Image_Medium", true);
	$largeImageUrl = ReviewAZON_get_post_setting($postID, "Product_Image_Large", true);
	
	//Rewrite Images	
	if(ReviewAZON_get_affiliate_setting("Allow_Image_Link_Cloaking"))
	{
		$smallImageUrl = ReviewAZON_get_rewrite_image($smallImageUrl,$asin);
		$mediumImageUrl = ReviewAZON_get_rewrite_image($mediumImageUrl,$asin);
		$largeImageUrl = ReviewAZON_get_rewrite_image($largeImageUrl,$asin);
	}

	
	if(empty($customerReviewCount))
	{
		$customerReviewCount = "0";
	}
	
	$text = str_replace("%%IMAGEPATH%%",ReviewAZON_IMAGES_FOLDER_NOSLASH, $text);
	$text = str_replace("%%TITLE%%",$title, $text);
	$text = str_replace("%%REVIEWCOUNT%%",$customerReviewCount, $text);
	$text = str_replace("%%DESCRIPTION%%",ReviewAZON_truncate_description($description,$postID), $text);
	$text = str_replace("%%RATINGBAR%%",$customerRating, $text);
	$text = str_replace("%%AMAZONSMALLIMAGE%%",$smallImageUrl, $text);
	$text = str_replace("%%AMAZONMEDIUMIMAGE%%",$mediumImageUrl, $text);
	$text = str_replace("%%AMAZONLARGEIMAGE%%",$largeImageUrl, $text);	
	$text = str_replace("%%PERMALINK%%",$permalink, $text);
	
	//Make the call to Amazon if expanded tokens are allowed.
	if(ReviewAZON_get_affiliate_setting("Allow_Expanded_Tokens"))
	{
		
		$current = ReviewAZON_get_amazon_current_node($asin,null,null,null,$trackingID);
			
		//Links
		$detailPageUrl = $current->DetailPageURL;
		$technicalDetailsPageUrl = GetItemLink($current,"Technical Details");
		$addToBabyRegistryPageUrl = GetItemLink($current,"Add To Baby Registry");
		$addToWeddingRegistryPageUrl = GetItemLink($current,"Add To Wedding Registry");
		$addToWishListPageUrl = GetItemLink($current,"Add To Wishlist");
		$tellAFriendPageUrl = GetItemLink($current,"Tell A Friend");
		$allCustomerReviewsPageUrl = GetItemLink($current,"All Customer Reviews");
		
		//Convert links to friendly url if link cloaking is turned on.
		if(ReviewAZON_get_affiliate_setting("Allow_Link_Cloaking"))
		{
			$sanitizedTitle = sanitize_title_with_dashes($title);
			$detailPageUrl = ReviewAZON_cloak_link('detailpage',$asin,$sanitizedTitle,$trackingID);
			$technicalDetailsPageUrl = ReviewAZON_cloak_link('techdetails',$asin,$sanitizedTitle,$trackingID);
			$addToBabyRegistryPageUrl = ReviewAZON_cloak_link('baby',$asin,$sanitizedTitle,$trackingID);
			$addToWeddingRegistryPageUrl = ReviewAZON_cloak_link('wedding',$asin,$sanitizedTitle,$trackingID);
			$addToWishListPageUrl = ReviewAZON_cloak_link('wish',$asin,$sanitizedTitle,$trackingID);
			$tellAFriendPageUrl = ReviewAZON_cloak_link('taf',$asin,$sanitizedTitle,$trackingID);
			$allCustomerReviewsPageUrl = ReviewAZON_cloak_link('custreview',$asin,$sanitizedTitle,$trackingID);		
		}
		
		$maximumAge = ($current->ItemAttributes->AmazonMaximumAge / 12);
		$minimumAge = ($current->ItemAttributes->AmazonMinimumAge /12);
		$brand = $current->ItemAttributes->Brand;
		$UPC = '00'.$current->ItemAttributes->UPC;
		$productFeatures = GetProductFeatures($current);
		$height = ($current->ItemAttributes->ItemDimensions->Height /100);
		$width = ($current->ItemAttributes->ItemDimensions->Width /100);
		$length = ($current->ItemAttributes->ItemDimensions->Length /100);
		$weight = ($current->ItemAttributes->ItemDimensions->Weight /100);
		$listPrice = $current->ItemAttributes->ListPrice->FormattedPrice;
		$lowestNewPrice = $current->OfferSummary->LowestNewPrice->FormattedPrice;
		$manufacturer = $current->ItemAttributes->Manufacturer;
		$model = $current->ItemAttributes->Model;
		
		$freeShipping = $current->Offers->Offer->OfferListing->IsEligibleForSuperSaverShipping;
		$availibility = $current->Offers->Offer->OfferListing->Availability;
		if(empty($availibility))
		{
			$availibility = '<a href="'.$detailPageUrl.'">View Product Availability</a>';
		}
		
		$amountSaved = $current->Offers->Offer->OfferListing->AmountSaved->FormattedPrice;
		$percentageSaved = $current->Offers->Offer->OfferListing->PercentageSaved;
		
		if(!empty($percentageSaved))
		{
			$percentageSaved = $percentageSaved.'%';
		}
		
		if(empty($lowestNewPrice))
		{
			if(isset($current->OfferSummary->LowestNewPrice->FormattedPrice))
			{
				$lowestNewPrice = $current->OfferSummary->LowestNewPrice->FormattedPrice;
			}
			else
			{
				$lowestNewPrice = "View Sale Price";		
			}
		}
	
		if(empty($listPrice))
		{
			$listPrice = "Varies based on product options";		
		}
		
		if($freeShipping == 1)
		{
			$freeShipping = stripslashes(ReviewAZON_get_affiliate_setting("Free_Shipping_Text"));
		}
		else
		{
			$freeShipping = "";
		}	
	
		//Links
		$text = str_replace('%%DETAILPAGELINK%%',$detailPageUrl,$text);
		$text = str_replace('%%TECHNICALDETAILPAGELINK%%',$technicalDetailsPageUrl,$text);
		$text = str_replace('%%ADDTOBABYREGISTRYLINK%%',$addToBabyRegistryPageUrl,$text);
		$text = str_replace('%%ADDTOWEDDINGREGISTRYLINK%%',$addToWeddingRegistryPageUrl,$text);
		$text = str_replace('%%ADDTOWISHLISTLINK%%',$addToWishListPageUrl,$text);
		$text = str_replace('%%TELLAFRIENDLINK%%',$tellAFriendPageUrl,$text);
		$text = str_replace('%%ALLCUSTOMERREVIEWSPAGELINK%%',$allCustomerReviewsPageUrl,$text);	
		
		//Product Attributes
		$text = str_replace('%%AMAZONMAXIMUMAGE%%',$maximumAge,$text);
		$text = str_replace('%%AMAZONMINIMUMAGE%%',$minumumAge,$text);
		$text = str_replace('%%UPCCODE%%',$UPC,$text);
		$text = str_replace('%%PRODUCTHEIGHT%%',$height,$text);
		$text = str_replace('%%PRODUCTLENGTH%%',$length,$text);
		$text = str_replace('%%PRODUCTWIDTH%%',$width,$text);
		$text = str_replace('%%PRODUCTWEIGHT%%',$weight,$text);	
		$text = str_replace('%%UPCCODE%%',$UPC,$text);	
		
		$text = str_replace('%%MANUFACTURER%%',$manufacturer,$text);	
		$text = str_replace('%%LISTPRICE%%',$listPrice,$text);
		$text = str_replace('%%BRAND%%',$brand,$text);
		$text = str_replace('%%MODEL%%',$model,$text);
		$text = str_replace('%%PRODUCTDETAILS%%',$productFeatures,$text);
		$text = str_replace('%%RELATEDPRODUCTS%%',$similarProducts,$text);
		$text = str_replace('%%PRODUCTACCESSORIES%%',$productAccessories,$text);
		$text = str_replace('%%SALEPRICE%%',$lowestNewPrice,$text);
		$text = str_replace('%%VIDEOREVIEWS%%',$videoReviews,$text);
		$text = str_replace('%%SIMILARPRODUCTS%%',$similarProducts,$text);
		
		$text = str_replace('%%FREESHIPPING%%',$freeShipping,$text);
		$text = str_replace('%%SHIPPINGAVAILIBILITY%%',$availibility,$text);
		$text = str_replace('%%AMOUNTSAVED%%',$amountSaved,$text);
		$text = str_replace('%%PERCENTSAVED%%',$percentageSaved,$text);
		$text = str_replace('%%ASIN%%',$asin,$text);
		
	}
	
	return $text;	
}



function ReviewAZON_Update_ReviewData($postid,$reviewCount,$rating)
{
	global $wpdb;
	$query = "UPDATE ".$wpdb->prefix."reviewazon_post_settings SET Customer_Review_Count = '{$reviewCount}', Product_Review_Rating = '{$rating}' WHERE Post_ID = '{$postid}'";
	$wpdb->query($query);
}
   
#Builds the post and page product reviews
function ReviewAZON_build_post($asin,$postid,$trackingID)
{
	global $wpdb;

	$current = ReviewAZON_get_amazon_current_node($asin,null,null,null,$trackingID);	
	
	$ebayAuctions = '';
	
	//Grab the search query and get the auction results
	if(ReviewAZON_get_affiliate_setting("Show_Ebay_Auctions"))
	{
		if(function_exists(phpBayLite))
		{
			$ebayAuctions = phpBayLite(stripslashes(ReviewAZON_get_post_setting($postid, 'Ebay_Search_Query')));
		}
		else
		{
			if(function_exists(phpBayPro))
			{
				$ebayAuctions = phpBayPro(stripslashes(ReviewAZON_get_post_setting($postid, 'Ebay_Search_Query')));
			}
			else
			{
				$ebayAuctions = "No auction software is installed.";
			}
		}
	}
	
	if(ReviewAZON_get_affiliate_setting("Show_Overstock_Items"))
	{
		if(function_exists(phpOStock_filter))
		{
			$overstockListings = phpOStock_filter(stripslashes(ReviewAZON_get_post_setting($postid, 'Overstock_Search_Query')));
			if($overstockListings == '<div><table width="100%" border="0"></table></div>')
			{
				$overstockListings = ReviewAZON_get_affiliate_setting("Empty_PhpOStock_Text");
			}
		}
		else
		{
			$overstockListings = "PhpOStock is not installed. <a href=\"http://reviewazon.com/phpostock.html\" target=\"_blank\">Get PhpOStock Now!</a>";
		}
	}
	
	$averageRatingNumber = $current->CustomerReviews->AverageRating;	
	$title = ReviewAZON_get_post_setting($postid, "Review_Title", true);

	
	//Links
	$detailPageUrl = $current->DetailPageURL;
	$technicalDetailsPageUrl = GetItemLink($current,"Technical Details");
	$addToBabyRegistryPageUrl = GetItemLink($current,"Add To Baby Registry");
	$addToWeddingRegistryPageUrl = GetItemLink($current,"Add To Wedding Registry");
	$addToWishListPageUrl = GetItemLink($current,"Add To Wishlist");
	$tellAFriendPageUrl = GetItemLink($current,"Tell A Friend");
	$allCustomerReviewsPageUrl = GetItemLink($current,"All Customer Reviews");
	
	//Convert links to friendly url if link cloaking is turned on.
	if(ReviewAZON_get_affiliate_setting("Allow_Link_Cloaking"))
	{
		$sanitizedTitle = sanitize_title_with_dashes($title);
		$detailPageUrl = ReviewAZON_cloak_link('detailpage',$asin,$sanitizedTitle,$trackingID);
		$technicalDetailsPageUrl = ReviewAZON_cloak_link('techdetails',$asin,$sanitizedTitle,$trackingID);
		$addToBabyRegistryPageUrl = ReviewAZON_cloak_link('baby',$asin,$sanitizedTitle,$trackingID);
		$addToWeddingRegistryPageUrl = ReviewAZON_cloak_link('wedding',$asin,$sanitizedTitle,$trackingID);
		$addToWishListPageUrl = ReviewAZON_cloak_link('wish',$asin,$sanitizedTitle,$trackingID);
		$tellAFriendPageUrl = ReviewAZON_cloak_link('taf',$asin,$sanitizedTitle,$trackingID);
		$allCustomerReviewsPageUrl = ReviewAZON_cloak_link('custreview',$asin,$sanitizedTitle,$trackingID);		
	}

	$amazonProdDesc = GetProductEditoralAmazonDotCom($current);
	$normalProdDesc = GetProductEditoralStandard($current);
	
	$description = "";
	if(!empty($amazonProdDesc))
	{
		$description = $amazonProdDesc;
	}
	else
	{
		if(!empty($normalProdDesc))
		{
			$description = $normalProdDesc;
		}
	}
	
	$descriptionTemplate = ReviewAZON_get_template(ReviewAZON_get_affiliate_setting("Default_Template_Group"),"description");
	$description = str_replace('%%DESCRIPTION%%',$description,$descriptionTemplate);
	
	//Images
	$smallImageUrl = $current->SmallImage->URL;
	$mediumImageUrl = $current->MediumImage->URL;
	$largeImageUrl = $current->LargeImage->URL;
	
	if(empty($smallImageUrl))
	{
		$smallImageUrl = get_option('siteurl').'/images/noimage-75.gif';			
	}
				
	if(empty($mediumImageUrl))
	{
		$mediumImageUrl = get_option('siteurl').'/images/noimage.gif';			
	}
	
	if(empty($largeImageUrl))
	{
		$largeImageUrl = get_option('siteurl').'/images/noimage.gif';			
	}
	
	//Rewrite Images	
	if(ReviewAZON_get_affiliate_setting("Allow_Image_Link_Cloaking"))
	{
		$smallImageUrl = ReviewAZON_get_rewrite_image($smallImageUrl,$asin);
		$mediumImageUrl = ReviewAZON_get_rewrite_image($mediumImageUrl,$asin);
		$largeImageUrl = ReviewAZON_get_rewrite_image($largeImageUrl,$asin);
	}
	
	$maximumAge = ($current->ItemAttributes->AmazonMaximumAge / 12);
	$minimumAge = ($current->ItemAttributes->AmazonMinimumAge / 12);
	$brand = $current->ItemAttributes->Brand;
	$UPC = '00'.$current->ItemAttributes->UPC;
	$height = ($current->ItemAttributes->ItemDimensions->Height /100);
	$width = ($current->ItemAttributes->ItemDimensions->Width /100);
	$length = ($current->ItemAttributes->ItemDimensions->Length /100);
	$weight = ($current->ItemAttributes->ItemDimensions->Weight /100);
	$listPrice = $current->ItemAttributes->ListPrice->FormattedPrice;
	$lowestNewPrice = $current->OfferSummary->LowestNewPrice->FormattedPrice;
	$manufacturer = $current->ItemAttributes->Manufacturer;
	$model = $current->ItemAttributes->Model;
	
	$freeShipping = $current->Offers->Offer->OfferListing->IsEligibleForSuperSaverShipping;
	$availibility = $current->Offers->Offer->OfferListing->Availability;
	if(empty($availibility))
	{
		$availibility = '<a href="'.$detailPageUrl.'">View Product Availability</a>';
	}
	
	$amountSaved = $current->Offers->Offer->OfferListing->AmountSaved->FormattedPrice;
	$percentageSaved = $current->Offers->Offer->OfferListing->PercentageSaved;
	
	$newCount = $current->OfferSummary->TotalNew;
	$usedCount = $current->OfferSummary->TotalUsed;
	$lowestNPrice = $current->OfferSummary->LowestNewPrice->FormattedPrice;
	$lowestUPrice = $current->OfferSummary->LowestUsedPrice->FormattedPrice;
	
	$newFromText = $newCount." new from ".$lowestNPrice;
	
	$usedFromText =  $usedCount." used from ".$lowestUPrice;
	
	if(!empty($percentageSaved))
	{
		$percentageSaved = $percentageSaved.'%';
	}
	
	if(empty($listPrice))
	{
		$listPrice = "Varies based on product options";		
	}
		
	if(empty($lowestNewPrice))
	{
		if(isset($current->OfferSummary->LowestNewPrice->FormattedPrice))
		{
			$lowestNewPrice = $current->OfferSummary->LowestNewPrice->FormattedPrice;
		}
		else
		{
			$lowestNewPrice = "View Sale Price";		
		}
	}
	
	if($freeShipping == 1)
	{
		$freeShipping = stripslashes(ReviewAZON_get_affiliate_setting("Free_Shipping_Text"));
	}
	else
	{
		$freeShipping = "";
	}
	
	$cachedDateTime = '';
	if(ReviewAZON_get_affiliate_setting("Page_Cache_Length") > 1)
	{
		$cachedDateTime = ' <p>Amazon.com Price: '.$lowestNewPrice.' (as of '.current_time('mysql',1).' GMT) Product prices and availability are accurate as of the date/time indicated and are subject to change. Any price and availability information displayed on Amazon.com at the time of purchase will apply to the purchase of this product.</p>';
	}
	
	$productFeatures = GetProductFeatures($current);
	$totalReviewPages = $current->CustomerReviews->TotalReviewPages;
	
	if(empty($customerReviewCount))
	{
		$customerReviewCount = 0;
	}

	$videoReviews = ReviewAZON_get_video_reviews($postid);	

	$similarProducts = SimilarProducts($current,'',$trackingID,5);

	$productAccessories = ProductAccessories($current,'',$trackingID,5);	

	if(ReviewAZON_get_affiliate_setting("Cache_Description"))
	{		
		$description = stripslashes(ReviewAZON_get_post_setting($postid, "Review_Description"));
	}
	
	//Set review sorting parameter
	$reviewSorting = ReviewAZON_get_affiliate_setting("Min_Review_Score_To_Display");
	

	if(isset($current->CustomerReviews->IFrameURL))
	{
		$iframeUrl = (string)$current->CustomerReviews->IFrameURL;
		if(!get_post_meta($postid,"Rating_Date",true))
		{
			update_post_meta($postid,"Rating_Date",date("Y-m-d H:i:s", time()));
			$reviewData = ReviewAZON_Get_Product_Rating_Customer_Count($iframeUrl);
			ReviewAZON_Update_ReviewData($postid,$reviewData["ReviewCount"],$reviewData["AverageRating"]);
		}
		
		if(strtotime("now") > strtotime(get_post_meta($postid,"Rating_Date",true)."+2 day"))
		{
			update_post_meta($postid,"Rating_Date",date("Y-m-d H:i:s", time()));
			$reviewData = ReviewAZON_Get_Product_Rating_Customer_Count($iframeUrl);
			ReviewAZON_Update_ReviewData($postid,$reviewData["ReviewCount"],$reviewData["AverageRating"]);
		}
		
		$customerRating = ReviewAZON_customer_rating(ReviewAZON_get_post_setting($postid,"Product_Review_Rating"), false, "");
		$customerReviewCount = ReviewAZON_get_post_setting($postid,"Customer_Review_Count");
		
		update_post_meta($postid,'ReviewAZON_Iframe_URL',$iframeUrl);
		$iFrame = '<iframe id="crf" onLoad="autoResize(\'crf\');" marginheight="0" frameborder="0" width="100%" scrolling="auto" src="'.ReviewAZON_URL_ROOT.'reviewazon_customer_reviews.php?id='.$postid.'"></iframe>';
		$iFrameScript = "\n\n
		<script language='JavaScript'>
		<!--
		function autoResize(id){
		    var newheight;	
		    if(document.getElementById){
		        newheight=document.getElementById(id).contentWindow.document.body.scrollHeight+50;
		    }	
		    if(newheight == 0)
		    {
		       newheight = '2000';
		    }
		    document.getElementById(id).height= (newheight) + 'px';
		}
		//-->
		</script>
		";
		$reviews = $iFrame.$iFrameScript;		
	}
	else
	{
		$reviews = stripslashes(ReviewAZON_get_affiliate_setting("Empty_Customer_Reviews_Text"));
	}
	
	if(ReviewAZON_get_affiliate_setting("Default_Template_Group") == "Tabs")
	{
		$text = ReviewAZON_get_template("Tabs","main");		

		$jQueryTabs = '<div id="tabs">';
		$jQueryTabs.= '<ul>';
		$queryTabs = "SELECT * FROM ".$wpdb->prefix."reviewazon_tabs WHERE Show_Tab = 1 Order By Display_Order ASC";
		$tabs = $wpdb->get_results($queryTabs);
		foreach($tabs as $tab)
		{
			$divs.= '<div id="tabs-'.$tab->Tab_ID.'">'.$tab->Tab_Body.'</div>';
			$jQueryTabs.= '<li><a href="#tabs-'.$tab->Tab_ID.'">'.$tab->Tab_Title.'</a></li>';
		}
		$jQueryTabs.= '</ul>';		
		$jQueryTabs.= $divs;
		$jQueryTabs.= '</div>';
	}
	else
	{
		$text = ReviewAZON_get_template(ReviewAZON_get_affiliate_setting("Default_Template_Group"),"main");
	}		
	
	$text = str_replace("%%IMAGEPATH%%",ReviewAZON_IMAGES_FOLDER_NOSLASH, $text);
	$text = str_replace('%%PLUGINURL%%',WP_PLUGIN_URL.'/ReviewAZON',$text);
	$text = str_replace('%%TABS%%',$jQueryTabs,$text);
	$text = str_replace('%%ASIN%%',$asin,$text);
	$text = str_replace('%%CACHEDPRICEDISCLAIMER%%',$cachedDateTime,$text);
	$text = str_replace('%%BLOGURL%%',get_option('siteurl'),$text); 
	
	
	//Links
	$text = str_replace('%%DETAILPAGELINK%%',$detailPageUrl,$text);
	$text = str_replace('%%TECHNICALDETAILPAGELINK%%',$technicalDetailsPageUrl,$text);
	$text = str_replace('%%ADDTOBABYREGISTRYLINK%%',$addToBabyRegistryPageUrl,$text);
	$text = str_replace('%%ADDTOWEDDINGREGISTRYLINK%%',$addToWeddingRegistryPageUrl,$text);
	$text = str_replace('%%ADDTOWISHLISTLINK%%',$addToWishListPageUrl,$text);
	$text = str_replace('%%TELLAFRIENDLINK%%',$tellAFriendPageUrl,$text);
	$text = str_replace('%%ALLCUSTOMERREVIEWSPAGELINK%%',$allCustomerReviewsPageUrl,$text);	
	
	//Images
	$text = str_replace('%%AMAZONSMALLIMAGE%%',$smallImageUrl,$text);
	$text = str_replace('%%AMAZONMEDIUMIMAGE%%',$mediumImageUrl,$text);
	$text = str_replace('%%AMAZONLARGEIMAGE%%',$largeImageUrl,$text);	
	
	//Product Attributes
	$text = str_replace('%%AMAZONMAXIMUMAGE%%',$maximumAge,$text);
	$text = str_replace('%%AMAZONMINIMUMAGE%%',$minumumAge,$text);
	$text = str_replace('%%UPCCODE%%',$UPC,$text);
	$text = str_replace('%%PRODUCTHEIGHT%%',$height,$text);
	$text = str_replace('%%PRODUCTLENGTH%%',$length,$text);
	$text = str_replace('%%PRODUCTWIDTH%%',$width,$text);
	$text = str_replace('%%PRODUCTWEIGHT%%',$weight,$text);		
	
	$text = str_replace('%%MANUFACTURER%%',$manufacturer,$text);
	$text = str_replace('%%UPCCODE%%',$UPC,$text);	
	$text = str_replace('%%TITLE%%',$title,$text);
	$text = str_replace('%%RATINGBAR%%',$customerRating,$text);
	$text = str_replace('%%REVIEWCOUNT%%',$customerReviewCount,$text);
	$text = str_replace('%%LISTPRICE%%',$listPrice,$text);
	$text = str_replace('%%BRAND%%',$brand,$text);
	$text = str_replace('%%MODEL%%',$model,$text);
	$text = str_replace('%%DESCRIPTION%%',$description,$text);
	$text = str_replace('%%PRODUCTDETAILS%%',$productFeatures,$text);
	$text = str_replace('%%CUSTOMERREVIEWS%%',$reviews,$text);
	$text = str_replace('%%EBAYAUCTIONS%%',$ebayAuctions,$text);
	$text = str_replace('%%OVERSTOCKLISTINGS%%',$overstockListings,$text);	
	$text = str_replace('%%RATINGNUMBER%%',$averageRatingNumber,$text);
	$text = str_replace('%%RELATEDPRODUCTS%%',$similarProducts,$text);
	$text = str_replace('%%PRODUCTACCESSORIES%%',$productAccessories,$text);
	$text = str_replace('%%SALEPRICE%%',$lowestNewPrice,$text);
	$text = str_replace('%%VIDEOREVIEWS%%',$videoReviews,$text);
	$text = str_replace('%%SIMILARPRODUCTS%%',$similarProducts,$text);
	
	$text = str_replace('%%FREESHIPPING%%',$freeShipping,$text);
	$text = str_replace('%%SHIPPINGAVAILIBILITY%%',$availibility,$text);
	$text = str_replace('%%AMOUNTSAVED%%',$amountSaved,$text);
	$text = str_replace('%%PERCENTSAVED%%',$percentageSaved,$text);
	$text = str_replace('%%NEWFROMTEXT%%',$newFromText,$text);
	$text = str_replace('%%USEDFROMTEXT%%',$usedFromText,$text);
	
	$text = ReviewAZON_set_custom_tokens($text);
	
	$vendivaShortCode = get_post_meta($postid, "VENDIVA", true);
	$vendivaComparisons = "";
	
	if(!empty($vendivaShortCode))
	{
		if(function_exists(vendiva_pricecomparisons))
		{
			$vendivaComparisons = vendiva_pricecomparisons($vendivaShortCode);
		}		
	}
	
	$text = str_replace('%%VENDIVACOMPARISONS%%',$vendivaComparisons,$text);
	
	
	if(ReviewAZON_get_affiliate_setting("Page_Cache_Length") > 0)
	{
		ReviewAZON_update_page_cache($asin,$customerReviewCount,$averageRatingNumber,$smallImageUrl,$mediumImageUrl,$largeImageUrl,addslashes($reviews),addslashes($similarProducts),addslashes($productAccessories),addslashes($text),$postid);
	}
	return $text;	
	
}

function ReviewAZON_print_search_query($searchIndex, $query, $itemPage, $maxCount, $sort, $trackingID)
{
	$parsed_xml = ReviewAZON_get_amazon_current_node_search($searchIndex, $query, $itemPage, $sort, $trackingID);	
	
	$templateGroup = ReviewAZON_get_affiliate_setting("Default_Template_Group");		

	$count = 0;	
	$numOfItems = $parsed_xml->Items->TotalResults;
	$numOfPages = $parsed_xml->Items->TotalPages;
	
	foreach($parsed_xml->Items->Item as $current)
	{
		if($count < $maxCount)
		{
			$asin = $current->ASIN;
			$text = ReviewAZON_get_template($templateGroup,'multi_inline_post');
			$averageRatingNumber = $current->CustomerReviews->AverageRating;	
			$title = $current->ItemAttributes->Title;
			$description = strip_tags($current->EditorialReviews->EditorialReview->Content);
			$descriptionTemplate = ReviewAZON_get_template(ReviewAZON_get_affiliate_setting("Default_Template_Group"),"Description");
			$description = str_replace('%%DESCRIPTION%%',$description,$descriptionTemplate);	
			
			//Links
			$detailPageUrl = $current->DetailPageURL;
			$technicalDetailsPageUrl = GetItemLink($current,"Technical Details");
			$addToBabyRegistryPageUrl = GetItemLink($current,"Add To Baby Registry");
			$addToWeddingRegistryPageUrl = GetItemLink($current,"Add To Wedding Registry");
			$addToWishListPageUrl = GetItemLink($current,"Add To Wishlist");
			$tellAFriendPageUrl = GetItemLink($current,"Tell A Friend");
			$allCustomerReviewsPageUrl = GetItemLink($current,"All Customer Reviews");
			
			//Convert links to friendly url if link cloaking is turned on.
			if(ReviewAZON_get_affiliate_setting("Allow_Link_Cloaking"))
			{
				$sanitizedTitle = sanitize_title_with_dashes($title);
				$detailPageUrl = ReviewAZON_cloak_link('detailpage',$asin,$sanitizedTitle,$trackingID);
				$technicalDetailsPageUrl = ReviewAZON_cloak_link('techdetails',$asin,$sanitizedTitle,$trackingID);
				$addToBabyRegistryPageUrl = ReviewAZON_cloak_link('baby',$asin,$sanitizedTitle,$trackingID);
				$addToWeddingRegistryPageUrl = ReviewAZON_cloak_link('wedding',$asin,$sanitizedTitle,$trackingID);
				$addToWishListPageUrl = ReviewAZON_cloak_link('wish',$asin,$sanitizedTitle,$trackingID);
				$tellAFriendPageUrl = ReviewAZON_cloak_link('taf',$asin,$sanitizedTitle,$trackingID);
				$allCustomerReviewsPageUrl = ReviewAZON_cloak_link('custreview',$asin,$sanitizedTitle,$trackingID);		
			}
		
			//Images
			$smallImageUrl = $current->SmallImage->URL;
			$mediumImageUrl = $current->MediumImage->URL;
			$largeImageUrl = $current->LargeImage->URL;
			
			if(empty($smallImageUrl))
			{
				$smallImageUrl = get_option('siteurl').'/images/noimage-75.gif';			
			}
						
			if(empty($mediumImageUrl))
			{
				$mediumImageUrl = get_option('siteurl').'/images/noimage.gif';			
			}
			
			if(empty($largeImageUrl))
			{
				$largeImageUrl = get_option('siteurl').'/images/noimage.gif';			
			}
					
			//Rewrite Images	
			if(ReviewAZON_get_affiliate_setting("Allow_Image_Link_Cloaking"))
			{
				$smallImageUrl = ReviewAZON_get_rewrite_image($smallImageUrl,$asin);
				$mediumImageUrl = ReviewAZON_get_rewrite_image($mediumImageUrl,$asin);
				$largeImageUrl = ReviewAZON_get_rewrite_image($largeImageUrl,$asin);
			}
			
			$brand = $current->ItemAttributes->Brand;
			$UPC = '00'.$current->ItemAttributes->UPC;
			$productFeatures = GetProductFeatures($current);
			$listPrice = $current->ItemAttributes->ListPrice->FormattedPrice;
			$lowestNewPrice = $current->OfferSummary->LowestNewPrice->FormattedPrice;
			$manufacturer = $current->ItemAttributes->Manufacturer;
			$model = $current->ItemAttributes->Model;
			
			$freeShipping = $current->Offers->Offer->OfferListing->IsEligibleForSuperSaverShipping;
			$availibility = $current->Offers->Offer->OfferListing->Availability;
			if(empty($availibility))
			{
				$availibility = '<a href="'.$detailPageUrl.'">View Product Availability</a>';
			}
					
			$amountSaved = $current->Offers->Offer->OfferListing->AmountSaved->FormattedPrice;
			$percentageSaved = $current->Offers->Offer->OfferListing->PercentageSaved;
			
			if(!empty($percentageSaved))
			{
				$percentageSaved = $percentageSaved.'%';
			}
			
			if(empty($listPrice))
			{
				$listPrice = "Varies based on product options";		
			}
			
			if(empty($lowestNewPrice))
			{
				if(isset($current->OfferSummary->LowestNewPrice->FormattedPrice))
				{
					$lowestNewPrice = $current->OfferSummary->LowestNewPrice->FormattedPrice;
				}
				else
				{
					$lowestNewPrice = "View Sale Price";		
				}
			}
			
			if($freeShipping == 1)
			{
				$freeShipping = stripslashes(ReviewAZON_get_affiliate_setting("Free_Shipping_Text"));
			}
			else
			{
				$freeShipping = "";
			}
			
			$iframeUrl = (string)$current->CustomerReviews->IFrameURL;
			$reviewData = ReviewAZON_Get_Product_Rating_Customer_Count($iframeUrl);
			if(isset($reviewData["AverageRating"]))
			{
				$averageRatingNumber = $reviewData["AverageRating"];
			}
			if(isset($reviewData["ReviewCount"]))
			{
				$reviewCount = $reviewData["ReviewCount"];
			}	
			
			$customerRating = ReviewAZON_customer_rating($averageRatingNumber, false, "");
			$customerReviewCount = $reviewCount;
		
			
			if(empty($customerReviewCount))
			{
				$customerReviewCount = 0;
			}
			
			$text = str_replace("%%IMAGEPATH%%",ReviewAZON_IMAGES_FOLDER_NOSLASH, $text);
			$text = str_replace('%%PLUGINURL%%',WP_PLUGIN_URL.'/ReviewAZON',$text);
			$text = str_replace('%%ASIN%%',$asin,$text);
			
			//Links
			$text = str_replace('%%DETAILPAGELINK%%',$detailPageUrl,$text);
			$text = str_replace('%%TECHNICALDETAILPAGELINK%%',$technicalDetailsPageUrl,$text);
			$text = str_replace('%%ADDTOBABYREGISTRYLINK%%',$addToBabyRegistryPageUrl,$text);
			$text = str_replace('%%ADDTOWEDDINGREGISTRYLINK%%',$addToWeddingRegistryPageUrl,$text);
			$text = str_replace('%%ADDTOWISHLISTLINK%%',$addToWishListPageUrl,$text);
			$text = str_replace('%%TELLAFRIENDLINK%%',$tellAFriendPageUrl,$text);
			$text = str_replace('%%ALLCUSTOMERREVIEWSPAGELINK%%',$allCustomerReviewsPageUrl,$text);	
			
			//Images
			$text = str_replace('%%AMAZONSMALLIMAGE%%',$smallImageUrl,$text);
			$text = str_replace('%%AMAZONMEDIUMIMAGE%%',$mediumImageUrl,$text);
			$text = str_replace('%%AMAZONLARGEIMAGE%%',$largeImageUrl,$text);	
			
			//Product Attributes
			$text = str_replace('%%UPCCODE%%',$UPC,$text);
			$text = str_replace('%%DESCRIPTION%%',$description,$text);
			$text = str_replace('%%PRODUCTDETAILS%%',$productFeatures,$text);
			$text = str_replace('%%MANUFACTURER%%',$manufacturer,$text);
			$text = str_replace('%%UPCCODE%%',$UPC,$text);	
			$text = str_replace('%%TITLE%%',$title,$text);
			$text = str_replace('%%RATINGBAR%%',$customerRating,$text);
			$text = str_replace('%%REVIEWCOUNT%%',$customerReviewCount,$text);
			$text = str_replace('%%LISTPRICE%%',$listPrice,$text);
			$text = str_replace('%%BRAND%%',$brand,$text);
			$text = str_replace('%%MODEL%%',$model,$text);
			$text = str_replace('%%RATINGNUMBER%%',$averageRatingNumber,$text);
			$text = str_replace('%%SALEPRICE%%',$lowestNewPrice,$text);
			$text = str_replace('%%FREESHIPPING%%',$freeShipping,$text);
			$text = str_replace('%%SHIPPINGAVAILIBILITY%%',$availibility,$text);
			$text = str_replace('%%AMOUNTSAVED%%',$amountSaved,$text);
			$text = str_replace('%%PERCENTSAVED%%',$percentageSaved,$text);
			$text = str_replace('%%NEWFROMTEXT%%',$newFromText,$text);
			$text = str_replace('%%USEDFROMTEXT%%',$usedFromText,$text);
			
			$textToReturn.= $text;
			$count++;
		}
	}
	
	return '<div class="RZMultiInlinePost">'.$textToReturn.'</div>';
}


function reviewAZON_func($atts)
{		
	global $wpdb,$post;
	
	extract(shortcode_atts(array(
		'asin' => '0',
		'display' => '',
		'query' => '',
		'count' => '5',
		'category' => '',
		'sort' => '',
		'page' => '',
		'trackingid' => '',
		), $atts));
	
	if($trackingid == "")
	{
		$trackingID = get_post_meta($post->ID, 'ReviewAZON_Tracking_ID', true);
	}
	else{
		$trackingID = $trackingid;
	}	
	
		
	if(is_feed())
	{
		return ReviewAZON_print_rss_excerpts($asin,$post->ID,$trackingID);
	}
			
	if($display == "searchquery")
	{
		return ReviewAZON_print_search_query($category, $query, $page, $count, $sort, $trackingID);
	}
	if($display == "inlinepost")
	{
		return ReviewAZON_print_inline_post($asin,$post->ID,$trackingID);
	}
	else
	{
		if(!is_feed())
		{
			if(is_author())
			{
				return ReviewAZON_print_post_excerpts($asin,$post->ID,$trackingID);			
			}
			elseif(ReviewAZON_get_page_excerpt_setting('frontpage') && is_front_page())
			{
				return ReviewAZON_print_post_excerpts($asin,$post->ID,$trackingID);			
			}
			elseif(ReviewAZON_get_page_excerpt_setting('category') && is_category())
			{
				return ReviewAZON_print_post_excerpts($asin,$post->ID,$trackingID);	
			}
			elseif(ReviewAZON_get_page_excerpt_setting('home') && is_home())
			{
				return ReviewAZON_print_post_excerpts($asin,$post->ID,$trackingID);	
			}
			elseif(ReviewAZON_get_page_excerpt_setting('search') && is_search())
			{
				return ReviewAZON_print_post_excerpts($asin,$post->ID,$trackingID);	
			}
			elseif(ReviewAZON_get_page_excerpt_setting('tag') && is_tag())
			{
				return ReviewAZON_print_post_excerpts($asin,$post->ID,$trackingID);	
			}
			elseif(ReviewAZON_get_page_excerpt_setting('sticky') && is_sticky())
			{
				return ReviewAZON_print_post_excerpts($asin,$post->ID,$trackingID);	
			}
			else
			{
				$blogtime = current_time('mysql'); 
				$cacheTime = ReviewAZON_get_post_cache_date($post->ID);
				$pageCacheLength = ReviewAZON_get_affiliate_setting("Page_Cache_Length");
	
				if($pageCacheLength > 0)
				{
					if(ReviewAZON_get_page_cache($post->ID) == "")
					{
						//Need to update the cache
						return ReviewAZON_build_post($asin,$post->ID,$trackingID);			
					}
					else
					{
	
						if($elapsedTime=ReviewAZON_get_time_diff($cacheTime,$blogtime))
						{			
							if(!empty($elapsedTime['days']))	
							{
								//Means that we have gone days without updating, so cache needs to be refreshed
								return ReviewAZON_build_post($asin,$post->ID,$trackingID);
							}
							else
							{
								if($elapsedTime['hours'] >= $pageCacheLength)
								{
									//Update Cache
									return ReviewAZON_build_post($asin,$post->ID,$trackingID);
								}
								else
								{
									//Get the cached version
									return ReviewAZON_get_page_cache($post->ID);
								}				
							}
						}
						
					}
				}
				else
				{
					return ReviewAZON_build_post($asin,$post->ID,$trackingID);
				}
			}
		}
	}
}

function GetSingleProductValues($asin, $trackingID = "")
{

	$current = ReviewAZON_get_amazon_current_node($asin,null,null,null,$trackingID);
	
	$averageRatingNumber = $current->CustomerReviews->AverageRating;
	$customerRating = ReviewAZON_customer_rating($averageRatingNumber, false, "");
	$customerReviewCount = $current->CustomerReviews->TotalReviews;
	$lowestNewPrice = $current->OfferSummary->LowestNewPrice->FormattedPrice;
	$description = $current->EditorialReviews->EditorialReview->Content;
	$smallImageUrl = $current->SmallImage->URL;
	$mediumImageUrl = $current->MediumImage->URL;
	$largeImageUrl = $current->LargeImage->URL;
	$title = $current->ItemAttributes->Title;
	$itemURL = $current->DetailPageURL;
	$listPrice = $current->ItemAttributes->ListPrice->FormattedPrice;
	$amountSaved = $current->Offers->Offer->OfferListing->AmountSaved->FormattedPrice;
	$percentageSaved = $current->Offers->Offer->OfferListing->PercentageSaved;
	
	if(!empty($percentageSaved))
	{
		$percentageSaved = $percentageSaved.'%';
	}
	
	if(empty($smallImageUrl))
	{
		$smallImageUrl = get_option('siteurl').'/images/noimage-75.gif';			
	}
				
	if(empty($mediumImageUrl))
	{
		$mediumImageUrl = get_option('siteurl').'/images/noimage.gif';			
	}
	
	if(empty($largeImageUrl))
	{
		$largeImageUrl = get_option('siteurl').'/images/noimage.gif';			
	}
	
	//Rewrite Images	
	if(ReviewAZON_get_affiliate_setting("Allow_Image_Link_Cloaking"))
	{
		$smallImageUrl = ReviewAZON_get_rewrite_image($smallImageUrl,$asin);
		$mediumImageUrl = ReviewAZON_get_rewrite_image($mediumImageUrl,$asin);
		$largeImageUrl = ReviewAZON_get_rewrite_image($largeImageUrl,$asin);
	}
	
	//Convert links to friendly url if link cloaking is turned on.
	if(ReviewAZON_get_affiliate_setting("Allow_Link_Cloaking"))
	{
		$sanitizedTitle = sanitize_title_with_dashes($title);
		$itemURL = ReviewAZON_cloak_link('detailpage',$asin,$sanitizedTitle,$trackingID);
	}
	
	if(empty($listPrice))
	{
		$listPrice = "Varies based on product options";		
	}
	
	if(empty($lowestNewPrice))
	{
		if(isset($current->OfferSummary->LowestNewPrice->FormattedPrice))
		{
			$lowestNewPrice = $current->OfferSummary->LowestNewPrice->FormattedPrice;
		}
		else
		{
			$lowestNewPrice = "View Sale Price";		
		}
	}
	
	if(empty($customerReviewCount))
	{
		$customerReviewCount = 0;		
	}
	
	$returnArray = array();
	$returnArray['link'] = $itemURL;
	$returnArray['title'] = $title;
	$returnArray['customerrating'] = $customerRating;
	$returnArray['reviewcount'] = $customerReviewCount;
	$returnArray['smallimage'] = $smallImageUrl;
	$returnArray['mediumimage'] = $mediumImageUrl;
	$returnArray['largeimage'] = $largeImageUrl;
	$returnArray['description'] = $description;
	$returnArray['salesprice'] = $lowestNewPrice;
	$returnArray['listprice'] = $listPrice;
	$returnArray['amountsaved'] = $amountSaved;
	$returnArray['percentsaved'] = $percentageSaved;
	
	return $returnArray;
	
}
#=======================================================================================================================================================================================

#Product Review helper functions
#=======================================================================================================================================================================================

function ReviewAZON_GetProductDetailUrl($asin,$trackingID)
{
		$current = ReviewAZON_get_amazon_current_node_link($asin,$trackingID);
		
		$detailPageUrl = $current->DetailPageURL;
		$title = $current->ItemAttributes->Title;
		if(ReviewAZON_get_affiliate_setting("Allow_Link_Cloaking"))
		{
			$sanitizedTitle = sanitize_title_with_dashes($title);
			$detailPageUrl = ReviewAZON_cloak_link('detailpage',$asin,$sanitizedTitle,$trackingID);
		}
		
		return $detailPageUrl;
}


function ReviewAZON_set_custom_tokens($text)
{
	$custom_fields = get_post_custom();
	
	foreach ( $custom_fields as $key => $value )
	{
		if(strpos($key,"REVIEWAZON_"))
		{
			$text = str_replace($key,$custom_fields[$key][0],$text);  	
	 	}
	}
	
 	if(strpos($text,"%%REVIEWAZON_"))
 	{
 		$text = preg_replace("/(%%REVIEWAZON_(.*)%%)/","",$text);
 	}
 	
 	return $text;
}


function ReviewAZON_cloak_link($linkType,$asin,$title,$trackingID)
{
	global $post;

	$seoLinkPrefix = ReviewAZON_get_affiliate_setting("SEO_Link_Prefix");

	switch($linkType)
	{
		case "detailpage":
			$link = get_bloginfo('url')."/{$seoLinkPrefix}/product/{$asin}/{$trackingID}/{$title}";
			break;	
		case "techdetails":
			$link = get_bloginfo('url')."/{$seoLinkPrefix}/techdetails/{$asin}/{$trackingID}/{$title}";
			break;	
		case "baby":
			$link = get_bloginfo('url')."/{$seoLinkPrefix}/baby/{$asin}/{$trackingID}/{$title}";			
			break;	
		case "wedding":
			$link = get_bloginfo('url')."/{$seoLinkPrefix}/wedding/{$asin}/{$trackingID}/{$title}";			
			break;	
		case "wish":
			$link = get_bloginfo('url')."/{$seoLinkPrefix}/wishlist/{$asin}/{$trackingID}/{$title}";			
			break;	
		case "taf":
			$link = get_bloginfo('url')."/{$seoLinkPrefix}/taf/{$asin}/{$trackingID}/{$title}";			
			break;	
		case "custreview":
			$link = get_bloginfo('url')."/{$seoLinkPrefix}/custreview/{$asin}/{$trackingID}/{$title}";			
			break;	
	}	
	return $link;
}

function ReviewAZON_get_rewrite_image($imageUrl,$asin)
{
	$pos = strpos($imageUrl,'noimage.gif');
	if($pos === false)
	{
	  return get_bloginfo('url').'/images/'.$asin.'/'.str_replace("http://ecx.images-amazon.com/images/I/","",$imageUrl);
	}
	else
	{
		return $imageUrl;
	}	
}

function ReviewAZON_get_amazon_current_node_search($searchIndex, $query, $itemPage, $sort, $trackingID = "")
{		
	
		ReviewAZON_AWS_config_error();
		
		$params = array();
		
		if($sort != "default")
		{
			$params["Sort"] = $sort;
		}
		else
		{
			$params["Sort"] = "";
		}

		if($searchIndex != "Blended")
		{
			//$params["MerchantId"] = "All";
			$params["MerchantId"] = "Featured";
			$params["Condition"] = "All";
			//$params["Availability"] = "Available";
		}		

		$params["AssociateTag"] = $trackingID;
		$params["Operation"] = "ItemSearch";
		$params["ResponseGroup"] = "Large,OfferFull";
		$params["SearchIndex"] = $searchIndex;
		$params["Keywords"] = urlencode($query);
		$params["ItemPage"] = $itemPage;
		
		$requestURL = aws_signed_request($params);
		
		$session = curl_init($requestURL);
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($session);
		curl_close($session); 
		$parsed_xml = simplexml_load_string($response);	
		
		if(empty($parsed_xml))
		{
			die("<p>There was a problem completing this request. Check your configuration settings to ensure your authentication credentials are setup properly.</p>");
		}

		if($parsed_xml->Items->Request->IsValid == "False")
		{
			die(AWSERRORHEADER.$parsed_xml->Items->Request->Errors->Error->Code."<br><br>".$parsed_xml->Items->Request->Errors->Error->Message);
		}
		
		if(isset($parsed_xml->OperationRequest->Errors->Error))
		{		
			die(AWSERRORHEADER.$parsed_xml->OperationRequest->Errors->Error->Code."<br><br>".$parsed_xml->OperationRequest->Errors->Error->Message);
		}	
		
		$current = $parsed_xml;
		
		return $current;
}

function ReviewAZON_get_amazon_current_node($asin, $reviewSorting = "", $reviewPageNumber = 1, $mode = "Single",$trackingID = "")
{		
		ReviewAZON_AWS_config_error();
		
		$params = array();
		
		if($reviewSorting != "default")
		{
			$params["ReviewSort"] = $reviewSorting;
		}
		
		$params["MerchantId"] = "FeaturedBuyBoxMerchant";
		$params["ReviewPage"] = $reviewPageNumber;
		$params["AssociateTag"] = $trackingID;
		$params["Operation"] = "ItemLookup";
		$params["ResponseGroup"] = "Large,OfferFull";
		$params["ItemId"] = $asin;
		$requestURL = aws_signed_request($params);

		$session = curl_init($requestURL);
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($session);
		curl_close($session); 
		$parsed_xml = simplexml_load_string($response);	
		
		if(empty($parsed_xml))
		{
			die("<p>There was a problem completing this request. Check your configuration settings to ensure your authentication credentials are setup properly.</p>");
		}
		
		if($parsed_xml->Items->Request->IsValid == "False")
		{
			die(AWSERRORHEADER.$parsed_xml->Items->Request->Errors->Error->Code."<br><br>".$parsed_xml->Items->Request->Errors->Error->Message);
		}
		
		if(isset($parsed_xml->OperationRequest->Errors->Error))
		{		
			die(AWSERRORHEADER.$parsed_xml->OperationRequest->Errors->Error->Code."<br><br>".$parsed_xml->OperationRequest->Errors->Error->Message);
		}	
		
		
		if($mode == "Reviews")
		{
			$current = $parsed_xml;
		}
		else
		{
			$current = $parsed_xml->Items->Item;
		}
		
		return $current;
}

function ReviewAZON_AWS_config_error()
{
	if(ReviewAZON_get_affiliate_setting("AWS_Webservice_Key") == "" || ReviewAZON_get_affiliate_setting("AWS_Affiliate_Country") == "" || ReviewAZON_get_affiliate_setting("AWS_Secret_Key") == "")
	{
		echo '<p><img src="'.ReviewAZON_ADMIN_IMAGES.'error.gif" style="padding-right:5px;vertical-align:sub;" /><b>Amazon Web Service Configuration Error!</b></p>';
		echo '<p>You must have a valid Amazon Web Services key and Secret Code to get started.</P>';		
		echo '<p>If you have already signed up for your AWS key, you will need to enter it on the <a href="admin.php?page=Affiliate Settings">ReviewAZON Affiliate Settings</a> page.</p>';
		echo '<p>If you need to create an AWS account, you can visit <a target="_blank" href="http://aws.amazon.com/">Amazon Web Services</a> to sign up!</p>';
		die('');
	}	

}

function ReviewAZON_get_amazon_current_node_link($asin,$trackingID)
{		
	
		ReviewAZON_AWS_config_error();
		
		$params = array();
		
		$params["MerchantId"] = "FeaturedBuyBoxMerchant";
		$params["AssociateTag"] = $trackingID;
		$params["Operation"] = "ItemLookup";
		$params["ResponseGroup"] = "ItemAttributes";
		$params["ItemId"] = $asin;
		$requestURL = aws_signed_request($params);

		$session = curl_init($requestURL);
		curl_setopt($session, CURLOPT_HEADER, false);
		curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
		$response = curl_exec($session);
		curl_close($session); 
		$parsed_xml = simplexml_load_string($response);	
		
		if(empty($parsed_xml))
		{
			die("<p>There was a problem completing this request. Check your configuration settings to ensure your authentication credentials are setup properly.</p>");
		}
		
		if($parsed_xml->Items->Request->IsValid == "False")
		{
			die(AWSERRORHEADER.$parsed_xml->Items->Request->Errors->Error->Code."<br><br>".$parsed_xml->Items->Request->Errors->Error->Message);
		}
		
		if(isset($parsed_xml->OperationRequest->Errors->Error))
		{		
			die(AWSERRORHEADER.$parsed_xml->OperationRequest->Errors->Error->Code."<br><br>".$parsed_xml->OperationRequest->Errors->Error->Message);
		}	
		
		
		$current = $parsed_xml->Items->Item;
		
		return $current;
}


function ReviewAZON_get_video_reviews($postID)
{
	global $wpdb;
	
	$query = "SELECT * FROM ".$wpdb->prefix."reviewazon_video_reviews WHERE post_id='".$postID."'";
	$videos = $wpdb->get_results($query);

	if(count($videos) > 0)
	{
		foreach($videos as $video)
		{
			$reviewVideos = ReviewAZON_get_template(ReviewAZON_get_affiliate_setting("Default_Template_Group"),"video_reviews");	
			$reviewVideos = str_replace("%%VIDEOTITLE%%",$video->Video_Title,$reviewVideos);
			$videoObject = '<object width="425" height="344"><param name="movie" value="http://www.youtube.com/v/'.$video->Video_Watch_Url.'"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/'.$video->Video_Watch_Url.'" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" width="425" height="344"></embed></object>';
			$reviewVideos = str_replace("%%VIDEOWATCHLINK%%",$videoObject,$reviewVideos);
			$returnVideos.= $reviewVideos;
		}
	}
	else
	{
		$returnVideos = stripslashes(ReviewAZON_get_affiliate_setting("Empty_Video_Reviews_Text"));
	}
	return $returnVideos;
}

function GetItemLink($current,$title)
{
	$returnLink = "";
	if(isset($current->EditorialReviews->EditorialReview))
	{
		foreach($current->ItemLinks->ItemLink as $link)
		{
			if($link->Description == $title)
			{
				$returnLink = $link->URL;
			}
		}
	}
	return $returnLink;
}

function GetProductEditoralAmazonDotCom($current)
{
	$returnContent = "";
	if(isset($current->EditorialReviews->EditorialReview))
	{
		foreach($current->EditorialReviews->EditorialReview as $review)
		{
			if(strpos(strtolower($review->Source),'amazon') !== false)
			{
				return $review->Content;
			}
		}
	}
	return $returnContent;
}

function GetProductEditoralStandard($current)
{
	$returnContent = "";
	if(isset($current->EditorialReviews->EditorialReview))
	{
		foreach($current->EditorialReviews->EditorialReview as $review)
		{
			if(strpos(strtolower($review->Source),'amazon') === false)
			{
				return $review->Content;
			}
		}
	}
	return $returnContent;
}

function GetProductFeatures($current)
{
	if(isset($current->ItemAttributes->Feature))
	{
		$features .= '<ul class="RZProductDetails">';
		foreach($current->ItemAttributes->Feature as $feature)
		{
			$features .= '<li>'.str_replace('&#191;','\'',$feature).'</li>';
		}
		$features .= '</ul>';
	}
	else
	{
		$features = stripslashes(ReviewAZON_get_affiliate_setting("Empty_Product_Details_Text"));
	}
	
    return $features;
}

function InitialReviews($response)
{

	$parsed_xml = $response;	
	$numOfItems = $parsed_xml->Items->TotalResults;
	
	$current = $parsed_xml->Items->Item;
		
	$maxReviewsToDisplay = ReviewAZON_get_affiliate_setting("No_Reviews_To_Display");
	
	static $maxReviewDisplayCount = 1;
	if(isset($current->CustomerReviews->Review))
	{
		foreach($current->CustomerReviews->Review as $review)
		{
			if($maxReviewDisplayCount <= $maxReviewsToDisplay)
			{
				$reviewerName = $review->Reviewer->Name;
				$reviewerNickname = $review->Reviewer->Nickname;
				$reviewerLocation = $review->Reviewer->Location;
				
				$customerReviews = ReviewAZON_get_template(ReviewAZON_get_affiliate_setting("Default_Template_Group"),"customer_review");
				$customerReviews = str_replace("%%IMAGEPATH%%",ReviewAZON_IMAGES_FOLDER_NOSLASH, $customerReviews);
				$customerReviews = str_replace("%%TITLE%%",$review->Summary,$customerReviews);
				$customerReviews = str_replace("%%RATINGBAR%%",ReviewAZON_customer_rating($review->Rating, false, ""),$customerReviews);
				$customerReviews = str_replace("%%REVIEWDESCRIPTION%%",$review->Content,$customerReviews);
				$customerReviews = str_replace("%%REVIEWDATE%%",date("F j, Y",strtotime($review->Date)),$customerReviews);
				$customerReviews = str_replace("%%REVIEWERNAME%%",$reviewerName,$customerReviews);
				$customerReviews = str_replace("%%REVIEWERNICKNAME%%",$reviewerNickname,$customerReviews);
				$customerReviews = str_replace("%%REVIEWERLOCATION%%",$reviewerLocation,$customerReviews);
				$returnReview .= $customerReviews;
				$maxReviewDisplayCount++;
			}
		}
	}
	else
	{
		$returnReview = stripslashes(ReviewAZON_get_affiliate_setting("Empty_Customer_Reviews_Text"));
	}

    return $returnReview;	
}

function EditoralReviews($current)
{
	$returnReview = "";
	if(isset($current->EditorialReviews->EditorialReview))
	{
		foreach($current->EditorialReviews->EditorialReview as $editorialReview)
		{
			$editorialReviews = ReviewAZON_get_template(ReviewAZON_get_affiliate_setting("Default_Template_Group"),"description");
			$editorialReviews = str_replace("%%EDITORIALTITLE%%",$editorialReview->Source,$editorialReviews);
			$editorialReviews = str_replace("%%EDITORIALCONTENT%%",$editorialReview->Content,$editorialReviews);
			
			$returnReview .= $editorialReviews;
		}
	}
    return $returnReview;	
}

function ReviewAZON_similiar_products_widget_search($query,$maxCount,$trackingID)
{
	$searchIndex = "Blended";
	if(AFFILIATECOUNTRY == "com")
	{
		$searchIndex = "All";
	}
	
	if(empty($query))
	{
		return stripslashes(ReviewAZON_get_affiliate_setting("Empty_Related_Products_Text"));
	}
	else
	{
		$parsed_xml = ReviewAZON_get_amazon_current_node_search($searchIndex, $query, 1, "default", $trackingID);
	}	

	$count = 1;	
	$numOfItems = $parsed_xml->Items->TotalResults;
	$numOfPages = $parsed_xml->Items->TotalPages;
	
	if($numOfItems == 0)
	{
		return stripslashes(ReviewAZON_get_affiliate_setting("Empty_Related_Products_Text"));
	}
	
	foreach($parsed_xml->Items->Item as $current)
	{
		if($count <= $maxCount)
		{
			$asin = $current->ASIN;
			$text = ReviewAZON_get_template(ReviewAZON_get_affiliate_setting("Default_Template_Group"),"similar_products");
			$averageRatingNumber = $current->CustomerReviews->AverageRating;	
			$title = $current->ItemAttributes->Title;
			$description = strip_tags($current->EditorialReviews->EditorialReview->Content);
			$descriptionTemplate = ReviewAZON_get_template(ReviewAZON_get_affiliate_setting("Default_Template_Group"),"Description");
			$description = str_replace('%%DESCRIPTION%%',$description,$descriptionTemplate);	
			
			//Links
			$detailPageUrl = $current->DetailPageURL;
			$technicalDetailsPageUrl = GetItemLink($current,"Technical Details");
			$addToBabyRegistryPageUrl = GetItemLink($current,"Add To Baby Registry");
			$addToWeddingRegistryPageUrl = GetItemLink($current,"Add To Wedding Registry");
			$addToWishListPageUrl = GetItemLink($current,"Add To Wishlist");
			$tellAFriendPageUrl = GetItemLink($current,"Tell A Friend");
			$allCustomerReviewsPageUrl = GetItemLink($current,"All Customer Reviews");
			
			//Convert links to friendly url if link cloaking is turned on.
			if(ReviewAZON_get_affiliate_setting("Allow_Link_Cloaking"))
			{
				$sanitizedTitle = sanitize_title_with_dashes($title);
				$detailPageUrl = ReviewAZON_cloak_link('detailpage',$asin,$sanitizedTitle,$trackingID);
				$technicalDetailsPageUrl = ReviewAZON_cloak_link('techdetails',$asin,$sanitizedTitle,$trackingID);
				$addToBabyRegistryPageUrl = ReviewAZON_cloak_link('baby',$asin,$sanitizedTitle,$trackingID);
				$addToWeddingRegistryPageUrl = ReviewAZON_cloak_link('wedding',$asin,$sanitizedTitle,$trackingID);
				$addToWishListPageUrl = ReviewAZON_cloak_link('wish',$asin,$sanitizedTitle,$trackingID);
				$tellAFriendPageUrl = ReviewAZON_cloak_link('taf',$asin,$sanitizedTitle,$trackingID);
				$allCustomerReviewsPageUrl = ReviewAZON_cloak_link('custreview',$asin,$sanitizedTitle,$trackingID);		
			}
		
			//Images
			$smallImageUrl = $current->SmallImage->URL;
			$mediumImageUrl = $current->MediumImage->URL;
			$largeImageUrl = $current->LargeImage->URL;
			
			if(empty($smallImageUrl))
			{
				$smallImageUrl = get_option('siteurl').'/images/noimage-75.gif';			
			}
						
			if(empty($mediumImageUrl))
			{
				$mediumImageUrl = get_option('siteurl').'/images/noimage.gif';			
			}
			
			if(empty($largeImageUrl))
			{
				$largeImageUrl = get_option('siteurl').'/images/noimage.gif';			
			}
					
			//Rewrite Images	
			if(ReviewAZON_get_affiliate_setting("Allow_Image_Link_Cloaking"))
			{
				$smallImageUrl = ReviewAZON_get_rewrite_image($smallImageUrl,$asin);
				$mediumImageUrl = ReviewAZON_get_rewrite_image($mediumImageUrl,$asin);
				$largeImageUrl = ReviewAZON_get_rewrite_image($largeImageUrl,$asin);
			}
			
			$brand = $current->ItemAttributes->Brand;
			$UPC = '00'.$current->ItemAttributes->UPC;
			$features = GetProductFeatures($current);
			$listPrice = $current->ItemAttributes->ListPrice->FormattedPrice;
			$lowestNewPrice = $current->OfferSummary->LowestNewPrice->FormattedPrice;
			$manufacturer = $current->ItemAttributes->Manufacturer;
			$model = $current->ItemAttributes->Model;
			
			$freeShipping = $current->Offers->Offer->OfferListing->IsEligibleForSuperSaverShipping;
			$availibility = $current->Offers->Offer->OfferListing->Availability;
			if(empty($availibility))
			{
				$availibility = '<a href="'.$detailPageUrl.'">View Product Availability</a>';
			}
					
			$amountSaved = $current->Offers->Offer->OfferListing->AmountSaved->FormattedPrice;
			$percentageSaved = $current->Offers->Offer->OfferListing->PercentageSaved;
			
			if(!empty($percentageSaved))
			{
				$percentageSaved = $percentageSaved.'%';
			}
			
			if(empty($listPrice))
			{
				$listPrice = "Varies based on product options";		
			}
			
			if(empty($lowestNewPrice))
			{
				if(isset($current->OfferSummary->LowestNewPrice->FormattedPrice))
				{
					$lowestNewPrice = $current->OfferSummary->LowestNewPrice->FormattedPrice;
				}
				else
				{
					$lowestNewPrice = "View Sale Price";		
				}
			}
			
			if($freeShipping == 1)
			{
				$freeShipping = stripslashes(ReviewAZON_get_affiliate_setting("Free_Shipping_Text"));
			}
			else
			{
				$freeShipping = "";
			}
			
			$customerRating = ReviewAZON_customer_rating($averageRatingNumber, false, "");
			$customerReviewCount = $current->CustomerReviews->TotalReviews;
		
			
			if(empty($customerReviewCount))
			{
				$customerReviewCount = 0;
			}
			
			$text = str_replace("%%IMAGEPATH%%",ReviewAZON_IMAGES_FOLDER_NOSLASH, $text);
			$text = str_replace('%%PLUGINURL%%',WP_PLUGIN_URL.'/ReviewAZON',$text);
			$text = str_replace('%%ASIN%%',$asin,$text);
			
			//Links
			$text = str_replace('%%DETAILPAGELINK%%',$detailPageUrl,$text);
			$text = str_replace('%%TECHNICALDETAILPAGELINK%%',$technicalDetailsPageUrl,$text);
			$text = str_replace('%%ADDTOBABYREGISTRYLINK%%',$addToBabyRegistryPageUrl,$text);
			$text = str_replace('%%ADDTOWEDDINGREGISTRYLINK%%',$addToWeddingRegistryPageUrl,$text);
			$text = str_replace('%%ADDTOWISHLISTLINK%%',$addToWishListPageUrl,$text);
			$text = str_replace('%%TELLAFRIENDLINK%%',$tellAFriendPageUrl,$text);
			$text = str_replace('%%ALLCUSTOMERREVIEWSPAGELINK%%',$allCustomerReviewsPageUrl,$text);	
			
			//Images
			$text = str_replace('%%AMAZONSMALLIMAGE%%',$smallImageUrl,$text);
			$text = str_replace('%%AMAZONMEDIUMIMAGE%%',$mediumImageUrl,$text);
			$text = str_replace('%%AMAZONLARGEIMAGE%%',$largeImageUrl,$text);	
			
			//Product Attributes
			$text = str_replace('%%UPCCODE%%',$UPC,$text);
			$text = str_replace('%%DESCRIPTION%%',$description,$text);
			$text = str_replace('%%MANUFACTURER%%',$manufacturer,$text);
			$text = str_replace('%%UPCCODE%%',$UPC,$text);	
			$text = str_replace('%%TITLE%%',$title,$text);
			$text = str_replace('%%RATINGBAR%%',$customerRating,$text);
			$text = str_replace('%%REVIEWCOUNT%%',$customerReviewCount,$text);
			$text = str_replace('%%LISTPRICE%%',$listPrice,$text);
			$text = str_replace('%%BRAND%%',$brand,$text);
			$text = str_replace('%%MODEL%%',$model,$text);
			$text = str_replace('%%RATINGNUMBER%%',$averageRatingNumber,$text);
			$text = str_replace('%%SALEPRICE%%',$lowestNewPrice,$text);
			$text = str_replace('%%FREESHIPPING%%',$freeShipping,$text);
			$text = str_replace('%%SHIPPINGAVAILIBILITY%%',$availibility,$text);
			$text = str_replace('%%AMOUNTSAVED%%',$amountSaved,$text);
			$text = str_replace('%%PERCENTSAVED%%',$percentageSaved,$text);
			$text = str_replace('%%NEWFROMTEXT%%',$newFromText,$text);
			$text = str_replace('%%USEDFROMTEXT%%',$usedFromText,$text);
			
			$textToReturn.= $text;
			$count++;
		}
	}
	
	return $textToReturn;
}


function SimilarProducts($current,$query="",$trackingID = "",$numberToDisplay = 5)
{
	if(isset($current->SimilarProducts->SimilarProduct))
	{
		$counter = 1;
		foreach($current->SimilarProducts->SimilarProduct as $similarProduct)
		{
			if($counter <= $numberToDisplay)
			{	
				$value = GetSingleProductValues($similarProduct->ASIN,$trackingID);
				$similarProducts = ReviewAZON_get_template(ReviewAZON_get_affiliate_setting("Default_Template_Group"),"similar_products");
			    $similarProducts = str_replace("%%IMAGEPATH%%",ReviewAZON_IMAGES_FOLDER_NOSLASH, $similarProducts);			
				$similarProducts = str_replace("%%DETAILPAGELINK%%",$value['link'], $similarProducts);
			    $similarProducts = str_replace("%%AMAZONSMALLIMAGE%%",$value['smallimage'], $similarProducts);
			    $similarProducts = str_replace("%%AMAZONMEDIUMIMAGE%%",$value['mediumimage'], $similarProducts);
			    $similarProducts = str_replace("%%AMAZONLARGEIMAGE%%",$value['largeimage'], $similarProducts);
			    $similarProducts = str_replace("%%TITLE%%",$value['title'], $similarProducts);
			    $similarProducts = str_replace("%%REVIEWCOUNT%%",$value['reviewcount'], $similarProducts);
			    $similarProducts = str_replace("%%RATINGBAR%%",$value['customerrating'], $similarProducts);
			    $similarProducts = str_replace("%%DESCRIPTION%%",ReviewAZON_truncate_description($value['description']), $similarProducts);	   
			    $similarProducts = str_replace("%%SALEPRICE%%",$value['salesprice'], $similarProducts);	   
			    $similarProducts = str_replace("%%LISTPRICE%%",$value['listprice'], $similarProducts);	
			    $similarProducts = str_replace('%%AMOUNTSAVED%%',$value['amountsaved'],$similarProducts);
				$similarProducts = str_replace('%%PERCENTSAVED%%',$value['percentsaved'],$similarProducts);   
			    $returnSimilarProducts .= $similarProducts;
			    $counter ++;
			}
		}
	}
	else
	{
		//$returnSimilarProducts = stripslashes(ReviewAZON_get_affiliate_setting("Empty_Related_Products_Text"));
		$returnSimilarProducts = ReviewAZON_similiar_products_widget_search($query,$maxCount,$trackingID);	
	}

    return $returnSimilarProducts;	
}

function ReviewAZON_product_accessories_widget_search($query,$maxCount,$trackingID)
{
	$searchIndex = "Blended";
	if(AFFILIATECOUNTRY == "com")
	{
		$searchIndex = "All";
	}
	
	if(empty($query))
	{
		return stripslashes(ReviewAZON_get_affiliate_setting("Empty_Product_Accessories_Text"));
	}
	else
	{
		$parsed_xml = ReviewAZON_get_amazon_current_node_search($searchIndex, $query, 1, "default", $trackingID);
	}	

	$count = 1;	
	$numOfItems = $parsed_xml->Items->TotalResults;
	$numOfPages = $parsed_xml->Items->TotalPages;
	
	if($numOfItems == 0)
	{
		return stripslashes(ReviewAZON_get_affiliate_setting("Empty_Product_Accessories_Text"));
	}
	
	foreach($parsed_xml->Items->Item as $current)
	{
		if($count <= $maxCount)
		{
			$asin = $current->ASIN;
			$text = ReviewAZON_get_template(ReviewAZON_get_affiliate_setting("Default_Template_Group"),"product_accessories");
			$averageRatingNumber = $current->CustomerReviews->AverageRating;	
			$title = $current->ItemAttributes->Title;
			$description = strip_tags($current->EditorialReviews->EditorialReview->Content);
			$descriptionTemplate = ReviewAZON_get_template(ReviewAZON_get_affiliate_setting("Default_Template_Group"),"Description");
			$description = str_replace('%%DESCRIPTION%%',$description,$descriptionTemplate);	
			
			//Links
			$detailPageUrl = $current->DetailPageURL;
			$technicalDetailsPageUrl = GetItemLink($current,"Technical Details");
			$addToBabyRegistryPageUrl = GetItemLink($current,"Add To Baby Registry");
			$addToWeddingRegistryPageUrl = GetItemLink($current,"Add To Wedding Registry");
			$addToWishListPageUrl = GetItemLink($current,"Add To Wishlist");
			$tellAFriendPageUrl = GetItemLink($current,"Tell A Friend");
			$allCustomerReviewsPageUrl = GetItemLink($current,"All Customer Reviews");
			
			//Convert links to friendly url if link cloaking is turned on.
			if(ReviewAZON_get_affiliate_setting("Allow_Link_Cloaking"))
			{
				$sanitizedTitle = sanitize_title_with_dashes($title);
				$detailPageUrl = ReviewAZON_cloak_link('detailpage',$asin,$sanitizedTitle,$trackingID);
				$technicalDetailsPageUrl = ReviewAZON_cloak_link('techdetails',$asin,$sanitizedTitle,$trackingID);
				$addToBabyRegistryPageUrl = ReviewAZON_cloak_link('baby',$asin,$sanitizedTitle,$trackingID);
				$addToWeddingRegistryPageUrl = ReviewAZON_cloak_link('wedding',$asin,$sanitizedTitle,$trackingID);
				$addToWishListPageUrl = ReviewAZON_cloak_link('wish',$asin,$sanitizedTitle,$trackingID);
				$tellAFriendPageUrl = ReviewAZON_cloak_link('taf',$asin,$sanitizedTitle,$trackingID);
				$allCustomerReviewsPageUrl = ReviewAZON_cloak_link('custreview',$asin,$sanitizedTitle,$trackingID);		
			}
		
			//Images
			$smallImageUrl = $current->SmallImage->URL;
			$mediumImageUrl = $current->MediumImage->URL;
			$largeImageUrl = $current->LargeImage->URL;
			
			if(empty($smallImageUrl))
			{
				$smallImageUrl = get_option('siteurl').'/images/noimage-75.gif';			
			}
						
			if(empty($mediumImageUrl))
			{
				$mediumImageUrl = get_option('siteurl').'/images/noimage.gif';			
			}
			
			if(empty($largeImageUrl))
			{
				$largeImageUrl = get_option('siteurl').'/images/noimage.gif';			
			}
					
			//Rewrite Images	
			if(ReviewAZON_get_affiliate_setting("Allow_Image_Link_Cloaking"))
			{
				$smallImageUrl = ReviewAZON_get_rewrite_image($smallImageUrl,$asin);
				$mediumImageUrl = ReviewAZON_get_rewrite_image($mediumImageUrl,$asin);
				$largeImageUrl = ReviewAZON_get_rewrite_image($largeImageUrl,$asin);
			}
			
			$brand = $current->ItemAttributes->Brand;
			$UPC = '00'.$current->ItemAttributes->UPC;
			$features = GetProductFeatures($current);
			$listPrice = $current->ItemAttributes->ListPrice->FormattedPrice;
			$lowestNewPrice = $current->OfferSummary->LowestNewPrice->FormattedPrice;
			$manufacturer = $current->ItemAttributes->Manufacturer;
			$model = $current->ItemAttributes->Model;
			
			$freeShipping = $current->Offers->Offer->OfferListing->IsEligibleForSuperSaverShipping;
			$availibility = $current->Offers->Offer->OfferListing->Availability;
			if(empty($availibility))
			{
				$availibility = '<a href="'.$detailPageUrl.'">View Product Availability</a>';
			}
					
			$amountSaved = $current->Offers->Offer->OfferListing->AmountSaved->FormattedPrice;
			$percentageSaved = $current->Offers->Offer->OfferListing->PercentageSaved;
			
			if(!empty($percentageSaved))
			{
				$percentageSaved = $percentageSaved.'%';
			}
			
			if(empty($listPrice))
			{
				$listPrice = "Varies based on product options";		
			}
			
			if(empty($lowestNewPrice))
			{
				if(isset($current->OfferSummary->LowestNewPrice->FormattedPrice))
				{
					$lowestNewPrice = $current->OfferSummary->LowestNewPrice->FormattedPrice;
				}
				else
				{
					$lowestNewPrice = "View Sale Price";		
				}
			}
			
			if($freeShipping == 1)
			{
				$freeShipping = stripslashes(ReviewAZON_get_affiliate_setting("Free_Shipping_Text"));
			}
			else
			{
				$freeShipping = "";
			}
			
			$customerRating = ReviewAZON_customer_rating($averageRatingNumber, false, "");
			$customerReviewCount = $current->CustomerReviews->TotalReviews;
		
			
			if(empty($customerReviewCount))
			{
				$customerReviewCount = 0;
			}
			
			$text = str_replace("%%IMAGEPATH%%",ReviewAZON_IMAGES_FOLDER_NOSLASH, $text);
			$text = str_replace('%%PLUGINURL%%',WP_PLUGIN_URL.'/ReviewAZON',$text);
			$text = str_replace('%%ASIN%%',$asin,$text);
			
			//Links
			$text = str_replace('%%DETAILPAGELINK%%',$detailPageUrl,$text);
			$text = str_replace('%%TECHNICALDETAILPAGELINK%%',$technicalDetailsPageUrl,$text);
			$text = str_replace('%%ADDTOBABYREGISTRYLINK%%',$addToBabyRegistryPageUrl,$text);
			$text = str_replace('%%ADDTOWEDDINGREGISTRYLINK%%',$addToWeddingRegistryPageUrl,$text);
			$text = str_replace('%%ADDTOWISHLISTLINK%%',$addToWishListPageUrl,$text);
			$text = str_replace('%%TELLAFRIENDLINK%%',$tellAFriendPageUrl,$text);
			$text = str_replace('%%ALLCUSTOMERREVIEWSPAGELINK%%',$allCustomerReviewsPageUrl,$text);	
			
			//Images
			$text = str_replace('%%AMAZONSMALLIMAGE%%',$smallImageUrl,$text);
			$text = str_replace('%%AMAZONMEDIUMIMAGE%%',$mediumImageUrl,$text);
			$text = str_replace('%%AMAZONLARGEIMAGE%%',$largeImageUrl,$text);	
			
			//Product Attributes
			$text = str_replace('%%UPCCODE%%',$UPC,$text);
			$text = str_replace('%%DESCRIPTION%%',$description,$text);
			$text = str_replace('%%MANUFACTURER%%',$manufacturer,$text);
			$text = str_replace('%%UPCCODE%%',$UPC,$text);	
			$text = str_replace('%%TITLE%%',$title,$text);
			$text = str_replace('%%RATINGBAR%%',$customerRating,$text);
			$text = str_replace('%%REVIEWCOUNT%%',$customerReviewCount,$text);
			$text = str_replace('%%LISTPRICE%%',$listPrice,$text);
			$text = str_replace('%%BRAND%%',$brand,$text);
			$text = str_replace('%%MODEL%%',$model,$text);
			$text = str_replace('%%RATINGNUMBER%%',$averageRatingNumber,$text);
			$text = str_replace('%%SALEPRICE%%',$lowestNewPrice,$text);
			$text = str_replace('%%FREESHIPPING%%',$freeShipping,$text);
			$text = str_replace('%%SHIPPINGAVAILIBILITY%%',$availibility,$text);
			$text = str_replace('%%AMOUNTSAVED%%',$amountSaved,$text);
			$text = str_replace('%%PERCENTSAVED%%',$percentageSaved,$text);
			$text = str_replace('%%NEWFROMTEXT%%',$newFromText,$text);
			$text = str_replace('%%USEDFROMTEXT%%',$usedFromText,$text);
			
			$textToReturn.= $text;
			$count++;
		}
	}
	
	return $textToReturn;
}

function ProductAccessories($current, $query="", $trackingID = "",$numberToDisplay = 5)
{
	if(isset($current->Accessories->Accessory))
	{
		$counter = 1;
		foreach($current->Accessories->Accessory as $accessory)
		{
			if($counter <= $numberToDisplay)
			{				
				$value = GetSingleProductValues($accessory->ASIN,$trackingID);
				$productAccessories = ReviewAZON_get_template(ReviewAZON_get_affiliate_setting("Default_Template_Group"),"product_accessories");
				$productAccessories = str_replace("%%IMAGEPATH%%",ReviewAZON_IMAGES_FOLDER_NOSLASH, $productAccessories);	
			    $productAccessories = str_replace("%%DETAILPAGELINK%%",$value['link'], $productAccessories);
			    $productAccessories = str_replace("%%AMAZONSMALLIMAGE%%",$value['smallimage'], $productAccessories);
			    $productAccessories = str_replace("%%AMAZONMEDIUMIMAGE%%",$value['mediumimage'], $productAccessories);
			    $productAccessories = str_replace("%%AMAZONLARGEIMAGE%%",$value['largeimage'], $productAccessories);
			    $productAccessories = str_replace("%%TITLE%%",$value['title'], $productAccessories);
			    $productAccessories = str_replace("%%REVIEWCOUNT%%",$value['reviewcount'], $productAccessories);
			    $productAccessories = str_replace("%%RATINGBAR%%",$value['customerrating'], $productAccessories);
			    $productAccessories = str_replace("%%DESCRIPTION%%",ReviewAZON_truncate_description($value['description']), $productAccessories);	   
			    $productAccessories = str_replace("%%SALEPRICE%%",$value['salesprice'], $productAccessories);	  
			    $productAccessories = str_replace("%%LISTPRICE%%",$value['listprice'], $productAccessories);	  
			    $productAccessories = str_replace('%%AMOUNTSAVED%%',$value['amountsaved'],$productAccessories);
				$productAccessories = str_replace('%%PERCENTSAVED%%',$value['percentsaved'],$productAccessories);   
			    $returnProductAccessories .= $productAccessories;
			    $counter ++;
			}
		}
	}
	else
	{
		//$returnProductAccessories = stripslashes(ReviewAZON_get_affiliate_setting("Empty_Product_Accessories_Text"));
		$returnProductAccessories = ReviewAZON_product_accessories_widget_search($query,$numberToDisplay,$trackingID);	
				
	}
    return $returnProductAccessories;	
}
#======================================================================================================================================================================================

/*===================================================================================
 	Widget Functions 
 ====================================================================================*/

function widget_auction() 
{
	global $post,$wpdb;
	$options = get_option("ReviewAZON_Auction_Widget");
	$showEbayAuctions = ReviewAZON_get_affiliate_setting("Show_Ebay_Auctions");
	$eBayWidgetTitle = ReviewAZON_get_post_setting($post->ID,"Ebay_Widget_Title");
	$eBayEmptyMessage = ReviewAZON_get_affiliate_setting("Empty_Ebay_Auctions_Text");
	$ebayPostQuery = get_post_meta($post->ID, 'EBAY', true);
	$ebayPostTitle = get_post_meta($post->ID, 'EBAYTITLE', true);	
	
	if($options['useCustomSearchTextWhenEmpty'] == 'true')
	{
		$eBayAuctionShortCode = '[phpbay]'.$options['text'].', '.$options['auctionCount'].'[/phpbay]';
	}
	else
	{
		$eBayAuctionShortCode = ReviewAZON_get_post_setting($post->ID,"Ebay_Search_Query");
		if(empty($eBayAuctionShortCode))
		{
			if(!empty($ebayPostQuery))
			{
				$eBayAuctionShortCode = $ebayPostQuery;				
			}
			else
			{
				$eBayAuctionShortCode = '[phpbay]'.$options['text'].', '.$options['auctionCount'].'[/phpbay]';
			}
		}

	}

	if($showEbayAuctions)
	{
		if(is_single())
		{
			if(!empty($eBayWidgetTitle))
			{
				$widgetTitle = $eBayWidgetTitle;
			}
			else if(!empty($ebayPostTitle))
			{
				$widgetTitle = $ebayPostTitle;
			}
			else
			{
				$widgetTitle = $options['title'];
			}
			
			if(function_exists(phpBayLite))
			{
				$ebayAuctions = phpBayLite($eBayAuctionShortCode);
			}
			else
			{
				if(function_exists(phpBayPro))
				{
					$ebayAuctions = phpBayPro($eBayAuctionShortCode);
				}
				else
				{
					$ebayAuctions = "Auction software not installed.";
				}
			}

			$strPos = strpos(strtolower($ebayAuctions),"no items matching your keywords were found.");
			if(!$strPos === false)
			{
				$ebayAuctions = $eBayEmptyMessage;
			}
		}
		else
		{
			if (!is_array( $options ))
			{
				$ebayAuctions = '<img src="'.ReviewAZON_ADMIN_IMAGES.'error.gif" style="padding-right:5px;vertical-align:middle;" />Widget options have not been setup correctly.';
			}
			else
			{
				$widgetTitle = $options['title'];
				if(function_exists(phpBayLite))
				{
					$ebayAuctions = phpBayLite('[phpbay]'.$options['text'].', '.$options['auctionCount'].'[/phpbay]');
				}
				else
				{
					if(function_exists(phpBayPro))
					{
						$ebayAuctions = phpBayPro('[phpbay]'.$options['text'].', '.$options['auctionCount'].'[/phpbay]');
					}
					else
					{
						$ebayAuctions = "Auction software not installed.";
					}					
				}
				$strPos = strpos(strtolower($ebayAuctions),"no items matching your keywords were found.");
				if(!$strPos === false)
				{
					$ebayAuctions = $eBayEmptyMessage;
				}				
			}	
		}
	}
	else
	{
		$widgetTitle = "eBay Integration Not Enabled";
		$ebayAuctions = '<img src="'.ReviewAZON_ADMIN_IMAGES.'error.gif" style="padding-right:5px;vertical-align:middle;" />eBay integration is currently disabled. To use this functionality, please enable it on the ReviewAZON Advanced Settings page.';
	}
	
	if($options['alwaysShowCustomTitle'] == 'true')
	{
		$widgetTitle = $options['title'];		
	}
?>
  <h2 class="widgettitle"><?php echo $widgetTitle ?></h2>  
  <div id="widget_ebay_auctions" style="background-color:#FFFFFF;border:solid 1px gainsboro;padding:5px;"><?php echo $ebayAuctions ?></div>
<?php
}

function auction_control()
{
	  $options = get_option("ReviewAZON_Auction_Widget");
	
	  if (!is_array( $options ))
		{
			$options = array(
	        'title' => 'My Widget Title',
	        'text' => '',
	        'auctionCount' => '10',
	        'alwaysShowCustomTitle' => 'false',
			'useCustomSearchTextWhenEmpty' => 'false'
	      );
	  }      
	
	  if ($_POST['auction-Submit'])
	  {
	    $options['title'] = htmlspecialchars($_POST['widgetTitle']);
	    $options['text'] = htmlspecialchars($_POST['auctionText']);
	    $options['auctionCount'] = htmlspecialchars($_POST['numberOfAuctionsToDisplay']);
	    $options['alwaysShowCustomTitle'] = 'false';
	    $options['useCustomSearchTextWhenEmpty'] = 'false';
	    if(isset($_POST['alwaysUseCustomTitle']))
	    {
	    	$options['alwaysShowCustomTitle'] = 'true';
	    }
	    if(isset($_POST['useCustomSearchTextWhenEmpty']))
	    {
	    	$options['useCustomSearchTextWhenEmpty'] = 'true';
	    }
	    update_option("ReviewAZON_Auction_Widget", $options);
	  }
	
?>
<table cellpadding="3" cellspacing="3">
<tr><td colspan="2"><img src="<?php echo ReviewAZON_ADMIN_IMAGES ?>information.gif" style="vertical-align:middle;padding-left:5px;padding-right:5px" />By default, this widget uses the eBay search query generated when you create a new post. If you want to show alternative searches on pages other than product pages, enter the information below.</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr><td nowrap><label style="font-weight:bold">Widget Title:</label></td></tr>
<tr>
	<td><input type="text" id="widgetTitle" name="widgetTitle" value="<?php echo $options['title'];?>" /></td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr>
	<td><input type="checkbox" id="alwaysUseCustomTitle" name="alwaysUseCustomTitle" <?php if($options['alwaysShowCustomTitle'] == "true") { echo 'checked'; }?>" />&nbsp;&nbsp;<label>Always use custom title in place of post title.</label></td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr><td><label style="font-weight:bold">Search Text:</label></td></tr>
<tr>
	<td><input type="text" id="auctionText" name="auctionText" value="<?php echo $options['text'];?>" /></td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr>
	<td><input type="checkbox" id="useCustomSearchTextWhenEmpty" name="useCustomSearchTextWhenEmpty" <?php if($options['useCustomSearchTextWhenEmpty'] == "true") { echo 'checked'; }?>" />&nbsp;&nbsp;<label>Always use the default search text to display eBay listings. </label></td>
</tr>
<tr><td>&nbsp;</td></tr>
<tr>
	<td><label>Display&nbsp;</label><input type="text" id="numberOfAuctionsToDisplay" name="numberOfAuctionsToDisplay" style="width:75px;" value="<?php echo $options['auctionCount'];?>" /> auction(s) when using the default search text.</td>
</tr>
</table>
<input type="hidden" id="auction-Submit" name="auction-Submit" value="1" />
<?php
}


function widget_amazon_product_accessories()
{
	global $post,$wpdb;
	$options = get_option("ReviewAZON_Product_Accessories_Widget");
	$postASIN = get_post_meta($post->ID, 'ASIN', true);

	if($options['useDefaultNone'] == "true")
	{
		if(!empty($postASIN))
		{
			$current = ReviewAZON_get_amazon_current_node($postASIN,null,null,null,$options['trackingid']);	
			$productAccessories = ProductAccessories($current,$options['query'],$options['trackingid'],$options['numbertoshow']);			
		}
		else
		{
			if(ReviewAZON_get_post_setting($post->ID,"ASIN") != "")
			{
				$current = ReviewAZON_get_amazon_current_node(ReviewAZON_get_post_setting($post->ID,"ASIN"),null,null,null,$options['trackingid']);
				$productAccessories = ProductAccessories($current,$options['query'],$options['trackingid'],$options['numbertoshow']);
			}
			else
			{
				$productAccessories = ReviewAZON_product_accessories_widget_search($options['query'],$options['numbertoshow'],$options['trackingid']);
			}
		}
	}
	else
	{
		if($options['defaultasin'] != "" && $options['useDefaultHome'] == "true" && is_front_page())
		{
			$current = ReviewAZON_get_amazon_current_node($options['defaultasin'],null,null,null,$options['trackingid']);
			$productAccessories = ProductAccessories($current,$options['query'],$options['trackingid'],$options['numbertoshow']);	
			
		}
		else if($options['defaultasin'] != "" && $options['useDefaultPost']  == "true" && is_single())
		{
			$current = ReviewAZON_get_amazon_current_node($options['defaultasin'],null,null,null,$options['trackingid']);
			$productAccessories = ProductAccessories($current,$options['query'],$options['trackingid'],$options['numbertoshow']);	
			
		}
		else if($options['defaultasin'] != "" && $options['useDefaultCategory'] == "true" && is_category())
		{
			$current = ReviewAZON_get_amazon_current_node($options['defaultasin'],null,null,null,$options['trackingid']);
			$productAccessories = ProductAccessories($current,$options['query'],$options['trackingid'],$options['numbertoshow']);	
			
		}
		else if($options['defaultasin'] != "" && $options['useDefaultTag'] == "true" && is_tag())
		{
			$current = ReviewAZON_get_amazon_current_node($options['defaultasin'],null,null,null,$options['trackingid']);
			$productAccessories = ProductAccessories($current,$options['query'],$options['trackingid'],$options['numbertoshow']);	
			
		}	
		else
		{
			if(!empty($postASIN))
			{
				$current = ReviewAZON_get_amazon_current_node($postASIN,null,null,null,$options['trackingid']);	
				$productAccessories = ProductAccessories($current,$options['query'],$options['trackingid'],$options['numbertoshow']);	
				
			}
			else
			{
				if(ReviewAZON_get_post_setting($post->ID,"ASIN") != "")
				{
					$current = ReviewAZON_get_amazon_current_node(ReviewAZON_get_post_setting($post->ID,"ASIN"),null,null,null,$options['trackingid']);
					$productAccessories = ProductAccessories($current,$options['query'],$options['trackingid'],$options['numbertoshow']);	
					
				}
				else
				{
					$productAccessories = ReviewAZON_product_accessories_widget_search($options['query'],$options['numbertoshow'],$options['trackingid']);
				}				
			}	
		}
		
	}

?>
  <h2 class="widgettitle"><?php echo $options['title'];?></h2>  
  <div id="amazon_product_accessories" class="RZAmazonProductAccessories">
  	<?php echo $productAccessories ?>
  </div>

<?php 
}

function product_accessories_control()
{
	  $options = get_option("ReviewAZON_Product_Accessories_Widget");
	
	  if (!is_array( $options ))
		{
			$options = array(
	        'title' => 'Product Accessories',
			'defaultasin' => '',
			'trackingid' => '',
			'numbertoshow' => '5',
			'query' => ''
	      );
	  }      
	
	  if($options['numbertoshow'] == "")
	  {
	  	$options['numbertoshow'] = "5";
	  }
	  
	  if ($_POST['ReviewAZON_product_accessories_submit'])
	  {

	  	$options['useDefaultNone'] = "false";
	  	$options['useDefaultHome'] = "false";
	  	$options['useDefaultPost'] = "false";
	  	$options['useDefaultCategory'] = "false";
	  	$options['useDefaultTag'] = "false";
	  	
	  	if(isset($_POST['widget_product_acc_default_none']))
	    {
	    	$options['useDefaultNone'] = "true";
	    }
	    if(isset($_POST['widget_product_acc_default_home']))
	    {
	    	$options['useDefaultHome'] = "true";
	    }
	   	if(isset($_POST['widget_product_acc_default_post']))
	    {
	    	$options['useDefaultPost'] = "true";
	    }
	    if(isset($_POST['widget_product_acc_default_category']))
	    {
	    	$options['useDefaultCategory'] = "true";
	    }
	    if(isset($_POST['widget_product_acc_default_tag']))
	    {
	    	$options['useDefaultTag'] = "true";
	    }
	    
	    $options['title'] = htmlspecialchars($_POST['widgetProductAccessoriesTitle']);
	    $options['defaultasin'] = $_POST['widget_product_accessories_default_asin'];
	    $options['trackingid'] = $_POST['ReviewAZON_associate_id_product_accessories'];
	    $options['numbertoshow'] = $_POST['widget_product_accessories_show_number'];
	    $options['query'] = $_POST['widget_product_accessories_default_query'];
	    update_option("ReviewAZON_Product_Accessories_Widget", $options);
	  }
?>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#widget_product_acc_default_none').click(function(){
			if(jQuery(this).attr("checked"))
			{
				jQuery('#widget_product_acc_default_home').attr("disabled","disabled");
				jQuery('#widget_product_acc_default_home').attr("checked","");
				jQuery('#widget_product_acc_default_post').attr("disabled","disabled");
				jQuery('#widget_product_acc_default_post').attr("checked","");				
				jQuery('#widget_product_acc_default_category').attr("disabled","disabled");
				jQuery('#widget_product_acc_default_category').attr("checked","");				
				jQuery('#widget_product_acc_default_tag').attr("disabled","disabled");
				jQuery('#widget_product_acc_default_tag').attr("checked","");
				
				
			}
			else
			{
				jQuery('#widget_product_acc_default_home').attr("disabled","");
				jQuery('#widget_product_acc_default_post').attr("disabled","");
				jQuery('#widget_product_acc_default_category').attr("disabled","");
				jQuery('#widget_product_acc_default_tag').attr("disabled","");
				
			}
		});
});


</script>
<table cellpadding="2" cellspacing="2">
<tr><td colspan="2"><img src="<?php echo ReviewAZON_ADMIN_IMAGES ?>information.gif" style="vertical-align:middle;padding-left:5px;padding-right:5px" /> You can enter a default ASIN product number and choose where to show the default ASIN results. Also, you can specify an Amazon search query that will show results if there are no product accessories to display.</td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td colspan="2">
<div style="padding:5px;font-weight:bold;">Amazon Affiliate Tracking ID</div>
<select name="ReviewAZON_associate_id_product_accessories" id="ReviewAZON_associate_id_product_accessories" style="width:100%">
<?php 
global $wpdb;
global $post;
$affiliateCountry = ReviewAZON_get_affiliate_setting("AWS_Affiliate_Country");
$queryTrackingIDs = "SELECT * FROM ".$wpdb->prefix."reviewazon_tracking_id WHERE Affiliate_Country = '{$affiliateCountry}' Order By Display_Order ASC";
$trackingIDs = $wpdb->get_results($queryTrackingIDs);
foreach($trackingIDs as $trackingID)
{
	if($options['trackingid'] == $trackingID->Tracking_ID)
	{
		echo '<option value="'.$trackingID->Tracking_ID.'" selected="selected">'.$trackingID->Tracking_ID.'</option>';
	}
	else
	{
		echo '<option value="'.$trackingID->Tracking_ID.'" >'.$trackingID->Tracking_ID.'</option>';
	}
}
?>
</select>
</td></tr>
<tr><td>&nbsp;</td></tr>
<tr>
	<td nowrap><label><strong>Widget Title</strong>:</label></td>
	<td><input type="text" id="widgetProductAccessoriesTitle" name="widgetProductAccessoriesTitle" value="<?php echo $options['title'];?>" /></td>
</tr>
<tr>
	<td><label><strong>Default ASIN</strong>:</label></td>
	<td><input type="text" id="widget_product_accessories_default_asin" name="widget_product_accessories_default_asin" value="<?php echo $options['defaultasin'];?>" /></td>
</tr>
<tr>
	<td><label><strong>Search Text</strong>:</label></td>
	<td><input type="text" id="widget_product_accessories_default_query" name="widget_product_accessories_default_query" value="<?php echo $options['query'];?>" /></td>
</tr>
<tr>
	<td align="right"><label>Show </label></td>
	<td><input type="text" maxlength="1" style="width:50px;" id="widget_product_accessories_show_number" name="widget_product_accessories_show_number" value="<?php echo $options['numbertoshow'];?>" /> Items (Max is 5)</td>
</tr>
<tr><td colspan="2" style="font-weight:bold;padding:5px;padding-top:10px;"><p>Show default ASIN results on the following pages:</p></td></tr>
<tr>
	<td colspan="2" style="padding-left:10px;"><input type="checkbox" id="widget_product_acc_default_none" name="widget_product_acc_default_none" <?php if($options['useDefaultNone'] == "true") { echo 'checked'; }?>" />&nbsp;&nbsp;<label>None (uses the post ASIN)</label></td>
</tr>
<tr>
	<td colspan="2" style="padding-left:10px;"><input type="checkbox" id="widget_product_acc_default_home" name="widget_product_acc_default_home" <?php if($options['useDefaultHome'] == "true") { echo 'checked'; }?>" />&nbsp;&nbsp;<label>Home Page</label></td>
</tr>
<tr>
	<td colspan="2" style="padding-left:10px;"><input type="checkbox" id="widget_product_acc_default_post" name="widget_product_acc_default_post" <?php if($options['useDefaultPost'] == "true") { echo 'checked'; }?>" />&nbsp;&nbsp;<label>Post Page</label></td>
</tr>
<tr>
	<td colspan="2" style="padding-left:10px;"><input type="checkbox" id="widget_product_acc_default_category" name="widget_product_acc_default_category" <?php if($options['useDefaultCategory'] == "true") { echo 'checked'; }?>" />&nbsp;&nbsp;<label>Category Page</label></td>
</tr>
<tr>
	<td colspan="2" style="padding-left:10px;"><input type="checkbox" id="widget_product_acc_default_tag" name="widget_product_acc_default_tag" <?php if($options['useDefaultTag'] == "true") { echo 'checked'; }?>" />&nbsp;&nbsp;<label>Tag Page</label></td>
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
</table>

<input type="hidden" id="ReviewAZON_product_accessories_submit" name="ReviewAZON_product_accessories_submit" value="1" />
<?php 
}

function widget_amazon_similar_products()
{
	global $post,$wpdb;
	$options = get_option("ReviewAZON_Similar_Products_Widget");
	$postASIN = get_post_meta($post->ID, 'ASIN', true);

	if($options['useDefaultNone'] == "true")
	{
		if(!empty($postASIN))
		{
			$current = ReviewAZON_get_amazon_current_node($postASIN,null,null,null,$options['trackingid']);		
			$similarProducts = SimilarProducts($current,$options['query'],$options['trackingid'],$options['numbertoshow']);
			
		}
		else
		{
			if(ReviewAZON_get_post_setting($post->ID,"ASIN") != "")
			{
				$current = ReviewAZON_get_amazon_current_node(ReviewAZON_get_post_setting($post->ID,"ASIN"),null,null,null,$options['trackingid']);
				$similarProducts = SimilarProducts($current,$options['query'],$options['trackingid'],$options['numbertoshow']);
				
			}
			else
			{
				$similarProducts = ReviewAZON_similiar_products_widget_search($options['query'],$options['numbertoshow'],$options['trackingid']);
			}
			
		}
	}
	else
	{
		if($options['defaultasin'] != "" && $options['useDefaultHome'] == "true" && is_front_page())
		{
			$current = ReviewAZON_get_amazon_current_node($options['defaultasin'],null,null,null,$options['trackingid']);
			$similarProducts = SimilarProducts($current,$options['query'],$options['trackingid'],$options['numbertoshow']);			
			
		}
		else if($options['defaultasin'] != "" && $options['useDefaultPost']  == "true" && is_single())
		{
			$current = ReviewAZON_get_amazon_current_node($options['defaultasin'],null,null,null,$options['trackingid']);
			$similarProducts = SimilarProducts($current,$options['query'],$options['trackingid'],$options['numbertoshow']);			

		}
		else if($options['defaultasin'] != "" && $options['useDefaultCategory'] == "true" && is_category())
		{
			$current = ReviewAZON_get_amazon_current_node($options['defaultasin'],null,null,null,$options['trackingid']);
			$similarProducts = SimilarProducts($current,$options['query'],$options['trackingid'],$options['numbertoshow']);			
			
		}
		else if($options['defaultasin'] != "" && $options['useDefaultTag'] == "true" && is_tag())
		{
			$current = ReviewAZON_get_amazon_current_node($options['defaultasin'],null,null,null,$options['trackingid']);
			$similarProducts = SimilarProducts($current,$options['query'],$options['trackingid'],$options['numbertoshow']);			
			
		}	
		else
		{
			if(!empty($postASIN))
			{
				$current = ReviewAZON_get_amazon_current_node($postASIN,null,null,null,$options['trackingid']);	
				$similarProducts = SimilarProducts($current,$options['query'],$options['trackingid'],$options['numbertoshow']);			
				
			}
			else
			{
				if(ReviewAZON_get_post_setting($post->ID,"ASIN") != "")
				{
					$current = ReviewAZON_get_amazon_current_node(ReviewAZON_get_post_setting($post->ID,"ASIN"),null,null,null,$options['trackingid']);
					$similarProducts = SimilarProducts($current,$options['query'],$options['trackingid'],$options['numbertoshow']);			
					
				}
				else
				{
					$similarProducts = ReviewAZON_similiar_products_widget_search($options['query'],$options['numbertoshow'],$options['trackingid']);
				}
			}	
		}
	}	

?>
  <h2 class="widgettitle"><?php echo $options['title'];?></h2>  
  <div id="amazon_similar_products" class="RZAmazonSimilarProducts">
  	<?php echo $similarProducts ?>
  </div>
<?php 
}

function similar_products_control()
{
 $options = get_option("ReviewAZON_Similar_Products_Widget");
	
	  if (!is_array( $options ))
		{
			$options = array(
	        'title' => 'Similar Products',
			'trackingid' => '',
			'alwaysUseSPAsin' => 'false',
			'numbertoshow' => '5',
			'query' => ''
	      );
	  }      
	  
	  if($options['numbertoshow'] == "")
	  {
	  	$options['numbertoshow'] = "5";
	  }
	  
	  if ($_POST['ReviewAZON_similar_products_submit'])
	  {
	  	
	  	$options['useDefaultNone'] = "false";
	  	$options['useDefaultHome'] = "false";
	  	$options['useDefaultPost'] = "false";
	  	$options['useDefaultCategory'] = "false";
	  	$options['useDefaultTag'] = "false";
	  	
	  	if(isset($_POST['widget_similar_product_default_none']))
	    {
	    	$options['useDefaultNone'] = "true";
	    }
	    if(isset($_POST['widget_similar_product_default_home']))
	    {
	    	$options['useDefaultHome'] = "true";
	    }
	   	if(isset($_POST['widget_similar_product_default_post']))
	    {
	    	$options['useDefaultPost'] = "true";
	    }
	    if(isset($_POST['widget_similar_product_default_category']))
	    {
	    	$options['useDefaultCategory'] = "true";
	    }
	    if(isset($_POST['widget_similar_product_default_tag']))
	    {
	    	$options['useDefaultTag'] = "true";
	    }
	  	
	    $options['title'] = htmlspecialchars($_POST['widgetSimilarProductTitle']);
	    $options['defaultasin'] = $_POST['widget_similar_products_default_asin'];
	    $options['trackingid'] = $_POST['ReviewAZON_associate_id'];
	    $options['numbertoshow'] = $_POST['widget_similar_product_show_number'];
	    $options['query'] = $_POST['widget_similar_products_default_query'];    
	    
	    update_option("ReviewAZON_Similar_Products_Widget", $options);
	  }
?>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('#widget_similar_product_default_none').click(function(){
			if(jQuery(this).attr("checked"))
			{
				jQuery('#widget_similar_product_default_home').attr("disabled","disabled");
				jQuery('#widget_similar_product_default_home').attr("checked","");
				jQuery('#widget_similar_product_default_post').attr("disabled","disabled");
				jQuery('#widget_similar_product_default_post').attr("checked","");				
				jQuery('#widget_similar_product_default_category').attr("disabled","disabled");
				jQuery('#widget_similar_product_default_category').attr("checked","");				
				jQuery('#widget_similar_product_default_tag').attr("disabled","disabled");
				jQuery('#widget_similar_product_default_tag').attr("checked","");
				
				
			}
			else
			{
				jQuery('#widget_similar_product_default_home').attr("disabled","");
				jQuery('#widget_similar_product_default_post').attr("disabled","");
				jQuery('#widget_similar_product_default_category').attr("disabled","");
				jQuery('#widget_similar_product_default_tag').attr("disabled","");
				
			}
		});
});


</script>
<table cellpadding="2" cellspacing="2">
<tr><td colspan="2"><img src="<?php echo ReviewAZON_ADMIN_IMAGES ?>information.gif" style="vertical-align:middle;padding-left:5px;padding-right:5px" /> You can enter a default ASIN product number and choose where to show the default ASIN results. Also, you can specify an Amazon search query that will show results if there are no similiar products to display.</td></tr>
<tr><td>&nbsp;</td></tr>
<tr><td colspan="2">
<div style="padding:5px;font-weight:bold;">Amazon Affiliate Tracking ID</div>
<select name="ReviewAZON_associate_id" id="ReviewAZON_associate_id" style="width:100%">
<?php 
global $wpdb;
global $post;
$affiliateCountry = ReviewAZON_get_affiliate_setting("AWS_Affiliate_Country");
$queryTrackingIDs = "SELECT * FROM ".$wpdb->prefix."reviewazon_tracking_id WHERE Affiliate_Country = '{$affiliateCountry}' Order By Display_Order ASC";
$trackingIDs = $wpdb->get_results($queryTrackingIDs);
foreach($trackingIDs as $trackingID)
{
	if($options['trackingid'] == $trackingID->Tracking_ID)
	{
		echo '<option value="'.$trackingID->Tracking_ID.'" selected="selected">'.$trackingID->Tracking_ID.'</option>';
	}
	else
	{
		echo '<option value="'.$trackingID->Tracking_ID.'" >'.$trackingID->Tracking_ID.'</option>';
	}
}
?>
</select>
</td></tr>
<tr><td>&nbsp;</td></tr>
<tr>
	<td nowrap><label><strong>Widget Title</strong>:</label></td>
	<td><input type="text" id="widgetSimilarProductTitle" name="widgetSimilarProductTitle" value="<?php echo $options['title'];?>" /></td>
</tr>
<tr>
	<td><label><strong>Default ASIN</strong>:</label></td>
	<td><input type="text" id="widget_similar_products_default_asin" name="widget_similar_products_default_asin" value="<?php echo $options['defaultasin'];?>" /></td>
</tr>
<tr>
	<td><label><strong>Search Text</strong>:</label></td>
	<td><input type="text" id="widget_similar_products_default_query" name="widget_similar_products_default_query" value="<?php echo $options['query'];?>" /></td>
</tr>
<tr>
	<td align="right"><label>Show </label></td>
	<td><input type="text" maxlength="1" style="width:50px;" id="widget_similar_product_show_number" name="widget_similar_product_show_number" value="<?php echo $options['numbertoshow'];?>" /> Items (Max is 5)</td>
</tr>
<tr><td colspan="2" style="font-weight:bold;padding:5px;padding-top:10px;"><p>Show default ASIN results on the following pages:</p></td></tr>
<tr>
	<td colspan="2" style="padding-left:10px;"><input type="checkbox" id="widget_similar_product_default_none" name="widget_similar_product_default_none" <?php if($options['useDefaultNone'] == "true") { echo 'checked'; }?>" />&nbsp;&nbsp;<label>None (uses the post ASIN)</label></td>
</tr>
<tr>
	<td colspan="2" style="padding-left:10px;"><input type="checkbox" id="widget_similar_product_default_home" name="widget_similar_product_default_home" <?php if($options['useDefaultHome'] == "true") { echo 'checked'; }?>" />&nbsp;&nbsp;<label>Home Page</label></td>
</tr>
<tr>
	<td colspan="2" style="padding-left:10px;"><input type="checkbox" id="widget_similar_product_default_post" name="widget_similar_product_default_post" <?php if($options['useDefaultPost'] == "true") { echo 'checked'; }?>" />&nbsp;&nbsp;<label>Post Page</label></td>
</tr>
<tr>
	<td colspan="2" style="padding-left:10px;"><input type="checkbox" id="widget_similar_product_default_category" name="widget_similar_product_default_category" <?php if($options['useDefaultCategory'] == "true") { echo 'checked'; }?>" />&nbsp;&nbsp;<label>Category Page</label></td>
</tr>
<tr>
	<td colspan="2" style="padding-left:10px;"><input type="checkbox" id="widget_similar_product_default_tag" name="widget_similar_product_default_tag" <?php if($options['useDefaultTag'] == "true") { echo 'checked'; }?>" />&nbsp;&nbsp;<label>Tag Page</label></td>
</tr>
<tr><td colspan="2">&nbsp;</td></tr>
</table>
<input type="hidden" id="ReviewAZON_similar_products_submit" name="ReviewAZON_similar_products_submit" value="1" />
<?php 
}
function widget_amazon_featured() 
{
	global $post,$wpdb;
	//$options = get_option("ReviewAZON_Auction_Widget");
	

?>
  <h2 class="widgettitle">Featured Products</h2>  
  <div id="amazon_featured" style="background-color:#FFFFFF;border:solid 1px gainsboro;height:300px;width:343px;text-align:center;">
  	<div style="overflow:hidden;width:343px;height:300px;" id="amazon_product"></div>
  	<div id="hiddenDiv" style="display:none"></div>
  </div>
<?php
}
function widget_featuredProducts()
{
	$featuredProductsOptions = get_option("ReviewAZON_Featured_Products_Widget");
?>
<h2 class="widgettitle"><?php echo $featuredProductsOptions['featuredProductsTitle'] ?></h2> 
<div style="padding-left:5px;padding-top:10px;paddding-bottom:20px;background-color:white;">
<?php 
global $post;
 $myposts = get_posts('category='.$featuredProductsOptions['featuredProductsCategoryID']);
 foreach($myposts as $post) :
?> 
   <div style="padding-bottom:5px;"><a href="<?php the_permalink()?>"><?php the_title() ?></a></div>
<?php 
endforeach;
print '<div style="height:10px;"></div></div>';
}

function featuredProducts_control()
{
  $featuredProductsOptions = get_option("ReviewAZON_Featured_Products_Widget");

  if (!is_array( $featuredProductsOptions ))
	{
		$featuredProductsOptions = array(
        'featuredProductsTitle' => 'Featured Products',
        'featuredProductsCategoryID' => ''
    );
  }      

  if ($_POST['featuredProduct-Submit'])
  {
    $featuredProductsOptions['featuredProductsTitle'] = htmlspecialchars($_POST['FeaturedProductWidgetTitle']);
    $featuredProductsOptions['featuredProductsCategoryID'] = htmlspecialchars($_POST['FeaturedProductCategoryID']);
    update_option("ReviewAZON_Featured_Products_Widget", $featuredProductsOptions);
  }
	
?>
<table cellpadding="2" cellspacing="2">
<tr><td colspan="2"><img src="<?php echo ReviewAZON_ADMIN_IMAGES ?>information.gif" style="vertical-align:middle;padding-left:5px;padding-right:5px" />Enter the category ID of the Featured Products category and have thoses posts tagged with this category show up here.</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
	<td nowrap><label>Widget Title:</label></td>
	<td><input type="text" id="FeaturedProductWidgetTitle" name="FeaturedProductWidgetTitle" value="<?php echo $featuredProductsOptions['featuredProductsTitle'];?>" /></td>
</tr>
<tr>
	<td nowrap><label>Category ID:</label></td>
	<td><input type="text" id="FeaturedProductCategoryID" name="FeaturedProductCategoryID" style="width:75px" value="<?php echo $featuredProductsOptions['featuredProductsCategoryID'];?>" /></td>
</tr>
</table>
<input type="hidden" id="featuredProduct-Submit" name="featuredProduct-Submit" value="1" />
<?php	
}


function widget_brands()
{
	$brandOptions = get_option("ReviewAZON_Brand_Widget");
?>
<h2 class="widgettitle"><?php echo $brandOptions['brandTitle'] ?></h2> 
<div style="padding-left:5px;padding-top:10px;paddding-bottom:10px;background-color:white;">
<?php 
  $categories=  get_categories('child_of='.$brandOptions['brandCategoryID']); 
  foreach ($categories as $cat) { 
?>
	<a href="<?php echo get_category_link($cat->cat_ID) ?>"><?php echo $cat->cat_name ?></a>&nbsp;&nbsp;
<?php 
  }
  print '<div style="height:10px;"></div></div>';
}

function brands_control()
{
  $brandOptions = get_option("ReviewAZON_Brand_Widget");

  if (!is_array( $brandOptions ))
	{
		$brandOptions = array(
        'brandTitle' => 'Brands',
        'brandCategoryID' => ''
    );
  }      

  if ($_POST['Brand-Submit'])
  {
    $brandOptions['brandTitle'] = htmlspecialchars($_POST['BrandWidgetTitle']);
    $brandOptions['brandCategoryID'] = htmlspecialchars($_POST['BrandCategoryID']);
    update_option("ReviewAZON_Brand_Widget", $brandOptions);
  }
	
?>
<table cellpadding="2" cellspacing="2">
<tr><td colspan="2"><img src="<?php echo ReviewAZON_ADMIN_IMAGES ?>information.gif" style="vertical-align:middle;padding-left:5px;padding-right:5px" />Create a top level Brand category and enter the category ID beow. Any brand sub-category that has a post assinged to it will show up in the widget.</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
	<td nowrap><label>Widget Title:</label></td>
	<td><input type="text" id="BrandWidgetTitle" name="BrandWidgetTitle" value="<?php echo $brandOptions['brandTitle'];?>" /></td>
</tr>
<tr>
	<td nowrap><label>Category ID:</label></td>
	<td><input type="text" id="BrandCategoryID" name="BrandCategoryID" style="width:75px;" value="<?php echo $brandOptions['brandCategoryID'];?>" /></td>
</tr>
</table>
<input type="hidden" id="Brand-Submit" name="Brand-Submit" value="1" />
<?php	
}


function widget_priceRange()
{
	$priceRangeOptions = get_option("ReviewAZON_Price_Range_Widget");
?>
<h2 class="widgettitle"><?php echo $priceRangeOptions['priceRangeTitle'] ?></h2> 
<div style="padding-left:5px;padding-top:10px;paddding-bottom:10px;background-color:white;">
<?php 
  $categories=  get_categories('orderby=ID&child_of='.$priceRangeOptions['categoryID']); 
   foreach ($categories as $cat) {  	
	echo '<div style="padding-bottom:5px;"><a href="'.get_category_link($cat->cat_ID).'">'.$cat->cat_name.'</a></div>';
  }
  print '<div style="height:10px;"></div></div>';
}

function priceRange_control()
{
  $priceRangeOptions = get_option("ReviewAZON_Price_Range_Widget");

  if (!is_array( $priceRangeOptions ))
	{
		$priceRangeOptions = array(
        'priceRangeTitle' => 'Price Range',
        'categoryID' => ''
    );
  }      

  if ($_POST['PriceRange-Submit'])
  {
    $priceRangeOptions['priceRangeTitle'] = htmlspecialchars($_POST['PriceRangeWidgetTitle']);
    $priceRangeOptions['categoryID'] = htmlspecialchars($_POST['PriceRangeCategoryID']);
    update_option("ReviewAZON_Price_Range_Widget", $priceRangeOptions);
  }
	
?>
<table cellpadding="2" cellspacing="2">
<tr><td colspan="2"><img src="<?php echo ReviewAZON_ADMIN_IMAGES ?>information.gif" style="vertical-align:middle;padding-left:5px;padding-right:5px" />Create a top level Price Range category and enter the category ID beow. Any price range sub-category that has a post assinged to it will show up in the widget.</td></tr>
<tr><td colspan="2">&nbsp;</td></tr>
<tr>
	<td nowrap><label>Widget Title:</label></td>
	<td><input type="text" id="PriceRangeWidgetTitle" name="PriceRangeWidgetTitle" value="<?php echo $priceRangeOptions['priceRangeTitle'];?>" /></td>
</tr>
<tr>
	<td nowrap><label>Category ID:</label></td>
	<td><input type="text" id="PriceRangeCategoryID" name="PriceRangeCategoryID" value="<?php echo $priceRangeOptions['categoryID'];?>" /></td>
</tr>
</table>
<input type="hidden" id="PriceRange-Submit" name="PriceRange-Submit" value="1" />
<?php	
}
?>