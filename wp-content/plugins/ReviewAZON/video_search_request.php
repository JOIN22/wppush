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
	
	global $wpdb;
	
	if(ReviewAZON_isAjax() && ReviewAZON_is_from_admin($_SERVER['HTTP_REFERER']))
	{	
		if(check_ajax_referer('my-nonce'))
		{
			if(isset($_POST['action']))
			{
				if($_POST['action'] == 'addvideo')
				{
					ReviewAZON_add_new_review($_POST['postid'],$_POST['thumbnailurl'],$_POST['videourl'],$_POST['videotitle']);
				}
				if($_POST['action'] == 'getvideos')
				{
					ReviewAZON_video_review_search($_POST['url'],$_POST['query'],$_POST['itemPage'],$_POST['postid']);
				}
				if($_POST['action'] == 'removevideo')
				{
					ReviewAZON_remove_video($_POST['videoid'], $_POST['postid']);
				}
			}
		}
	}
#===========================================================================================================
# Function: ReviewAZON_remove_video
# Description: Removes a video review from the ReviewAZON_Video_Reviews database
#===========================================================================================================
	function ReviewAZON_remove_video($videoID, $postID)
	{
		global $wpdb;
		try
		{
			$query = "DELETE FROM ".$wpdb->prefix."reviewazon_video_reviews WHERE Video_ID = '".$videoID."'";
			$wpdb->query($query);
			$queryAddedVideos = "SELECT * FROM ".$wpdb->prefix."reviewazon_video_reviews WHERE post_id='{$postID}'";
			$videos = $wpdb->get_results($queryAddedVideos);
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
				echo '<div id="review_videos"><ul id="video_list"><li id="message"><img src="'.ReviewAZON_ADMIN_IMAGES.'error.gif" style="padding-right:5px;vertical-align:sub;" />No review videos are assigned.</li></ul></div>';		
			}
		}
		catch(SQLException $e)
		{
			echo $e->message;
		}		
	}
	
#===========================================================================================================
# Function: isVideoPublished
# Description: Checks to see if a video exists in the specified array
#===========================================================================================================
#***********************************************************************************************************
	
	function isVideoPublished($needle, $haystacks) 
	{
	    foreach ($haystacks as $haystack) 
	    {
	        if (in_array($needle, $haystack) ) 
	        {
	            return true;
	        }
   		}
	}
#***********************************************************************************************************


#===========================================================================================================
# Function: ReviewAZON_add_new_review
# Description: Checks to see if a video exists in the specified array
#===========================================================================================================
#***********************************************************************************************************	

	function ReviewAZON_add_new_review($postid,$thumbnailURL,$videoURL,$title)
	{
		global $wpdb;
		try
		{
			$query = "INSERT INTO ".$wpdb->prefix."reviewazon_video_reviews (post_id,video_title,video_image_url,video_watch_url)
				 VALUES ('{$postid}','{$title}','{$thumbnailURL}','{$videoURL}')";
			$wpdb->query($query);
			$queryAddedVideos = "SELECT * FROM ".$wpdb->prefix."reviewazon_video_reviews WHERE post_id='{$postid}'";
			$videos = $wpdb->get_results($queryAddedVideos);
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
				echo '<div id="review_videos"><ul id="video_list"><li id="message"><img src="'.ReviewAZON_ADMIN_IMAGES.'error.gif" style="padding-right:5px;vertical-align:sub;" />No review videos are assigned.</li></ul></div>';		
			}
		}
		catch(SQLException $e)
		{
			echo $e->message;
		}
	}
