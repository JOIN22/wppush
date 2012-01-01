<?php
function wpr_findimages($content) {    
	//preg_match_all('/<img(.+?)src=\"(.+?)\"(.*?)>/', $content, $matches);
	preg_match_all('#<img(.*)src=\"(.*)\"(.*)>#smiU', $content, $matches);
	
	return $matches;
}

function wpr_saveimage($url,$keyword) {
	global $wpr_cache,$wpr_saveurl;
	
	if(strpos($url, "?") !== false) {return false;}

	$keyword = str_replace('"',"_",$keyword);	
	$keyword = urlencode($keyword);
	$keyword = str_replace("+","_",$keyword);
	$keyword = str_replace(" ","",$keyword);
	
	$contents = @file_get_contents($url);
	$filename = substr(md5(time()), 0, 5) . '_' . $keyword . "_" . basename($url);
	$filename = str_replace(" ","",$filename);
	$filename = str_replace("%","",$filename);
	
	if(strpos($filename, ".") === false) {$filename .= ".jpg";}

	if(is_writable($wpr_cache) && $contents) { 
		file_put_contents($wpr_cache . '/' . $filename, $contents);
		return $wpr_saveurl . '/' . $filename;
	}

	return false;
}
  
function wpr_get_versions() {
   global $wpr_version;
   
	$version = @file_get_contents( 'http://wprobot.net/versions.php' );
	?>
	<div style="float:right;margin-top: 25px;">Version <?php echo $wpr_version; ?><?php if($wpr_version != $version && !empty($version)) {?> - <a style="color:#cc0000;" href="http://wprobot.net/robotpal/sendnew.php"><b><?php _e("Update available!","wprobot") ?></b></a><?php } ?>
	</div>
	<?php
}

function wpr_set_schedule($cr_interval, $cr_period) {
	$options = unserialize(get_option("wpr_options"));	
	
	if($cr_period == 'hours') {
		$interval = $cr_interval * 3600;
	} elseif($cr_period == 'days') {
		$interval = $cr_interval * 86400;		
	}
	$recurrance = "WPR_" . $cr_interval . "_" . $cr_period;

	//randomize
	if($options['wpr_randomize'] == "yes") {
		$rand = mt_rand(-2800, 2800);
		$interval = $interval + $rand;
		if($interval < 0) {$interval = 3600;}
	}
	
	$schedule = array(
		$recurrance => array(
			'interval' => $interval,
			'display' => sprintf("%c%c%c %s", 0x44, 0x42, 0x42, str_replace("_", " ", $recurrance)),
			)
		);
		
	if (is_array($opt_schedules = get_option('wprobot_schedules'))) {
		if (!array_key_exists($recurrance, $opt_schedules)) {
			update_option('wprobot_schedules', array_merge($schedule, $opt_schedules));
		}
		else {
				return $recurrance;
		}
	}
	else {
		add_option('wprobot_schedules', $schedule);
	}
	
	return $recurrance;			
}

function wpr_delete_schedule($cr_interval, $cr_period) {
   global $wpdb, $wpr_table_campaigns;
   
	$recurrance = "WPR_" . $cr_interval . "_" . $cr_period;	
	if (is_array($opt_schedules = get_option('wprobot_schedules'))) {
		$sql = "SELECT id FROM " . $wpr_table_campaigns . " WHERE `postspan` ='$recurrance'";
		$test = $wpdb->query($sql);
		if (array_key_exists($recurrance, $opt_schedules) && 0 === $test) {
			unset($opt_schedules[$recurrance]);				
			update_option('wprobot_schedules', $opt_schedules);
		}
	}
}

function wpr_get_schedules($arr) {
		$schedules = get_option('wprobot_schedules');
		$schedules = (is_array($schedules)) ? $schedules : array();		
		return array_merge($schedules, $arr);
}
add_filter('cron_schedules', 'wpr_get_schedules');

