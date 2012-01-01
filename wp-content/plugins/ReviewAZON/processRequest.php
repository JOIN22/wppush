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

	require_once("../../../wp-load.php");
	require_once("../../../wp-admin/includes/taxonomy.php");

	if(ReviewAZON_isAjax() && ReviewAZON_is_from_admin($_SERVER['HTTP_REFERER']))
	{
		if(isset($_GET['action']))
		{
			if(check_ajax_referer('my-nonce'))
			{
				if($_GET['action'] == 'settrackingidsortorder')
				{
					ReviewAZON_set_trackingid_sort_order($_GET['listItem']);
				}
				if($_GET['action'] == 'settabsortorder')
				{
					ReviewAZON_set_sort_order($_GET['listItem']);
				}	
				if($_GET['action'] == 'getsingletab')
				{
					ReviewAZON_get_single_tab($_GET['tabid']);
				}
				if($_GET['action'] == 'gettrackingid')
				{
					ReviewAZON_get_trackingID($_GET['id'],$_GET['trackingid'],$_GET['affiliatecountry']);
				}
				if($_GET['action'] == 'getsingleproduct')
				{
					ReviewAZON_get_single_product($_GET['asin']);
				}
				if($_GET['action'] == 'setbulkreviewsortorder')
				{
					ReviewAZON_set_bulk_review_sort_order($_GET['bulkReview']);
				}	
				if($_GET['action'] == 'getcurrenttime')
				{
						$blogtime = current_time('mysql'); 
						list( $today_year, $today_month, $today_day, $hour, $minute, $second ) = split( '([^0-9])', $blogtime );
						echo $hour.':'.$minute.':'.$second;			
				}	
			}
			else
			{
				die('');		
			}
		}
		
		if(isset($_POST['action']))
		{
			if(check_ajax_referer('my-nonce'))
			{
				if($_POST['action'] == 'getdashboardreport')
				{
					echo ReviewAZON_get_dashboard_report();
				}			
				
				if($_POST['action'] == 'getreportdetails')
				{
					echo ReviewAZON_get_report_details($_POST['id']);
				}			
				
				if($_POST['action'] == 'addcategory')
				{
					echo ReviewAZON_add_category($_POST['title'],$_POST['parent']);
				}			
				
				if($_POST['action'] == 'refreshcats')
				{
					echo ReviewAZON_refresh_categories();
				}		
									
				if($_POST['action'] == 'getsignedurl')
				{
					echo ReviewAZON_get_signed_url($_POST['asin']);
				}
				if($_POST['action'] == 'getPostTags')
				{
					echo ReviewAZON_create_tags($_POST['postTitle']);
				}			
				if($_POST['action'] == 'getproductsearch')
				{
					ReviewAZON_amazon_search_results($_POST['searchIndex'], $_POST['query'], $_POST['itemPage'], $_POST['requestType'], $_POST['sort']);
				}
				if($_POST['action'] == 'addproductdata')
				{
					ReviewAZON_add_amazon_data($_POST['asin'], $_POST['postid']);
				}
				if($_POST['action'] == 'deleteproductdata')
				{
					ReviewAZON_delete_amazon_data($_POST['postid']);
				}
				if($_POST['action'] == 'addnewtrackingid')
				{
					ReviewAZON_add_new_trackingID($_POST['trackingid'], $_POST['affiliatecountry']);
				}
				if($_POST['action'] == 'deletetrackingid')
				{
					ReviewAZON_delete_trackingID($_POST['trackingid'], $_POST['affiliatecountry']);
				}
				if($_POST['action'] == 'addnewtab')
				{
					ReviewAZON_add_new_tab($_POST['tabtitle'], $_POST['tabcontent'], $_POST['showtab']);
				}
				if($_POST['action'] == 'deletetab')
				{
					ReviewAZON_delete_tab($_POST['tabid']);
				}
				if($_POST['action'] == 'getalltabs')
				{
					ReviewAZON_get_all_tabs();
				}
				if($_POST['action'] == 'updatetab')
				{
					ReviewAZON_update_tab($_POST['tabid'],$_POST['tabtitle'],$_POST['tabcontent'],$_POST['showtab']);
				}
				if($_POST['action'] == 'updatetrackingid')
				{
					ReviewAZON_update_trackingID($_POST['id'],$_POST['trackingid'],$_POST['affiliatecountry']);
				}
				if($_POST['action'] == 'gettrackingidsbyaffcountry')
				{
					echo ReviewAZON_get_all_trackingID($_POST['affiliatecountry']);
				}
				
				//Bulk Product Requests
				if($_POST['action'] == 'addbulkreview')
				{
					ReviewAZON_add_bulk_review($_POST['title'],$_POST['imageURL'],$_POST['asin']);
				}
				if($_POST['action'] == 'removebulkreview')
				{
					ReviewAZON_remove_bulk_review($_POST['id']);
				}
				if($_POST['action'] == 'removebulkreviews')
				{
					ReviewAZON_remove_bulk_reviews();
				}
			}
			else
			{
				die('');			
			}
		}
	}	
	
	function ReviewAZON_get_dashboard_report()
	{
		global $wpdb;
		$reportQuery = "SELECT count(*) as Clicks, ASIN, Click_Date, Tracking_ID FROM ".$wpdb->prefix."reviewazon_reports Group By ASIN Order By Clicks DESC LIMIT 5";
		$reportRows = $wpdb->get_results($reportQuery);
		if(count($reportRows) > 0)
		{
			foreach($reportRows as $reportRow)
			{
				$current = ReviewAZON_get_amazon_current_node($reportRow->ASIN,null,null,null,null);
				
				echo '<tr class="iedit alternate"><td class="name column-name" style="text-align:center">'.$reportRow->Clicks.'</td><td class="column-name" style="width:10px"><img src="'.$current->SmallImage->URL.'" style="margin:5px;" /></td><td class="name column-name">'.$current->ItemAttributes->Title.'</td><td class="name column-name">'.$reportRow->ASIN.'</td><td class="name column-name" nowrap>'.$reportRow->Tracking_ID.'</td></tr>';
			}
		}
		else
		{
			echo '<tr class="iedit alternate"><td class="column-name" colspan="4">There are no clicks to report.</td></tr>';
		}
	}
	
	function ReviewAZON_get_report_details($asin)
	{
		global $wpdb;
		$reportQuery = "SELECT * FROM ".$wpdb->prefix."reviewazon_reports WHERE ASIN = '{$asin}' ORDER BY Click_Date DESC";
		$reportRows = $wpdb->get_results($reportQuery);
		if(count($reportRows) > 0)
		{
			foreach($reportRows as $reportRow)
			{
				$current = ReviewAZON_get_amazon_current_node($reportRow->ASIN,null,null,null,null);
				
				echo '<tr class="iedit alternate"><td class="column-name" style="width:10px;text-align:center;"><img src="'.$current->SmallImage->URL.'" style="margin:5px;" /></td><td class="name column-name">'.$reportRow->ASIN.'</td><td class="name column-name">'.$current->ItemAttributes->Title.'</td><td class="name column-name" nowrap>'.$reportRow->Tracking_ID.'</td><td class="name column-name" nowrap>'.$reportRow->IP_Address.'</td><td class="name column-name" nowrap>'.$reportRow->Click_Date.'</td></tr>';
			}
		}
		else
		{
			echo '<tr class="iedit alternate"><td class="column-name" colspan="4">There are no clicks to report.</td></tr>';
		}		
	}
	
	function ReviewAZON_refresh_categories()
	{
		$categoriesOption = get_categories('hide_empty=0&hierarchical=1&orderby=name');
		echo '<option value="none">Select Parent Category</option>';
		foreach ($categoriesOption as $catoption)
		{
			echo '<option value="'.$catoption->cat_ID.'">'.$catoption->cat_name.'</option>';
		}
	}
	
    function ReviewAZON_add_category($cat,$parent)
    {
    	if(!empty($parent) && $parent != "none")
    	{
    		wp_create_category($cat,$parent);
    	}
    	else
    	{
    		wp_create_category($cat);    	
    	}
    	$categories =  get_categories('hide_empty=0&hierarchical=1&orderby=name'); 
		foreach ($categories as $cat) 
		{
			if($cat->category_parent == 0)
			{
				echo '<li style="padding:5px;"><input type="checkbox" id="chkCategories" name="chkCategories[]" value="'.$cat->cat_ID.'" />&nbsp;'.$cat->cat_name.'</li>';
				$subcategories = get_categories('hide_empty=0&child_of='.$cat->cat_ID.'&orderby=name');
				foreach ($subcategories as $subcat)
				{
					echo '<li style="padding:5px;margin-left:10px;"><input type="checkbox" id="chkCategories" name="chkCategories[]" value="'.$subcat->cat_ID.'" />&nbsp;-&nbsp;'.$subcat->cat_name.'</li>';
				}
			}
		}
    }
		
	function ReviewAZON_get_signed_url($asin)	
	{
		$params = array();
		
		$params["MerchantId"] = "FeaturedBuyBoxMerchant";
		$params["Operation"] = "ItemLookup";
		$params["ResponseGroup"] = "Large,OfferFull";
		$params["ItemId"] = $asin;
		$requestURL = aws_signed_request($params);
		
		return $requestURL;
	}
	
	#Bulk Product Review Functions
	function ReviewAZON_add_bulk_review($title, $imageURL, $asin)
	{
		global $wpdb;
		$queryMaxDisplayOrder = "Select Max(Display_Order) From ".$wpdb->prefix."reviewazon_bulk_reviews";
		$maxDisplayNumber = $wpdb->get_var($queryMaxDisplayOrder) + 1;
		
		$query = "INSERT INTO ".$wpdb->prefix."reviewazon_bulk_reviews (ASIN, Product_Image, Title, Display_Order)VALUES
													  ('{$asin}', '{$imageURL}', '{$title}', '{$maxDisplayNumber}')";
		$wpdb->query($query);		

		$queryBulk = "SELECT * FROM ".$wpdb->prefix."reviewazon_bulk_reviews Order By Display_Order ASC";
		$pendingBulkReviews = $wpdb->get_results($queryBulk);
		
		
		if(count($pendingBulkReviews) > 0)
		{
			foreach($pendingBulkReviews as $pendingBulkReview)
			{
					$returnText.= '<li style="padding:5px;background-color:white;border:1px solid gainsboro;width:350px;" id="bulkReview_'.$pendingBulkReview->ASIN.'"><table class="handle" cellpadding="2" cellspacing="2" style="padding-top:10px;"><tr><td><img width="35" height="35" src="'.$pendingBulkReview->Product_Image.'" style="vertical-align:text-top;padding-right:5px;" /></td><td valign="top" style="font-size:1.0em;font-family:arial;"><span><b>'.addslashes($pendingBulkReview->Title).'</b></span><br /><span><img onclick="removeAmazonBulkItem('.$pendingBulkReview->ID.')" src="'.ReviewAZON_ADMIN_IMAGES.'delete.gif" style="vertical-align:middle;cursor:pointer;" />&nbsp;Remove</span></td></tr></table></li>';
			}
		}
		else
		{
			$returnText = '<li style="padding:10px;background-color:white;border:1px solid gainsboro;width:300px" id="default_item" style="padding:10px;"><img src="'.ReviewAZON_ADMIN_IMAGES.'error.gif" style="vertical-align:sub;padding-left:5px;padding-right:5px;" />Bulk review list is empty</li>';	
		}	
		
		echo $returnText;		
											
	}
	
	function ReviewAZON_remove_bulk_review($id)
	{
		global $wpdb;
		$query = "DELETE FROM ".$wpdb->prefix."reviewazon_bulk_reviews WHERE ID = '{$id}'";
		$wpdb->query($query);	
		
		$queryBulk = "SELECT * FROM ".$wpdb->prefix."reviewazon_bulk_reviews Order By Display_Order ASC";
		$pendingBulkReviews = $wpdb->get_results($queryBulk);
		
		
		if(count($pendingBulkReviews) > 0)
		{
			foreach($pendingBulkReviews as $pendingBulkReview)
			{
					$returnText.= '<li style="padding:5px;background-color:white;border:1px solid gainsboro;width:350px;" id="bulkReview_'.$pendingBulkReview->ASIN.'"><table class="handle" cellpadding="2" cellspacing="2" style="padding-top:10px;"><tr><td><img width="35" height="35" src="'.$pendingBulkReview->Product_Image.'" style="vertical-align:text-top;padding-right:5px;" /></td><td valign="top" style="font-size:1.0em;font-family:arial;"><span><b>'.addslashes($pendingBulkReview->Title).'</b></span><br /><span><img onclick="removeAmazonBulkItem('.$pendingBulkReview->ID.')" src="'.ReviewAZON_ADMIN_IMAGES.'delete.gif" style="vertical-align:middle;cursor:pointer;" />&nbsp;Remove</span></td></tr></table></li>';
			}
		}
		else
		{
			$returnText = '<li style="padding:10px;background-color:white;border:1px solid gainsboro;width:300px" id="default_item" style="padding:10px;"><img src="'.ReviewAZON_ADMIN_IMAGES.'error.gif" style="vertical-align:sub;padding-left:5px;padding-right:5px;" />Bulk review list is empty</li>';	
		}	
		
		echo $returnText;	
		
	}
	
	function ReviewAZON_remove_bulk_reviews()
	{
		global $wpdb;
		$query = "TRUNCATE TABLE reviewazon_bulk_reviews";
		$wpdb->query($query);	

		echo '<li style="padding:10px;background-color:white;border:1px solid gainsboro;width:300px" id="default_item" style="padding:10px;"><img src="'.ReviewAZON_ADMIN_IMAGES.'error.gif" style="vertical-align:sub;padding-left:5px;padding-right:5px;" />Bulk review list is empty</li>';	
	}
	
	function ReviewAZON_set_bulk_review_sort_order($listItems)
	{
		global $wpdb;
		foreach ($listItems as $position => $item) :
			$query = "UPDATE ".$wpdb->prefix."reviewazon_bulk_reviews SET Display_Order = '{$position}' WHERE ASIN = '{$item}'";
			$wpdb->query($query);
		endforeach;
	}
	
	//=======================================================================================================================
	//Tab Functions
	
	function ReviewAZON_get_single_tab($tabID)
	{
		global $wpdb;
		$queryTab = "SELECT * FROM ".$wpdb->prefix."reviewazon_tabs WHERE Tab_ID = '{$tabID}'";
		$tab = $wpdb->get_row($queryTab);
		echo '({"title": "'.$tab->Tab_Title.'","content": "'.$tab->Tab_Body.'","visible": "'.$tab->Show_Tab.'"})';
		
	}
	
	function ReviewAZON_update_tab($tabID, $tabTitle, $tabContent, $showTab)
	{
		global $wpdb;
		$query = "UPDATE ".$wpdb->prefix."reviewazon_tabs SET Tab_Title = '{$tabTitle}',
											 Tab_Body = '{$tabContent}',
											 Show_Tab = '{$showTab}'
											 WHERE Tab_ID = '{$tabID}'";
		$wpdb->query($query);
		echo ReviewAZON_get_all_tabs();
	}
	
	function ReviewAZON_delete_tab($tabID)
	{
		global $wpdb;
		$query = "DELETE FROM ".$wpdb->prefix."reviewazon_tabs WHERE Tab_ID = '{$tabID}'";
		$wpdb->query($query);
		echo ReviewAZON_get_all_tabs();
	}
	
	function ReviewAZON_get_all_tabs()
	{
		global $wpdb;
		$addTab = '<p><img title="Add Tab" style="vertical-align:middle;padding-right:10px;cursor:pointer;" src="'.ReviewAZON_ADMIN_IMAGES.'tab_add.gif" alt="move" width="16" height="16" /><a href="#" onclick="jQuery(\'#dialog\').dialog(\'open\');return false;">Add New Tab</a></p>';
		$queryTabs = "SELECT * FROM ".$wpdb->prefix."reviewazon_tabs Order By Display_Order ASC";
		$tabs = $wpdb->get_results($queryTabs);
		$moveTab = '<img title="Move to change sort order" style="vertical-align:middle;padding-right:5px;cursor:move;" src="'.ReviewAZON_ADMIN_IMAGES.'arrow_out.gif" alt="move" width="16" height="16" class="handle" />';
		foreach($tabs as $tab)
		{		
			$deleteTab = '<img onclick="deleteTab('.$tab->Tab_ID.')" title="Delete Tab" style="vertical-align:middle;padding-right:10px;padding-left:10px;cursor:pointer;" src="'.ReviewAZON_ADMIN_IMAGES.'tab_delete.gif" alt="move" width="16" height="16" />';
			$editTab = '<img onclick="getSingleTab('.$tab->Tab_ID.')" title="Edit Tab" style="vertical-align:middle;padding-right:10px;padding-left:10px;cursor:pointer;" src="'.ReviewAZON_ADMIN_IMAGES.'tab_edit.gif" alt="move" width="16" height="16" />';
			
		  	$returnTabs.= '<li id="listItem_'.$tab->Tab_ID.'">'.$moveTab.''.$deleteTab.''.$editTab.'<strong>'.$tab->Tab_Title.'</strong></li>';
		}
		return $returnTabs;
	}
	
	function ReviewAZON_add_new_tab($title,$content,$showTab)
	{
		global $wpdb;
		$queryMaxDisplayOrder = "Select Max(Display_Order) From ".$wpdb->prefix."reviewazon_tabs";
		$maxDisplayNumber = $wpdb->get_var($queryMaxDisplayOrder);
		$query = "INSERT INTO ".$wpdb->prefix."reviewazon_tabs SET Tab_Title = '{$title}',
												  Tab_Body = '{$content}',
												  Show_Tab = '{$showTab}',
												  Display_Order = $maxDisplayNumber + 1
												  ";
		$wpdb->query($query);
		echo ReviewAZON_get_all_tabs();	
	}
	
	function ReviewAZON_set_sort_order($listItems)
	{
		global $wpdb;
		foreach ($listItems as $position => $item) :
			$query = "UPDATE ".$wpdb->prefix."reviewazon_tabs SET Display_Order = '{$position}' WHERE Tab_ID = '{$item}'";
			$wpdb->query($query);
		endforeach;
	}
	
	//End Tab Functions
	//=======================================================================================================================
	
	//=======================================================================================================================
	//Affiliate Tracking ID Functions
	
	function ReviewAZON_get_trackingID($trackingID,$affiliateCountry)
	{
		global $wpdb;
		$query = "SELECT * FROM ".$wpdb->prefix."reviewazon_tracking_id WHERE ID = '{$trackingID}'";
		$trackingID = $wpdb->get_row($query);
		echo '({"trackingid": "'.$trackingID->Tracking_ID.'","visible": "'.$trackingID->ID.'"})';		
	}
	
	function ReviewAZON_update_trackingID($ID, $trackingID, $affiliateCountry)
	{
		global $wpdb;
		$query = "UPDATE ".$wpdb->prefix."reviewazon_tracking_id SET Tracking_ID = '{$trackingID}',
											 Affiliate_Country = '{$affiliateCountry}'
											 WHERE ID = '{$ID}'";
		$wpdb->query($query);
		echo ReviewAZON_get_all_trackingID($affiliateCountry);
	}
	
	
	function ReviewAZON_set_trackingid_sort_order($listItems)
	{
		global $wpdb;
		foreach ($listItems as $position => $item) :
			$query = "UPDATE ".$wpdb->prefix."reviewazon_tracking_id SET Display_Order = '{$position}' WHERE ID = '{$item}'";
			$wpdb->query($query);
		endforeach;
	}
	
	function ReviewAZON_delete_trackingID($trackingID,$affiliateCountry)
	{
		global $wpdb;
		$query = "DELETE FROM ".$wpdb->prefix."reviewazon_tracking_id WHERE ID = '{$trackingID}'";
		$wpdb->query($query);
		echo ReviewAZON_get_all_trackingID($affiliateCountry);
	}
	
	function ReviewAZON_add_new_trackingID($trackingID,$affiliateCountry)
	{
		global $wpdb;
		$queryMaxDisplayOrder = "Select Max(Display_Order) From ".$wpdb->prefix."reviewazon_tracking_id";
		$maxDisplayNumber = $wpdb->get_var($queryMaxDisplayOrder);
		$query = "INSERT INTO ".$wpdb->prefix."reviewazon_tracking_id SET Tracking_ID = '{$trackingID}',
												  Affiliate_Country = '{$affiliateCountry}',
												  Display_Order = $maxDisplayNumber + 1
												  ";
		$wpdb->query($query);
		echo ReviewAZON_get_all_trackingID($affiliateCountry);	
	}
	
	function ReviewAZON_get_all_trackingID($affiliateCountry)
	{
		global $wpdb;
		$queryTrackingIDs = "SELECT * FROM ".$wpdb->prefix."reviewazon_tracking_id WHERE Affiliate_Country = '{$affiliateCountry}' Order By Display_Order ASC";
		$trackingIDs = $wpdb->get_results($queryTrackingIDs);
		$moveTrackingID = '<img title="Move to change sort order" style="vertical-align:middle;padding-right:5px;cursor:move;" src="'.ReviewAZON_ADMIN_IMAGES.'arrow_out.gif" alt="move" width="16" height="16" class="handle" />';
		foreach($trackingIDs as $trackingID)
		{		
			$deleteTrackingID = '<img onclick="deleteTrackingID('.$trackingID->ID.')" title="Delete Tracking ID" style="vertical-align:middle;padding-right:10px;padding-left:10px;cursor:pointer;" src="'.ReviewAZON_ADMIN_IMAGES.'tag_blue_delete.gif" alt="move" width="16" height="16" />';
			$editTrackingID = '<img onclick="showDialog('.$trackingID->ID.',\'edit\')" title="Edit Tracking ID" style="vertical-align:middle;padding-right:10px;padding-left:10px;cursor:pointer;" src="'.ReviewAZON_ADMIN_IMAGES.'tag_blue_edit.gif" alt="move" width="16" height="16" />';
			
  			$returnTrackingID.= '<li id="listItem_'.$trackingID->ID.'"><span>'.$moveTrackingID.''.$deleteTrackingID.''.$editTrackingID.'<strong>'.$trackingID->Tracking_ID.'</strong></span></li>';
		}
		return $returnTrackingID;
	}
	
	
	//End Affiliate Tracking ID Functions
	//========================================================================================================================
	
	function ReviewAZON_delete_amazon_data($postid)
	{
		global $wpdb;
		$query = "DELETE FROM ".$wpdb->prefix."reviewazon_post_settings WHERE Post_ID = '{$postid}'";
		$wpdb->query($query);		
	}
	
	function ReviewAZON_get_single_product($asin)
	{
		try
		{
		
			$current = ReviewAZON_get_amazon_current_node($asin);
			
			$mediumImageUrl = $current->LargeImage->URL;
			
			echo '<div style="width:343px;height:300px;background-image:url('.$mediumImageUrl.');"></div>';
			
		}
		catch(Exception $ex)
		{
			echo $ex->message;			
		}
	}

	
	function ReviewAZON_add_amazon_data($asin, $postId)
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
		$displayOverstockListings = "display:none";
		
		try
		{
				
			$current = ReviewAZON_get_amazon_current_node($asin);	
			
			/*$averageRatingNumber = $current->CustomerReviews->AverageRating;
			$customerReviewCount = $current->CustomerReviews->TotalReviews;

			if(empty($customerReviewCount))
			{
				$customerReviewCount = 0;
			}*/
			
			$smallImageUrl = $current->SmallImage->URL;
			$mediumImageUrl = $current->MediumImage->URL;
			$largeImageUrl = $current->LargeImage->URL;
			$title =  addslashes($current->ItemAttributes->Title);
			
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
			
			$totalReviewPages = $current->CustomerReviews->TotalReviewPages;
			
			if(ReviewAZON_get_affiliate_setting("Cache_Description"))
			{
				$showDescription = true;
			}
			
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
			
			
			if(ReviewAZON_get_affiliate_setting("Cache_Similar_Products") && ReviewAZON_get_affiliate_setting("Page_Cache_Length") != 0)
			{
				$showSimilarProducts = true;
			}
			$similarProducts = addslashes(SimilarProducts($current));
			
			if(ReviewAZON_get_affiliate_setting("Cache_Product_Accessories") && ReviewAZON_get_affiliate_setting("Page_Cache_Length") != 0)
			{
				$showProductAccessories = true;
			}
			$productAccessories = addslashes(ProductAccessories($current));
					
			$overstockSearchQuery = '[phpostock]'.str_replace('"','',$title).', 10[/phpostock]';
			$ebaySearchQuery = '[phpbay]'.str_replace('"','',$title).', 10[/phpbay]';
			$ebayWidgetTitle = $title;
			
			if(ReviewAZON_get_affiliate_setting("Show_Ebay_Auctions"))
			{
				$showEbayOptions = true;		
			}
			
			if(ReviewAZON_get_affiliate_setting("Show_Overstock_Items"))
			{
				$showOverstockOptions = true;		
			}
			$brand = $current->ItemAttributes->Brand;
			
			$averageRatingNumber = 0;
			$customerReviewCount = 0;
			if(isset($current->CustomerReviews->IFrameURL))
			{
				$iframeUrl = (string)$current->CustomerReviews->IFrameURL;
				$reviewData = ReviewAZON_Get_Product_Rating_Customer_Count($iframeUrl);
				if(isset($reviewData["AverageRating"]))
				{
					$averageRatingNumber = $reviewData["AverageRating"];
				}
				if(isset($reviewData["ReviewCount"]))
				{
					$customerReviewCount = $reviewData["ReviewCount"];
				}
			}
			
			update_post_meta($postId,"Rating_Date",date("Y-m-d H:i:s", time()));
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
			$querySelect = "SELECT * FROM ".$wpdb->prefix."reviewazon_post_settings WHERE post_id='".$postId."'";
			$postSettings = $wpdb->get_row($querySelect);
			
			if(!empty($postSettings))
			{
				 //echo '<div style="padding-bottom:10px"><img src="'.ReviewAZON_ADMIN_IMAGES.'refresh.gif" align="absmiddle" />&nbsp;<a href="">Refresh Review Product Settings</a></div>';
				 echo '<div style="padding-top:10px;padding-bottom:2px;height:0px;"><b>Review Title</b></div><br /><input style="width:100%" type="text" name="ReviewAZON_review_title" id="ReviewAZON_review_title" value="'.$postSettings->Review_Title.'" />';
				 
				 if($showEbayOptions)
				 {				 	
				 	$displayEbayOptions = "display:";
				 }
				 if($showOverstockOptions)
				 {				 	
				 	$displayOverstockListings = "display:";
				 }
				 $ebayQuery = stripslashes($postSettings->Ebay_Search_Query);
				 $overstockQuery = stripslashes($postSettings->Overstock_Search_Query);
				 
				 echo "<div style=\"{$displayOverstockListings}\"><div style=\"padding-top:10px;padding-bottom:2px;height:0px;\"><b>Overstock.com Search Query</b></div><br /><textarea style=\"width:100%;height:25px\" name=\"ReviewAZON_overstock_query\" id=\"ReviewAZON_overstock_query\">{$overstockQuery}</textarea></div>";
				 echo "<div style=\"{$displayEbayOptions}\"><div style=\"padding-top:10px;padding-bottom:2px;height:0px;\"><b>Ebay Search Query</b></div><br /><textarea style=\"width:100%;height:25px;\" name=\"ReviewAZON_ebay_query\" id=\"ReviewAZON_ebay_query\" >{$ebayQuery}</textarea></div>";
		 		 echo '<div style="'.$displayEbayOptions.'"><div style="padding-top:10px;padding-bottom:2px;height:0px;"><b>Ebay Widget Title</b></div><br /><input style="width:100%" type="text" name="ReviewAZON_ebay_widget_title" id="ReviewAZON_ebay_widget_title" value="'.$postSettings->Ebay_Widget_Title.'" /></div>';
				 echo '<div style="padding-top:10px;padding-bottom:2px;height:0px;"><b>Product Excerpt</b></div><br /><textarea id="ReviewAZON_review_excerpt" name="ReviewAZON_review_excerpt" style="width:100%;height:150px;">'.$postSettings->Review_Excerpt.'</textarea>';
				 if($showDescription)
				 {
				 	$displayDescription = "display:";				 	
				 }
				 echo '<div style="'.$displayDescription.'"><div style="padding-top:10px;padding-bottom:2px;height:0px;"><b>Product Description</b></div><br /><textarea id="ReviewAZON_review_description" name="ReviewAZON_review_description" style="width:100%;height:150px;">'.$postSettings->Review_Description.'</textarea></div>';
			     echo '<input type="hidden"  id="hdnBrand" name="hdnBrand" value="'.$brand.'" />';
			}	
		}
		catch(Exception $ex)
		{
			echo $ex->message;
		}	
	}
	
	function array_in_array($needle, $haystacks) 
	{
	    foreach ($haystacks as $haystack) 
	    {
	        if (in_array($needle, $haystack) ) 
	        {
	            return true;
	        }
	    }

    	return false;
	} 
	
	function ReviewAZON_amazon_search_results($searchIndex, $query, $itemPage, $requestType, $sort) 
	{	
		global $wpdb;
		$savedSortValue = $sort;
		$isProductReviewPosted = false;
		$isBulkReviewReady = false;
		$isEligibleForFreeShip = false;
	    if(isset($itemPage))
		{
			$currentPageNumber = $itemPage;
			$nextPageNumber = ($currentPageNumber + 1);
			$previousPageNumber = ($currentPageNumber -1);
		}
		else
		{
			$currentPageNumber = 1;
			$nextPageNumber = ($currentPageNumber + 1);
			$previousPageNumber = 1;		
		}		
		 
		$parsed_xml = ReviewAZON_get_amazon_current_node_search($searchIndex, $query, $itemPage, $sort);	
			
		$numOfItems = $parsed_xml->Items->TotalResults;
		$numOfPages = $parsed_xml->Items->TotalPages;	
	
		$strBody = '<br /><table cellpadding="2" cellspacing="2" border="0" width="95%">';
		$strBody.= '<tr>';
		$strBody.= '<td align="left" valign="top">'.$numOfItems.' items found - Page '.$currentPageNumber.' of '.$numOfPages.'</td>';
		if($currentPageNumber > 1)
		{
			$strBody.= '<td align="right" valign="top"> &laquo; <a href="" onclick="getAmazonItemData(\''.$previousPageNumber.'\');return false;">Previous Page</a></td>';
		}
		if($currentPageNumber != $numOfPages)
		{
			$strBody.= '<td align="right" valign="top"><a href="" onclick="getAmazonItemData(\''.$nextPageNumber.'\');return false;">Next Page</a> &raquo;</td>';
		}
		$strBody.= '</tr></table><div style="width:95%;margin-top:20px;margin-bottom:20px;border-bottom:1px solid gainsboro;"></div>';
		
		if($requestType == "bulk")
		{
			$strBody.='<table style="width:90%;margin-bottom:20px;"><tr><td align="right"><img src="'.ReviewAZON_ADMIN_IMAGES.'add.gif" align="absmiddle" />&nbsp;<a href="#" onclick="selectAllItems()" style="font-weight:bold;font-size:1.0em;">Select All Products</a></td></tr></table>';
		}
		$queryASINS = "SELECT ASIN FROM ".$wpdb->prefix."reviewazon_post_settings";
		$asins = $wpdb->get_results($queryASINS, ARRAY_N);
		
		if($requestType == 'bulk')
		{
			$queryBulkReviews = "SELECT ASIN FROM ".$wpdb->prefix."reviewazon_bulk_reviews";
			$bulkReviewASIN = $wpdb->get_results($queryBulkReviews, ARRAY_N);			
		}
		
		foreach($parsed_xml->Items->Item as $current)
		{
			$brand = $current->ItemAttributes->Brand;
			$averageRatingNumber = $current->CustomerReviews->AverageRating;
			if(!isset($current->CustomerReviews->TotalReviews))
			{
				$averageRatingNumber = '0';
			}
			$customerReviewCount = $current->CustomerReviews->TotalReviews;
			if(!isset($current->CustomerReviews->TotalReviews))
			{
				$customerReviewCount = '0';
			}
			
			$lowestNewPrice = $current->OfferSummary->LowestNewPrice->FormattedPrice;
			
			if($current->Offers->Offer->OfferListing->IsEligibleForSuperSaverShipping == 1)
			{
				$isEligibleForFreeShip = true;
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
			
			$asin = $current->ASIN;
			
			if($requestType == "normal" || $requestType == "bulk")
			{
				if(count($asins)>0)
				{
					if (array_in_array($asin, $asins)) 
					{
						$isProductReviewPosted = true;	
					}
				}
			}
			
			if(count($bulkReviewASIN)>0)
			{
				if (array_in_array($asin, $bulkReviewASIN)) 
				{
					$isBulkReviewReady = true;	
				}
			}		
			
			
			$smallImage = $current->SmallImage->URL;
			$mediumImage = $current->MediumImage->URL;
			$title = str_replace("\"","",$current->ItemAttributes->Title);
			
			if(empty($smallImage))
			{
				$smallImage = ReviewAZON_ADMIN_IMAGES.'noimage-75.gif';
			}
			if(empty($mediumImage))
			{
				$mediumImage = ReviewAZON_ADMIN_IMAGES.'noimage.gif';
			}
			
			if($isProductReviewPosted)
			{
				$opacity = "opacity:0.4;";
			}
			else
			{
				$opacity = "";
			}
			
			if($requestType != "searchquery")
			{
				if($requestType == "singleinlinepost")
				{
					$addLink = '<img src="'.ReviewAZON_ADMIN_IMAGES.'add.gif" align="absmiddle" />&nbsp;<a href="#" style="" onClick="addSingleInlinePost(\''.$asin.'\')">Add Single Review</a>';
				}
				else
				{
					if($requestType == "bulk")
					{
						if($isBulkReviewReady)
						{
							$addLink = '<img src="'.ReviewAZON_ADMIN_IMAGES.'accept.gif" align="absmiddle" />&nbsp;<span style="color:blue;font-weight:bold;">Item Added</span>';
						}
						else
						{
							$addLink = '<span id="bulkLink_'.$asin.'" ><img src="'.ReviewAZON_ADMIN_IMAGES.'add.gif" align="absmiddle" />&nbsp;<a href="#" class="addBulkLink" style="" onClick="addAmazonBulkLink(\''.addslashes($title).'\',\''.$asin.'\',\''. $mediumImage.'\',\'' .addslashes($brand). '\')">Add Review</a>&nbsp;<img id="ajaxloader_'.$asin.'" src="'.ReviewAZON_ADMIN_IMAGES.'ajax-loader_small.gif" style="display:none;" align="absmiddle" /></span>';
						}
					}
					else
					{
						$addLink = '<img src="'.ReviewAZON_ADMIN_IMAGES.'add.gif" align="absmiddle" />&nbsp;<a href="#" style="" onClick="insertAmazonLink(\''.addslashes($title).'\',\''.$asin.'\',\''. $mediumImage.'\')">Add Review</a>';
					}
				}
			}
				
			$stars = ReviewAZON_customer_rating($averageRatingNumber, false, "");
			$strBody.= '<table cellpadding="2" cellspacing="2" border="0" width="90%">';
			$strBody.= '<tr>';
			$strBody.= '<td align="left" valign="top" style="width:85px;padding:10px;border-bottom:1px dotted gainsboro;"><img src="'.$smallImage.'" style="'.$opacity.'" /></td>';
			$strBody.= '<td valign="top" style="padding:10px;border-bottom:1px dotted gainsboro;">';
			$strBody.= '<table cellpadding="3" cellspacing="3" border="0" style="width:100%;'.$opacity.'">';
			$strBody.= '<tr>';
			$strBody.= '<td style="WORD-BREAK:BREAK-ALL;"><b>'.$title.'</b></td>';
			$strBody.= '</tr>';
			//$strBody.= '<tr><td>'.$stars.'</td></tr>';
			$strBody.= '<tr><td style="font-weight:bold;">ASIN: '.$asin.'</td></tr>';
			$strBody.= '<tr><td nowrap><b>Price: </b><span style="color:#A91B33;font-weight:bold;">'.$lowestNewPrice.'</span></td></tr>';
			//$strBody.= '<tr><td nowrap><img src="'.ReviewAZON_ADMIN_IMAGES.'user_comment.gif" align="absmiddle" />&nbsp;<b>Customer Reviews</b> ('.$customerReviewCount.')</td>';
			$strBody.= '</tr>';

			if($isEligibleForFreeShip)
			{
				$strBody.= '<tr><td><img src="'.ReviewAZON_ADMIN_IMAGES.'package.gif" align="absmiddle" />&nbsp;<span style="color:green"><b>Eligible for FREE Shipping</b></span></td></tr>';
				
			}
			$strBody.= '</table>';
			$strBody.= '</td>';
			
			if($isProductReviewPosted)
			{
				$strBody.= '<td align="right" valign="top" style="padding:10px;border-bottom:1px dotted gainsboro;" nowrap><img src="'.ReviewAZON_ADMIN_IMAGES.'accept.gif" align="absmiddle" />&nbsp;<span style="color:red;font-weight:bold;">This Item Is Published</span></td>';
			}
			else
			{
				$strBody.= '<td align="right" valign="top" style="padding:10px;border-bottom:1px dotted gainsboro;" nowrap>'.$addLink.'</td>';				
			}
			$strBody.= '</tr></table>';
			
			$isProductReviewPosted = false;
			$isBulkReviewReady = false;
			$isEligibleForFreeShip = false;
		}
				
		$strBody.= '<br /><table cellpadding="2" cellspacing="2" border="0" width="95%">';
		$strBody.= '<tr>';
		$strBody.= '<td align="left" valign="top">'.$numOfItems.' items found - Page '.$currentPageNumber.' of '.$numOfPages.'</td>';
		if($currentPageNumber > 1)
		{
			$strBody.= '<td align="right" valign="top"> &laquo; <a href="" onclick="getAmazonItemData(\''.$previousPageNumber.'\');return false;">Previous Page</a></td>';
		}
		if($currentPageNumber != $numOfPages)
		{
			$strBody.= '<td align="right" valign="top"><a href="" onclick="getAmazonItemData(\''.$nextPageNumber.'\');return false;">Next Page</a> &raquo;</td>';
		}
		$strBody.= '</tr></table>';
	
		if($numOfItems == 0)
		{
			echo '<br /><br />No products were found.';
		}
		else
		{
			if(ReviewAZON_get_affiliate_setting("Save_Last_Search_Results"))
			{
				update_option("search_category",$searchIndex);
				update_option("search_text",$query);
				update_option("search_sort",$savedSortValue);
				update_option("search_page",$itemPage);
			}
			else
			{
				update_option("search_category","");
				update_option("search_text","");	
				update_option("search_page","1");
				update_option("search_sort","");				
			}

			echo $strBody;
		}	
	}
?>



