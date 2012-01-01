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

	
	$posW = strpos(dirname( __FILE__), "wp-content\plugins\ReviewAZON");
	$posL = strpos(dirname( __FILE__), "wp-content/plugins/ReviewAZON");

	if(!empty($posW))
	{
		$path = str_replace("wp-content\plugins\ReviewAZON","",dirname( __FILE__));
		$file = '\wp-load.php';
	}
	
	if(!empty($posL))
	{
		$path = str_replace("wp-content/plugins/ReviewAZON","",dirname( __FILE__));
		$file = '/wp-load.php';
	}
	
	require_once($path.$file);
	
	$nonce= wp_create_nonce  ('my-nonce');

	global $wpdb,$current_user;
	$publishNow = false;
	get_currentuserinfo();
	$blogtime = current_time('mysql'); 
	
	$noComments = 'closed';
	$noPings = 'closed';
	
	if(isset($_POST['chkNoComments']))
	{
		$noComments = 'open';
	}
	
	if(isset($_POST['chkNoPings']))
	{
		$noPings = 'open';
	}
	
	list( $today_year, $today_month, $today_day, $hour, $minute, $second ) = split( '([^0-9])', $blogtime );
	$time = '<img onclick="getCurrentTime()" src="'.ReviewAZON_ADMIN_IMAGES.'clock.gif" style="vertical-align:middle;padding-left:5px;padding-right:5px;cursor:pointer;" title="Get Current Time" />';
	
	if(isset($_POST['hdnSubmit']) && $_POST['hdnSubmit'] == "submit")
	{
		if(check_admin_referer( 'my-nonce'))
		{
			if(isset($_POST['chkPublishBulkReviewsNow']))
			{
				$timestamp = time();
				$publishNow = true;
			}
			else
			{
				$startTimestamp = strtotime($_POST['txtDate'].' '.$_POST['txtCurrentTime']);
				$timestamp = strtotime($_POST['txtDate'].' '.$_POST['txtCurrentTime']);
			}
			
			$query = "SELECT * FROM ".$wpdb->prefix."reviewazon_bulk_reviews Order By Display_Order ASC";
			$pendingBulkReviews = $wpdb->get_results($query);
			$intervalMinutes = $_POST['txtIntervalMinutes'];
			if(count($pendingBulkReviews) > 0)
			{
				foreach($pendingBulkReviews as $pendingBulkReview)
				{
					if(!$publishNow)
					{
						if(isset($_POST['chkRandomizePosts']))
						{
							$timestamp = $timestamp + ((60*60)*$_POST['txtInterval']);
							$timestamp = strtotime("+$intervalMinutes minutes",$timestamp);
							$publishTime = rand( $startTimestamp, $timestamp );
						}
						else
						{
							$timestamp = $timestamp + ((60*60)*$_POST['txtInterval']);
							$timestamp = strtotime("+$intervalMinutes minutes",$timestamp);
							$publishTime = $timestamp;
						}
					}
					else
					{
						$publishTime = $timestamp;
					}
					
					$autoTags = "";
					
					if(ReviewAZON_get_affiliate_setting("Create_Tags"))
					{
						$autoTags = ReviewAZON_create_tags($pendingBulkReview->Title);
					}
					
					$post = array(
						'post_title' => $pendingBulkReview->Title,
						'post_content' => '[ReviewAZON asin="'.$pendingBulkReview->ASIN.'"]',
						'post_status' => $_POST['cboStatus'],
						'post_author' => $_POST['cboPostName'],
						'post_date' => date("Y-m-d H:i:s", $publishTime),
						'post_category' => $_POST['chkCategories'],
						'tags_input' => $autoTags,
						'comment_status' => $noComments,
						'ping_status' => $noPings
						);
					 $postID = wp_insert_post($post);
					 
					 update_post_meta($postID,"ReviewAZON_Tracking_ID",$_POST['ReviewAZON_associate_id']);
					
					 ReviewAZON_add_bulk_review_data($pendingBulkReview->ASIN,$postID,$_POST['txtEbayNumber'],$_POST['txtOverstockNumber']);
					
					 $queryDelete = "DELETE FROM ".$wpdb->prefix."reviewazon_bulk_reviews WHERE ID = '{$pendingBulkReview->ID}'";
					 $wpdb->query($queryDelete);
					
	
				}
			}
			$message = '<div id="message" class="updated fade"><p><img src="'.ReviewAZON_ADMIN_IMAGES.'accept.gif" align="absmiddle" />&nbsp;Bulk product reviews posted successfully.</p></div>';
		}
		else
		{
			die('');
		}
	}
	
	function ReviewAZON_add_bulk_review_data($asin, $postId, $numEbayListings, $numOverstockListings)
	{
		global $wpdb;
		$showDescription = false;
		$showCustomerReviews = false;
		$showSimilarProducts = false;
		$showProductAccessories = false;
		$showEbayOptions = false;
		$showOverstockOptions = false;
		
		$displayDescription = "display:none";
		$displayCustomerReviews = "display:none";
		$displaySimilarProducts = "display:none";
		$displayProductAccessories = "display:none";
		$displayEbayOptions = "display:none";
		$displayOverstockOptions = "display:none";
		
		try
		{
				
			$current = ReviewAZON_get_amazon_current_node($asin,null,null,null,$_POST['ReviewAZON_associate_id']);	
			
			$averageRatingNumber = $current->CustomerReviews->AverageRating;
			$customerReviewCount = $current->CustomerReviews->TotalReviews;
			
			$smallImageUrl = $current->SmallImage->URL;
			$mediumImageUrl = $current->MediumImage->URL;
			$largeImageUrl = $current->LargeImage->URL;
			$title = addslashes($current->ItemAttributes->Title);
			
			$amazonProdDesc = GetProductEditoralAmazonDotCom($current);
			$normalProdDesc = GetProductEditoralStandard($current);

			$reviewExcerpt = "";
			if(!empty($amazonProdDesc))
			{
				$reviewExcerpt = addslashes(strip_tags($amazonProdDesc));
			}
			else
			{
				if(!empty($normalProdDesc))
				{
					$reviewExcerpt = addslashes(strip_tags($normalProdDesc));
				}
			}

			if(isset($_POST['chkGenerateExcerpts']))
			{
				$excerptLength = 0;
				if(isset($_POST['txtExcerptLength']))
				{
					$excerptLength = $_POST['txtExcerptLength'];
				}
				$trimmedDescription = substr($reviewExcerpt,0,$excerptLength);
				$updatePost = get_post($postId);
				$updatePost->post_excerpt = $trimmedDescription;
				wp_update_post($updatePost);
			}
						
			
			$totalReviewPages = $current->CustomerReviews->TotalReviewPages;
					
			$description = "";
			if(!empty($amazonProdDesc))
			{
				$description = addslashes($amazonProdDesc);
			}
			else
			{
				if(!empty($normalProdDesc))
				{
					$description = addslashes($normalProdDesc);
				}
			}	

			if(ReviewAZON_get_affiliate_setting("Cache_Similar_Products") & ReviewAZON_get_affiliate_setting("Page_Cache_Length") != 0)
			{
				$showSimilarProducts = true;
				$similarProducts = addslashes(SimilarProducts($current));
			}
			
			if(ReviewAZON_get_affiliate_setting("Cache_Product_Accessories")&& ReviewAZON_get_affiliate_setting("Page_Cache_Length") != 0)
			{
				$showProductAccessories = true;
				$productAccessories = addslashes(ProductAccessories($current));	
			}
			
			$ebaySearchQuery = '[phpbay]'.$title.', '.$numEbayListings.'[/phpbay]';
			$overstockSearchQuery = '[phpostock]'.$title.', '.$numOverstockListings.'[/phpostock]';
			$ebayWidgetTitle = $title;
			$showEbayOptions = true;		

			
			$queryDelete = "DELETE FROM ".$wpdb->prefix."reviewazon_post_settings WHERE Post_ID = '{$postId}'";
			$wpdb->query($queryDelete);
			
			$query = "INSERT INTO ".$wpdb->prefix."reviewazon_post_settings SET Post_ID = '{$postId}',
															   ASIN = '{$asin}',
															   Customer_Review_Count = '{$customerReviewCount}',
															   Product_Review_Rating = '{$averageRatingNumber}',
															   Ebay_Search_Query = '{$ebaySearchQuery}',
															   Overstock_Search_Query = '{$overstockSearchQuery}',
															   Review_Excerpt = '{$reviewExcerpt}',
															   Review_Description = '{$description}',
															   Review_Title = '{$title}',
															   Ebay_Widget_Title = '{$ebayWidgetTitle}',
															   Product_Image_Small = '{$smallImageUrl}',
															   Product_Image_Medium = '{$mediumImageUrl}',
															   Product_Image_Large = '{$largeImageUrl}',
															   Similar_Products = '{$similarProducts}',
															   Product_Accessories = '{$productAccessories}'
															   ";
			$wpdb->query($query);
		}
		catch(Exception $ex)
		{
			echo $ex->message;
		}	
	}
	$getSavedSearchResults = "height: 600";
	$savedSearchPageIndex = get_option("search_page");
	if(ReviewAZON_get_affiliate_setting("Save_Last_Search_Results"))
	{
		$getSavedSearchResults = "height: 600,
								  open: function(event, ui) {
									  if(jQuery('#search_text').val() != '')
									  {
									  	getAmazonItemData('{$savedSearchPageIndex}');
									  }
								  }";
		
	}

