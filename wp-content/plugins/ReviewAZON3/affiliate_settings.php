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

$addTrackingID = '<p><img title="Add Tracking ID" style="vertical-align:middle;padding-right:10px;cursor:pointer;" src="'.ReviewAZON_ADMIN_IMAGES.'tag_blue.gif" alt="move" width="16" height="16" /><a href="#" onclick="showDialog(\'new\');return false;">Add New Tracking ID</a></p>';

$showAddTrackingID = 'style="display:"';
if(ReviewAZON_get_affiliate_setting("AWS_Affiliate_Country") == "")
{
	$showAddTrackingID = 'style="display:none"';	
}


if(isset($_POST['action']))
{
	if(check_admin_referer( 'my-nonce'))
	{
		if($_POST['action'] == "saveaffiliatesettings")
		{
			global $wpdb;
			ReviewAZON_save_affiliate_settings($wpdb->escape($_POST['ReviewAZON_aws_webservices_key']),$wpdb->escape($_POST['ReviewAZON_aws_secret_key']),$wpdb->escape($_POST['ReviewAZON_associate_id']),$wpdb->escape($_POST['ReviewAZON_associate_country']));
			$message = '<div id="message" class="updated fade"><p><img src="'.ReviewAZON_ADMIN_IMAGES.'accept.gif" align="absmiddle" />&nbsp;Settings saved successfully.</p></div>';
			$showAddTrackingID = 'style="display:"';
			if(ReviewAZON_get_affiliate_setting("AWS_Affiliate_Country") == "")
			{
				$showAddTrackingID = 'style="display:none"';	
			}
			
		}	
	}
	else
	{
		die('');
	}
}


function ReviewAZON_save_affiliate_settings($webServiceKey,$webServiceSecretKey,$associateID,$affilliateCountry)
{
	global $wpdb;
	$webServiceKey = $wpdb->escape($webServiceKey);
	$webServiceSecretKey = $wpdb->escape($webServiceSecretKey);
	$affilliateCountry = $wpdb->escape($affilliateCountry);
	$query = "UPDATE ".$wpdb->prefix."reviewazon_settings SET AWS_Webservice_Key = '{$webServiceKey}', AWS_Secret_Key = '{$webServiceSecretKey}', AWS_Affiliate_Country = '{$affilliateCountry}'";
	$wpdb->query($query);
}


?>
	<link type="text/css" href="<?php echo ReviewAZON_ADMIN ?>theme/ui.all.css" rel="Stylesheet" />
	<script type="text/javascript" src="<?php echo ReviewAZON_ADMIN ?>js/jquery-1.3.1.js"></script>
	<script type="text/javascript" src="<?php echo ReviewAZON_ADMIN ?>js/jquery-ui-personalized-1.6rc6.js"></script>
