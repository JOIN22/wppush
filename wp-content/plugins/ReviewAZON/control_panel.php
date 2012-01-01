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

$nonce= wp_create_nonce  ('my-nonce');

	$getSavedSearchResults = "height: 600";

	if(ReviewAZON_get_affiliate_setting("Save_Last_Search_Results"))
	{
		$getSavedSearchResults = "height: 600,
								  open: function(event, ui) {
									  if(jQuery('#search_text').val() != '')
									  {
									  	getAmazonItemData(jQuery('#hdnPageIndex').val());
									  }
								  }";
		
	}
?>

<link type="text/css" href="<?php echo ReviewAZON_ADMIN ?>metabox/theme_ReviewAZON/ui.all.css" rel="Stylesheet" />
<link type="text/css" href="<?php echo ReviewAZON_ADMIN ?>css/admin.css" rel="Stylesheet" />
<script type="text/javascript" src="<?php echo ReviewAZON_ADMIN ?>metabox/jquery-ui-personalized-1.5.3.js"></script>

<script type="text/javascript">

jQuery("form").submit(function() {	
	jQuery('#tmpPostID1').val(jQuery('#post_ID').val());
});

jQuery(document).ready(function(){
	// Dialog			
	jQuery('#dialog').dialog({
		autoOpen: false,
		resizable: false,
		buttons: {
			"Close": function() { 
				jQuery(this).dialog("close"); 
			}
		},
		modal: true,
		overlay:{ background: "gray", opacity: 0.9 },
		width: 750,
		<?php echo $getSavedSearchResults ?>
	});
	jQuery('#youtube_dialog').dialog({
		autoOpen: false,
		resizable: false,
		buttons: {
			"Close": function() { 
				jQuery(this).dialog("close"); 
			} 
		},
		modal: true,
		overlay:{ background: "gray", opacity: 0.9 },
		width: 750,
		height: 600
	});

	jQuery('#tmpID').val(jQuery('#post_ID').val());
	jQuery('#dialog').removeAttr("style");
	jQuery('#youtube_dialog').removeAttr("style");	

});

function getAmazonItemData(pageNumber)
{
	var queryText = jQuery('#search_text').val();
	var searchCategory = jQuery('#SearchIndexValue').val();
	var searchSort = jQuery('#ReviewAZON_Sort_Value').val();
	jQuery('#hdnPageIndex').val(pageNumber);
	jQuery("#ReviewAZON_Result").empty();
	jQuery("#ReviewAZON_Result").html('<div style="position:relative;top:150px;left:285px;width:130px;height:60px;"><img id="video_loader_amazon_results" src="<?php echo ReviewAZON_ADMIN_IMAGES ?>ajax-loader.gif" style="display:none;" /></div>');
	jQuery('#video_loader_amazon_results').show();
	jQuery.post("<?php echo ReviewAZON_PROCESS_REQUEST_FILE ?>", { sort: searchSort, action: 'getproductsearch', query: queryText, searchIndex: searchCategory, itemPage: pageNumber, requestType: jQuery('#hdnRequestType').val(), _ajax_nonce:'<?php echo $nonce ?>' }, function(data){
		jQuery("#ReviewAZON_Result").html(data);
		jQuery('#video_loader_amazon_results').hide();
    });				
}

function setEditorContent(sEditorID, sContent) 
{
    var oEditor = document.getElementById(sEditorID);
    if(oEditor) {
        tinyMCE.execInstanceCommand(sEditorID, 'mceSetContent', false, sContent);
    }
    return;
}