?>
<link type="text/css" href="<?php echo ReviewAZON_ADMIN ?>css/admin.css" rel="Stylesheet" />
<link type="text/css" href="<?php echo ReviewAZON_ADMIN ?>theme/ui.all.css" rel="Stylesheet" />
<script type="text/javascript" src="<?php echo ReviewAZON_ADMIN ?>js/jquery-1.3.1.js"></script>
<script type="text/javascript" src="<?php echo ReviewAZON_ADMIN ?>js/jquery-ui-personalized-1.6rc6.js"></script>
<script type="text/javascript">
jQuery(document).ready(function(){
	
	jQuery('#dialog').dialog({
		autoOpen: false,
		resizable: false,
		modal: true,
		buttons: {
			"Close": function() { 
				jQuery(this).dialog("close"); 
			} 
		},
		overlay:{ background: "gray", opacity: 0.9 },
		width: 750,
		<?php echo $getSavedSearchResults ?>				
	});

	jQuery('#dialog').removeAttr("style");

	jQuery('#datepicker').datepicker({
		inline: true,
		dateFormat: 'yy-mm-dd',
		onSelect: function(dateText){
		jQuery('#txtDate').val(dateText);
		}		
	});

	jQuery("#test-list").sortable({
	      handle : '.handle',
	      update : function () {
			jQuery('#test-list').css("opacity","0.4");
			var order = jQuery('#test-list').sortable('serialize');
			jQuery.get("<?php echo ReviewAZON_PROCESS_REQUEST_FILE ?>?"+order+"&action=setbulkreviewsortorder&_ajax_nonce=<?php echo $nonce ?>",{}, function(data){
				jQuery('#test-list').css("opacity","");
			});
			}	
	    });

	jQuery('#chkPublishBulkReviewsNow').click(function(){
		if($(this).attr("checked"))
		{
			jQuery('#txtCurrentTime').attr("disabled","disabled");
			jQuery('#txtInterval').attr("disabled","disabled");
			jQuery('#chkRandomizePosts').attr("disabled","disabled");
			
		}
		else
		{
			jQuery('#txtCurrentTime').attr("disabled","");
			jQuery('#txtInterval').attr("disabled","");
			jQuery('#chkRandomizePosts').attr("disabled","");
			
		}
	});

	jQuery('#chkRandomizePosts').click(function(){
		if($(this).attr("checked"))
		{
			jQuery('#chkPublishBulkReviewsNow').attr("disabled","disabled");
		}
		else
		{
			jQuery('#chkPublishBulkReviewsNow').attr("disabled","");
		}
	});
});

