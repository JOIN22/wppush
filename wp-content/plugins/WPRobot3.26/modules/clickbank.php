<?php

function wpr_clickbankpost($keyword,$num,$start,$optional="",$comments="") {
	global $wpdb,$wpr_table_templates;

	if($keyword == "") {
		$return["error"]["module"] = "Clickbank";
		$return["error"]["reason"] = "No keyword";
		$return["error"]["message"] = __("No keyword specified.","wprobot");
		return $return;	
	}
	
	$template = $wpdb->get_var("SELECT content FROM " . $wpr_table_templates . " WHERE type = 'clickbank'");
	if($template == false || empty($template)) {
		$return["error"]["module"] = "Clickbank";
		$return["error"]["reason"] = "No template";
		$return["error"]["message"] = __("Module Template does not exist or could not be loaded.","wprobot");
		return $return;	
	}			
	$options = unserialize(get_option("wpr_options"));
	$affid = $options['wpr_cb_affkey'];if ($affid == '') { $affid = 'lun4tic' ;}
	$keyword = str_replace( '"',"",$keyword );	
	$keyword = str_replace( " ","+",$keyword );
	$search_url = "http://www.clickbank.com/mkplSearchResult.htm?dores=true&includeKeywords=$keyword&firstResult=$start";
	$posts = array();

	// make the cURL request to $search_url
	if ( function_exists('curl_init') ) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT, 'Firefox (WindowsXP) - Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6');
		curl_setopt($ch, CURLOPT_URL,$search_url);
		curl_setopt($ch, CURLOPT_FAILONERROR, true);
		curl_setopt($ch, CURLOPT_AUTOREFERER, true);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_TIMEOUT, 45);
		$html = curl_exec($ch);
		if (!$html) {
			$return["error"]["module"] = "Clickbank";
			$return["error"]["reason"] = "cURL Error";
			$return["error"]["message"] = __("cURL Error Number ","wprobot").curl_errno($ch).": ".curl_error($ch);	
			return $return;
		}		
		curl_close($ch);
	} else { 				
		$html = @file_get_contents($search_url);
		if (!$html) {
			$return["error"]["module"] = "Clickbank";
			$return["error"]["reason"] = "cURL Error";
			$return["error"]["message"] = __("cURL is not installed on this server!","wprobot");	
			return $return;		
		}
	}

	// parse the html into a DOMDocument  

	$dom = new DOMDocument();
	@$dom->loadHTML($html);
	
	$xpath1 = new DOMXPath($dom);			
	$paras1 = $xpath1->query("//div[@id='results']//tr/td[@class='details']/h4/a");

	for ($i = 0;  $i < $paras1->length; $i++ ) {		//$paras->length
		if($i < $num) {
			$para1 = $paras1->item($i);
			$urlt = $para1->textContent;	

			if($urlt == '' | $urlt == null) {
				$posts["error"]["module"] = "Clickbank";
				$posts["error"]["reason"] = "No content";
				$posts["error"]["message"] = __("No (more) Clickbank products found.","wprobot");	
				return $posts;	
			} else {
				$url = $para1->getAttribute('href');
				$url = str_replace("zzzzz", $affid, $url);	

				$xpath2 = new DOMXPath($dom);
				$paras2 = $xpath2->query("//div[@id='results']//td[@class='details']//div[@class='description']");
					$para2 = $paras2->item($i);
					$description = $para2->textContent;	
					
				$xpath3 = new DOMXPath($dom);			
				$paras3 = $xpath3->query("//div[@id='results']//td[@class='details']//h4/a");
					$para3 = $paras3->item($i);
					$title = $para3->textContent;					
				
				$link = '<a rel="nofollow" href="'.$url.'">'.$title . '</a>';	

				$ff = $options['wpr_cb_filter'];
				$stop = 0;
				if($ff == "yes") {
					$pos = strpos($description, "Commission");
					if ($pos !== false) {$stop = 1;}				
					$pos = strpos($description, "commission");
					if ($pos !== false) {$stop = 1;}
					$pos = strpos($description, "affiliate");
					if ($pos !== false) {$stop = 1;}	
					$pos = strpos($description, "Affiliate");
					if ($pos !== false) {$stop = 1;}						
					$pos = strpos($description, "affiliates");
					if ($pos !== false) {$stop = 1;}						
				}								
				
				if($stop == 0) {				
					$post = $template;
					$post = wpr_random_tags($post);
					$post = str_replace("{link}", $link, $post);							
					$post = str_replace("{description}", $description, $post);
					$post = str_replace("{url}", $url, $post);	
					$noqkeyword = str_replace('"', '', $keyword);
					$post = str_replace("{keyword}", $noqkeyword, $post);
					$post = str_replace("{Keyword}", ucwords($noqkeyword), $post);				
					$post = str_replace("{title}", $title, $post);	
					if(function_exists("wpr_translate_partial")) {
						$post = wpr_translate_partial($post);
					}
					
					$posts[$i]["unique"] = $title;
					$posts[$i]["title"] = $title;
					$posts[$i]["content"] = $post;	
				}	
			}
		}
	}
	
	if(empty($posts)) {
		$posts["error"]["module"] = "Clickbank";
		$posts["error"]["reason"] = "No content";
		$posts["error"]["message"] = __("No (more) Clickbank ads found.","wprobot");	
		return $posts;			
	} else {
		return $posts;	
	}					
}

function wpr_clickbank_options_default() {
	$options = array(
		"wpr_cb_filter" => "",
		"wpr_cb_affkey" => ""
	);
	return $options;
}

function wpr_clickbank_options($options) {
	?>
	<h3 style="text-transform:uppercase;border-bottom: 1px solid #ccc;"><?php _e("Clickbank Options","wprobot") ?></h3>
		<table class="addt" width="100%" cellspacing="2" cellpadding="5" class="editform"> 	
			<tr <?php if($options['wpr_cb_affkey'] == "") {echo 'style="background:#F8E0E0;"';} ?> valign="top"> 
				<td width="40%" scope="row"><?php _e("Clickbank Affiliate ID:","wprobot") ?></td> 
				<td><input size="40" name="wpr_cb_affkey" type="text" id="wpr_cb_affkey" value="<?php echo $options['wpr_cb_affkey'];?>"/>
				<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('This option is not required but you will only earn affiliate commission if you enter your Clickbank affiliate ID.',"wprobot") ?></span></a>
			</td> 
			</tr>	
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Filter Ads?","wprobot") ?></td> 
				<td><input name="wpr_cb_filter" type="checkbox" id="wpr_cb_filter" value="yes" <?php if ($options['wpr_cb_filter']=='yes') {echo "checked";} ?>/> <?php _e("Yes","wprobot") ?>
				<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('When enabled ads that contain the words "Commission" or "Affiliate" are skipped and removed from the results in order to weed out ads targeted at potential affiliates that are unfortunatelly not uncommon in the Clickbank marketplace.',"wprobot") ?></span></a></td> 
			</tr>					
		</table>	
	<?php
}
?>