function insertAmazonLink(title, asin, imageURL) 
{	
	jQuery('#dialog').dialog("close");
	jQuery('#reviewazon_selected_product').empty();	
	jQuery('#reviewazon_selected_product').html('<img align="absmiddle" id="amazon_loader" src="<?php echo ReviewAZON_ADMIN_IMAGES ?>ajax-loader.gif" />');
	if(jQuery('#hdnDialogType').val() == "fullpost")
	{
		if(typeof(tinyMCE) !== 'undefined')
		{
			setEditorContent('content','');
		}
		document.getElementById('title').value = '';
		document.getElementById('title').value = title;
	}
	if(jQuery('#hdnDialogType').val() == "fullpost")
	{
		send_to_editor('[ReviewAZON asin="' + asin + '" display="fullpost"]');	
	}
	else
	{
		send_to_editor('[ReviewAZON asin="' + asin + '" display="inlinepost"]');
	}
	jQuery('#amazon_loader').hide();
	jQuery('#reviewazon_selected_product').append('<img style="padding-left:10px;" src="' + imageURL + '" />'); 
	jQuery('#hdnASIN').val(asin);
<?php	
if(ReviewAZON_get_affiliate_setting("Create_Tags"))
{
?>
	jQuery.post("<?php echo ReviewAZON_PROCESS_REQUEST_FILE ?>", { action: 'getPostTags', postTitle: title , _ajax_nonce:'<?php echo $nonce ?>'}, function(data){
		if(jQuery("#newtag").length > 0)
		{
			jQuery("#newtag").val(data);
		}
		if(jQuery("#new-tag-post_tag").length > 0)
		{
			jQuery("#new-tag-post_tag").focus();
			jQuery("#new-tag-post_tag").val(data);
			jQuery("input.tagadd").trigger('click');
		}		
    });
<?php
}
?>
	addAmazonItemData(asin);
}

function addAmazonItemData(asin)
{
	var postID;
	if(jQuery('#tmpID').val() != '')
	{
		postID = jQuery('#tmpID').val();
	}
	else
	{
		postID = jQuery('#post_id').val();
	}
	jQuery('#ReviewAZON_post_properties').empty();
	jQuery('#ReviewAZON_post_properties').html('<img align="absmiddle" id="amazon_loader_properties" src="<?php echo ReviewAZON_ADMIN_IMAGES ?>ajax-loader.gif" />');
	jQuery.post("<?php echo ReviewAZON_PROCESS_REQUEST_FILE ?>", { action: 'addproductdata', asin: asin, postid: postID , _ajax_nonce:'<?php echo $nonce ?>'}, function(data){
		jQuery("#ReviewAZON_post_properties").html(data);
		jQuery('#amazon_loader_properties').hide();
		<?php
				if(ReviewAZON_get_affiliate_setting("Create_Brand_Category"))
				{
			  		echo "if(jQuery('#hdnBrand').val() != '')";
			  		echo "{";
			  		echo "jQuery('#category-add-toggle').trigger('click');";
			  		echo "jQuery('#newcat').focus();";
			  		echo "jQuery('#newcat').val(jQuery('#hdnBrand').val());";
			  		echo "jQuery('#category-add-sumbit').trigger('click');";  		
			  		echo "}\n\n";
				}   
		?>		
    });

	
}

function getVideoReviewData(url,pageNumber)
{
	var queryText = jQuery('#video_search_text').val();
	jQuery("#ReviewAZON_video_Result").empty();
	jQuery("#ReviewAZON_video_Result").html('<div style="position:relative;top:150px;left:285px;width:130px;height:60px;"><img id="video_loader_video_results" src="<?php echo ReviewAZON_ADMIN_IMAGES ?>ajax-loader.gif" /></div>');
	jQuery.post("<?php echo ReviewAZON_VIDEO_SEARCH_FILE ?>", { action: 'getvideos', url: url, query: queryText, itemPage: pageNumber, postid: jQuery('#tmpID').val() , _ajax_nonce:'<?php echo $nonce ?>'}, function(data){
		jQuery("#ReviewAZON_video_Result").html(data);
		jQuery('#video_loader_video_results').hide();
		
    });		
}

function addVideoReview(thumbnailUrl,videoUrl, title)
{	
	var text = '<img src="<?php echo ReviewAZON_ADMIN_IMAGES ?>accept.gif" align="absmiddle" />&nbsp;<span style="color:blue;font-weight:bold;">Item Added</span>';
	jQuery('#ajaxloader_'+videoUrl).show();
	jQuery.post("<?php echo ReviewAZON_VIDEO_SEARCH_FILE ?>", { action: 'addvideo', postid: jQuery('#tmpID').val(),
		       thumbnailurl: thumbnailUrl, videourl: videoUrl, videotitle: title, _ajax_nonce:'<?php echo $nonce ?>' }, function(data){
		    		jQuery("#video_list").html(data);
		    		jQuery('#videoLink_'+videoUrl).html(text);
		       }); 	
}

