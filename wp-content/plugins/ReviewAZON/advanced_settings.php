<?php
#########################################################################
#                                                                       #
#                   ReviewAZON 1.x Wordpress Plugin                     #
#                        www.reviewazon.com                             #
#                                                                       #
#########################################################################
# COPYRIGHT NOTICE                                                      #
# Copyright 2009 Niche Web Strategies LLC.  All Rights Reserved.        #
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

global $wpdb;
$showHome = false;
$showFrontPage = false;
$showCategory = false;
$showSearchPage = false;
$showTagPage = false;
$showStickyPage = false;

$nonce= wp_create_nonce  ('my-nonce');
$selectedStatus = "All";

if(isset($_POST['action']))
{
	if(check_admin_referer( 'my-nonce'))
	{
		$selectedStatus = $_POST['ReviewAZON_update_status'];
		
		if($_POST['action'] == "resetdefaultaffiliatesettings")
		{
			ReviewAZON_reset_advanced_settings();
			ReviewAZON_clear_page_cache();
			$message = '<div id="message" class="updated fade"><p><img src="'.ReviewAZON_ADMIN_IMAGES.'accept.gif" align="absmiddle" />&nbsp;Settings saved successfully.</p></div>';  
		}
		
		if($_POST['action'] == "recreatelinkfile")
		{
			$return = ReviewAZON_create_redirect_file();
			if($return == "success")
			{
				$message = '<div id="message" class="updated fade"><p><img src="'.ReviewAZON_ADMIN_IMAGES.'accept.gif" align="absmiddle" />&nbsp;Settings saved successfully.</p></div>';
			}
			else
			{
				$message = '<div id="message" class="error"><p><img src="'.ReviewAZON_ADMIN_IMAGES.'error.gif" align="absmiddle" />&nbsp;<strong>The Reviewazon.php file was not located in the root blog directory and ReviewAZON could not create the file automatically due to permissions</strong>.</p>
				<p>Just copy the <strong>reviewazon.php</strong> file from the <strong>wp-content/plugins/ReviewAZON/seo</strong> folder to the root folder of your Wordpress blog. Once you do this you should not see this message again.</p></div>';
			}
		}

		if($_POST['action'] == "updatetrackingid")
		{
			global $post;
			if($_POST['ReviewAZON_update_status'] == "All")
			{
				$myposts = get_posts('numberposts=-1&post_status=blank');
			}
			if($_POST['ReviewAZON_update_status'] == "Publish")
			{
				$myposts = get_posts('numberposts=-1&post_status=publish');
			}
			if($_POST['ReviewAZON_update_status'] == "Future")
			{
				$myposts = get_posts('numberposts=-1&post_status=future');
			}
			if($_POST['ReviewAZON_update_status'] == "Draft")
			{
				$myposts = get_posts('numberposts=-1&post_status=draft');
			}			
		 	
		 	$asscID = $_POST['ReviewAZON_associate_id'];
		 	foreach($myposts as $post)
		 	{
		 		update_post_meta($post->ID, 'ReviewAZON_Tracking_ID', $asscID);
		 		$message = '<div id="message" class="updated fade"><p><img src="'.ReviewAZON_ADMIN_IMAGES.'accept.gif" align="absmiddle" />&nbsp;Settings saved successfully.</p></div>';		 		
		 	}		
		}
		
		
		if($_POST['action'] == "refreshimageratingdata")
		{
			$postSettingsQuery = "SELECT ID, ASIN FROM ".$wpdb->prefix."reviewazon_post_settings";
			$postDataRows = $wpdb->get_results($postSettingsQuery);
			foreach($postDataRows as $postDataRow)
			{
				$current = ReviewAZON_get_amazon_current_node($postDataRow->ASIN,null,null,null,null);
				$averageRatingNumber = $current->CustomerReviews->AverageRating;
				$customerReviewCount = $current->CustomerReviews->TotalReviews;
				$smallImageUrl = $current->SmallImage->URL;
				$mediumImageUrl = $current->MediumImage->URL;
				$largeImageUrl = $current->LargeImage->URL;
				
				$updateQuery = "UPDATE ".$wpdb->prefix."reviewazon_post_settings SET Customer_Review_Count = '".$customerReviewCount."', Product_Review_Rating = '".$averageRatingNumber."', Product_Image_Small = '".$smallImageUrl."', Product_Image_Medium = '".$mediumImageUrl."', Product_Image_Large = '".$largeImageUrl."' WHERE ASIN = '".$postDataRow->ASIN."'";
				$wpdb->query($updateQuery);
			}
			$message = '<div id="message" class="updated fade"><p><img src="'.ReviewAZON_ADMIN_IMAGES.'accept.gif" align="absmiddle" />&nbsp;Settings saved successfully.</p></div>';			
		}

		
		if($_POST['action'] == "truncatepostsettings")
		{
			$postSettingsQuery = "TRUNCATE TABLE ".$wpdb->prefix."reviewazon_post_settings";
			$wpdb->query($postSettingsQuery);
			$message = '<div id="message" class="updated fade"><p><img src="'.ReviewAZON_ADMIN_IMAGES.'accept.gif" align="absmiddle" />&nbsp;Settings saved successfully.</p></div>';			
		}
		if($_POST['action'] == "rollbackdbversion")
		{
			update_option( "ReviewAZON_db_version", $_POST['ReviewAZON_db_version'] );
			$message = '<div id="message" class="updated fade"><p><img src="'.ReviewAZON_ADMIN_IMAGES.'accept.gif" align="absmiddle" />&nbsp;Settings saved successfully.</p></div>';			
		}
		if($_POST['action'] == "truncatereportrecords")
		{
			$reportRecordsQuery = "TRUNCATE TABLE ".$wpdb->prefix."reviewazon_reports";
			$wpdb->query($reportRecordsQuery);
			$message = '<div id="message" class="updated fade"><p><img src="'.ReviewAZON_ADMIN_IMAGES.'accept.gif" align="absmiddle" />&nbsp;Settings saved successfully.</p></div>';			
		}
		if($_POST['action'] == "uninstallreviewazon")
		{
			$uninstallQuery = "DROP TABLE IF EXISTS ".$wpdb->prefix."reviewazon_bulk_reviews, ".$wpdb->prefix."reviewazon_post_settings, 
							   ".$wpdb->prefix."reviewazon_reports, ".$wpdb->prefix."reviewazon_settings, ".$wpdb->prefix."reviewazon_tabs, 
							   ".$wpdb->prefix."reviewazon_templates, ".$wpdb->prefix."reviewazon_tracking_id, ".$wpdb->prefix."reviewazon_video_reviews"; 
			$wpdb->query($uninstallQuery);
			update_option( "ReviewAZON_db_state", "uninstalled" );
			$message = '<div id="message" class="updated fade"><p><img src="'.ReviewAZON_ADMIN_IMAGES.'accept.gif" align="absmiddle" />&nbsp;Settings saved successfully.</p></div>';			
		}
		if($_POST['action'] == "reinstallreviewazon")
		{
			$uninstallQuery = "DROP TABLE IF EXISTS ".$wpdb->prefix."reviewazon_bulk_reviews, ".$wpdb->prefix."reviewazon_post_settings, 
							   ".$wpdb->prefix."reviewazon_reports, ".$wpdb->prefix."reviewazon_settings, ".$wpdb->prefix."reviewazon_tabs, 
							   ".$wpdb->prefix."reviewazon_templates, ".$wpdb->prefix."reviewazon_tracking_id, ".$wpdb->prefix."reviewazon_video_reviews"; 
			$wpdb->query($uninstallQuery);
			ReviewAZON_activate();
			update_option( "ReviewAZON_db_state", "reinstalled" );
			$message = '<div id="message" class="updated fade"><p><img src="'.ReviewAZON_ADMIN_IMAGES.'accept.gif" align="absmiddle" />&nbsp;Settings saved successfully.</p></div>';			
		}
		
		
		if($_POST['action'] == "saveadvancedsettings")
		{		
			$showExcerptPages = serialize($_POST['chkShowExcerpts']);
	
			if(!$_POST['ReviewAZON_save_last_search_results'])
			{
				update_option("search_category","");
				update_option("search_text","");			
				update_option("search_page","1");			
			}
			
			ReviewAZON_clear_page_cache();
			
			$freeShippingText = addslashes($_POST['ReviewAZON_free_shipping_text']);
			$emptyProductAccessories = addslashes($_POST['ReviewAZON_product_accessories_text']);
			$emptyRelatedProducts = addslashes($_POST['ReviewAZON_related_products_text']);
			$emptyEbayAuctionsText = addslashes($_POST['ReviewAZON_ebay_auction_empty_text']);
			$emptyVideoReviewText = addslashes($_POST['ReviewAZON_video_reviews_text']);
			$emptyProductDetailsText = addslashes($_POST['ReviewAZON_product_details_text']);
			$emptyCustomerReviewText = addslashes($_POST['ReviewAZON_customer_reviews_text']);
			$emptyPhpOStockText = addslashes($_POST['ReviewAZON_phpOStock_empty_text']);
			
			
			$query = "UPDATE ".$wpdb->prefix."reviewazon_settings SET Excerpt_Layout_Mode = '{$_POST['ReviewAZON_Page_Layout']}', 
													 Default_Template_Group = '{$_POST['ReviewAZON_Layout_Group']}',
													 No_Reviews_To_Display = '{$_POST['ReviewAZON_number_of_reviews']}',
													 Min_Review_Score_To_Display = '{$_POST['ReviewAZON_default_review_score']}',
													 Show_Ebay_Auctions = '{$_POST['ReviewAZON_show_ebay_auctions']}',
													 Page_Cache_Length = '{$_POST['ReviewAZON_default_Page_cache']}',
													 Invalidate_Page_Cache = '{$_POST['ReviewAZON_invalidate_cache']}',
													 Save_Last_Search_Results = '{$_POST['ReviewAZON_save_last_search_results']}',
													 Free_Shipping_Text = '{$freeShippingText}', 
													 Empty_Product_Accessories_Text = '{$emptyProductAccessories}',  
													 Empty_Related_Products_Text = '{$emptyRelatedProducts}',  
													 Empty_Ebay_Auctions_Text = '{$emptyEbayAuctionsText}',  
													 Empty_Video_Reviews_Text = '{$emptyVideoReviewText}', 
													 Empty_Product_Details_Text = '{$emptyProductDetailsText}',
													 Empty_Customer_Reviews_Text = '{$emptyCustomerReviewText}',
													 Allow_Expanded_Tokens = '{$_POST['ReviewAZON_allow_expanded_tokens']}',
													 Truncate_Excerpt = '{$_POST['ReviewAZON_truncate_excerpt']}',
													 Truncate_Excerpt_Text = '{$_POST['ReviewAZON_truncate_excerpt_text']}',												 
													 Cache_Description = '{$_POST['ReviewAZON_cache_description']}',
													 Allow_Link_Cloaking = '{$_POST['ReviewAZON_allow_link_cloaking']}',
													 Allow_Image_Link_Cloaking = '{$_POST['ReviewAZON_allow_image_link_cloaking']}',
													 SEO_Link_Prefix = '{$_POST['ReviewAZON_seo_link_prefix']}',
													 Enable_Debugging = '{$_POST['ReviewAZON_enable_debugging']}',
													 Css_Location = '{$_POST['ReviewAZON_css_location']}',
													 Empty_PhpOStock_Text = '{$emptyPhpOStockText}',
													 Show_Overstock_Items = '{$_POST['ReviewAZON_show_phpOStock']}',
													 Show_Excerpts_Pages = '{$showExcerptPages}',
													 Create_Brand_Category = '{$_POST['ReviewAZON_create_brand']}',
													 Create_Tags = '{$_POST['ReviewAZON_create_tags']}',
													 Tag_Filter = '{$_POST['ReviewAZON_tag_filter']}'												 
													 ";
			$wpdb->query($query);
			
			if($_POST['ReviewAZON_allow_link_cloaking'])
			{
				$return = ReviewAZON_create_redirect_file();
				if($return == "success")
				{
					$message = '<div id="message" class="updated fade"><p><img src="'.ReviewAZON_ADMIN_IMAGES.'accept.gif" align="absmiddle" />&nbsp;Settings saved successfully.</p></div>';
				}
				else
				{
					$message = '<div id="message" class="error"><p><img src="'.ReviewAZON_ADMIN_IMAGES.'error.gif" align="absmiddle" />&nbsp;<strong>The Reviewazon.php file was not located in the root blog directory and ReviewAZON could not create the file automatically due to permissions</strong>.</p>
					<p>Just copy the <strong>reviewazon.php</strong> file from the <strong>wp-content/plugins/ReviewAZON/seo</strong> folder to the root folder of your Wordpress blog. Once you do this you should not see this message again.</p></div>';
				}
			}
			else
			{
				$message = '<div id="message" class="updated fade"><p><img src="'.ReviewAZON_ADMIN_IMAGES.'accept.gif" align="absmiddle" />&nbsp;Settings saved successfully.</p></div>';			
			}
		}	
	}
	else
	{
		die('');
	}
}


