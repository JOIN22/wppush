<?php

function wpr_yahoonewsrequest($keyword,$num,$start) {	
	libxml_use_internal_errors(true);
	$options = unserialize(get_option("wpr_options"));	
	$appid = $options['wpr_yap_appkey'];
	if(empty($appid)) {$appid = $options['wpr_yan_appkey'];}
	$region = $options['wpr_yan_lang'];

	$keyword = urlencode($keyword);
	
    $request = "http://search.yahooapis.com/NewsSearchService/V1/newsSearch?appid=".$appid."&query=".$keyword."&language=".$region."&start=".$start."&results=".$num;

	if ( function_exists('curl_init') ) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (compatible; Konqueror/4.0; Microsoft Windows) KHTML/4.0.80 (like Gecko)");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_URL, $request);
		curl_setopt($ch, CURLOPT_TIMEOUT, 60);
		$response = curl_exec($ch);
		if (!$response) {
			$return["error"]["module"] = "Yahoo News";
			$return["error"]["reason"] = "cURL Error";
			$return["error"]["message"] = __("cURL Error Number ","wprobot").curl_errno($ch).": ".curl_error($ch);	
			return $return;
		}		
		curl_close($ch);
	} else { 				
		$response = @file_get_contents($request);
		if (!$response) {
			$return["error"]["module"] = "Yahoo News";
			$return["error"]["reason"] = "cURL Error";
			$return["error"]["message"] = __("cURL is not installed on this server!","wprobot");	
			return $return;		
		}
	}
    
	$pxml = simplexml_load_string($response);
	//print_r($pxml);
	if ($pxml === False) {
		$pxml = simplexml_load_file($request); 
		if ($pxml === False) {	
			$emessage = __("Failed loading XML, errors returned: ","wprobot");
			foreach(libxml_get_errors() as $error) {
				$emessage .= $error->message . ", ";
			}	
			libxml_clear_errors();
			$return["error"]["module"] = "Yahoo News";
			$return["error"]["reason"] = "XML Error";
			$return["error"]["message"] = $emessage;	
			return $return;		
		} else {
			return $pxml;
		}			
	} else {
		return $pxml;
	}
}

function wpr_yahoonewspost($keyword,$num,$start,$optional="",$comments="") {
	global $wpdb,$wpr_table_templates;

	if($keyword == "") {
		$return["error"]["module"] = "Yahoo News";
		$return["error"]["reason"] = "No keyword";
		$return["error"]["message"] = __("No keyword specified.","wprobot");
		return $return;	
	}
	
	$template = $wpdb->get_var("SELECT content FROM " . $wpr_table_templates . " WHERE type = 'yahoonews'");
	if($template == false || empty($template)) {
		$return["error"]["module"] = "Yahoo News";
		$return["error"]["reason"] = "No template";
		$return["error"]["message"] = __("Module Template does not exist or could not be loaded.","wprobot");
		return $return;	
	}		
	$x = 0;
	$newscontent = array();
	$pxml = wpr_yahoonewsrequest($keyword,$num,$start);
	if(!empty($pxml["error"])) {return $pxml;}
	if ($pxml === False) {
		$newscontent["error"]["module"] = "Yahoonews";
		$newscontent["error"]["reason"] = "Request fail";
		$newscontent["error"]["message"] = __("API request could not be sent.","wprobot");	
		return $newscontent;	
	} else {
		if (isset($pxml->Result)) {
			foreach($pxml->Result as $news) {		

				$title = $news->Title;					
				$summary = $news->Summary;				
				$url = $news->ClickUrl;				
				$source = $news->NewsSource;
				$thumb = $news->Thumbnail;
				
				$source = "Read more on <a rel=\"nofollow\" href=\"$url\">$source</a><br/><br/>";
				if($thumb != "") {$thumbnail = '<a href="'.$url.'" rel="nofollow"><img style="float:left;margin: 0 20px 10px 0;" src="'.$img.'" /></a>';} else {$thumbnail = '';}
				$content = $template;
				$content = wpr_random_tags($content);
				$content = str_replace("{thumbnail}", $thumbnail, $content);
				$content = str_replace("{title}", $title, $content);
				$summary = str_replace("$", "$ ", $summary); 
				$content = str_replace("{summary}", $summary, $content);
				$content = str_replace("{source}", $source, $content);
				$content = str_replace("{url}", $url, $content);						
				$noqkeyword = str_replace('"', '', $keyword);
				$content = str_replace("{keyword}", $noqkeyword, $content);
				$content = str_replace("{Keyword}", ucwords($noqkeyword), $content);									
					if(function_exists("wpr_translate_partial")) {
						$content = wpr_translate_partial($content);
					}
					
				$newscontent[$x]["unique"] = $url;
				$newscontent[$x]["title"] = $title;
				$newscontent[$x]["content"] = $content;	
				$x++;
			}
			
			if(empty($newscontent)) {
				$newscontent["error"]["module"] = "Yahoonews";
				$newscontent["error"]["reason"] = "No content";
				$newscontent["error"]["message"] = __("No (more) Yahoo news items found.","wprobot");	
				return $newscontent;		
			} else {
				return $newscontent;	
			}			
		} else {
			if (isset($pxml->Message)) {
				$message = __('There was a problem with your API request. This is the error Yahoo returned:',"wprobot").' <b>'.$pxml->Message.'</b>';	
				$newscontent["error"]["module"] = "Yahoonews";
				$newscontent["error"]["reason"] = "API fail";
				$newscontent["error"]["message"] = $message;	
				return $newscontent;			
			} else {
				$newscontent["error"]["module"] = "Yahoonews";
				$newscontent["error"]["reason"] = "No content";
				$newscontent["error"]["message"] = __("No (more) Yahoo news items found.","wprobot");	
				return $newscontent;				
			}			
		}
	}	
}