function deleteVideoReview(id,videoId,postId)
{
	var answer = confirm("Remove this video?");
	if(answer)
	{	
		jQuery.post("<?php echo ReviewAZON_VIDEO_SEARCH_FILE ?>", { action: 'removevideo', videoid: videoId, postid: postId, _ajax_nonce:'<?php echo $nonce ?>' }, function(data){
			jQuery("#video_list").html(data);
		});
	}
}

function showDebugInfo()
{
	if(jQuery('#hdnASIN').val() == "")
	{
		alert("You must choose a product to view first.");
	}
	else
	{
		var asin = jQuery('#hdnASIN').val();
		jQuery.post("<?php echo ReviewAZON_PROCESS_REQUEST_FILE ?>", { action: 'getsignedurl', asin: asin , _ajax_nonce:'<?php echo $nonce ?>'}, function(data){
			window.open(data,"View AWS Debug Information");	
	    });
	}	
}

function addInlineSearchQuery()
{
	send_to_editor('[ReviewAZON display="searchquery" query="' + jQuery('#search_text').val() + '" count="5" category="' + jQuery('#SearchIndexValue').val() + '" page="' + jQuery('#hdnPageIndex').val() + '" sort="' + jQuery('#ReviewAZON_Sort_Value').val() + '"]');	
	jQuery('#dialog').dialog('close');
}

function addSingleInlinePost(asin)
{
	var trackingID = jQuery('#ReviewAZON_associate_id').val();
	send_to_editor('[ReviewAZON asin="' + asin + '" display="inlinepost"]');	
	jQuery('#dialog').dialog('close');
}

function openAmazonSearchDialog(dialogType,requestType)
{
	if(requestType == "searchquery")
	{
		jQuery("#dialog").dialog("option", "buttons", {
			  "Save Search Query": function() { addInlineSearchQuery(); },
			  "Close": function() { jQuery(this).dialog("close"); }
			});
	}
	jQuery('#hdnRequestType').val(requestType);
	jQuery('#hdnDialogType').val(dialogType);
	jQuery('#dialog').dialog('open');	
}
</script>