if(get_option("ReviewAZON_db_state") != "uninstalled")
{
	$showTabs = 'display:none';
	if(ReviewAZON_get_affiliate_setting("Default_Template_Group") == 'Tabs')
	{
		$showTabs = 'display:';
	}
	
	$showExcerptPages = ReviewAZON_get_affiliate_setting("Show_Excerpts_Pages");
	$pagesToShow = unserialize($showExcerptPages);
	
	if(count($pagesToShow) > 0)
	{
		if(in_array('home',$pagesToShow))
		{
			$showHome = true;
		}
		if(in_array('frontpage',$pagesToShow))
		{
			$showFrontPage = true;
		}
		if(in_array('category',$pagesToShow))
		{
			$showCategory = true;
		}
		if(in_array('search',$pagesToShow))
		{
			$showSearchPage = true;
		}
		if(in_array('tag',$pagesToShow))
		{
			$showTagPage = true;
		}
		if(in_array('sticky',$pagesToShow))
		{
			$showStickyPage = true;
		}
	}
}

?>
<link type="text/css" href="<?php echo ReviewAZON_ADMIN ?>theme/ui.all.css" rel="Stylesheet" />
<script type="text/javascript" src="<?php echo ReviewAZON_ADMIN ?>js/jquery-1.3.1.js"></script>
<script type="text/javascript" src="<?php echo ReviewAZON_ADMIN ?>js/jquery-ui-personalized-1.6rc6.js"></script>

