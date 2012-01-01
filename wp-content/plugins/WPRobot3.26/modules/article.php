<?php

function wpr_articlepost($keyword,$num,$start,$optional="",$comments="") {
	global $wpdb,$wpr_table_templates;
	
	if($keyword == "") {
		$return["error"]["module"] = "Article";
		$return["error"]["reason"] = "No keyword";
		$return["error"]["message"] = __("No keyword specified.","wprobot");
		return $return;	
	}	
	
	$template = $wpdb->get_var("SELECT content FROM " . $wpr_table_templates . " WHERE type = 'article'");
	if($template == false || empty($template)) {
		$return["error"]["module"] = "Article";
		$return["error"]["reason"] = "No template";
		$return["error"]["message"] = __("Module Template does not exist or could not be loaded.","wprobot");
		return $return;	
	}		
	$options = unserialize(get_option("wpr_options"));
 	$posts = array();
	
	$keyword2 = $keyword;	
	$keyword = str_replace( " ","+",$keyword );	
	$keyword = urlencode($keyword);
	
	$blist[] = "Mozilla/5.0 (compatible; Konqueror/4.0; Microsoft Windows) KHTML/4.0.80 (like Gecko)";
    $blist[] = "Mozilla/5.0 (compatible; Konqueror/3.92; Microsoft Windows) KHTML/3.92.0 (like Gecko)";
    $blist[] = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.0; WOW64; SLCC1; .NET CLR 2.0.50727; .NET CLR 3.0.04506; Media Center PC 5.0; .NET CLR 1.1.4322; Windows-Media-Player/10.00.00.3990; InfoPath.2";
    $blist[] = "Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1; .NET CLR 1.1.4322; InfoPath.1; .NET CLR 2.0.50727; .NET CLR 3.0.04506.30; Dealio Deskball 3.0)";
    $blist[] = "Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; NeosBrowser; .NET CLR 1.1.4322; .NET CLR 2.0.50727)";
    $ua = $blist[array_rand($blist)];	

	$page = $start / 15;
	$page = (string) $page; 
	$page = explode(".", $page);	
	$page=(int)$page[0];	
	$page++;	

	if($page == 0) {$page = 1;}
	$prep = floor($start / 15);
	$numb = $start - $prep * 15;

	$lang = $options['wpr_eza_lang'];
	if($lang == "en") {
		$search_url = "http://www.articlesbase.com/find-articles.php?q=$keyword&page=$page";
	} elseif($lang == "fr") {
		$search_url = "http://fr.articlesbase.com/find-articles.php?q=$keyword&page=$page";	
	} elseif($lang == "es") {
		$search_url = "http://www.articuloz.com/find-articles.php?q=$keyword&page=$page";
	} elseif($lang == "pg") {
		$search_url = "http://www.artigonal.com/find-articles.php?q=$keyword&page=$page";
	} elseif($lang == "ru") {
		$search_url = "http://www.rusarticles.com/find-articles.php?q=$keyword&page=$page";
	}

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
			$return["error"]["module"] = "Article";
			$return["error"]["reason"] = "cURL Error";
			$return["error"]["message"] = __("cURL Error Number ","wprobot").curl_errno($ch).": ".curl_error($ch);	
			return $return;
		}		
		curl_close($ch);
	} else { 				
		$html = @file_get_contents($search_url);
		if (!$html) {
			$return["error"]["module"] = "Article";
			$return["error"]["reason"] = "cURL Error";
			$return["error"]["message"] = __("cURL is not installed on this server!","wprobot");	
			return $return;		
		}
	}	

	// parse the html into a DOMDocument  

	$dom = new DOMDocument();
	@$dom->loadHTML($html);

	// Grab Product Links  

	$xpath = new DOMXPath($dom);
	$paras = $xpath->query("//div//h3/a");
	
	$x = 0;
	$end = $numb + $num;
	
		if($paras->length == 0) {
			$posts["error"]["module"] = "Article";
			$posts["error"]["reason"] = "No content";
			$posts["error"]["message"] = __("No (more) articles found.","wprobot");	
			return $posts;		
		}	
	
	if($end > $paras->length) { $end = $paras->length;}
	for ($i = $numb;  $i < $end; $i++ ) {
	
		$para = $paras->item($i);
	
		if(empty($para)) {
			$posts["error"]["module"] = "Article";
			$posts["error"]["reason"] = "No content";
			$posts["error"]["message"] = __("No (more) articles found.","wprobot");	
			//print_r($posts);
			return $posts;		
		} else {
		
			$target_url = $para->getAttribute('href'); // $target_url = "http://www.articlesbase.com" . $para->getAttribute('href');		
			
			// make the cURL request to $search_url
			if ( function_exists('curl_init') ) {
				$ch = curl_init();
				curl_setopt($ch, CURLOPT_USERAGENT, 'Firefox (WindowsXP) - Mozilla/5.0 (Windows; U; Windows NT 5.1; en-GB; rv:1.8.1.6) Gecko/20070725 Firefox/2.0.0.6');
				curl_setopt($ch, CURLOPT_URL,$target_url);
				curl_setopt($ch, CURLOPT_FAILONERROR, true);
				curl_setopt($ch, CURLOPT_AUTOREFERER, true);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
				curl_setopt($ch, CURLOPT_TIMEOUT, 45);
				$html = curl_exec($ch);
				if (!$html) {
					$return["error"]["module"] = "Article";
					$return["error"]["reason"] = "cURL Error";
					$return["error"]["message"] = __("cURL Error Number ","wprobot").curl_errno($ch).": ".curl_error($ch);	
					return $return;
				}		
				curl_close($ch);
			} else { 				
				$html = @file_get_contents($target_url);
				if (!$html) {
					$return["error"]["module"] = "Article";
					$return["error"]["reason"] = "cURL Error";
					$return["error"]["message"] = __("cURL is not installed on this server!","wprobot");	
					return $return;		
				}
			}

			// parse the html into a DOMDocument  

			$dom = new DOMDocument();
			@$dom->loadHTML($html);
				
			// Grab Article Title 			
			$xpath1 = new DOMXPath($dom);
			$paras1 = $xpath1->query("//div/h1");
			$para1 = $paras1->item(0);
			$title = $para1->textContent;	

			// Grab Article	Syndicate Box
			//$xpath2 = new DOMXPath($dom);			
			//$paras2 = $xpath2->query("//textarea[@id='texttocopy']"); 
			//$para2 = $paras2->item(0);	
			//echo strip_tags($dom->saveXml($para2),'<p><strong><b><a><br>');			
			
			// Grab Article	
			$xpath2 = new DOMXPath($dom);
			//$paras2 = $xpath2->query("//div[@class='KonaBody']"); 
			$paras2 = $xpath2->query("//div[@class='article_cnt']/div[@class='KonaBody']"); 
			$para2 = $paras2->item(0);		
			$string = $dom->saveXml($para2);	
		
			$string = strip_tags($string,'<p><strong><b><a><br>');
			$string = str_replace('<div class="KonaBody">', "", $string);	
			$string = str_replace("</div>", "", $string);
			$string = str_replace("&nbsp;", "", $string);	
			if ($options['wpr_eza_striplinks']=='yes') {$string = wpr_strip_selected_tags($string, array('a','iframe','script'));}	
			//if($lang == "es") {$string = utf8_decode  (  $string  );	}		
			$articlebody .= $string . ' ';			

			// Grab Ressource Box	

			$xpath3 = new DOMXPath($dom);
			$paras3 = $xpath3->query("//div[@class='author_details']/p");		//$para = $paras->item(0);		
			
			$ressourcetext = "";
			for ($y = 0;  $y < $paras3->length; $y++ ) {  //$paras->length
				$para3 = $paras3->item($y);
				$ressourcetext .= $dom->saveXml($para3);	
			}	
			
			$ressourcetext .= '<br/>Article from <a href="'.$target_url.'">articlesbase.com</a>';
			
			$title = utf8_decode($title);
			
			// Split into Pages
			if($options['wpr_eza_split'] == "yes") {
				$articlebody = wordwrap($articlebody, $options['wpr_eza_splitlength'], "<!--nextpage-->");
			}
			
			$post = $template;
			$post = wpr_random_tags($post);
			$post = str_replace("{article}", $articlebody, $post);			
			$post = str_replace("{authortext}", $ressourcetext, $post);	
			$noqkeyword = str_replace('"', '', $keyword2);
			$post = str_replace("{keyword}", $noqkeyword, $post);
			$post = str_replace("{Keyword}", ucwords($noqkeyword), $post);				
			$post = str_replace("{title}", $title, $post);	
			$post = str_replace("{url}", $target_url, $post);
			
					if(function_exists("wpr_translate_partial")) {
						$post = wpr_translate_partial($post);
					}	
					
			$posts[$x]["unique"] = $target_url;
			$posts[$x]["title"] = $title;
			$posts[$x]["content"] = $post;				
			$x++;
		}	
	}	
	return $posts;
}