function validateSettings()
{
	if(jQuery('#test-list').children('li:first').text() != "Bulk review list is empty")
	{
		if(jQuery('#txtCurrentTime').val() != "" && jQuery('#txtInterval').val() != "")
		{
			var itemCount = jQuery('#test-list').children().length;
			var postStatus = jQuery('#cboStatus').val();
			if(postStatus == "draft")
			{
				if(confirm("Are you sure you want to create "+itemCount+ " product post(s) in DRAFT mode? \n\n **NOTICE** These posts will not publish automatically. If you want them to publish automatically, set the Post Status to Published before you save."))
				{
					jQuery('#frmMain').submit();
					return true;
				}
			}
			else
			{
				if(confirm("You are about to create " +itemCount + " product post(s). Continue?"))
				{
					jQuery('#frmMain').submit();
					return true;
				}
			}

			return false;
		}
		else
		{
			alert("Enter a valid time and interval.");
			return false;
		}
	}	
	else
	{
		alert("You currently do not have any products to add. \n\nYou can add Amazon product reviews by clicking on the Amazon Product Search button.");
		return false;
	}
	return false;
}

function getAmazonItemData(pageNumber)
{
	var queryText = jQuery('#search_text').val();
	var searchCategory = jQuery('#SearchIndexValue').val();
	var searchSort = jQuery('#ReviewAZON_Sort_Value').val();
	jQuery("#ReviewAZON_Result").empty();
	jQuery("#ReviewAZON_Result").html('<div style="position:relative;top:150px;left:300px;width:130px;height:60px;"><img id="video_loader_amazon_results" src="<?php echo ReviewAZON_ADMIN_IMAGES ?>ajax-loader.gif" style="display:none;" /></div>');
	jQuery('#video_loader_amazon_results').show();
	jQuery.post("<?php echo ReviewAZON_PROCESS_REQUEST_FILE ?>", { sort: searchSort, action: 'getproductsearch', query: queryText, searchIndex: searchCategory, itemPage: pageNumber, requestType: 'bulk', _ajax_nonce:'<?php echo $nonce ?>' }, function(data){
		jQuery("#ReviewAZON_Result").html(data);
		jQuery('#video_loader_amazon_results').hide();
    });				
}