<script type="text/javascript">
jQuery.noConflict();
jQuery(document).ready(function(){
	jQuery('#ReviewAZON_default_review_score').val("<?php echo ReviewAZON_get_affiliate_setting("Min_Review_Score_To_Display") ?>");
	// Tabs
	jQuery('#tabs').tabs({
		select: function(event, ui) {jQuery('#ReviewAZON_selected_tab').val(ui.panel.id);}
	});
	jQuery('#tabs').show();
	jQuery('#tabs').tabs('select', '#' + jQuery('#ReviewAZON_selected_tab').val());
		

	// Add Dialog			
	jQuery('#dialog').dialog({
		autoOpen: false,
		modal: true,
		overlay:{ background: "gray", opacity: 1.0 },
		width: 750,
		height: 600,
		buttons: {
			"Ok": function() { 
				var title = jQuery('#tab_name').val();
				var content = jQuery('#tab_body').val();
				var showTab = 0;
				if(jQuery('#show_tab').is(':checked'))
				{
					showTab = 1;
				}				
				jQuery.post("<?php echo ReviewAZON_PROCESS_REQUEST_FILE ?>",{action: 'addnewtab', tabtitle: title, tabcontent: content, showtab: showTab, _ajax_nonce:'<?php echo $nonce ?>'},function(data){
					jQuery('#tab_name').val('');
					jQuery('#tab_body').val('');
					jQuery('#test-list').html(data);
				});
				jQuery(this).dialog("close"); 
			}, 
			"Cancel": function() { 
				jQuery(this).dialog("close"); 
			} 
		}
	});

	// Edit Dialog			
	jQuery('#editDialog').dialog({
		autoOpen: false,
		modal: true,
		overlay:{ background: "gray", opacity: 1.0 },
		width: 750,
		height: 600,
		buttons: {
			"Ok": function() { 
				var title = jQuery('#edit_tab_name').val();
				var content = jQuery('#edit_tab_body').val();
				var id = jQuery('#hdn_tab_id').val();
				var showTab = 0;
				if(jQuery('#edit_show_tab').is(':checked'))
				{
					showTab = 1;
				}				
				
				jQuery.post("<?php echo ReviewAZON_PROCESS_REQUEST_FILE ?>",{action: 'updatetab', tabid: id, tabtitle: title, tabcontent: content, showtab: showTab, _ajax_nonce:'<?php echo $nonce ?>'},function(data){
					jQuery('#edit_tab_name').val('');
					jQuery('#edit_tab_body').val('');
					jQuery('#hdn_tab_id').val('');
					jQuery('#edit_show_tab').val('');
					jQuery('#test-list').html(data);
				});
				jQuery(this).dialog("close"); 
			}, 
			"Cancel": function() { 
				jQuery('#edit_tab_name').val('');
				jQuery('#edit_tab_body').val('');
				jQuery('#hdn_tab_id').val('');
				jQuery('#edit_show_tab').val('');
				jQuery(this).dialog("close"); 
			} 
		}
	});

	jQuery('#ReviewAZON_Layout_Group').change(function(){
		if(jQuery(this).val() == "Tabs")
		{
			jQuery('#ReviewAZON_tab_settings').show();
		}
		else{
			jQuery('#ReviewAZON_tab_settings').hide();
		}

	});

	jQuery("#test-list").sortable({
	      handle : '.handle',
	      update : function () {
			var order = jQuery('#test-list').sortable('serialize');
			jQuery("#progress").load("<?php echo ReviewAZON_PROCESS_REQUEST_FILE ?>?"+order+"&action=settabsortorder&_ajax_nonce=<?php echo $nonce ?>");
			}	
	    });
		
});

function deleteTab(id)
{
	if(confirm('Delete Tab?'))
	{
		jQuery.post("<?php echo ReviewAZON_PROCESS_REQUEST_FILE ?>",{action: 'deletetab', tabid: id, _ajax_nonce:'<?php echo $nonce ?>'},function(data){
			//Refresh tabs
			jQuery('#test-list').html(data);
		});
	}
}


function getSingleTab(id)
{
	jQuery.getJSON("<?php echo ReviewAZON_PROCESS_REQUEST_FILE ?>",{action: 'getsingletab', tabid: id , _ajax_nonce:'<?php echo $nonce ?>'},function(data){
		jQuery('#edit_tab_name').val(data.title);
		jQuery('#edit_tab_body').val(data.content);
		jQuery('#hdn_tab_id').val(id);
		
		if(data.visible == 1)
		{
			jQuery('#edit_show_tab').attr('checked','checked');
		}
		else
		{
			jQuery('#edit_show_tab').attr('checked','');
		}
		jQuery('#editDialog').dialog('open');
	});
}

function setAction(action)
{
	
	if(action == 'refreshimageratingdata')
	{
		if(confirm('Are you sure you want to reset all product links and ratings?\n\nThis will overwrite any previous changes.\n\n'))
		{
			jQuery('#action').val(action);
			jQuery("form").submit();
		}
	}
	else if(action == 'resetdefaultaffiliatesettings')
	{
		if(confirm('Are you sure you want to reset to the default settings?\n\nThis will overwrite any previous changes.\n\n'))
		{
			jQuery('#action').val(action);
			jQuery("form").submit();
		}
	}
	else if(action == 'truncatepostsettings')
	{
		if(confirm('Are you sure you want to delete all records? \n\nYou will loose all existing data and this action cannot be undone. Proceed?'))
		{
			jQuery('#action').val(action);
			jQuery("form").submit();
		}
	}
	else if(action == 'truncatereportrecords')
	{
		if(confirm('Are you sure you want to delete all records? \n\nYou will loose all existing data and this action cannot be undone. Proceed?'))
		{
			jQuery('#action').val(action);
			jQuery("form").submit();
		}
	}
	else if(action == 'uninstallreviewazon')
	{
		if(confirm('Are you sure you want to uninstall ReviewAZON?. \n\nYou will loose all existing data and this action cannot be undone. Proceed?'))
		{
			jQuery('#action').val(action);
			jQuery("form").submit();
		}
	}	
	else if(action == 'reinstallreviewazon')
	{
		if(confirm('Are you sure you want to reinstall ReviewAZON?. \n\nYou will loose all existing data and this action cannot be undone. Proceed?'))
		{
			jQuery('#action').val(action);
			jQuery("form").submit();
		}
	}
	else if(action == 'recreatelinkfile')
	{
		if(confirm('This will recreate the ReviewAZON link file. Proceed?'))
		{
			jQuery('#action').val(action);
			jQuery("form").submit();
		}
	}
	else if(action == 'updatetrackingid')
	{
		if(confirm('This will reset the tracking ID on all your posts. Proceed?'))
		{
			jQuery('#action').val(action);
			jQuery("form").submit();
		}
	}
	else if(action == 'rollbackdbversion')
	{
		if(confirm('This action will roll back the current database version. Proceed?'))
		{
			jQuery('#action').val(action);
			jQuery("form").submit();
		}
	}
	else
	{
		jQuery('#action').val(action);
		jQuery("form").submit();
	}
}
</script>

<form method="post">
<div class="wrap">
	<h2><img src="<?php echo ReviewAZON_ADMIN_IMAGES ?>largestar.gif" style="vertical-align:middle;margin:5px;" />Advanced Settings - ReviewAZON Wordpress Plugin Version <?php echo REVIEWAZONVER ?></h2>
	<br />
	<?php echo $message ?><br />
	<table cellpadding="2" cellspacing="2" width="100%">
		<tr><td align="right"><img src="<?php echo ReviewAZON_ADMIN_IMAGES?>disk.gif" style="margin:5px;vertical-align:middle;"/><a href="#" onclick="setAction('saveadvancedsettings')" style="font-size:10pt;font-weight:bold;">Save Advanced Settings</a>&nbsp;&nbsp;  | &nbsp;<img src="<?php echo ReviewAZON_ADMIN_IMAGES?>arrow_refresh.gif" style="margin:5px;vertical-align:middle;"/><a style="font-size:10pt;font-weight:bold;" href="#" onclick="setAction('resetdefaultaffiliatesettings')">Reset Advanced Settings to Default</a>&nbsp;&nbsp;</td></tr>
	</table>
<div id="tabs" style="display:none">
	<ul>
		<li><a href="#tabs-1">Display Settings</a></li>
		<li><a href="#tabs-2">eBay Integration</a></li>
		<li><a href="#tabs-3">phpOStock Integration</a></li>
		<li><a href="#tabs-4">Text Options</a></li>
		<li><a href="#tabs-5">Link Options</a></li>
		<li><a href="#tabs-6">Debug Settings</a></li>
		<li><a href="#tabs-7">Maintenance Options</a></li>
	</ul>
	<div id="tabs-1">