function wpr_article_options_default() {
	$options = array(
		"wpr_eza_lang" => "en",
		"wpr_eza_split" => "no",
		"wpr_eza_striplinks" => "no",
		"wpr_eza_splitlength" => "10000"
	);
	return $options;
}

function wpr_article_options($options) {
	?>
	<h3 style="text-transform:uppercase;border-bottom: 1px solid #ccc;"><?php _e("Article Options","wprobot") ?></h3>
		<table class="addt" width="100%" cellspacing="2" cellpadding="5" class="editform"> 	
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Article Language:","wprobot") ?></td> 
				<td>
				<select name="wpr_eza_lang" id="wpr_eza_lang">
					<option value="en" <?php if($options['wpr_eza_lang']=="en"){_e('selected');}?>><?php _e("English","wprobot") ?></option>
					<option value="fr" <?php if($options['wpr_eza_lang']=="fr"){_e('selected');}?>><?php _e("French","wprobot") ?></option>
					<option value="es" <?php if($options['wpr_eza_lang']=="es"){_e('selected');}?>><?php _e("Spanish","wprobot") ?></option>
					<option value="pg" <?php if($options['wpr_eza_lang']=="pg"){_e('selected');}?>><?php _e("Portuguese","wprobot") ?></option>
					<option value="ru" <?php if($options['wpr_eza_lang']=="ru"){_e('selected');}?>><?php _e("Russian","wprobot") ?></option>
				</select>
			</td> 
			</tr>		
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Pages:","wprobot") ?></td> 
				<td>
					<input name="wpr_eza_split" type="checkbox" value="yes" <?php if ($options['wpr_eza_split']=='yes') {echo "checked";} ?>/> <?php _e("Split long articles into several pages after every","wprobot") ?> <input size="5" name="wpr_eza_splitlength" type="text" value="<?php echo $options['wpr_eza_splitlength'];?>"/> <?php _e("characters.","wprobot") ?>
				</td> 
			</tr>		
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Strip All Links from...","wprobot") ?></td> 
				<td><input name="wpr_eza_striplinks" type="checkbox" id="wpr_eza_striplinks" value="yes" <?php if ($options['wpr_eza_striplinks']=='yes') {echo "checked";} ?>/> <?php _e("Article Body","wprobot") ?>
				<!--Tooltip--><a target="_blank" class="tooltip" href="#">?<span><?php _e('<strong>Warning:</strong> Removing links from the articles content is against the articlesbase.com and authors terms of use. Use this setting at your own risk!',"wprobot") ?></span></a>				
				</td> 
			</tr>				
		</table>		
	<?php
}
?>