<div class="wrap">
	<h2><img src="<?php echo ReviewAZON_ADMIN_IMAGES ?>largestar.gif" style="vertical-align:middle;margin:5px;" />Affiliate Settings - ReviewAZON Wordpress Plugin Version <?php echo REVIEWAZONVER ?></h2>
	<br />
	<?php echo $message ?>
	<p>In order to use ReviewAZON, you will need to sign up for a Amazon Web Services key. Once you sign up for the AWS key, you will need to enter the key below.</p> <p>If you plan on making money with Amazon, you will also need to apply for the Amazon Affiliate program. Once accepted, you 
	will need to create an Amazon Associate ID and enter it in the space below. </p><br />
	<p><img src="<?php echo ReviewAZON_ADMIN_IMAGES ?>information.gif" style="vertical-align:middle;padding-left:5px;padding-right:5px" /><a href="http://aws.amazon.com/associates/">Sign Up for the Amazon Associate Web Service and Associate ID</a></p>
	

	<script type="text/javascript">
	jQuery.noConflict();
	jQuery(document).ready(function(){
	// Add Dialog			
	jQuery('#dialog').dialog({
		autoOpen: false,
		modal: true,
		overlay:{ background: "gray", opacity: 1.0 },
		width: 400,
		height: 200,
		buttons: {
			"Ok": function() { 
				var trackingID = jQuery('#trackingID').val();			
				var affiliateCountry = jQuery('#ReviewAZON_associate_country').val();	
				var action = jQuery('#hdnAction').val();

				if(action == 'new')
				{
					jQuery.post("<?php echo ReviewAZON_PROCESS_REQUEST_FILE ?>",{action: 'addnewtrackingid', trackingid: trackingID, affiliatecountry: affiliateCountry, _ajax_nonce:'<?php echo $nonce ?>'},function(data){
						jQuery('#hdnAction').val('');
						jQuery('#trackingID').val('');
						jQuery('#hdnTrackingID').val('');
						jQuery('#trackingid-list').html(data);
					});
					jQuery(this).dialog("close"); 
				}
				else
				{
					var id = jQuery('#hdnTrackingID').val();
					jQuery.post("<?php echo ReviewAZON_PROCESS_REQUEST_FILE ?>",{action: 'updatetrackingid', id: id, trackingid: trackingID, affiliatecountry: affiliateCountry, _ajax_nonce:'<?php echo $nonce ?>'},function(data){
						jQuery('#hdnAction').val('');
						jQuery('#trackingID').val('');
						jQuery('#hdnTrackingID').val('');
						jQuery('#trackingid-list').html(data);
					});
					jQuery(this).dialog("close"); 
				}
			}, 
			"Cancel": function() { 
				jQuery(this).dialog("close"); 
			} 
		}
	});

	jQuery("#trackingid-list").sortable({
	      handle : '.handle',
	      update : function () {
			var order = jQuery('#trackingid-list').sortable('serialize');
			jQuery("#progress").load("<?php echo ReviewAZON_PROCESS_REQUEST_FILE ?>?"+order+"&action=settrackingidsortorder&_ajax_nonce=<?php echo $nonce ?>");
			}	
	    });
	});

	function showDialog(id,dialogType)
	{
		var trackingID = jQuery('#trackingID').val();			
		var affiliateCountry = jQuery('#ReviewAZON_associate_country').val();	

		if(dialogType == "edit")
		{
			jQuery('#hdnAction').val('edit');
			jQuery.getJSON("<?php echo ReviewAZON_PROCESS_REQUEST_FILE ?>",{action: 'gettrackingid', id: id, trackingid: trackingID, affiliatecountry: affiliateCountry, _ajax_nonce:'<?php echo $nonce ?>' },function(data){
				jQuery('#trackingID').val(data.trackingid);
				jQuery('#hdnTrackingID').val(id);
				jQuery('#dialog').dialog('open');
			});
		}
		else
		{
			jQuery('#hdnAction').val('new');
			jQuery('#trackingID').val('');
			jQuery('#hdnTrackingID').val('');		
			jQuery('#dialog').dialog('open');
		}

	}

	function deleteTrackingID(id)
	{
		if(confirm('Delete Tracking ID?'))
		{
			var affiliateCountry = jQuery('#ReviewAZON_associate_country').val();
			jQuery.post("<?php echo ReviewAZON_PROCESS_REQUEST_FILE ?>",{action: 'deletetrackingid', trackingid: id, affiliatecountry: affiliateCountry, _ajax_nonce:'<?php echo $nonce ?>'},function(data){
			jQuery('#trackingid-list').html(data);
			});
		}
	}

	function getTrackingIDByAffiliateCountry()
	{
		var affiliateCountry = jQuery('#ReviewAZON_associate_country').val();
		if(affiliateCountry == "")
		{
			jQuery('#addtrackingid').hide();	
		}
		else
		{
			jQuery('#addtrackingid').show();	
			jQuery.post("<?php echo ReviewAZON_PROCESS_REQUEST_FILE ?>",{action: 'gettrackingidsbyaffcountry', affiliatecountry: affiliateCountry, _ajax_nonce:'<?php echo $nonce ?>'},function(data){
			jQuery('#trackingid-list').html(data);
			});
		}
	}
	</script>
	
	
	<table cellpadding="2" cellspacing="2" width="100%">
		<tr><td align="right">&nbsp;<img src="<?php echo ReviewAZON_ADMIN_IMAGES?>disk.gif" style="margin:5px;vertical-align:middle;"/><a style="font-size:10pt;font-weight:bold;" href="#" onclick="jQuery('form').submit()">Save Affiliate Settings</a>&nbsp;&nbsp;</td></tr>
	</table>
	<form method="post">
		<fieldset class="options">
	    <legend></legend> 
	    <table cellpadding="5" cellspacing="10" style="background-color:white;border:1px solid gainsboro;width:100%;">
	    	<tr><td colspan="3">&nbsp;</td></tr>
	    	<tr>
	    		<td valign="top" nowrap><label><strong>AWS Access Key ID:</strong> &nbsp;&nbsp;</label></td>
	    		<td valign="top" nowrap width="350"><input name="ReviewAZON_aws_webservices_key" type="text" value="<?php echo ReviewAZON_get_affiliate_setting('AWS_Webservice_Key'); ?>" style="width:300px;" /></td>
	    		<td valign="top">Your Amazon Web Services unique key that allows you to make calls to the Amazon web service.</td>
	    	</tr>
	    	<tr>
	    		<td valign="top" nowrap><label><strong>AWS Secret Access Key:</strong> &nbsp;&nbsp;</label></td>
	    		<td valign="top" nowrap width="350"><input name="ReviewAZON_aws_secret_key" type="text" value="<?php echo ReviewAZON_get_affiliate_setting('AWS_Secret_Key'); ?>" style="width:300px;" /></td>
	    		<td valign="top">Your Amazon Secret Access Key is used to make encrypted and secure calls to the Amazon Web Service.</td>
	    	</tr>
	    	<tr><td colspan="3"><hr style="border:1px solid gainsboro;"></td></tr>
	     	<tr>
	    		<td valign="top" nowrap><label><strong>Select Affiliate Country:</strong> &nbsp;&nbsp;</label></td>
	    		<td valign="top" nowrap>
		    		<select name="ReviewAZON_associate_country" id="ReviewAZON_associate_country" style="width:300px" onchange="getTrackingIDByAffiliateCountry()">
						<option <?php if(ReviewAZON_get_affiliate_setting("AWS_Affiliate_Country") == '') {echo 'selected="selected"';} ?> value="">Choose Your Affiliate Program</option>
						<option <?php if(ReviewAZON_get_affiliate_setting("AWS_Affiliate_Country") == 'ca') {echo 'selected="selected"';} ?> value="ca">Canada</option>
						<option <?php if(ReviewAZON_get_affiliate_setting("AWS_Affiliate_Country") == 'de') {echo 'selected="selected"';} ?> value="de">Germany</option>
						<option <?php if(ReviewAZON_get_affiliate_setting("AWS_Affiliate_Country") == 'fr') {echo 'selected="selected"';} ?> value="fr">France</option>
						<option <?php if(ReviewAZON_get_affiliate_setting("AWS_Affiliate_Country") == 'jp') {echo 'selected="selected"';} ?> value="jp">Japan</option>
						<option <?php if(ReviewAZON_get_affiliate_setting("AWS_Affiliate_Country") == 'co.uk') {echo 'selected="selected"';} ?> value="co.uk">United Kingdom</option>
						<option <?php if(ReviewAZON_get_affiliate_setting("AWS_Affiliate_Country") == 'com') {echo 'selected="selected"';} ?> value="com">United States</option>
				  	</select>	    		
	    		</td>
	    		<td valign="top">Select the country of the Amazon Associate program for which you are a member.</td>
	    	</tr>
	    	<tr id="addtrackingid" <?php echo $showAddTrackingID ?>>
	    		<td valign="top" nowrap><label><strong>Amazon Affilaite Tracking IDs:</strong> &nbsp;&nbsp;</label></td>
	    		<td colspan="2" align="left" valign="top">
	    		<div><?php echo $addTrackingID ?></div>
	  			<ul id="trackingid-list" style="width:300px">