<fieldset class="options">
    <legend></legend> 
       <table cellpadding="5" cellspacing="10">
       <tr><td colspan="3">&nbsp;</td></tr>
       <tr>
    		<td valign="top" nowrap><label><strong>Review Excerpt Layout:</strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap width="130">
				<select name="ReviewAZON_Page_Layout" id="ReviewAZON_Page_Layout" style="width:100px">
					<option value="Normal" <?php if(ReviewAZON_get_affiliate_setting("Excerpt_Layout_Mode") == 'Normal') {echo 'selected="selected"';} ?>>Normal</option>
					<option value="Flow" <?php if(ReviewAZON_get_affiliate_setting("Excerpt_Layout_Mode") == 'Flow') {echo 'selected="selected"';} ?>>Flow</option>				
				</select>
    		</td>
    		<td valign="top">Select the default page layout for your product reviews. Normal view shows review excerpts in a horizontal layout while Flow layout shows reviews in a 2 column layout. <p><img src="<?php echo ReviewAZON_ADMIN_IMAGES ?>information.gif" style="vertical-align:middle;padding-left:5px;padding-right:5px" /> <b>Note: Flow layout requires a template change for most Wordpress templates</b>. <b>Please consult the ReviewAZON documentation for more information</b>.</p></td>
    	</tr>
    	<tr>
    		<td valign="top" nowrap><label><strong>Show excerpts on the following pages:</strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap>
    			<input type="checkbox" id="chkShowExcerpts" name="chkShowExcerpts[]" value="home" <?php if($showHome) {echo 'checked="checked"';} ?>/>&nbsp;<strong>Home Page</strong><br>
	   			<input type="checkbox" id="chkShowExcerpts" name="chkShowExcerpts[]" value="frontpage" <?php if($showFrontPage) {echo 'checked="checked"';} ?>/>&nbsp;<strong>Front Page</strong><br>
    			<input type="checkbox" id="chkShowExcerpts" name="chkShowExcerpts[]" value="category" <?php if($showCategory) {echo 'checked="checked"';} ?>/>&nbsp;<strong>Category Page</strong><br>
    			<input type="checkbox" id="chkShowExcerpts" name="chkShowExcerpts[]" value="search" <?php if($showSearchPage) {echo 'checked="checked"';} ?>/>&nbsp;<strong>Search Page</strong><br>
    			<input type="checkbox" id="chkShowExcerpts" name="chkShowExcerpts[]" value="tag" <?php if($showTagPage) {echo 'checked="checked"';} ?> />&nbsp;<strong>Tag Page</strong><br>
    			<input type="checkbox" id="chkShowExcerpts" name="chkShowExcerpts[]" value="sticky" <?php if($showStickyPage) {echo 'checked="checked"';} ?> />&nbsp;<strong>Sticky Post</strong><br>
    		</td>
    		<td valign="top">You can choose to show product post excerpts on certain pages. Check the pages where you want to only show product post excerpts. All other pages will show full posts if allowed by the Wordpress theme you are using.</td>
    	</tr>  
    	<tr><td colspan="3"><hr style="background-color:gainsboro;border:1px solid gainsboro;"></td></tr>	
    	<tr>
    		<td valign="top" nowrap><label><strong>Default Template Group:</strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap width="130">
				<select name="ReviewAZON_Layout_Group" id="ReviewAZON_Layout_Group" style="width:100px">
<?php
				$addTab = '<p><img title="Add Tab" style="vertical-align:middle;padding-right:10px;cursor:pointer;" src="'.ReviewAZON_ADMIN_IMAGES.'tab_add.gif" alt="move" width="16" height="16" /><a href="#" onclick="jQuery(\'#dialog\').dialog(\'open\');return false;">Add New Tab</a></p>';
				$d = dir(WP_PLUGIN_DIR.'/ReviewAZON/templates');
				while(($entry = $d->read()) !== false) 
				{
					if($entry != '.' && $entry != '..')
					{
?>
				   		<option value="<?php echo $entry ?>" <?php if(ReviewAZON_get_affiliate_setting("Default_Template_Group") == $entry) {echo 'selected="selected"';} ?>><?php echo $entry ?></option>
<?php 	
					}
				}
				$d->close();
?>
			</select>
    		</td>
    		<td valign="top">Select the default template you would like to use to display review information for the main product review pages, posts and excerpts.</td>
    	</tr>
    	<tr  id="ReviewAZON_tab_settings" style="<?php echo $showTabs?>;padding-top:10px;">
    		<td>&nbsp;</td>
    		<td align="left" valign="top">
			<div>
			<div id="progress"></div>
			<div><?php echo $addTab ?></div>
				<ul id="test-list" style="width:300px">
<?php 
				$queryTabs = "SELECT * FROM ".$wpdb->prefix."reviewazon_tabs Order By Display_Order ASC";
				$tabs = $wpdb->get_results($queryTabs);
				$moveTab = '<img title="Move to change sort order" style="vertical-align:middle;padding-right:5px;cursor:move;" src="'.ReviewAZON_ADMIN_IMAGES.'arrow_out.gif" alt="move" width="16" height="16" class="handle" />';
				foreach($tabs as $tab)
				{		
					$deleteTab = '<img onclick="deleteTab('.$tab->Tab_ID.')" title="Delete Tab" style="vertical-align:middle;padding-right:10px;padding-left:10px;cursor:pointer;" src="'.ReviewAZON_ADMIN_IMAGES.'tab_delete.gif" alt="move" width="16" height="16" />';
					$editTab = '<img onclick="getSingleTab('.$tab->Tab_ID.')" title="Edit Tab" style="vertical-align:middle;padding-right:10px;padding-left:10px;cursor:pointer;" src="'.ReviewAZON_ADMIN_IMAGES.'tab_edit.gif" alt="move" width="16" height="16" />';
					
  					echo '<li id="listItem_'.$tab->Tab_ID.'"><span>'.$moveTab.''.$deleteTab.''.$editTab.'<strong>'.$tab->Tab_Title.'</strong></span></li>';
				}