<?php 
if(ReviewAZON_get_affiliate_setting("AWS_Webservice_Key") == "" || ReviewAZON_get_affiliate_setting("AWS_Affiliate_Country") == "" || ReviewAZON_get_affiliate_setting("AWS_Secret_Key") == "")
{
	echo '<p><img src="'.ReviewAZON_ADMIN_IMAGES.'error.gif" style="padding-right:5px;vertical-align:sub;" /><b>Amazon Web Service Configuration Error!</b></p>';
	echo '<p>You must have a valid Amazon Web Services key and Secret Code to get started.</P>';		
	echo '<p>If you have already signed up for your AWS key, you will need to enter it on the <a href="admin.php?page=Affiliate Settings">ReviewAZON Affiliate Settings</a> page.</p>';
	echo '<p>If you need to create an AWS account, you can visit <a target="_blank" href="http://aws.amazon.com/">Amazon Web Services</a> to sign up!</p>';
}
else
{
?>
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
<hr style="border:1px solid gainsboro;margin-top:10px;">
<p><img title="Add Amazon Product Review" style="vertical-align:middle;margin:5px;cursor:pointer;" src="<?php echo ReviewAZON_ADMIN_IMAGES ?>search.gif" alt="move" width="16" height="16" /><a onclick="openAmazonSearchDialog('fullpost','normal');return false;" style="cursor:pointer;font-weight:bold;font-size:9pt;">Amazon Product Post</a></p>
<p><img title="Add Amazon In-line Product Review" style="vertical-align:middle;margin:5px;cursor:pointer;" src="<?php echo ReviewAZON_ADMIN_IMAGES ?>inline.gif" alt="move" width="16" height="16" /><a onclick="openAmazonSearchDialog('inlinepost','singleinlinepost');return false;" style="cursor:pointer;font-weight:bold;font-size:9pt;">Single In-line Product Display</a></p>
<p><img title="Add Amazon In-line Product List" style="vertical-align:middle;margin:5px;cursor:pointer;" src="<?php echo ReviewAZON_ADMIN_IMAGES ?>multi_inline_post.gif" alt="move" width="16" height="16" /><a onclick="openAmazonSearchDialog('inlinepost','searchquery');return false;" style="cursor:pointer;font-weight:bold;font-size:9pt;">Multi In-line Product Display</a></p>
<p><img title="YouTube Video Review Search" style="vertical-align:middle;margin:5px;cursor:pointer;" src="<?php echo ReviewAZON_ADMIN_IMAGES ?>television.gif" alt="move" width="16" height="16" /><a onclick="jQuery('#youtube_dialog').dialog('open');return false;" style="cursor:pointer;font-weight:bold;font-size:9pt;">YouTube Review Video Search</a></p>
<?php 
if(ReviewAZON_get_affiliate_setting("Enable_Debugging"))
{
	echo '<p><img title="Amazon Product Search" style="vertical-align:middle;margin:5px;cursor:pointer;" src="'.ReviewAZON_ADMIN_IMAGES.'bug.gif" alt="move" width="16" height="16" /><a href="#" onclick="showDebugInfo();" style="cursor:pointer;font-weight:bold;font-size:9pt;text-decoration:none;">View AWS Debug Information</a></p>';
}


?>
<div id="reviewazon_selected_product" style="padding-top:10px;text-align:center;">
<?php 
global $post;

if($post->ID > 0)
{
	echo '<img style="padding-left:10px;" src="'.ReviewAZON_get_post_setting($post->ID, 'Product_Image_Medium').'" />';
}

?>
</div>
<?php 
}
?>
<div class="ReviewAZON" id="dialog" title="Amazon Product Search" style="display:none">
<table cellpadding="2" cellspacing="2" style="height:50px;text-align:center" border="0">
<tr>
	<td valign="bottom"><?php include('aws_affiliate_dropdown.php')?></td>
		<td valign="bottom"><input type="text" name="search_text" id="search_text" style="width:250px;height:25px;border:solid 1px gainsboro;" value="<?php echo get_option("search_text") ?>" />&nbsp;<input type="button" onclick="getAmazonItemData('1')" class="button-primary" value="Search" />&nbsp;</td></tr>
</table>
<div id="ReviewAZON_Result" style="overflow:auto;width:715px;height:470px;">
<?php echo get_option("search_results") ?>
</div>
</div>

<div class="ReviewAZON" id="youtube_dialog" title="YouTube Product Review Video Search" style="display:none">
<table cellpadding="2" cellspacing="2" style="margin-top:20px;">
<tr>
	<td><img src="<?php echo ReviewAZON_ADMIN_IMAGES ?>television.gif" style="margin:5px;vertical-align:middle;"/>Find video product reviews for&nbsp; </td><td style="width:350px;"><input type="text" name="video_search_text" id="video_search_text" style="width:350px;height:25px;border:solid 1px gainsboro;" value="<?php echo get_option("video_search_text") ?>" /></td><td width="50"><input type="button" onclick="getVideoReviewData('','1')" class="button-primary" value="Search" /></td></tr>
</table>
<div id="ReviewAZON_video_Result" style="overflow:auto;width:715px;height:460px;">
</div>
</div>
<input type="hidden" id="tmpID" name="tmpID" value=""/>
<input type="hidden" id="tmpPostID1" name="tmpPostID1" value="0" />
<input type="hidden" id="hdnASIN" name="hdnASIN" value="<?php echo ReviewAZON_get_post_setting($post->ID, 'ASIN')?>" />
<input type="hidden" id="hdnPageIndex" name="hdnPageIndex" value="<?php echo get_option("search_page"); ?>"/>
<input type="hidden" id="hdnDialogType" name="hdnDialogType" value=""/>
<input type="hidden" id="hdnRequestType" name="hdnRequestType" value=""/>