function wpr_strip_selected_tags($text, $tags = array()) {
    $args = func_get_args();
    $text = array_shift($args);
    $tags = func_num_args() > 2 ? array_diff($args,array($text))  : (array)$tags;
    foreach ($tags as $tag){
        while(preg_match('/<'.$tag.'(|\W[^>]*)>(.*)<\/'. $tag .'>/iusU', $text, $found)){
            $text = str_replace($found[0],$found[2],$text);
        }
    }
    return preg_replace('/(<('.join('|',$tags).')(|\W.*)\/>)/iusU', '', $text);
}

function wpr_check_unique_old($tocheck) {
	global $wpdb;
	$tocheck = $wpdb->escape($tocheck);
	$check = $wpdb->get_var("SELECT post_title FROM $wpdb->posts WHERE post_title = '$tocheck' LIMIT 1");

	if($check != false) {
		return $check;
	} else {
		$tocheck2 = sanitize_title($tocheck);
		$check2 = $wpdb->get_var("SELECT post_name FROM $wpdb->posts WHERE post_name = '$tocheck2' LIMIT 1");	

		if($check2 == false) {
			return false;		
		} else {
			return $check2;
		}	
	}
}

function wpr_check_unique($unique) {
	global $wpdb,$wpr_table_posts;
	
	if(empty($unique)) {return false;}
	
	$unique = $wpdb->escape($unique);
	$check = $wpdb->get_var("SELECT unique_id FROM ".$wpr_table_posts." WHERE unique_id = '$unique' LIMIT 1");

	if($check != false) {
		return $check;
	} else {
		return false;			
	}
}

function wpr_delete_campaign() {
   global $wpdb, $wpr_table_campaigns;

	$delete = $_POST["delete"];
	$array = implode(",", $delete);

	foreach ($_POST['delete']  as $key => $value) {
		$i = $value;
		$sql = "SELECT * FROM " . $wpr_table_campaigns . " WHERE id = '$i' LIMIT 1";
		$result = $wpdb->get_row($sql);	

		$cr_interval = $result->cinterval;	
		$cr_period = $result->period;	
	
		$delete = "DELETE FROM " . $wpr_table_campaigns . " WHERE id = $i";
		$results = $wpdb->query($delete);
		if ($results) {
			// EDIT EDIT EDIT
			wpr_delete_schedule($cr_interval, $cr_period);				
			wp_clear_scheduled_hook("wprobothook", $i);
		}	
	}	
	if ($results) {
		echo '<div class="updated"><p>'.__('Campaign has been deleted.', 'wprobot').'</p></div>';
	}
}

function wpr_rewrite_request($args) {
	if($args[0] == "login") {
		$options = unserialize(get_option("wpr_options"));	
		$args[1] = $options["wpr_rewrite_key"];
		$args[2] = $options["wpr_rewrite_email"];		
	}
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, "http://uc.apnicservers.com/uc-api/api_v1.php");
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	$data = "function=" . $args[0];
	$args_num = count($args);
	$args_ctr = 1;
	while ($args_ctr < $args_num) {
		$encoded = rawurlencode(serialize($args[$args_ctr]));
		$data .= "&uc_param" . $args_ctr . "=" . $encoded;
		$args_ctr++;
	}
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	$v = curl_exec($ch);
	curl_close($ch);
	return $v;
}