?>				
				</ul>
			</div>
			</td>
			<td align="left" valign="top">Tab View: When tabs is selected, your product review information such as customer reviews, description and details can be shown in a tab presentation.
			<p><img src="<?php echo ReviewAZON_ADMIN_IMAGES ?>information.gif" style="vertical-align:middle;padding-left:5px;padding-right:5px" /> <b>Note: You can create, edit, change the display order and delete tabs using the features on the left. </b></p>
			<p><b>Use the <img title="Move to change sort order" style="vertical-align:middle;padding-right:5px;cursor:move;" src="<?php echo ReviewAZON_ADMIN_IMAGES ?>arrow_out.gif" alt="move" width="16" height="16" /> icon to move your tabs above or below the other tabs to change the display order on the review page</b>.</p>
			</td>
    	</tr>
    	<tr>
    		<td valign="top" nowrap><label><strong>Default stylesheet location:</strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap><input name="ReviewAZON_css_location" id="ReviewAZON_css_location" type="text" value="<?php echo ReviewAZON_get_affiliate_setting("Css_Location") ?>" style="width:100%;"/></td>
    		<td valign="top">You can specify the path to your default template group stylesheet. Normally this is wp-content/plugins/ReviewAZON/templates/{Template Group Name}/css, but you can change it to any location. <p>For example: if you wanted to put your stylesheet in the root folder
    		of your blog, you would use the following path: /templates/{Group Template Name}/css or /templates/Default/css.</p>
    		<p><strong>If you want to use the out of the box path, just type in the word default in the textbox.</strong></td>
    	</tr>    	
    	<tr><td colspan="3"><hr style="background-color:gainsboro;border:1px solid gainsboro;"></td></tr>	
    	<tr>
    		<td valign="top" nowrap><label><strong>Number of customer reviews to display:</strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap><input name="ReviewAZON_number_of_reviews" id="ReviewAZON_number_of_reviews" type="text" value="<?php echo ReviewAZON_get_affiliate_setting("No_Reviews_To_Display") ?>" maxlength="3" style="width:35px;"/></td>
    		<td valign="top">You can specify the number of customer reviews to display on the main product pages.
    		<p><img src="<?php echo ReviewAZON_ADMIN_IMAGES ?>error.gif" style="vertical-align:middle;padding-left:5px;padding-right:5px" /><b>Use this setting very carefully since some products have well over 100 or more reviews. You can degrade the viewing performance if you specify high number of reviews to display since the it will require multiple calls to the web service to return this data.</b></p> </td>
    	</tr>
    	<tr>
    		<td valign="top" nowrap><label><strong>Customer review sort order:</strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap>
    			<select name="ReviewAZON_default_review_score" id="ReviewAZON_default_review_score">
    				<option value="default">Default Sorting</option>
	    			<option value="HelpfulVotes">Helpful Votes low to high</option>
	    			<option value="-HelpfulVotes">Helpful Votes high to low</option>
	    			<option value="OverallRating">Overall Rating low to high</option>
	    			<option value="-OverallRating">Overall Rating high to low</option>
	    			<option value="SubmissionDate">Submission Date low to high</option>
	    			<option value="-SubmissionDate">Submission Date high to low</option>   			
	    		</select>
    		</td>
    		<td valign="top">You can customize the order in which Customer Reviews are displayed on your pages. Just choose one of the sort options from the dropdown.</b>
    		</td>
    	</tr>
    	<tr><td colspan="3"><hr style="background-color:gainsboro;border:1px solid gainsboro;"></td></tr>	
    	<tr>
    		<td valign="top" nowrap><label><strong>Cache page content for:</strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap>
				<select id="ReviewAZON_default_Page_cache" name="ReviewAZON_default_Page_cache">
				<option value="0" <?php if(ReviewAZON_get_affiliate_setting("Page_Cache_Length") == "1") { echo 'selected'; }?>>0</option>
				<option value="1" <?php if(ReviewAZON_get_affiliate_setting("Page_Cache_Length") == "1") { echo 'selected'; }?>>1</option>
				<option value="2" <?php if(ReviewAZON_get_affiliate_setting("Page_Cache_Length") == "2") { echo 'selected'; }?>>2</option>
				<option value="3" <?php if(ReviewAZON_get_affiliate_setting("Page_Cache_Length") == "3") { echo 'selected'; }?>>3</option>
				<option value="4" <?php if(ReviewAZON_get_affiliate_setting("Page_Cache_Length") == "4") { echo 'selected'; }?>>4</option>
				<option value="5" <?php if(ReviewAZON_get_affiliate_setting("Page_Cache_Length") == "5") { echo 'selected'; }?>>5</option>
				<option value="6" <?php if(ReviewAZON_get_affiliate_setting("Page_Cache_Length") == "6") { echo 'selected'; }?>>6</option>
				<option value="7" <?php if(ReviewAZON_get_affiliate_setting("Page_Cache_Length") == "7") { echo 'selected'; }?>>7</option>
				<option value="8" <?php if(ReviewAZON_get_affiliate_setting("Page_Cache_Length") == "8") { echo 'selected'; }?>>8</option>
				<option value="9" <?php if(ReviewAZON_get_affiliate_setting("Page_Cache_Length") == "9") { echo 'selected'; }?>>9</option>
				<option value="10" <?php if(ReviewAZON_get_affiliate_setting("Page_Cache_Length") == "10") { echo 'selected'; }?>>10</option>
				<option value="11" <?php if(ReviewAZON_get_affiliate_setting("Page_Cache_Length") == "11") { echo 'selected'; }?>>11</option>
				<option value="12" <?php if(ReviewAZON_get_affiliate_setting("Page_Cache_Length") == "12") { echo 'selected'; }?>>12</option>
				<option value="13" <?php if(ReviewAZON_get_affiliate_setting("Page_Cache_Length") == "13") { echo 'selected'; }?>>13</option>
				<option value="14" <?php if(ReviewAZON_get_affiliate_setting("Page_Cache_Length") == "14") { echo 'selected'; }?>>14</option>
				<option value="15" <?php if(ReviewAZON_get_affiliate_setting("Page_Cache_Length") == "15") { echo 'selected'; }?>>15</option>
				<option value="16" <?php if(ReviewAZON_get_affiliate_setting("Page_Cache_Length") == "16") { echo 'selected'; }?>>16</option>
				<option value="17" <?php if(ReviewAZON_get_affiliate_setting("Page_Cache_Length") == "17") { echo 'selected'; }?>>17</option>
				<option value="18" <?php if(ReviewAZON_get_affiliate_setting("Page_Cache_Length") == "18") { echo 'selected'; }?>>18</option>
				<option value="19" <?php if(ReviewAZON_get_affiliate_setting("Page_Cache_Length") == "19") { echo 'selected'; }?>>19</option>
				<option value="20" <?php if(ReviewAZON_get_affiliate_setting("Page_Cache_Length") == "20") { echo 'selected'; }?>>20</option>
				<option value="21" <?php if(ReviewAZON_get_affiliate_setting("Page_Cache_Length") == "21") { echo 'selected'; }?>>21</option>
				<option value="22" <?php if(ReviewAZON_get_affiliate_setting("Page_Cache_Length") == "22") { echo 'selected'; }?>>22</option>
				<option value="23" <?php if(ReviewAZON_get_affiliate_setting("Page_Cache_Length") == "23") { echo 'selected'; }?>>23</option>
				
				</select> Hour(s)				
			</td>
    		<td valign="top">You can use product review data caching to increase the page load time and reduce the number of web service calls to Amazon. The default setting is 0 (no caching), but you can enable page caching by setting the value to 1 hour or greater. Use this if you experience slow load times for excerpts and pages.
    		<p><img src="<?php echo ReviewAZON_ADMIN_IMAGES ?>error.gif" style="padding-right:5px;vertical-align:sub;" /><b>Important: Per the Amazon terms and conditions, certain data can be stored up to 24 hours. Max cache settings are allowed up to 23 hours to comply with these restrictions. </b></p>
    		</td>
    	</tr>
    	<tr><td colspan="3"><hr style="background-color:gainsboro;border:1px solid gainsboro;"></td></tr>	
    	<tr>
    		<td valign="top" nowrap><label><strong>Save Last Search Results?</strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap width="130">
    			  <input name="ReviewAZON_save_last_search_results" type="radio" value="1" <?php if(ReviewAZON_get_affiliate_setting("Save_Last_Search_Results") == '1') {echo 'checked';} ?> /> Yes
  				  <input name="ReviewAZON_save_last_search_results" type="radio" value="0" <?php if(ReviewAZON_get_affiliate_setting("Save_Last_Search_Results") == '0') {echo 'checked';} ?> /> No
    		</td>
    		<td valign="top">You can have ReviewAZON save your last search results and have them auto load when you launch the product search dialog box. This is useful when you want to add multiple items of the same category type via the 
    		Add New Post page. The Default value is NO.</td>
    	</tr>
    	<!-- <tr>
    		<td valign="top" nowrap><label><strong>Remove page cache on template change?</strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap width="130">
    			  <input name="ReviewAZON_invalidate_cache" type="radio" value="1" <?php if(ReviewAZON_get_affiliate_setting("Invalidate_Page_Cache") == '1') {echo 'checked';} ?> /> Yes
  				  <input name="ReviewAZON_invalidate_cache" type="radio" value="0" <?php if(ReviewAZON_get_affiliate_setting("Invalidate_Page_Cache") == '0') {echo 'checked';} ?> /> No
    		</td>
    		<td valign="top">You can have ReviewAZON delete the page cache when you switch from one template group to another. The Default value is YES.</td>
    	</tr> -->
    	<tr><td colspan="3"><hr style="background-color:gainsboro;border:1px solid gainsboro;"></td></tr>	
    	<tr>
    		<td valign="top" nowrap><label><strong>Allow expanded tokens in Excerpts?</strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap width="130">
    			  <input name="ReviewAZON_allow_expanded_tokens" type="radio" value="1" <?php if(ReviewAZON_get_affiliate_setting("Allow_Expanded_Tokens") == '1') {echo 'checked';} ?> /> Yes
  				  <input name="ReviewAZON_allow_expanded_tokens" type="radio" value="0" <?php if(ReviewAZON_get_affiliate_setting("Allow_Expanded_Tokens") == '0') {echo 'checked';} ?> /> No
    		</td>
    		<td valign="top">By default, excerpts are built using minimal cached data elements stored when a new product is added or updated through the caching process. Since a page can display multiple post excerpts, this increases the load time significantly. 
    		You do have the option of expanding the range of tokens you can use in this template, but a call must be made to Amazon to get those values everytime the exceprt is displayed on the page. The Default value is YES.
    		</td>
    	</tr>
   		<tr>
    		<td valign="top" nowrap><label><strong>Cache product descriptions?</strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap width="130">
    			  <input name="ReviewAZON_cache_description" type="radio" value="1" <?php if(ReviewAZON_get_affiliate_setting("Cache_Description") == '1') {echo 'checked';} ?> /> Yes
  				  <input name="ReviewAZON_cache_description" type="radio" value="0" <?php if(ReviewAZON_get_affiliate_setting("Cache_Description") == '0') {echo 'checked';} ?> /> No
    		</td>
    		<td valign="top">Some product descriptions are not very long or may be empty in some cases. If you choose to cache product descriptions, they will not be overwritten if you make changes to them in the Add New Post or Edit Post pages.</td>
    	</tr>  	
     	<tr>
    		<td valign="top" nowrap><label><strong>Auto create product brand category?</strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap width="130">
    			  <input name="ReviewAZON_create_brand" type="radio" value="1" <?php if(ReviewAZON_get_affiliate_setting("Create_Brand_Category") == '1') {echo 'checked';} ?> /> Yes
  				  <input name="ReviewAZON_create_brand" type="radio" value="0" <?php if(ReviewAZON_get_affiliate_setting("Create_Brand_Category") == '0') {echo 'checked';} ?> /> No
    		</td>
    		<td valign="top">You can have ReviewAZON automatically create the brand category of the product post and assign it to the brand category when adding a new single product post. When you crate new posts using the Bulk Product Post page, the category will be created and you must manually check the category to assign newly created bulk posts.</td>
    	</tr>  	
      	<tr>
    		<td valign="top" nowrap><label><strong>Auto create post tags?</strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap width="130">
    			  <input name="ReviewAZON_create_tags" type="radio" value="1" <?php if(ReviewAZON_get_affiliate_setting("Create_Tags") == '1') {echo 'checked';} ?> /> Yes
  				  <input name="ReviewAZON_create_tags" type="radio" value="0" <?php if(ReviewAZON_get_affiliate_setting("Create_Tags") == '0') {echo 'checked';} ?> /> No
    		</td>
    		<td valign="top">You can have ReviewAZON automatically create post tags based on the product title.</td>
    	</tr>  	
       	<tr>
    		<td valign="top" nowrap><label><strong>Auto post tag filter:</strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap width="130">
    			  <input name="ReviewAZON_tag_filter" type="text" style="width:200px" value="<?php echo ReviewAZON_get_affiliate_setting("Tag_Filter")?>" />
    		</td>
    		<td valign="top">By default, auto post tagging filters words that are 3 characters or less. If there are other words that you want to prevent auto tagging to create, you can enter those here. Enter the name of the word seperated by commas. <i>Ex: tag1,tag2,tag3</i></td>
    	</tr>  	
    	</table>
    </fieldset>