function selectAllItems()
{
	jQuery('.addBulkLink').trigger('click');
}

function addAmazonBulkLink(title, asin, imageURL, brand)
{
	jQuery('#ajaxloader_'+asin).show();
	var id = '#bulkLink_' + asin;
	var text = '<img src="<?php echo ReviewAZON_ADMIN_IMAGES ?>accept.gif" align="absmiddle" />&nbsp;<span style="color:blue;font-weight:bold;">Item Added</span>';

	jQuery.post("<?php echo ReviewAZON_PROCESS_REQUEST_FILE ?>", { action: 'addbulkreview', title: title, imageURL: imageURL, asin: asin, _ajax_nonce:'<?php echo $nonce ?>' }, function(data){
		jQuery('#test-list').html(data);
		jQuery(id).empty();
		jQuery(id).html(text);
    });
<?php 
	if(ReviewAZON_get_affiliate_setting("Create_Brand_Category"))
	{
		echo "addPostCategory(brand,'');";
	}
?>
}

function addPostCategory(name,parent)
{
	jQuery.post("<?php echo ReviewAZON_PROCESS_REQUEST_FILE ?>", { action: 'addcategory', title: name, parent: parent, _ajax_nonce:'<?php echo $nonce ?>' }, function(data){
		jQuery('#bulkCats').html(data);
	});
	jQuery.post("<?php echo ReviewAZON_PROCESS_REQUEST_FILE ?>", { action: 'refreshcats', _ajax_nonce:'<?php echo $nonce ?>'}, function(data){
		jQuery('#newcat_parent').html(data);
	});
	jQuery('#newcat').val("");
}

function removeAmazonBulkItem(id)
{
	if(confirm("Remove this item from the list?"))
	{
		jQuery.post("<?php echo ReviewAZON_PROCESS_REQUEST_FILE ?>", { action: 'removebulkreview', id: id, _ajax_nonce:'<?php echo $nonce ?>' }, function(data){
			jQuery('#test-list').html(data);
	    });
	}
}

