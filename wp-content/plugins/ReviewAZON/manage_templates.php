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

if(isset($_POST['action']))
{
	if($_POST['action'] == "resetdefaulttemplates")
	{
		if(check_admin_referer( 'my-nonce'))
		{
			ReviewAZON_load_default_templates();
		}
		else
		{
			die('');
		}
	}
	
	if($_POST['action'] == "updatetemplates")
	{
		if(check_admin_referer( 'my-nonce'))
		{
			global $wpdb;
			try
			{
				if(ReviewAZON_get_affiliate_setting("Invalidate_Page_Cache"))
				{
					ReviewAZON_clear_page_cache();
				}
				 
				$mainTemplate = addslashes($_POST['ReviewAZON_main_template']);
				$excerptTemplate = addslashes($_POST['ReviewAZON_excerpt_template']);
				$excerptFlowTemplate = addslashes($_POST['ReviewAZON_excerpt_flow_template']);
				$customerReviewTemplate = addslashes($_POST['ReviewAZON_customer_review_template']);
				$descriptionTemplate = addslashes($_POST['ReviewAZON_product_description_template']);
				$similarProductsTemplate = addslashes($_POST['ReviewAZON_similar_products_template']);
				$productAccessoriesTemplate = addslashes($_POST['ReviewAZON_product_accessories_template']);
				$videoReviewsTemplate = addslashes($_POST['ReviewAZON_video_review_template']);
				$inlinePostTemplate = addslashes($_POST['ReviewAZON_inline_post_template']);
				$multiInlinePostTemplate = addslashes($_POST['ReviewAZON_multi_inline_post_template']);
				
				
				
				$queryMain = "UPDATE ".$wpdb->prefix."reviewazon_templates SET Template_Html = '{$mainTemplate}' WHERE Template_Group = 'Custom' and Template_Name = 'Main'"; 
				$wpdb->query($queryMain);
				
				$queryExcerpt = "UPDATE ".$wpdb->prefix."reviewazon_templates SET Template_Html = '{$excerptTemplate}' WHERE Template_Group = 'Custom' and Template_Name = 'Excerpt'"; 
				$wpdb->query($queryExcerpt);
				
				$queryExcerptFlow = "UPDATE ".$wpdb->prefix."reviewazon_templates SET Template_Html = '{$excerptFlowTemplate}' WHERE Template_Group = 'Custom' and Template_Name = 'Excerpt_Flow'"; 
				$wpdb->query($queryExcerptFlow);
				
				$queryCustomerReview = "UPDATE ".$wpdb->prefix."reviewazon_templates SET Template_Html = '{$customerReviewTemplate}' WHERE Template_Group = 'Custom' and Template_Name = 'Customer_Review'"; 
				$wpdb->query($queryCustomerReview);
				
				$queryDescription = "UPDATE ".$wpdb->prefix."reviewazon_templates SET Template_Html = '{$descriptionTemplate}' WHERE Template_Group = 'Custom' and Template_Name = 'Description'"; 
				$wpdb->query($queryDescription);
				
				$querySimilarProducts = "UPDATE ".$wpdb->prefix."reviewazon_templates SET Template_Html = '{$similarProductsTemplate}' WHERE Template_Group = 'Custom' and Template_Name = 'Similar_Products'"; 
				$wpdb->query($querySimilarProducts);
				
				$queryProductAccessories = "UPDATE ".$wpdb->prefix."reviewazon_templates SET Template_Html = '{$productAccessoriesTemplate}' WHERE Template_Group = 'Custom' and Template_Name = 'Product_Accessories'"; 
				$wpdb->query($queryProductAccessories);
				
				$queryVideoReview = "UPDATE ".$wpdb->prefix."reviewazon_templates SET Template_Html = '{$videoReviewsTemplate}' WHERE Template_Group = 'Custom' and Template_Name = 'Video_Reviews'"; 
				$wpdb->query($queryVideoReview);
				
				$queryInlinePost = "UPDATE ".$wpdb->prefix."reviewazon_templates SET Template_Html = '{$inlinePostTemplate}' WHERE Template_Group = 'Custom' and Template_Name = 'Inline_Post'"; 
				$wpdb->query($queryInlinePost);
				
				$queryMultiInlinePost = "UPDATE ".$wpdb->prefix."reviewazon_templates SET Template_Html = '{$multiInlinePostTemplate}' WHERE Template_Group = 'Custom' and Template_Name = 'Multi_Inline_Post'"; 
				$wpdb->query($queryMultiInlinePost);
				
				$message = '<div id="message" class="updated fade"><p><img src="'.ReviewAZON_ADMIN_IMAGES.'accept.gif" align="absmiddle" />&nbsp;Settings saved successfully.</p></div>'; 
	
			}
			catch(Exception $e)
			{
				$e->message;		
			}
		}
		else
		{
			die('');
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
	// Tabs
	jQuery('#tabs').tabs({
		select: function(event, ui) {jQuery('#ReviewAZON_selected_tab').val(ui.panel.id);}
	});
	jQuery('#tabs').show();
	jQuery('#tabs').tabs('select', '#' + jQuery('#ReviewAZON_selected_tab').val());
});
function setAction(action)
{
	if(action == 'resetdefaulttemplates')
	{
		if(confirm('Are you sure you want to reset all custom templates to the default settings?\n\nThis will overwrite any previous changes.\n'))
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
	<h2><img src="<?php echo ReviewAZON_ADMIN_IMAGES ?>largestar.gif" style="vertical-align:middle;margin:5px;" />Manage Custom Templates - ReviewAZON Wordpress Plugin Version <?php echo REVIEWAZONVER ?></h2>
	<br />
	<?php echo $message ?>
	<p>Use custom templates to change that layout and view of your product reviews. Enter valid HTML in the tabs below along with the tokens you want to use.</p>
	<br />
	<table cellpadding="2" cellspacing="2" width="100%">
		<tr><td align="right"><img src="<?php echo ReviewAZON_ADMIN_IMAGES?>disk.gif" style="margin:5px;vertical-align:middle;"/><a href="#" onclick="setAction('updatetemplates')" style="font-size:10pt;font-weight:bold;">Save Custom Templates</a>&nbsp;&nbsp;  | &nbsp;<img src="<?php echo ReviewAZON_ADMIN_IMAGES?>arrow_refresh.gif" style="margin:5px;vertical-align:middle;"/><a style="font-size:10pt;font-weight:bold;" href="#" onclick="setAction('resetdefaulttemplates')">Reset Templates to Default</a></td></tr>
	</table>
<div id="tabs" style="display:none">
	<ul>
		<li><a href="#tabs-1">Main</a></li>
		<li><a href="#tabs-2">Excerpt</a></li>
		<li><a href="#tabs-3">Excerpt Flow</a></li>
		<li><a href="#tabs-4">Customer Review</a></li>
		<li><a href="#tabs-5">Description</a></li>
		<li><a href="#tabs-6">Similar Products</a></li>
		<li><a href="#tabs-7">Product Accessories</a></li>
		<li><a href="#tabs-8">Video Reviews</a></li>
		<li><a href="#tabs-9">Single In-line</a></li>
		<li><a href="#tabs-10">Multi In-line</a></li>
	</ul>
	<div id="tabs-1">	    	
	    	<table width="100%">
	    	<tr><td><label><strong>Main Review Template</strong></label></td><td><label><strong>Tokens</strong></label></td></tr>
	    	<tr>
	    	<td width="100%">
	    	<textarea style="width:100%;height:400px;border:solid 1px gainsboro;font-family: Consolas, Monaco, Courier, monospace;font-size: 12px;" name="ReviewAZON_main_template" id="ReviewAZON_main_template" tabindex="1"><?php echo stripslashes(ReviewAZON_get_template('Custom','Main')); ?></textarea>
	    	</td>
	    	<td>
	    	<div style="height:388px;overflow:auto;width:300px;border:1px solid gainsboro;padding:5px;">
	    	<table cellpadding="2" cellspacing="2" width="100%">
	    		<tr>
	    			<td colspan="2" style="background-color:gray;color:white;padding:2px;font-weight:bold;">Main Template Tokens</td>
	    		</tr>    		
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Cached Price Disclaimer</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%CACHEDPRICEDISCLAIMER%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Product Description</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%DESCRIPTION%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Product Details</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%PRODUCTDETAILS%%</td>
	    		</tr>	    	
		    	<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Customer Reviews</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%CUSTOMERREVIEWS%%</td>
	    		</tr>	    	
		    	<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Similar Products</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%SIMILARPRODUCTS%%</td>
	    		</tr>	    	
		    	<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Product Accessories</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%PRODUCTACCESSORIES%%</td>
	    		</tr>	    	
		    	<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Video Reviews</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%VIDEOREVIEWS%%</td>
	    		</tr>		    	
		    	<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">eBay Auctions</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%EBAYAUCTIONS%%</td>
	    		</tr>	
		    	<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Overstock.com Listings</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%OVERSTOCKLISTINGS%%</td>
	    		</tr>	    			    	
		    	<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Vendiva Price Comparisons</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%VENDIVACOMPARISONS%%</td>
	    		</tr>	    			    	
	    		<tr>
	    			<td colspan="2" style="background-color:gray;color:white;padding:2px;font-weight:bold;">Link Tokens</td>
	    		</tr>    		
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;font-weight:bold;">Post Permalink</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%PERMALINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Detail Page Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%DETAILPAGELINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Technical Details Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%TECHNICALDETAILPAGELINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Add To Baby Registry Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%ADDTOBABYREGISTRYLINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Add To Wedding Registry Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%ADDTOWEDDINGREGISTRYLINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Add To Wishlist Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%ADDTOWISHLISTLINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Tell A Friend Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%TELLAFRIENDLINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">All Customer Reviews Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%ALLCUSTOMERREVIEWSPAGELINK%%</td>
	    		</tr>
	    		<tr>
	    			<td colspan="2" style="background-color:gray;color:white;padding:2px;font-weight:bold;">Product Image Tokens</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Small Product Image URL</td>
	    		</tr>   			    		
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMAZONSMALLIMAGE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Medium Product Image URL</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMAZONMEDIUMIMAGE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Large Product Image URL</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMAZONLARGEIMAGE%%</td>
	    		</tr>
	    		<tr>
	    			<td colspan="2" style="background-color:gray;color:white;padding:2px;font-weight:bold;">Product Property Tokens</td>
	    		</tr>	    			    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Brand</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%BRAND%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Average Review Rating</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%RATINGBAR%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Customer Review Count</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%REVIEWCOUNT%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Item Height</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%PRODUCTHEIGHT%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Item Length</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%PRODUCTLENGTH%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Item Width</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%PRODUCTWIDTH%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Item Weight</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%PRODUCTWEIGHT%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">List Price</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%LISTPRICE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Lowest New Price</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%SALEPRICE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Manufacturer</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%MANUFACTURER%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Manufacturer Maximum Age</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMAZONMAXIMUMAGE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Manufacturer Minimum Age</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMAZONMINIMUMAGE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Model Number</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%MODEL%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Title</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%TITLE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">UPC</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%UPCCODE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Is Eligible For Super Saver Shipping</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%FREESHIPPING%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Shipping Availability</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%SHIPPINGAVAILIBILITY%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Amount Saved</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMOUNTSAVED%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Percent Saved</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%PERCENTSAVED%%</td>
	    		</tr>
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">New From Text</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%NEWFROMTEXT%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Used From Text</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%USEDFROMTEXT%%</td>
	    		</tr>	    	
	    	</table>	    	
	    	</div>
	    	</td></tr></table>

	</div>
	<div id="tabs-2">
		    <table width="100%">
	    	<tr><td><label><strong>Excerpt Template</strong></label></td></tr>
	    	<tr>
	    	<td width="100%">
	    	<textarea style="width:100%;height:400px;border:solid 1px gainsboro;font-family: Consolas, Monaco, Courier, monospace;font-size: 12px;" name="ReviewAZON_excerpt_template" id="ReviewAZON_excerpt_template" tabindex="1"><?php echo stripslashes(ReviewAZON_get_template('Custom','Excerpt')); ?></textarea>
	    	</td>
	    	<td>
	    	<div style="height:388px;overflow:auto;width:300px;border:1px solid gainsboro;padding:5px;">
	    	<table cellpadding="2" cellspacing="2" width="100%">
	    		<tr>
	    			<td colspan="2" style="background-color:gray;color:white;padding:2px;font-weight:bold;">Link Tokens</td>
	    		</tr>    		
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;font-weight:bold;">Post Permalink</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%PERMALINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Detail Page Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%DETAILPAGELINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Technical Details Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%TECHNICALDETAILPAGELINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Add To Baby Registry Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%ADDTOBABYREGISTRYLINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Add To Wedding Registry Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%ADDTOWEDDINGREGISTRYLINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Add To Wishlist Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%ADDTOWISHLISTLINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Tell A Friend Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%TELLAFRIENDLINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">All Customer Reviews Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%ALLCUSTOMERREVIEWSPAGELINK%%</td>
	    		</tr>
	    		<tr>
	    			<td colspan="2" style="background-color:gray;color:white;padding:2px;font-weight:bold;">Product Image Tokens</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Small Product Image URL</td>
	    		</tr>   			    		
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMAZONSMALLIMAGE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Medium Product Image URL</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMAZONMEDIUMIMAGE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Large Product Image URL</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMAZONLARGEIMAGE%%</td>
	    		</tr>
	    		<tr>
	    			<td colspan="2" style="background-color:gray;color:white;padding:2px;font-weight:bold;">Product Property Tokens</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Product Description</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%DESCRIPTION%%</td>
	    		</tr>	    				    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Brand</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%BRAND%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Average Review Rating</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%RATINGBAR%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Customer Review Count</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%REVIEWCOUNT%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Item Height</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%PRODUCTHEIGHT%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Item Length</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%PRODUCTLENGTH%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Item Width</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%PRODUCTWIDTH%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Item Weight</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%PRODUCTWEIGHT%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">List Price</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%LISTPRICE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Lowest New Price</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%SALEPRICE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Manufacturer</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%MANUFACTURER%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Manufacturer Maximum Age</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMAZONMAXIMUMAGE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Manufacturer Minimum Age</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMAZONMINIMUMAGE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Model Number</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%MODEL%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Title</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%TITLE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">UPC</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%UPCCODE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Is Eligible For Super Saver Shipping</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%FREESHIPPING%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Shipping Availability</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%SHIPPINGAVAILIBILITY%%</td>
	    		</tr>	  
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Amount Saved</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMOUNTSAVED%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Percent Saved</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%PERCENTSAVED%%</td>
	    		</tr>  	
	    	</table>	    	
	    	</div>
	    	</td></tr></table>
	</div>
	<div id="tabs-3">
		    <table width="100%">
	    	<tr><td><label><strong>Excerpt Flow Template</strong></label></td></tr>
	    	<tr>
	    	<td width="100%">
	    	<textarea style="width:100%;height:400px;border:solid 1px gainsboro;font-family: Consolas, Monaco, Courier, monospace;font-size: 12px;" name="ReviewAZON_excerpt_flow_template" id="ReviewAZON_excerpt_flow_template" tabindex="1"><?php echo stripslashes(ReviewAZON_get_template('Custom','Excerpt_Flow')); ?></textarea>
	    	</td>
	    	<td>
	    	<div style="height:388px;overflow:auto;width:300px;border:1px solid gainsboro;padding:5px;">
	    	<table cellpadding="2" cellspacing="2" width="100%">
	    		<tr>
	    			<td colspan="2" style="background-color:gray;color:white;padding:2px;font-weight:bold;">Link Tokens</td>
	    		</tr>    		
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;font-weight:bold;">Post Permalink</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%PERMALINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Detail Page Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%DETAILPAGELINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Technical Details Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%TECHNICALDETAILPAGELINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Add To Baby Registry Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%ADDTOBABYREGISTRYLINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Add To Wedding Registry Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%ADDTOWEDDINGREGISTRYLINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Add To Wishlist Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%ADDTOWISHLISTLINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Tell A Friend Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%TELLAFRIENDLINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">All Customer Reviews Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%ALLCUSTOMERREVIEWSPAGELINK%%</td>
	    		</tr>
	    		<tr>
	    			<td colspan="2" style="background-color:gray;color:white;padding:2px;font-weight:bold;">Product Image Tokens</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Small Product Image URL</td>
	    		</tr>   			    		
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMAZONSMALLIMAGE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Medium Product Image URL</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMAZONMEDIUMIMAGE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Large Product Image URL</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMAZONLARGEIMAGE%%</td>
	    		</tr>
	    		<tr>
	    			<td colspan="2" style="background-color:gray;color:white;padding:2px;font-weight:bold;">Product Property Tokens</td>
	    		</tr>	    			 
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Product Description</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%DESCRIPTION%%</td>
	    		</tr>	    	
	    		   	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Brand</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%BRAND%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Average Review Rating</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%RATINGBAR%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Customer Review Count</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%REVIEWCOUNT%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Item Height</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%PRODUCTHEIGHT%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Item Length</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%PRODUCTLENGTH%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Item Width</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%PRODUCTWIDTH%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Item Weight</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%PRODUCTWEIGHT%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">List Price</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%LISTPRICE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Lowest New Price</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%SALEPRICE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Manufacturer</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%MANUFACTURER%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Manufacturer Maximum Age</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMAZONMAXIMUMAGE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Manufacturer Minimum Age</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMAZONMINIMUMAGE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Model Number</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%MODEL%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Title</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%TITLE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">UPC</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%UPCCODE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Is Eligible For Super Saver Shipping</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%FREESHIPPING%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Shipping Availability</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%SHIPPINGAVAILIBILITY%%</td>
	    		</tr>	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Amount Saved</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMOUNTSAVED%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Percent Saved</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%PERCENTSAVED%%</td>
	    		</tr>    	
	    	</table>	    	
	    	</div>
	    	</td></tr></table>
	</div>
	<div id="tabs-4">
	    	<table width="100%">
	    	<tr><td><label><strong>Customer Review Template</strong></label></td><td><label><strong>Tokens</strong></label></td></tr>
	    	<tr>
	    	<td width="100%">
	    	<textarea style="width:100%;height:400px;border:solid 1px gainsboro;font-family: Consolas, Monaco, Courier, monospace;font-size: 12px;" name="ReviewAZON_customer_review_template" id="ReviewAZON_customer_review_template" tabindex="1"><?php echo stripslashes(ReviewAZON_get_template('Custom','Customer_Review')); ?></textarea>
	    	</td>
	    	<td>
	    	<div style="height:388px;overflow:auto;width:300px;border:1px solid gainsboro;padding:5px;">
	    	<table cellpadding="2" cellspacing="2" width="100%">
	    		<tr>
	    			<td colspan="2" style="background-color:gray;color:white;padding:2px;font-weight:bold;">Customer Review Tokens</td>
	    		</tr> 
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;font-weight:bold;">Review Description</td>
	    		</tr>	    		   	
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%REVIEWDESCRIPTION%%</td>
	    		</tr>	    	
	    			
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;font-weight:bold;">Review Date</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%REVIEWDATE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Review Rating</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%RATINGBAR%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Review Title</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%TITLE%%</td>
	    		</tr>
	    		<tr>
	    			<td colspan="2" style="background-color:gray;color:white;padding:2px;font-weight:bold;">Reviewer Data</td>
	    		</tr>    		
    			    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Reviewer Name</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%REVIEWERNAME%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Reviewer Nickname</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%REVIEWERNICKNAME%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Reviewer Location</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%REVIEWERLOCATION%%</td>
	    		</tr>    		
	    	</table>	    	
	    	</div>
	    	</td></tr></table>	   
	</div>
	<div id="tabs-5">
		    <table width="100%">
	    	<tr><td><label><strong>Product Description Template</strong></label></td><td><label><strong>Tokens</strong></label></td></tr>
	    	<tr>
	    	<td width="100%">	    	
	    	<textarea style="width:100%;height:400px;border:solid 1px gainsboro;font-family: Consolas, Monaco, Courier, monospace;font-size: 12px;" name="ReviewAZON_product_description_template" id="ReviewAZON_product_description_template" tabindex="1"><?php echo stripslashes(ReviewAZON_get_template('Custom','Description')); ?></textarea>
	   	    </td>
	    	<td>
	    	<div style="height:388px;overflow:auto;width:300px;border:1px solid gainsboro;padding:5px;">
	    	<table cellpadding="2" cellspacing="2" width="100%">
	    		<tr>
	    			<td colspan="2" style="background-color:gray;color:white;padding:2px;font-weight:bold;">Product Description Tokens</td>
	    		</tr>    		
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;font-weight:bold;">Product Description</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%DESCRIPTION%%</td>
	    		</tr>	    	
	    	</table>	    	
	    	</div>
	    	</td>
	    	</tr>
	    	</table>	
	</div>
	<div id="tabs-6">
		    <table width="100%">
	    	<tr><td><label><strong>Similiar Products Template</strong></label></td><td><label><strong>Tokens</strong></label></td></tr>
	    	<tr>
	    	<td width="100%">	        	
	    	<textarea style="width:100%;height:400px;border:solid 1px gainsboro;font-family: Consolas, Monaco, Courier, monospace;font-size: 12px;" name="ReviewAZON_similar_products_template" id="ReviewAZON_similar_products_template" tabindex="1"><?php echo stripslashes(ReviewAZON_get_template('Custom','Similar_Products')); ?></textarea>
			</td>
	    	<td>
	    	<div style="height:388px;overflow:auto;width:300px;border:1px solid gainsboro;padding:5px;">
	    	<table cellpadding="2" cellspacing="2" width="100%">
	    		<tr>
	    			<td colspan="2" style="background-color:gray;color:white;padding:2px;font-weight:bold;">Similar Products Tokens</td>
	    		</tr>    		
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;font-weight:bold;">Detail Page Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%DETAILPAGELINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;font-weight:bold;">Product Title</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%TITLE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;font-weight:bold;">Customer Review Count</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%REVIEWCOUNT%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;font-weight:bold;">Average Review Rating</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%RATINGBAR%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;font-weight:bold;">Product Description</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%DESCRIPTION%%</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;font-weight:bold;">Product List Price</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%LISTPRICE%%</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;font-weight:bold;">Product Sales Price</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%SALEPRICE%%</td>
	    		</tr>
	    		<tr>
	    			<td colspan="2" style="background-color:gray;color:white;padding:2px;font-weight:bold;">Product Image Tokens</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Small Product Image URL</td>
	    		</tr>   			    		
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMAZONSMALLIMAGE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Medium Product Image URL</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMAZONMEDIUMIMAGE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Large Product Image URL</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMAZONLARGEIMAGE%%</td>
	    		</tr>	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Amount Saved</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMOUNTSAVED%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Percent Saved</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%PERCENTSAVED%%</td>
	    		</tr>    			    	
	    	</table>	    	
	    	</div>
	    	</td>
	    	</tr>
	    	</table>	
	</div>
	<div id="tabs-7">
		    <table width="100%">
	    	<tr><td><label><strong>Product Accessories Template</strong></label></td></tr>
	    	<tr>
	    	<td width="100%">	        	
	    	<textarea style="width:100%;height:400px;border:solid 1px gainsboro;font-family: Consolas, Monaco, Courier, monospace;font-size: 12px;" name="ReviewAZON_product_accessories_template" id="ReviewAZON_product_accessories_template" tabindex="1"><?php echo stripslashes(ReviewAZON_get_template('Custom','Product_Accessories')); ?></textarea>
	    	</td>
	    	<td>
	    	<div style="height:388px;overflow:auto;width:300px;border:1px solid gainsboro;padding:5px;">
	    	<table cellpadding="2" cellspacing="2" width="100%">
	    		<tr>
	    			<td colspan="2" style="background-color:gray;color:white;padding:2px;font-weight:bold;">Similar Products Tokens</td>
	    		</tr>    		
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;font-weight:bold;">Detail Page Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%DETAILPAGELINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;font-weight:bold;">Product Title</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%TITLE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;font-weight:bold;">Customer Review Count</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%REVIEWCOUNT%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;font-weight:bold;">Average Review Rating</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%RATINGBAR%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;font-weight:bold;">Product Description</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%DESCRIPTION%%</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;font-weight:bold;">Product List Price</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%LISTPRICE%%</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;font-weight:bold;">Product Sales Price</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%SALEPRICE%%</td>
	    		</tr>
	    		<tr>
	    			<td colspan="2" style="background-color:gray;color:white;padding:2px;font-weight:bold;">Product Image Tokens</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Small Product Image URL</td>
	    		</tr>   			    		
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMAZONSMALLIMAGE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Medium Product Image URL</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMAZONMEDIUMIMAGE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Large Product Image URL</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMAZONLARGEIMAGE%%</td>
	    		</tr>
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Amount Saved</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMOUNTSAVED%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Percent Saved</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%PERCENTSAVED%%</td>
	    		</tr>	    			    	
	    	</table>	    	
	    	</div>
	    	</td>
	    	</tr>
	    	</table>
	</div>
	<div id="tabs-8">
		    <table width="100%">
	    	<tr><td><label><strong>Video Review Template</strong></label></td></tr>
	    	<tr>
	    	<td width="100%">	 
	    	<textarea style="width:100%;height:400px;border:solid 1px gainsboro;font-family: Consolas, Monaco, Courier, monospace;font-size: 12px;" name="ReviewAZON_video_review_template" id="ReviewAZON_video_review_template" tabindex="1"><?php echo stripslashes(ReviewAZON_get_template('Custom','Video_Reviews')); ?></textarea>
	    	</td>
	    	<td>
	    	<div style="height:388px;overflow:auto;width:300px;border:1px solid gainsboro;padding:5px;">
	    	<table cellpadding="2" cellspacing="2" width="100%">
	    		<tr>
	    			<td colspan="2" style="background-color:gray;color:white;padding:2px;font-weight:bold;">Video Review Tokens</td>
	    		</tr>    		
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;font-weight:bold;">Video Title</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%VIDEOTITLE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;font-weight:bold;">Video Watch Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%VIDEOWATCHLINK%%</td>
	    		</tr>	    	
	    	</table>	    	
	    	</div>
	    	</td>
	    	</tr>
	    	</table>
	</div>
		<div id="tabs-9">	    	
	    	<table width="100%">
	    	<tr><td><label><strong>In-line Post Template</strong></label></td><td><label><strong>Tokens</strong></label></td></tr>
	    	<tr>
	    	<td width="100%">
	    	<textarea style="width:100%;height:400px;border:solid 1px gainsboro;font-family: Consolas, Monaco, Courier, monospace;font-size: 12px;" name="ReviewAZON_inline_post_template" id="ReviewAZON_inline_post_template" tabindex="1"><?php echo stripslashes(ReviewAZON_get_template('Custom','Inline_Post')); ?></textarea>
	    	</td>
	    	<td>
	    	<div style="height:388px;overflow:auto;width:300px;border:1px solid gainsboro;padding:5px;">
	    	<table cellpadding="2" cellspacing="2" width="100%">	    			    	
	    		<tr>
	    			<td colspan="2" style="background-color:gray;color:white;padding:2px;font-weight:bold;">Link Tokens</td>
	    		</tr>    		
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Detail Page Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%DETAILPAGELINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Technical Details Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%TECHNICALDETAILPAGELINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Add To Baby Registry Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%ADDTOBABYREGISTRYLINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Add To Wedding Registry Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%ADDTOWEDDINGREGISTRYLINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Add To Wishlist Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%ADDTOWISHLISTLINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Tell A Friend Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%TELLAFRIENDLINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">All Customer Reviews Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%ALLCUSTOMERREVIEWSPAGELINK%%</td>
	    		</tr>
	    		<tr>
	    			<td colspan="2" style="background-color:gray;color:white;padding:2px;font-weight:bold;">Product Image Tokens</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Small Product Image URL</td>
	    		</tr>   			    		
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMAZONSMALLIMAGE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Medium Product Image URL</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMAZONMEDIUMIMAGE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Large Product Image URL</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMAZONLARGEIMAGE%%</td>
	    		</tr>
	    		<tr>
	    			<td colspan="2" style="background-color:gray;color:white;padding:2px;font-weight:bold;">Product Property Tokens</td>
	    		</tr>	  
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Product Description</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%DESCRIPTION%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Product Details</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%PRODUCTDETAILS%%</td>
	    		</tr>	    		  			    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Brand</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%BRAND%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Average Review Rating</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%RATINGBAR%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Customer Review Count</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%REVIEWCOUNT%%</td>
	    		</tr>	   	
				<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">List Price</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%LISTPRICE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Lowest New Price</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%SALEPRICE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Manufacturer</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%MANUFACTURER%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Model Number</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%MODEL%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Title</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%TITLE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">UPC</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%UPCCODE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Is Eligible For Super Saver Shipping</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%FREESHIPPING%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Shipping Availability</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%SHIPPINGAVAILIBILITY%%</td>
	    		</tr>	 
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Amount Saved</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMOUNTSAVED%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Percent Saved</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%PERCENTSAVED%%</td>
	    		</tr>   	
	    	</table>	    	
	    	</div>
	    	</td></tr></table>

	</div>
			<div id="tabs-10">	    	
	    	<table width="100%">
	    	<tr><td><label><strong>Multi In-line Post Template</strong></label></td><td><label><strong>Tokens</strong></label></td></tr>
	    	<tr>
	    	<td width="100%">
	    	<textarea style="width:100%;height:400px;border:solid 1px gainsboro;font-family: Consolas, Monaco, Courier, monospace;font-size: 12px;" name="ReviewAZON_multi_inline_post_template" id="ReviewAZON_multi_inline_post_template" tabindex="1"><?php echo stripslashes(ReviewAZON_get_template('Custom','Multi_Inline_Post')); ?></textarea>
	    	</td>
	    	<td>
	    	<div style="height:388px;overflow:auto;width:300px;border:1px solid gainsboro;padding:5px;">
	    	<table cellpadding="2" cellspacing="2" width="100%">	    			    	
	    		<tr>
	    			<td colspan="2" style="background-color:gray;color:white;padding:2px;font-weight:bold;">Link Tokens</td>
	    		</tr>    		
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Detail Page Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%DETAILPAGELINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Technical Details Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%TECHNICALDETAILPAGELINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Add To Baby Registry Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%ADDTOBABYREGISTRYLINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Add To Wedding Registry Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%ADDTOWEDDINGREGISTRYLINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Add To Wishlist Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%ADDTOWISHLISTLINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Tell A Friend Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%TELLAFRIENDLINK%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">All Customer Reviews Link</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%ALLCUSTOMERREVIEWSPAGELINK%%</td>
	    		</tr>
	    		<tr>
	    			<td colspan="2" style="background-color:gray;color:white;padding:2px;font-weight:bold;">Product Image Tokens</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Small Product Image URL</td>
	    		</tr>   			    		
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMAZONSMALLIMAGE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Medium Product Image URL</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMAZONMEDIUMIMAGE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Large Product Image URL</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMAZONLARGEIMAGE%%</td>
	    		</tr>
	    		<tr>
	    			<td colspan="2" style="background-color:gray;color:white;padding:2px;font-weight:bold;">Product Property Tokens</td>
	    		</tr>
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Product Description</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%DESCRIPTION%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Product Details</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%PRODUCTDETAILS%%</td>
	    		</tr>    			    			    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Brand</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%BRAND%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Average Review Rating</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%RATINGBAR%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Customer Review Count</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%REVIEWCOUNT%%</td>
	    		</tr>	   	
				<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">List Price</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%LISTPRICE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Lowest New Price</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%SALEPRICE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Manufacturer</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%MANUFACTURER%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Model Number</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%MODEL%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Title</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%TITLE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">UPC</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%UPCCODE%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Is Eligible For Super Saver Shipping</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%FREESHIPPING%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Shipping Availability</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%SHIPPINGAVAILIBILITY%%</td>
	    		</tr>	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Amount Saved</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%AMOUNTSAVED%%</td>
	    		</tr>	    	
	    		<tr>
	    			<td style="border-top:dotted 1px gainsboro;padding-top:2px;font-size:8pt;font-weight:bold;">Percent Saved</td>
	    		</tr>
	    		<tr>
	    			<td style="padding-top:2px;font-size:8pt;">%%PERCENTSAVED%%</td>
	    		</tr>    	
	    	</table>	    	
	    	</div>
	    	</td></tr></table>
	</div>
</div>
</div>
<input type="hidden" name="action" id="action" value="" />
<input type="hidden" name="ReviewAZON_selected_tab" id="ReviewAZON_selected_tab" value="<?php echo $_POST['ReviewAZON_selected_tab']?>" />
<?php wp_nonce_field('my-nonce'); ?>
</form>