function wpr_yahoonews_options_default() {
	$options = array(
		"wpr_yan_appkey" => "",
		"wpr_yan_lang" => "en"
	);
	return $options;
}

function wpr_yahoonews_options($options) {
	if(empty($options['wpr_yan_appkey']) && !empty($options['wpr_yap_appkey'])) {$options['wpr_yan_appkey'] = $options['wpr_yap_appkey'];}
	?>
	<h3 style="text-transform:uppercase;border-bottom: 1px solid #ccc;"><?php _e("Yahoo News Options","wprobot") ?></h3>			
		<table class="addt" width="100%" cellspacing="2" cellpadding="5" class="editform"> 
			<tr <?php if($options['wpr_yan_appkey'] == "" && $options['wpr_yap_appkey'] == "") {echo 'style="background:#F8E0E0;"';} ?> valign="top"> 
				<td width="40%" scope="row"><?php _e("Yahoo Application ID:","wprobot") ?></td> 
				<td><input size="40" name="wpr_yan_appkey" type="text" id="wpr_yan_appkey" value="<?php echo $options['wpr_yan_appkey'] ;?>"/>
				<!--Tooltip--><a target="_blank" class="tooltip" href="http://developer.yahoo.com/answers/">?<span><?php _e('This setting is required for the Yahoo News module to work!<br/><br/><b>Click to go to the Yahoo API sign up page!</b>',"wprobot") ?></span></a>
			</td> 
			</tr>			
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Country:","wprobot") ?></td> 
				<td>
				<select name="wpr_yan_lang" id="wpr_yan_lang">
					<option value="en" <?php if($options['wpr_yan_lang']=="en"){_e('selected');}?>><?php _e("English","wprobot") ?></option>
					<option value="de" <?php if($options['wpr_yan_lang']=="de"){_e('selected');}?>><?php _e("German","wprobot") ?></option>
					<option value="fr" <?php if($options['wpr_yan_lang']=="fr"){_e('selected');}?>><?php _e("French","wprobot") ?></option>
					<option value="it" <?php if($options['wpr_yan_lang']=="it"){_e('selected');}?>><?php _e("Italian","wprobot") ?></option>
					<option value="es" <?php if($options['wpr_yan_lang']=="es"){_e('selected');}?>><?php _e("Spanish","wprobot") ?></option>
					<option value="nl" <?php if($options['wpr_yan_lang']=="nl"){_e('selected');}?>><?php _e("Dutch","wprobot") ?></option>
					<option value="cn" <?php if($options['wpr_yan_lang']=="cn"){_e('selected');}?>><?php _e("Chinese","wprobot") ?></option>
					<option value="tw" <?php if($options['wpr_yan_lang']=="tw"){_e('selected');}?>><?php _e("Taiwanese","wprobot") ?></option>								
				</select>
			</td> 
			</tr>									
		</table>		
	<?php
}

?>