function removeAmazonBulkItems()
{
	if(jQuery('#test-list').children().length > 1)
	{
		if(confirm("Remove all bulk reviews from the list?"))
		{
			jQuery.post("<?php echo ReviewAZON_PROCESS_REQUEST_FILE ?>", { action: 'removebulkreviews', _ajax_nonce:'<?php echo $nonce ?>' }, function(data){
				jQuery('#test-list').html(data);
		    });
		}
	}
	else if(jQuery('#test-list').children().length = 1)
	{
		if(jQuery('#test-list').children()[0].id != 'default_item')
		{
			if(confirm("Remove all bulk reviews from the list?"))
			{
				jQuery.post("<?php echo ReviewAZON_PROCESS_REQUEST_FILE ?>", { action: 'removebulkreviews', _ajax_nonce:'<?php echo $nonce ?>' }, function(data){
					jQuery('#test-list').html(data);
			    });
			}
		}
		else
		{
			alert("There are no reviews to remove at this time.");
		}
	}
	else
	{
		alert("There are no reviews to remove at this time.");
	}
}

function getCurrentTime()
{
	jQuery.get("<?php echo ReviewAZON_PROCESS_REQUEST_FILE ?>",{action: 'getcurrenttime', _ajax_nonce:'<?php echo $nonce ?>' },function(data){		
	jQuery('#txtCurrentTime').val(data);
	});
}

function addCustomCategory()
{
	if(jQuery('#newcat').val() == "")
	{
		alert("You must enter a category name.");
	}
	else
	{
		addPostCategory(jQuery('#newcat').val(), jQuery('#newcat_parent').val());
	}

}


</script>
<style type="text/css">
	.handle{cursor:move;}
</style>


<form id="frmMain" method="post" action="">
	<div class="wrap">
		<h2><img src="<?php echo ReviewAZON_ADMIN_IMAGES ?>largestar.gif" style="vertical-align:middle;margin:5px;" />Manage Bulk Product Posts - ReviewAZON Wordpress Plugin Version <?php echo REVIEWAZONVER ?></h2>
		<br />
		<?php echo $message ?>
	<p>It's easy to add multiple Amazon product reviews using the Bulk Product Post page. Just click on the Amazon Product Search link to search for your review products and click on the Add Review link to add them to the queue. Once in the queue, you can re-arrange them to post in the order you want them to show
	as well as schedule them to post at certain intervals. You can also assign them to categories, set the posting name, as well as posting posting status.</p><br>
	<table cellpadding="2" cellspacing="2">
	<tr>
		<td colspan="2" style="padding-bottom:15px;">
			<span><img src="<?php echo ReviewAZON_ADMIN_IMAGES ?>search.gif" style="vertical-align:middle;padding-left:5px;padding-right:5px" /><a onclick="jQuery('#dialog').dialog('open');return false;" style="cursor:pointer;font-weight:bold;font-size:10pt;">Amazon Product Search</a></span>
			<span> | <img src="<?php echo ReviewAZON_ADMIN_IMAGES ?>delete.gif" style="vertical-align:middle;padding-left:5px;padding-right:5px" /><a onclick="removeAmazonBulkItems()" style="cursor:pointer;font-weight:bold;font-size:10pt;">Clear Bulk Post List</a></span>
			<span> | <img src="<?php echo ReviewAZON_ADMIN_IMAGES ?>disk.gif" style="vertical-align:middle;padding-left:5px;padding-right:5px" /><a onclick="return validateSettings()" id="linkAddBulkReviews" style="cursor:pointer;font-weight:bold;font-size:10pt;">Save Bulk Product Posts</a></span>		
		</td>
	</tr>
	<tr>
		<td  valign="top">
		<div>
			<ul id="test-list">	