</div>
<div id="tabs-2">
<fieldset class="options">
    <legend></legend> 
       <table cellpadding="5" cellspacing="10">
       <tr><td colspan="3"><p>If you want to display eBay auctions related to the product reviews, you can have ReviewAZON create the eBay search shortcode for you. You can use the popular PhpBayLite or PhpBayPro eBay auction listing display plug-in for Wordpress found <a href="">here</a>. You will need to down load PhpBayLite or PhpBayPro
       and install the plugin in your blog before you are able to use this functionality.</p>
       <p> <a href="http://reviewazon.com/phpbay.html" target="_blank">Click here to download PhpBayLite or purchase PhpBayPro.</a></p>
      </td></tr>
    	<tr>
    		<td valign="top" nowrap><label><strong>Display Ebay Auctions?</strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap width="130">
    			  <input name="ReviewAZON_show_ebay_auctions" type="radio" value="1" <?php if(ReviewAZON_get_affiliate_setting("Show_Ebay_Auctions") == '1') {echo 'checked';} ?> /> Yes
  				  <input name="ReviewAZON_show_ebay_auctions" type="radio" value="0" <?php if(ReviewAZON_get_affiliate_setting("Show_Ebay_Auctions") == '0') {echo 'checked';} ?> /> No
    		</td>
    		<td valign="top">You can choose to have ReviewAZON grab eBay auctions that are associated with your product post. The default setting is NO.</td>
    	</tr>
     	<tr>
    		<td valign="top" nowrap><label><strong>Empty Ebay Auction Text</strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap width="130">
	    	<textarea style="width:350px;height:85px;" name="ReviewAZON_ebay_auction_empty_text" id="ReviewAZON_ebay_auction_empty_text" tabindex="1"><?php echo stripslashes(ReviewAZON_get_affiliate_setting("Empty_Ebay_Auctions_Text")); ?></textarea>
    		</td>
    		<td valign="top">Enter the html or text you would like to use when eBay does not return any results.</td>
    	</tr>
    	</table>
    </fieldset>
</div>
<div id="tabs-3">
<fieldset class="options">
    <legend></legend> 
       <table cellpadding="5" cellspacing="10">
       <tr><td colspan="3"><p>If you want to display Overstock.com products related to the product reviews, you can have ReviewAZON create the phpOStock search shortcode for you. You can use the popular phpOStock Worpdress plug-in found <a href="http://reviewazon.com/phpostock.html" target="_blank">here</a>. You will need to download phpOStock
       and install the plugin in your blog before you are able to use this functionality.</p>
       <p> <a href="http://reviewazon.com/phpostock.html" target="_blank">Click here to purchase and download phpOStock Now!</a></p>
      </td></tr>
    	<tr>
    		<td valign="top" nowrap><label><strong>Display phpOStock product listings?</strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap width="130">
    			  <input name="ReviewAZON_show_phpOStock" type="radio" value="1" <?php if(ReviewAZON_get_affiliate_setting("Show_Overstock_Items") == '1') {echo 'checked';} ?> /> Yes
  				  <input name="ReviewAZON_show_phpOStock" type="radio" value="0" <?php if(ReviewAZON_get_affiliate_setting("Show_Overstock_Items") == '0') {echo 'checked';} ?> /> No
    		</td>
    		<td valign="top">You can choose to have ReviewAZON grab products from Overstock.com via the phpOStock plug-in that are associated with your product post. The default setting is NO.</td>
    	</tr>
     	<tr>
    		<td valign="top" nowrap><label><strong>Empty phpOStock Text</strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap width="130">
	    	<textarea style="width:350px;height:85px;" name="ReviewAZON_phpOStock_empty_text" id="ReviewAZON_phpOStock_empty_text" tabindex="1"><?php echo stripslashes(ReviewAZON_get_affiliate_setting("Empty_PhpOStock_Text")); ?></textarea>
    		</td>
    		<td valign="top">Enter the html or text you would like to use when Overstock.com does not return any results.</td>
    	</tr>
    	</table>
    </fieldset>