<?php 
				global $wpdb;
				$affiliateCountry = ReviewAZON_get_affiliate_setting("AWS_Affiliate_Country");
				$queryTrackingIDs = "SELECT * FROM ".$wpdb->prefix."reviewazon_tracking_id WHERE Affiliate_Country = '{$affiliateCountry}' Order By Display_Order ASC";
				$trackingIDs = $wpdb->get_results($queryTrackingIDs);
				$moveTrackingID = '<img title="Move to change sort order" style="vertical-align:middle;padding-right:5px;cursor:move;" src="'.ReviewAZON_ADMIN_IMAGES.'arrow_out.gif" alt="move" width="16" height="16" class="handle" />';
				foreach($trackingIDs as $trackingID)
				{		
					$deleteTrackingID = '<img onclick="deleteTrackingID('.$trackingID->ID.')" title="Delete Tracking ID" style="vertical-align:middle;padding-right:10px;padding-left:10px;cursor:pointer;" src="'.ReviewAZON_ADMIN_IMAGES.'tag_blue_delete.gif" alt="move" width="16" height="16" />';
					$editTrackingID = '<img onclick="showDialog('.$trackingID->ID.',\'edit\')" title="Edit Tracking ID" style="vertical-align:middle;padding-right:10px;padding-left:10px;cursor:pointer;" src="'.ReviewAZON_ADMIN_IMAGES.'tag_blue_edit.gif" alt="move" width="16" height="16" />';
					
  					echo '<li id="listItem_'.$trackingID->ID.'"><span>'.$moveTrackingID.''.$deleteTrackingID.''.$editTrackingID.'<strong>'.$trackingID->Tracking_ID.'</strong></span></li>';
				}
?>				
				</ul>

				</td>
			</tr>	    	
	    </table>
	   <input type="hidden" name="action" id="action" value="saveaffiliatesettings" />
	    </fieldset>
<div class="ReviewAZON" id="dialog" title="Add New Tracking ID" style="display:none">
<br/><br/>
<b>Amazon Tracking ID</b><br />
<input type="text" id="trackingID" name="trackingID" value="" style="width:90%" /><br /><br />
<input type="hidden" id="hdnAction" value="" />
<input type="hidden" id="hdnTrackingID" value="" />
</div>
<?php wp_nonce_field('my-nonce'); ?>
	  </form>

</div>