<?php 
			$query = "SELECT * FROM ".$wpdb->prefix."reviewazon_bulk_reviews Order By Display_Order ASC";
			$pendingBulkReviews = $wpdb->get_results($query);
			
			if(count($pendingBulkReviews) > 0)
			{
				foreach($pendingBulkReviews as $pendingBulkReview)
				{
					echo '<li style="padding:5px;background-color:white;border:1px solid gainsboro;width:350px;" id="bulkReview_'.$pendingBulkReview->ASIN.'"><table class="handle" cellpadding="2" cellspacing="2" style="padding-top:10px;"><tr><td valign="top"><img width="35" height="35" src="'.$pendingBulkReview->Product_Image.'" style="vertical-align:text-top;padding-right:5px;" /></td><td valign="top" style="font-size:1.0em;font-family:arial;"><span><b>'.$pendingBulkReview->Title.'</b></span><br /><span><img onclick="removeAmazonBulkItem('.$pendingBulkReview->ID.')" src="'.ReviewAZON_ADMIN_IMAGES.'delete.gif" style="vertical-align:middle;cursor:pointer;" />&nbsp;Remove</span></td></tr></table></li>';
				}
			}
			else
			{
				echo '<li style="padding:10px;background-color:white;border:1px solid gainsboro;width:300px" id="default_item" style="padding:10px;"><img src="'.ReviewAZON_ADMIN_IMAGES.'error.gif" style="vertical-align:middle;padding-left:5px;padding-right:5px;" />Bulk review list is empty</li>';	
			}
?>
				
			</ul>
			</div>
		</td>
		<td valign="top" style="padding-left:10px;">
			<div style="border:solid 1px gainsboro;background-color:white;padding:10px;padding-top:0px;width:228px;">
			<p><img src="<?php echo ReviewAZON_ADMIN_IMAGES ?>wrench.gif" style="vertical-align:middle;padding-left:5px;padding-right:10px" /><b>Bulk Review Options</b></p>
			<hr style="border:1px solid gainsboro;">
			<p><input type="checkbox" id="chkGenerateExcerpts" name="chkGenerateExcerpts" value="" />&nbsp; <b>Create Post Excerpt</b></p>
			<p><strong>Excerpt Length:</strong> <input type="text" id="txtExcerptLength" name="txtExcerptLength" value="200" style="width:50px">&nbsp;<strong>chars</p>
			<hr style="border:1px solid gainsboro;">
			<div style="padding-bottom:5px;padding-top:5px;"><b>Posting Date</b></div>
			<div id="datepicker"></div><br />
			<hr style="border:1px solid gainsboro;">
			<p><div style="padding-bottom:5px;"><b>Post Review As</b></div><select name="cboPostName" id="cboPostName" style="width:90%"><option value="<?php echo $current_user->ID; ?>"><?php echo get_usermeta($current_user->ID,'nickname');?></option></select></p>
			<p><div style="padding-bottom:5px;"><b>Post Status</b></div><select name="cboStatus" id="cboStatus" style="width:90%"><option value="draft">Draft</option><option value="publish">Publish</option><option value="pending">Pending</option></select></p>
			<p><input type="checkbox" id="chkPublishBulkReviewsNow" name="chkPublishBulkReviewsNow" value="" />&nbsp; <b>Publish Bulk Posts Now</b></p>
			<p><input type="checkbox" id="chkRandomizePosts" name="chkRandomizePosts" value="" />&nbsp; <b>Randomize Posting Date and Time?</b></p>
			<p><div style="padding-bottom:5px;"><b>Start Post Time</b></div><i>(hh:mm:ss)</i><input type="text" id="txtCurrentTime" name="txtCurrentTime" value="<?php echo $hour.':'.$minute.':'.$second ?>"><?php echo $time?></p>
			<p><div style="padding-bottom:5px;"><b>Posting Interval</b></div><input type="text" id="txtInterval" name="txtInterval" value="24" style="width:50px">&nbsp;hrs&nbsp;&nbsp;&nbsp;<input type="text" id="txtIntervalMinutes" name="txtIntervalMinutes" value="0" style="width:50px">&nbsp;Minutes</p>
		</div>
		</td>
		<td valign="top" style="padding-left:10px;">
		<div style="border:solid 1px gainsboro;background-color:white;padding:10px;padding-top:0px;width:228px;">
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
		<p><input type="checkbox" id="chkNoComments" name="chkNoComments" value="" checked="checked"/>&nbsp;Allow post comments.</p>
		<p><input type="checkbox" id="chkNoPings" name="chkNoPings" value="" checked="checked" />&nbsp;Allow post pings.</p>
		<p>Show <input type="text" id="txtEbayNumber" name="txtEbayNumber" value="10" style="width:50px">&nbsp;eBay Listings</p>
		<p>Show <input type="text" id="txtOverstockNumber" name="txtOverstockNumber" value="10" style="width:50px">&nbsp;PhpOStock Listings</p>
		
		
		
		
		
		<p><b>Posting Categories</b></p>
						<hr style="border:1px solid gainsboro;">
						<ul id="bulkCats">