</div>
<div id="tabs-4">
<fieldset class="options">
    <legend></legend> 
       <table cellpadding="5" cellspacing="10">
         <tr><td colspan="3">&nbsp;</td></tr>      
    	<tr>
    		<td valign="top" nowrap><label><strong>Truncate post excerpt after </strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap width="130">
	    	<input type="text" style="width:100px;" name="ReviewAZON_truncate_excerpt" id="ReviewAZON_truncate_excerpt" tabindex="1" value="<?php echo stripslashes(ReviewAZON_get_affiliate_setting("Truncate_Excerpt")); ?>" />&nbsp;characters.
    		</td>
    		<td valign="top">If you want to show only a certain number of characters in your post excerpt, enter the number of characters here. Setting this value to 0 will show the entire excerpt. The default is 350.</td>
    	</tr>
    	<tr>
    		<td valign="top" nowrap><label><strong>Truncate post link text </strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap width="130">
	    	<textarea style="width:350px;height:85px;" name="ReviewAZON_truncate_excerpt_text" id="ReviewAZON_truncate_excerpt_text" ><?php echo stripslashes(ReviewAZON_get_affiliate_setting("Truncate_Excerpt_Text")); ?></textarea>
    		</td>
    		<td valign="top">Enter the default text you want to show up after the excerpt is truncated. Use the token %%PERMALINK%% if you want to link to the full post page.</td>
    	</tr>
    	<tr><td colspan="3"><hr style="background-color:gainsboro;border:1px solid gainsboro;"></td></tr>	
     	<tr>
    		<td valign="top" nowrap><label><strong>Default Super Saver Shipping Text</strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap width="130">
	    	<textarea style="width:350px;height:100px;" name="ReviewAZON_free_shipping_text" id="ReviewAZON_free_shipping_text" tabindex="1"><?php echo stripslashes(ReviewAZON_get_affiliate_setting("Free_Shipping_Text")); ?></textarea>
    		</td>
    		<td valign="top">Enter the html or text you would like to display when a product is eligible for super saver shipping.</td>
    	</tr>
    	<tr>
    		<td valign="top" nowrap><label><strong>Empty Product Accessories Text</strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap width="130">
	    	<textarea style="width:350px;height:85px;" name="ReviewAZON_product_accessories_text" id="ReviewAZON_product_accessories_text" tabindex="1"><?php echo stripslashes(ReviewAZON_get_affiliate_setting("Empty_Product_Accessories_Text")); ?></textarea>
    		</td>
    		<td valign="top">Enter the html or text you would like to display when there are no product accessories available to display.</td>
    	</tr>
    	<tr>
    		<td valign="top" nowrap><label><strong>Empty Similiar Products Text</strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap width="130">
	    	<textarea style="width:350px;height:85px;" name="ReviewAZON_related_products_text" id="ReviewAZON_related_products_text" tabindex="1"><?php echo stripslashes(ReviewAZON_get_affiliate_setting("Empty_Related_Products_Text")); ?></textarea>
    		</td>
    		<td valign="top">Enter the html or text you would like to display when there are no similar products available to display.</td>
    	</tr>
    	<tr>
    		<td valign="top" nowrap><label><strong>Empty Video Reviews Text</strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap width="130">
	    	<textarea style="width:350px;height:85px;" name="ReviewAZON_video_reviews_text" id="ReviewAZON_video_reviews_text" tabindex="1"><?php echo stripslashes(ReviewAZON_get_affiliate_setting("Empty_Video_Reviews_Text")); ?></textarea>
    		</td>
    		<td valign="top">Enter the html or text you would like to display when there are no video reviews available to display.</td>
    	</tr>
    	<tr>
    		<td valign="top" nowrap><label><strong>Empty Product Details Text</strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap width="130">
	    	<textarea style="width:350px;height:85px;" name="ReviewAZON_product_details_text" id="ReviewAZON_product_details_text" tabindex="1"><?php echo stripslashes(ReviewAZON_get_affiliate_setting("Empty_Product_Details_Text")); ?></textarea>
    		</td>
    		<td valign="top">Enter the html or text you would like to display when there are no product details available to display.</td>
    	</tr>
    	<tr>
    		<td valign="top" nowrap><label><strong>Empty Customer Reviews Text</strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap width="130">
	    	<textarea style="width:350px;height:85px;" name="ReviewAZON_customer_reviews_text" id="ReviewAZON_product_details_text" tabindex="1"><?php echo stripslashes(ReviewAZON_get_affiliate_setting("Empty_Customer_Reviews_Text")); ?></textarea>
    		</td>
    		<td valign="top">Enter the html or text you would like to display when there are no customer reviews available to display.</td>
    	</tr>
    	</table>
    </fieldset>
</div>
<div id="tabs-5">
<fieldset class="options">
    <legend></legend> 
       <table cellpadding="5" cellspacing="10">
        <tr><td colspan="3">&nbsp;</td></tr>
     	<tr>
    		<td valign="top" nowrap><label><strong>Enable SEO links?</strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap width="130">
    			  <input name="ReviewAZON_allow_link_cloaking" type="radio" value="1" <?php if(ReviewAZON_get_affiliate_setting("Allow_Link_Cloaking") == '1') {echo 'checked';} ?> /> Yes
  				  <input name="ReviewAZON_allow_link_cloaking" type="radio" value="0" <?php if(ReviewAZON_get_affiliate_setting("Allow_Link_Cloaking") == '0') {echo 'checked';} ?> /> No
    		</td>
    		<td valign="top">Rewrite links to a user friendly version.</td>
    	</tr>
    	<tr>
    		<td valign="top" nowrap><label><strong>Enable SEO image links?</strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap width="130">
    			  <input name="ReviewAZON_allow_image_link_cloaking" type="radio" value="1" <?php if(ReviewAZON_get_affiliate_setting("Allow_Image_Link_Cloaking") == '1') {echo 'checked';} ?> /> Yes
  				  <input name="ReviewAZON_allow_image_link_cloaking" type="radio" value="0" <?php if(ReviewAZON_get_affiliate_setting("Allow_Image_Link_Cloaking") == '0') {echo 'checked';} ?> /> No
    		</td>
    		<td valign="top">Rewrite image links to a user friendly version.</td>
    	</tr>
     	<tr>
    		<td valign="top" nowrap><label><strong>SEO link prefix:</strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap width="130">
    			  <input name="ReviewAZON_seo_link_prefix" type="text" value="<?php echo ReviewAZON_get_affiliate_setting("SEO_Link_Prefix"); ?>" />
    		</td>
    		<td valign="top">The default link prefix is "Review", but you can change this value to someting else if you wish.</td>
    	</tr>
    	<tr><td colspan="3">&nbsp;</td></tr>
     	<tr><td colspan="3"><?php echo ReviewAZON_link_cloaking_message(); ?></td></tr>
    	</table>
    </fieldset>
</div>
<div id="tabs-6">
<fieldset class="options">
    <legend></legend> 
       <table cellpadding="5" cellspacing="10">
        <tr><td colspan="3">&nbsp;</td></tr>
    	<tr>
    		<td valign="top" nowrap><label><strong>Enable Product Debugging?</strong> &nbsp;&nbsp;</label></td>
    		<td valign="top" nowrap width="130">
    			  <input name="ReviewAZON_enable_debugging" type="radio" value="1" <?php if(ReviewAZON_get_affiliate_setting("Enable_Debugging") == '1') {echo 'checked';} ?> /> Yes
  				  <input name="ReviewAZON_enable_debugging" type="radio" value="0" <?php if(ReviewAZON_get_affiliate_setting("Enable_Debugging") == '0') {echo 'checked';} ?> /> No
    		</td>
    		<td valign="top">If you want to see all the attributes that are returned for a product, you can enable product debugging to see those values. When you enable debugging, a debug link is added to the Add/Edit Post and Page screen in the ReviewAZON control panel. 
    		When you add/edit your product post, you can view all the data that is returned from the Amazon web service for this product. 
    		</td>
    	</tr>
    	</table>
    </fieldset>