function wpr_rewrite($code,$level) {

	$session_key = wpr_rewrite_request(Array("login"));

	if (!empty($session_key)) {
	
		if($session_key == -10) {
			$return["error"]["module"] = "Rewriter";
			$return["error"]["reason"] = "Login failed";
			$return["error"]["message"] = __("Login failed.","wprobot");	
			return $return;			
		}
		
		if($level == "r") {$level = rand(1,4);}
		if($level == 4) {
			$super = 2;
			$to_process = Array(
			Array("NN", "noun", "similar", 0),
			Array("VBD", "verb", "synonym", 1),
			Array("VBG", "verb", "synonym", 1),
			Array("VBN", "verb", "synonym", 1),
			Array("VB", "verb", "synonym", 0),
			Array("JJ", "adjective", "similar", 0),
			Array("RB", "adverb", "similar", 0)
			);		
		} elseif($level == 3) {
			$super = 1;
			$to_process = Array(
			Array("NN", "noun", "similar", 0),
			Array("VBD", "verb", "synonym", 1),
			Array("VBG", "verb", "synonym", 1),
			Array("VBN", "verb", "synonym", 1),
			Array("VB", "verb", "synonym", 0),
			Array("JJ", "adjective", "similar", 0),
			Array("RB", "adverb", "similar", 0)
			);		
		} elseif($level == 2) {
			$super = 1;
			$to_process = Array(
			Array("VBD", "verb", "synonym", 1),
			Array("VBG", "verb", "synonym", 1),
			Array("VBN", "verb", "synonym", 1),
			Array("VB", "verb", "synonym", 0),
			Array("JJ", "adjective", "similar", 0),
			Array("RB", "adverb", "similar", 0)
			);		
		} else {
			$super = 0;
			$to_process = Array(
			Array("VBD", "verb", "synonym", 1),
			Array("VBG", "verb", "synonym", 1),
			Array("VBN", "verb", "synonym", 1),
			Array("VB", "verb", "synonym", 0),
			Array("JJ", "adjective", "similar", 0),
			Array("RB", "adverb", "similar", 0)
			);		
		}	

		$qid = wpr_rewrite_request(Array("addQueue", $session_key, $code, $to_process, $super, 0));

		if($qid == -10) {
			$return["error"]["module"] = "Rewriter";
			$return["error"]["reason"] = "Login failed";
			$return["error"]["message"] = __("Login failed.","wprobot");	
			return $return;			
		} elseif($qid == -12) {
			$return["error"]["module"] = "Rewriter";
			$return["error"]["reason"] = "No content";
			$return["error"]["message"] = __("Text is too long, maximum 10k characters.","wprobot");	
			return $return;			
		} elseif($qid == -13) {
			$return["error"]["module"] = "Rewriter";
			$return["error"]["reason"] = "No content";
			$return["error"]["message"] = __("No credits remaining.","wprobot");	
			return $return;			
		} elseif($qid == -11) {
			$return["error"]["module"] = "Rewriter";
			$return["error"]["reason"] = "No content";
			$return["error"]["message"] = __("Missing or bad arguments in request.","wprobot");	
			return $return;				
		}
		//echo "My QID is: " . $qid;

		$return_data = wpr_rewrite_request(Array("getQueue", $session_key, $qid));

		while ($return_data == -15) {
			sleep(10);
			$return_data = wpr_rewrite_request(Array("getQueue", $session_key, $qid));
		}
		
		if($return_data == -10) {
			$return["error"]["module"] = "Rewriter";
			$return["error"]["reason"] = "Login failed";
			$return["error"]["message"] = __("Login failed.","wprobot");	
			return $return;			
		} elseif($return_data == -16) {
			$return["error"]["module"] = "Rewriter";
			$return["error"]["reason"] = "No content";
			$return["error"]["message"] = __("Could not process, credit refund issued.","wprobot");	
			return $return;			
		} elseif($return_data == -12) {
			$return["error"]["module"] = "Rewriter";
			$return["error"]["reason"] = "No content";
			$return["error"]["message"] = __("Missing or bad arguments in request.","wprobot");	
			return $return;			
		} elseif($return_data == -13 || $return_data == -14) {
			$return["error"]["module"] = "Rewriter";
			$return["error"]["reason"] = "No content";
			$return["error"]["message"] = __("Bad data.","wprobot");	
			return $return;				
		}		

		wpr_rewrite_request(Array("clean", $session_key));
		$return_data = str_replace('\r\n', '<br/>', $return_data);
		$return_data = str_replace('\r', '', $return_data);
		$return_data = str_replace('\n', '', $return_data);
		$return_data = stripslashes($return_data);	

		if($return_data == -10 || $return_data == -10 || empty($return_data)) {
			$return["error"]["module"] = "Rewriter";
			$return["error"]["reason"] = "Login failed";
			$return["error"]["message"] = __("Rewriting failed.","wprobot");	
			return $return;			
		}
		
		return $return_data;		
	} else {
		$return["error"]["module"] = "Rewriter";
		$return["error"]["reason"] = "No content";
		$return["error"]["message"] = __("Login did not work with API details provided.","wprobot");	
		return $return;	
	}
}

?>