<?php 
	$categories = get_categories('hide_empty=0&hierarchical=1&orderby=name'); 

	foreach ($categories as $cat) 
	{
		if($cat->category_parent == 0)
		{
			echo '<li style="padding:5px;"><input type="checkbox" id="chkCategories" name="chkCategories[]" value="'.$cat->cat_ID.'" />&nbsp;'.$cat->cat_name.'</li>';
			$subcategories = get_categories('hide_empty=0&child_of='.$cat->cat_ID.'&orderby=name');
			foreach ($subcategories as $subcat)
			{
				echo '<li style="padding:5px;margin-left:10px;">&nbsp;-&nbsp;<input type="checkbox" id="chkCategories" name="chkCategories[]" value="'.$subcat->cat_ID.'" />&nbsp;'.$subcat->cat_name.'</li>';
			}
		}
	}
?>
</ul>
			<hr style="border:1px solid gainsboro;">

	<p id="category-add">
		<label for="newcat">Add New Category</label><br><input type="text" tabindex="3" value="" id="newcat" name="newcat"/>
		<br><label for="newcat_parent" >Parent category:</label><br>
		<select tabindex="3" id="newcat_parent" name="newcat_parent" style="width:90%">
		<option value="none">Select Parent Category</option>
<?php 
	$categoriesOption = get_categories('hide_empty=0&hierarchical=1&orderby=name');
	foreach ($categoriesOption as $catoption)
	{
		echo '<option value="'.$catoption->cat_ID.'">'.$catoption->cat_name.'</option>';
	}
?>
		</select>
		<br><input onclick="addCustomCategory();" type="button" tabindex="3" value="Add" class="add:categorychecklist:category-add button" id="category-add-sumbit"/></p>
	</div>	
		</td>
	</tr>	
	</table>
	</div>
	<input type="hidden" name="txtDate" id="txtDate" value="<?php echo $today_year.'-'.$today_month.'-'.$today_day; ?>" />
	<input type="hidden" name="hdnSubmit" id="hdnSubmit" value="submit" />
	<?php wp_nonce_field('my-nonce'); ?>
</form>
<div class="ReviewAZON" id="dialog" title="Amazon Product Search" style="display:none">
<?php 
if(ReviewAZON_get_affiliate_setting("AWS_Webservice_Key") == "" || ReviewAZON_get_affiliate_setting("AWS_Affiliate_Country") == "")
{
	echo '<p><img src="'.ReviewAZON_ADMIN_IMAGES.'error.gif" style="padding-right:5px;vertical-align:sub;" /><b>Amazon web services settings error: Web Services key or affiliate program not set!</b></p>';
	echo '<p>You must have a valid Amazon Web Services key to get started. A valid affiliate program must also be selected.</P>';		
	echo '<p>If you have already signed up for your AWS key, you will need to enter it on the <a href="admin.php?page=Affiliate Settings">ReviewAZON Affiliate Settings</a> page.</p>';
	echo '<p>If you need to create an AWS account, you can visit <a target="_blank" href="http://aws.amazon.com/">Amazon Web Services</a> to sign up!</p>';
}
else
{
?>
<table cellpadding="2" cellspacing="2" style="height:50px;text-align:center;margin-left:20px;" border="1">
<tr>
	<td valign="bottom"><?php include('aws_affiliate_dropdown.php')?></td>
		<td valign="bottom"><input type="text" name="search_text" id="search_text" style="width:250px;height:25px;border:solid 1px gainsboro;" value="<?php echo get_option("search_text") ?>" />&nbsp;<input type="button" onclick="getAmazonItemData('1')" class="button-primary" value="Search" />&nbsp;</td></tr>
</table>
<div id="ReviewAZON_Result" style="overflow:auto;width:715px;height:450px;">
<?php echo get_option("search_results") ?>
</div>
<?php 
}
?>
</div>