#***********************************************************************************************************

	
#===========================================================================================================
# Function: ReviewAZON_video_review_search
# Description: Gets videos using the YouTube api
#===========================================================================================================	
#***********************************************************************************************************	
	function ReviewAZON_video_review_search($url,$query,$itemPage,$postid)
	{
		global $wpdb;
		$isVideoThere = false;
		if(!empty($url))
		{
			$request = $url;		
		}
		else
		{
			$request = 'http://gdata.youtube.com/feeds/api/videos?q='.urlencode($query).'&orderby=relevance&max-results=50&start-index='.$itemPage;
		}
		
		try
		{
			$session = curl_init($request);
			curl_setopt($session, CURLOPT_HEADER, false);
			curl_setopt($session, CURLOPT_RETURNTRANSFER, true);
			$response = curl_exec($session);
			curl_close($session); 
			$parsed_xml = simplexml_load_string($response);
			
			if(empty($parsed_xml))
			{
				die("<p>Error: Check your connection.</p>");
			}
			
			// get summary counts from opensearch: namespace
		      $counts = $parsed_xml->children('http://a9.com/-/spec/opensearchrss/1.0/');
		      $total = $counts->totalResults; 
		      $startOffset = $counts->startIndex; 
		      $endOffset = ($startOffset-1) + $counts->itemsPerPage; 
		      
		      $links = $parsed_xml->children('http://www.w3.org/2005/Atom'); 
		      
		      foreach($links as $link) 
		      {   
			      	foreach($link->attributes() as $key => $value) 
			      	{    
			      		if($key === "rel" && $value == "next")
			      		{
			      			$attrs = $link->attributes();
			      			$nextLink = $attrs['href'];  
			      		}
			      	    if($key === "rel" && $value == "previous")
			      		{
			      			$attrs = $link->attributes();
			      			$previousLink = $attrs['href'];  
			      		}
			      	}
		
				} 
		
		 
			  $strBody = '<br /><table cellpadding="2" cellspacing="2" border="0" width="95%" align="center">';
			  $strBody.= '<tr>';
			  $strBody.= '<td align="left" valign="top">Viewing items '.$startOffset.' - '.$endOffset.'</td>';
			  if(!empty($previousLink))
			  {
			   	$strBody.= '<td align="right" valign="top"> &laquo; <a href="" onclick="getVideoReviewData(\''.$previousLink.'\');return false;">Previous Page</a></td>';
			  }
			  if(!empty($nextLink))
			  {
				$strBody.= '<td align="right" valign="top"><a href="" onclick="getVideoReviewData(\''.$nextLink.'\',\'\');return false;">Next Page</a> &raquo;</td>';
			  }
			  $strBody.= '</tr></table><div style="margin-top:20px;margin-bottom:20px;border-bottom:1px solid gainsboro;"></div>';
		
			  	$queryVideoIDS = "SELECT Video_Watch_Url FROM ".$wpdb->prefix."reviewazon_video_reviews WHERE POST_ID = '{$postid}'";
				$videoids = $wpdb->get_results($queryVideoIDS, ARRAY_N);
		
		      
		      foreach ($parsed_xml->entry as $entry) 
		      {
		        // get nodes in media: namespace for media information
		        $media = $entry->children('http://search.yahoo.com/mrss/');
		        
		        // get video player URL
		        $attrs = $media->group->player->attributes();
		        $watch = $attrs['url']; 
		        $watch = substr($watch,(strpos($watch,'v=')+2));
		        
		      	if(count($videoids)>0)
				{
					if (isVideoPublished($watch, $videoids)) 
					{
						$isVideoThere = true;	
					}
				}
				
		      	if($isVideoThere)
				{
					$opacity = "opacity:0.4;";
				}
				else
				{
					$opacity = "";
				}
		                
		        // get video thumbnail
		        $attrs = $media->group->thumbnail[0]->attributes();
		        $thumbnail = $attrs['url']; 
		        
		        // get <gd:rating> node for video ratings
		        $gd = $entry->children('http://schemas.google.com/g/2005'); 
		        if ($gd->rating) {
		          $attrs = $gd->rating->attributes();
		          $rating = $attrs['average']; 
		        } else {
		          $rating = 0; 
		        }
		
		        $stars = ReviewAZON_customer_rating($rating, false, "");
		        $title = $media->group->title;
		        $description = substr($media->group->description,0,350);
		        
		        
				$strBody.= '<table cellpadding="2" cellspacing="2" border="0" width="650">';
				$strBody.= '<tr>';
				$strBody.= '<td align="left" valign="top" style="width:85px;padding:10px;border-bottom:1px dotted gainsboro;"><img src="'.$thumbnail.'" style="'.$opacity.'"/></td>';
				$strBody.= '<td width="250" valign="top" style="padding:10px;border-bottom:1px dotted gainsboro;">';
				$strBody.= '<table cellpadding="2" cellspacing="2" border="0" style="width:100%;'.$opacity.'">';
				$strBody.= '<tr>';
				$strBody.= '<td width="250" style="WORD-BREAK:BREAK-ALL;"><b>'.$title.'</b></td>';
				$strBody.= '</tr><tr><td>';
				$strBody.= '<table cellpadding="1" cellspacing="1" border="0">';
				$strBody.= '<tr>';
				$strBody.= '<td width="250" valign="middle" nowrap>'.$stars.' <span>('.$rating.'/5)</span></td>';
				$strBody.= '</tr>';
				$strBody.= '</table>';
				$strBody.= '</td></tr><tr>';
				$strBody.= '<td width="250">'.str_replace('=','',$description).'</td>';
				$strBody.= '</tr></table>';
				$strBody.= '</td>';
				if($isVideoThere)
				{
					$strBody.= '<td align="right" valign="top" style="padding:10px;border-bottom:1px dotted gainsboro;" nowrap><img src="'.ReviewAZON_ADMIN_IMAGES.'accept.gif" align="absmiddle" />&nbsp;<span style="color:red;font-weight:bold;">This Item Is Published</span></td>';
				}
				else
				{
					$strBody.= '<td align="right" valign="top" style="padding:10px;border-bottom:1px dotted gainsboro;" nowrap><span id="videoLink_'.$watch.'"><img src="'.ReviewAZON_ADMIN_IMAGES.'add.gif" align="absmiddle" />&nbsp;<a href="#" onClick="addVideoReview(\''.$thumbnail.'\',\''.$watch.'\',\''.$title.'\')">Add Video Review</a>&nbsp;<img id="ajaxloader_'.$watch.'" src="'.ReviewAZON_ADMIN_IMAGES.'ajax-loader_small.gif" style="display:none;" align="absmiddle" /></span></td>';
				}
				$strBody.= '</tr></table>';
				$isVideoThere = false;
		      }
		
		      if($total == 0)
		      {
		      	echo 'No results were found for your search query. ';
		      }
		      else
		      {
		      	echo $strBody;
		      }	
		}
		catch(Exception $ex)
		{
			echo $ex->message;
		}
}
?>