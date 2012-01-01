<?php

//we renamed all functions for better namespacing - add this for backwards compat if it won't conflict
if (!function_exists('mc_display_widget')){
    function mc_display_widget($args=array()){
        mailchimpSF_display_widget($args);
    }
}

function mailchimpSF_display_widget($args=array()){
    extract($args);

    $mv = get_option('mc_merge_vars');
    $ig = get_option('mc_interest_groups');
    if (!is_array($mv)){
        //apparently in WP 2.6 get_option() unserializes arrays for us.
        $mv = unserialize($mv);
    }
    if (!is_array($ig)){
        $ig = unserialize($ig);
    }
    if (!is_array($mv)){
        echo $before_widget;
        echo '<div class="mc_error_msg">There was a problem loading your MailChimp details. Please re-run the setup process under Settings->MailChimp Setup</div>';
        echo "$after_widget\n";
        return;
    }
	$msg = '';
	if (isset($_REQUEST['mc_signup_submit'])){
		$failed = false;
    	$listId = get_option('mc_list_id');
		$email = $_REQUEST['mc_mv_EMAIL'];
		$merge = array();
		$errs = array();
		foreach($mv as $var){
			$opt = 'mc_mv_'.$var['tag'];
			if ($var['req']=='Y' && trim($_REQUEST[$opt])==''){
				$failed = true;
				$errs[] = __("You must fill in", 'mailchimp_i18n').' '.htmlentities($var['name'],ENT_COMPAT,'UTF-8').'.';
			} else {
				if ($var['tag']!='EMAIL'){
					$merge[$var['tag']] = $_REQUEST[$opt];
				}
			}	
		}
		reset($mv);
		if (get_option('mc_show_interest_groups')){
			if ($ig['form_field']=='select' || $ig['form_field']=='radio'){
				$merge['INTERESTS'] = str_replace(',','\,',$_REQUEST['interests']);
			} elseif($ig['form_field']=='checkbox') {
				if (isset($_REQUEST['interests'])){
				    foreach($_REQUEST['interests'] as $i=>$nothing){
				        $merge['INTERESTS'] .= str_replace(',','\,',$i).',';
				    }
				}
			}
		}
		if (!$failed){
		    foreach($merge as $k=>$v){
		        if (trim($v)===''){
		            unset($merge[$k]);
		        }
		    }
			if (sizeof($merge) == 0 || $merge==''){ $merge = ''; }
			
            $GLOBALS["mc_api_key"] = get_option('mc_apikey');
	        $api = new MCAPI('no_login','is_needed');
			$retval = $api->listSubscribe( $listId, $email, $merge);
			if (!$retval){
			    switch($api->errorCode){
			        case '214' : $errs[] = __("That email address is already subscribed to the list", 'mailchimp_i18n').'.'; break;
			        case '250' : 
			            list($field, $rest) = explode(' ',$api->errorMessage,2);
    			        $errs[] = __("You must fill in", 'mailchimp_i18n').' '.htmlentities($field,ENT_COMPAT,'UTF-8').'.';
    			        break;
			        case '254' : 
			            list($i1, $i2, $i3, $field, $rest) = explode(' ',$api->errorMessage,5);
    			        $errs[] = sprintf(__("%s has invalid content", 'mailchimp_i18n'),htmlentities($field,ENT_COMPAT,'UTF-8')).'.';
    			        break;
			        case '270' : $errs[] = __("An invalid Interest Group was selected", 'mailchimp_i18n').'.'; break;
			        case '502' : $errs[] = __("That email address is invalid", 'mailchimp_i18n').'.'; break;
			        default:
			            $errs[] = $api->errorCode.":".$api->errorMessage; break;
			    }
				$failed = true;
			} else {
				$msg = "<strong class='mc_success_msg'>".__("Success, you've been signed up! Please look for our confirmation email!", 'mailchimp_i18n')."</strong>";
			}
		}
		if (sizeof($errs)>0){
			$msg = '<span class="mc_error_msg">';
			foreach($errs as $error){
				$msg .= "» ".htmlentities($error, ENT_COMPAT, 'UTF-8').'<br/>';
			}
			$msg .= '</span>';
		}
	}
	if ($_REQUEST['mc_submit_type']=='js'){
	    if (!headers_sent()){ //just in case...
            header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT', true, 200);
        }
	    echo $msg;
	    return;
	}
	$uid = get_option('mc_user_id');
	$list_name = get_option('mc_list_name');
	echo $before_widget;
	?>
	<a name="mc_signup_form"></a>
	<?php
	$header =  get_option('mc_header_content');
	if (strlen($header)==strlen(strip_tags($header))){
	    echo $before_title?$before_title:'<div class="mc_custom_border_hdr">';
	    echo get_option('mc_header_content');
    	echo $after_title?$after_title:'</div>';
	} else {
	    echo get_option('mc_header_content');
	}
	?>
    <div id="mc_signup_container">
	<form method="post" action="<?php echo $_SERVER['REQUEST_URI']; ?>#mc_signup_form" id="mc_signup_form">
	<div>
    <input type="hidden" id="mc_submit_type" name="mc_submit_type" value="html"/>
	<?php
    echo '<div class="updated" id="mc_message">'.$msg.'</div>';
    //don't show the "required" stuff if there's only 1 field to display.
    $num_fields = 0;
	foreach((array)$mv as $var){
	    $opt = 'mc_mv_'.$var['tag'];
		if ($var['req'] || get_option($opt)=='on'){
		    $num_fields++;
		}
	}
	if (is_array($mv)){
    	reset($mv);
    }
	foreach($mv as $var){
		$opt = 'mc_mv_'.$var['tag'];
		if ($var['req'] || get_option($opt)=='on'){
		    echo '<div class="mc_merge_var">';
			echo '<label for="'.$opt.'" class="mc_var_label">'.$var['name'];
			if ($var['req'] && $num_fields>1){ echo ' <span class="mc_required">*</span>'; }
			echo '</label><br/>';
			echo '<input type="text" size="18" value="" name="'.$opt.'" id="'.$opt.'" class="mc_input"/>';
		    echo '</div>';
		}
	}
	if ($num_fields>1){
    	echo '<div id="mc-indicates-required">* = '.__('required field', 'mailchimp_i18n').'</div>';
    }
	if (get_option('mc_show_interest_groups')=='on' && $ig){
		echo '<div id="mc_interests_header">'.htmlentities($ig['name'], ENT_COMPAT, 'UTF-8').'</div>';
		$i=0;
	    echo '<div class="mc_interest">';
		if ($ig['form_field']=='checkbox' || $ig['form_field']=='checkboxes'){
			foreach($ig['groups'] as $interest){
				echo '<input type="checkbox" name="interests['.$interest.']" id="mc_interest_'.$i.'" class="mc_interest"/>';
				echo '<label for="mc_interest_'.$i.'" class="mc_interest_label"> '.htmlentities($interest, ENT_COMPAT, 'UTF-8').'</label><br/>';
				$i++;
			}
		} elseif ($ig['form_field']=='radio'){
			foreach($ig['groups'] as $interest){
				echo '<input type="radio" name="interests" id="mc_interest_'.$i.'" class="mc_interest" value="'.$interest.'"/>';
				echo '<label for="mc_interest_'.$i.'" class="mc_interest_label"> '.htmlentities($interest, ENT_COMPAT, 'UTF-8').'</label><br/>';
				$i++;
			}
		} elseif ($ig['form_field']=='select' || $ig['form_field']=='dropdown'){
			echo '<select name="interests">';
			foreach($ig['groups'] as $interest){
				echo '<option value="'.$interest.'">'.htmlentities($interest, ENT_COMPAT, 'UTF-8').'</option>';
			}
			echo '</select>';
		}
		echo '</div>';
	}
	?>

	<div class="mc_signup_submit">
	<input type="submit" name="mc_signup_submit" id="mc_signup_submit" value="<?php echo htmlentities(get_option('mc_submit_text'), ENT_COMPAT, 'UTF-8'); ?>" class="button"/>
	</div>
	<?php
	if ( get_option('mc_use_unsub_link')=='on') {
		echo '<div id="mc_unsub_link" align="center"><a href="http://list-manage.com/unsubscribe/?u='.get_option('mc_user_id').'&amp;id='.get_option('mc_list_id').'" target="_blank">
		'.__('unsubscribe from list', 'mailchimp_i18n').'</a></div>';
	}
	if ( get_option('mc_rewards')=='on') {
		echo '<br/><div id="mc_display_rewards" align="center">'.__('powered by', 'mailchimp_i18n').' <a href="http://www.mailchimp.com/affiliates/?aid='.get_option('mc_user_id').'&amp;afl=1">MailChimp</a>!</div>';
	}
	?>
	</div>
	</form>
	</div>
	<?php
    echo $after_widget;
}

?>