</div>
<div id="tabs-7">
<fieldset class="options">
    <legend></legend> 
       <table cellpadding="5" cellspacing="10">
        <tr><td colspan="3">&nbsp;</td></tr>
     	<tr>
    		<td valign="top" nowrap width="130">
    			  <input style="width:200px" onclick="setAction('reinstallreviewazon');return false;" class="button-primary" name="ReviewAZON_reinstall" type="button" value="Reinstall ReviewAZON" />
    		</td>
    		<td valign="top">Use this function to reinstall all the ReviewAZON data tables. This function removes all the existing tables and data associated with ReviewAZON and reinstalls them to the default/out of the box version.</td>
    	</tr>          
    	<tr>
    		<td valign="top" nowrap width="130">
    			  <input style="width:200px;" onclick="setAction('recreatelinkfile');return false;" class="button-primary" name="ReviewAZON_truncate_product_settings" type="button" value="Create Link Redirect File" />
    		</td>
    		<td valign="top">Use this function to create/recreate the reviewazon.php Amazon link redirect file that is located in the root directory of your blog. This file is needed to redirect SEO links to Amazon and is created automatically when SEO links are enabled.
    		<p><img src="<?php echo ReviewAZON_ADMIN_IMAGES ?>error.gif" style="padding-right:5px;vertical-align:sub;" />If this file cannot be created due to permissions, you can copy the file from the wp-content/plugins/ReviewAZON/SEO folder to the root folder of your blog installation.</p>
    		</td>
    	</tr>
     	<tr>
    		<td valign="top" nowrap width="130">
    			  <input style="width:200px;" onclick="setAction('truncatepostsettings');return false;" class="button-primary" name="ReviewAZON_truncate_product_settings" type="button" value="Clear Product Settings Table" />
    		</td>
    		<td valign="top">Use this function if you want to clear all the ReviewAZON product post setting records. This DOES NOT delete blog posts associated with the product post records.</td>
    	</tr>
     	<tr>
    		<td valign="top" nowrap width="130">
    			  <input style="width:200px" onclick="setAction('truncatereportrecords');return false;" class="button-primary" name="ReviewAZON_truncate_click_tracking" type="button" value="Clear Click Tracking Table" />
    		</td>
    		<td valign="top">Use this function if you want to clear all the ReviewAZON click tracking reporting records.</td>
    	</tr>
      	<tr>
    		<td valign="top" nowrap width="130">
    			  <input style="width:200px" onclick="setAction('uninstallreviewazon');return false;" class="button-primary" name="ReviewAZON_uninstall" type="button" value="Uninstall ReviewAZON" />
    		</td>
    		<td valign="top">Use this function to uninstall ReviewAZON. This function removes all the tables and data associated with ReviewAZON. Once you uninstall, you can deactivate the ReviewAZON plugin.</td>
    	</tr>
    	<tr>
    		<td valign="top" nowrap width="130">
    			  <input style="width:200px" onclick="setAction('rollbackdbversion');return false;" class="button-primary" name="ReviewAZON_rollback_db_version" type="button" value="Rollback Database Version" />
    		</td>
    		<td valign="top">Use this function to roll back the ReviewAZON database version. Only use this this if you need to re-update the ReviewAZON table changes from one version to the next. Once you reset the ReviewAZON database version, deactivate the plug-in, then reactivate the ReviewAZON plug-in.<br><br>
    		<strong>Choose database version:</strong>:&nbsp;<select name="ReviewAZON_db_version" id="ReviewAZON_db_version" style="width:20%"><option value="1.0.6">1.0.6</option></select>
    		</td>
    	</tr>     	
    	<tr><td colspan="3"><hr style="background-color:gainsboro;border:1px solid gainsboro;"></td></tr>	
       	<tr>
    		<td valign="top" nowrap width="130">
    			  <input style="width:200px" onclick="setAction('updatetrackingid');return false;" class="button-primary" name="ReviewAZON_uninstall" type="button" value="Update Post Tracking ID" />
    		</td>
    		<td valign="top">
    			Use this function to reset the tracking ID on all posts to a particular tracking ID.<br><br>
				<strong>Choose your tracking ID</strong>:&nbsp;<select name="ReviewAZON_associate_id" id="ReviewAZON_associate_id" style="width:30%">
				<?php 
				global $wpdb;
				global $post;
				$affiliateCountry = ReviewAZON_get_affiliate_setting("AWS_Affiliate_Country");
				$queryTrackingIDs = "SELECT * FROM ".$wpdb->prefix."reviewazon_tracking_id WHERE Affiliate_Country = '{$affiliateCountry}' Order By Display_Order ASC";
				$trackingIDs = $wpdb->get_results($queryTrackingIDs);
				foreach($trackingIDs as $trackingID)
				{
					if(get_post_meta($post->ID, 'ReviewAZON_Tracking_ID', true) == $trackingID->Tracking_ID)
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
				<p><strong>Update the following post types</strong>:</p>   
				<input name="ReviewAZON_update_status" type="radio" value="All" <?php if($selectedStatus == 'All') {echo 'checked';} ?> /> All Post Types<BR>
				<input name="ReviewAZON_update_status" type="radio" value="Publish" <?php if($selectedStatus == 'Publish') {echo 'checked';} ?> /> Published Posts<BR>
				<input name="ReviewAZON_update_status" type="radio" value="Future" <?php if($selectedStatus == 'Future') {echo 'checked';} ?> /> Future Dated Posts<BR>
				<input name="ReviewAZON_update_status" type="radio" value="Draft" <?php if($selectedStatus == 'Draft') {echo 'checked';} ?> /> Draft Posts<BR>
	   		</td>
    	</tr>
		<!-- <tr>
    		<td valign="top" nowrap width="130">
    			  <input style="width:200px;" onclick="setAction('refreshimageratingdata');return false;" class="button-primary" name="ReviewAZON_refresh_image_rating_data" type="button" value="Refresh Image/Rating Data" />
    		</td>
    		<td valign="top">Use this function if you want to refresh the Amazon product image URLs and product review data.</td>
    	</tr> --> 
    	</table>
    </fieldset>
</div>
</div>
</div>
<div class="ReviewAZON" id="dialog" title="Add New Tab" style="display:none">
<br/>
<b>Tab Title</b><br />
<input type="text" id="tab_name" name="tab_name" value="" style="width:100%" /><br /><br />
<b>Tab Content</b><br />
<textarea id="tab_body" name="tab_body" style="width:100%;height:200px;"></textarea><br />
<p><input type="checkbox" name="show_tab" id="show_tab" value="" checked/> <b>Make this tab visible?</b></p>
<p><b>Use the following tokens in the tab content field:</b></p>
<table cellpadding="2" cellspacing="2" width="100%">
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Product Description</td>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Product Details</td>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Customer Reviews</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%DESCRIPTION%%</td>
	    		    <td style="padding-top:2px;font-size:8pt;">%%PRODUCTDETAILS%%</td>
	    			<td style="padding-top:2px;font-size:8pt;">%%CUSTOMERREVIEWS%%</td>    			
	    		</tr>	    	
    	
		    	<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Similar Products</td>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Product Accessories</td>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Video Reviews</td>
	    		
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%SIMILARPRODUCTS%%</td>
	    			<td style="padding-top:2px;font-size:8pt;">%%PRODUCTACCESSORIES%%</td>
	    			<td style="padding-top:2px;font-size:8pt;">%%VIDEOREVIEWS%%</td>

	    		</tr>	    	
		    	<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">eBay Auctions</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%EBAYAUCTIONS%%</td>
	    		</tr>	
</table>
</div>
<div class="ReviewAZON" id="editDialog" title="Edit Tab" style="display:none">
<br/>
<b>Tab Title</b><br />
<input type="text" id="edit_tab_name" name="edit_tab_name" value="" style="width:100%" /><br /><br />
<b>Tab Content</b><br />
<textarea id="edit_tab_body" name="edit_tab_body" style="width:100%;height:200px;"></textarea>	
<p><input type="checkbox" name="edit_show_tab" id="edit_show_tab" value="" /> <b>Make this tab visible?</b></p>
<p><b>Use the following tokens in the tab content field:</b></p>
<table cellpadding="2" cellspacing="2" width="100%">
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Product Description</td>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Product Details</td>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Customer Reviews</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%DESCRIPTION%%</td>
	    		    <td style="padding-top:2px;font-size:8pt;">%%PRODUCTDETAILS%%</td>
	    			<td style="padding-top:2px;font-size:8pt;">%%CUSTOMERREVIEWS%%</td>    			
	    		</tr>	    	
    	
		    	<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Similar Products</td>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Product Accessories</td>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Video Reviews</td>
	    		
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%SIMILARPRODUCTS%%</td>
	    			<td style="padding-top:2px;font-size:8pt;">%%PRODUCTACCESSORIES%%</td>
	    			<td style="padding-top:2px;font-size:8pt;">%%VIDEOREVIEWS%%</td>

	    		</tr>	    	
		    	<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">eBay Auctions</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%EBAYAUCTIONS%%</td>
	    		</tr>	
</table>
<input type="hidden" name="hdn_tab_id" id="hdn_tab_id" value="" />
</div>
<input type="hidden" name="action" id="action" value="" />
<input type="hidden" name="ReviewAZON_selected_tab" id="ReviewAZON_selected_tab" value="<?php echo $_POST['ReviewAZON_selected_tab']?>" />
<?php wp_nonce_field('my-nonce'); ?>
</form>