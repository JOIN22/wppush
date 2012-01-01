<?php

/*
Plugin Name: Chennai Central
Plugin URI: http://indiafascinates.com/chennai/chennaicentral/
Description:  Chennai Central saves bandwidth and saves your money
Version: 1.2
Author: Rajesh (India Fascinates)
Author URI: http://indiafascinates.com/
*/

/*
Copyright (C) 2009 Rajesh (India ascinates Dot Com) (Rajesh AT IndiaFascinates DOT com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA

*/

class Bandwidth_Saver {

	var $version = "1.2";
	var $bs_options; 
	var $bs_crawlers;
	var $bs_ipAddy;
	var $bs_referrer;
	var $bs_userAgent;
	var $bs_requestUri;
	var $bs_queryString;
	
	function Bandwidth_Saver() {
		global $wp_version;
		$this->wp_version = $wp_version;
		
		// Pre-2.6 compatibility
		if ( ! defined( 'WP_CONTENT_URL' ) )
			define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
		if ( ! defined( 'WP_CONTENT_DIR' ) )
			define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
		if ( ! defined( 'WP_PLUGIN_URL' ) )
			define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
		if ( ! defined( 'WP_PLUGIN_DIR' ) )
			define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );		
		
		$bs_options = get_option('bs_options');
		$this->bs_options = $bs_options;		
		$this->bs_crawlers = explode(',', $bs_options['bs_bots_list']); 
		
		//$timestamp = date('Y-m-d H:i:s');
		$this->bs_ipAddy = $_SERVER["REMOTE_ADDR"];
		$this->bs_referrer = $_SERVER["HTTP_REFERER"];
		$this->bs_userAgent = $_SERVER["HTTP_USER_AGENT"];
		$this->bs_requestUri = $_SERVER["REQUEST_URI"];
		$this->bs_queryString = $_SERVER["QUERY_STRING"];
	}

	/**
	 * getlastcommentmodified() - The date the last comment was modified
	 * 
	 * @return string Last comment modified date
	 */
	function getlastcommentmodified($postid = '', $timezone = 'server') {
		global $wpdb;   	

		switch ( strtolower($timezone)) {
			case 'gmt':
				if (!empty($postid) && $postid >= 0) {

					$lastcommentmodified = $wpdb->get_var("SELECT comment_date_gmt FROM $wpdb->comments WHERE comment_post_ID='$postid' and comment_approved = '1' ORDER BY comment_date_gmt DESC LIMIT 1");                    
					break;
				}			
		}			
		return $lastcommentmodified;
	}

	function checkCrawlerUA () {
		$this->bs_ipAddy = $_SERVER["REMOTE_ADDR"];
		$this->bs_referrer = $_SERVER["HTTP_REFERER"];
		$this->bs_userAgent = $_SERVER["HTTP_USER_AGENT"];
		$this->bs_requestUri = $_SERVER["REQUEST_URI"];
		$this->bs_queryString = $_SERVER["QUERY_STRING"];
        $userAgent = $this->bs_userAgent;
        $this->bs_crawlers = explode(',', $this->bs_options['bs_bots_list']);		
		$crawlers = $this->bs_crawlers;
		//echo "crawler1: $crawler";
		foreach ($crawlers as $crawler) {                        
			if (strstr($userAgent,$crawler) != FALSE) {                           		
                return true;
			}
		}
		return false;
	}
	/**
	 * get_lastpostmodified() 
	 * @return string The date the post was last modified.
	 */
	function getlastpostmodified($postid = '', $timezone = 'server') {

		global $wp_query;
		$bs_posts = $wp_query->posts;

		$post = $wp_query->get_queried_object();
        $post_id = $post->ID;
        $post_status = $post->post_status;
        $post_modified_gmt = $post->post_modified_gmt;
        $post_modified = $post->post_modified;      

        $post_comments = $wp_query->comments;
		
			switch(strtolower($timezone)) {

			case 'gmt':

				if (!empty($post_id) && $post_id >= 0) {                        

					if ($post_status == 'publish') {                       
						$lastpostmodified = $post_modified_gmt;
					}                  	
					break;

				} else {			

					$bs_lastpostmodified = 0;
					foreach ($bs_posts as $bs_post) {
						//use this if you are going to take only the last comment's gmt date into consideration
						if ($bs_post->post_status == 'publish') {   
						
							$curpostmodified = strtotime($bs_post->post_modified_gmt);
						
							if ($curpostmodified > $bs_lastpostmodified ){
								$bs_lastpostmodified = $curpostmodified;
								$lastpostmodified = $bs_post->post_modified_gmt;							
							}

						}						
                }
				break;
				}		

			if (is_single() || is_page()) {			
				$lastpostcomdate = $this->getlastcommentmodified($post_id, $timezone);			

				if ( $lastpostcomdate > $lastpostmodified ) {				
					$lastpostmodified = $lastpostcomdate;
				}
			}			
		}
		return $lastpostmodified;
	}	

    function init() {
      	if (function_exists('load_plugin_textdomain')) {
			load_plugin_textdomain('Bandwidth_Saver', WP_PLUGIN_DIR . '/bandwidth-saver');       	    
		}
    }

    function admin_menu() {
        $file = __FILE__;
        add_submenu_page('options-general.php', __('Chennai Central', 'Bandwidth_Saver'), __('Chennai Central','Bandwidth_Saver'), 10, $file, array($this, 'options_panel'));                
    }

	function send304header($postid = '', $timezone = 'server') {

		$onlyforbots = $this->bs_options['bs_bots'];
		if ($onlyforbots) {
			$isCrawler = $this->checkCrawlerUA();
			if (!$isCrawler) return;
		}
		global $wp_query;
		$post = $wp_query->get_queried_object();
        $post_id = $post->ID;        	

		if (is_single() || is_page()) {

			$p_last_modified = mysql2date('D, d M Y H:i:s', $this->getlastpostmodified($post_id, 'GMT'), 0).' GMT';

		} else if( is_home() || is_archive() || is_category() || is_tag() ) {

			$p_last_modified = mysql2date('D, d M Y H:i:s', $this->getlastpostmodified('', 'GMT'), 0).' GMT';			
		}

		if( is_single() || is_page() || is_home() || is_archive() || is_category() || is_tag()) {

			$p_etag = '"' . md5($p_last_modified) . '"';
			@header("Last-Modified: $p_last_modified");
			@header("ETag: $p_etag");                  

			if (isset($_SERVER['HTTP_IF_NONE_MATCH']))    		
				$client_etag = stripslashes(stripslashes($_SERVER['HTTP_IF_NONE_MATCH']));
			else $client_etag = false;
                  
			$client_last_modified = empty($_SERVER['HTTP_IF_MODIFIED_SINCE']) ? '' : trim($_SERVER['HTTP_IF_MODIFIED_SINCE']);
			// If string is empty, return 0. If not, attempt to parse into a timestamp
			$client_modified_timestamp = $client_last_modified ? strtotime($client_last_modified) : 0;
			// Make a timestamp for our most recent modification...
			$p_modified_timestamp = strtotime($p_last_modified);

			if ( ($client_last_modified && $client_etag) ?	(($client_modified_timestamp >= $p_modified_timestamp) && ($client_etag == $p_etag)) : (($client_modified_timestamp >= $p_modified_timestamp) || ($client_etag == $p_etag)) ) {
				//echo "header 304";				
				$this->setheaderstatus ( 304 );
				exit;
			}
		}
	}

	function setheaderstatus ( $header ) {
		$text = $this->getheaderstatus_desc( $header );

		if ( empty( $text ) )
			return false;

		$protocol = $_SERVER["SERVER_PROTOCOL"];
		if ( 'HTTP/1.1' != $protocol && 'HTTP/1.0' != $protocol )
			$protocol = 'HTTP/1.0';
		$status_header = "$protocol $header $text";
		
		if ( version_compare( phpversion(), '4.3.0', '>=' ) )
			return @header( $status_header, true, $header );
		else
			return @header( $status_header );
	}

	function convert_to_absint ( $maybeint ) {
		return abs( intval( $maybeint ) );
	}

	function getheaderstatus_desc( $code ) {
		global $wp_header_to_desc;

		$code = $this->convert_to_absint( $code );

		if ( !isset( $wp_header_to_desc ) ) {
			$wp_header_to_desc = array(
				100 => 'Continue',
				101 => 'Switching Protocols',

				200 => 'OK',
				201 => 'Created',
				202 => 'Accepted',
				203 => 'Non-Authoritative Information',
				204 => 'No Content',
				205 => 'Reset Content',
				206 => 'Partial Content',

				300 => 'Multiple Choices',
				301 => 'Moved Permanently',
				302 => 'Found',
				303 => 'See Other',
				304 => 'Not Modified',
				305 => 'Use Proxy',
				307 => 'Temporary Redirect',

				400 => 'Bad Request',
				401 => 'Unauthorized',
				403 => 'Forbidden',
				404 => 'Not Found',
				405 => 'Method Not Allowed',
				406 => 'Not Acceptable',
				407 => 'Proxy Authentication Required',
				408 => 'Request Timeout',
				409 => 'Conflict',
				410 => 'Gone',
				411 => 'Length Required',
				412 => 'Precondition Failed',
				413 => 'Request Entity Too Large',
				414 => 'Request-URI Too Long',
				415 => 'Unsupported Media Type',
				416 => 'Requested Range Not Satisfiable',
				417 => 'Expectation Failed',

				500 => 'Internal Server Error',
				501 => 'Not Implemented',
				502 => 'Bad Gateway',
				503 => 'Service Unavailable',
				504 => 'Gateway Timeout',
				505 => 'HTTP Version Not Supported'
			);
		}

		if ( isset( $wp_header_to_desc[$code] ) )
			return $wp_header_to_desc[$code];
		else
			return '';
	}
	
	function options_panel() {

        /*global $wpdb;
		$bs_table = $this->bs_table;        
        if($wpdb->get_var("SHOW TABLES LIKE '$bs_table'") != $bs_table) {

                $bs_table_exists = false;

        }

        if(!$bs_table_exists){

                $request = "CREATE TABLE `$bs_table` (
                                `bs_id` bigint(20) unsigned NOT NULL auto_increment,
                                `bs_date` date NOT NULL default '0000-00-00',
                                `bs_post_id` mediumint(9) unsigned NOT NULL default '0',                                
                                 `bs_userAgent` text NOT NULL,
								 `bs_referrer` text NOT NULL,
								 `bs_ipAddy` text NOT NULL,
								 `bs_requestUri` text NOT NULL,
								 `bs_queryString` text NOT NULL,
                                 PRIMARY KEY  (`bs_id`),
                                 KEY `post_status` (`psp_post_id`,`psp_bad_links`(7)),
                                 KEY `link_status` (`psp_link_status`)
                                ) TYPE=MyISAM
                        ";
                $wpdb->query($request);

        }

        if(!$bs_table_exists):
        ?>

        <div class="wrap">
                <h2><?php _e('Installation','Bandwidth_Saver'); ?></h2>
                <p><?php _e('The <b>Bandwidth Saver</b> plugin is now initialized. The Bot tracker table has been created. If you choose to deactivate the plugin you must drop the table manually.','Bandwidth_Saver'); ?></p>
        </div>
        <?php
        endif;
        $message = null;
        $message_updated = __("Bandwidth saver Options Updated.", 'Bandwidth_Saver');
		*/
		//commented as this may be introduced in future - Rajesh

		$message = null;
        $message_updated = __("Bandwidth saver Options Updated.", 'Bandwidth_Saver');
        // update options
        if ($_POST['action'] && $_POST['action'] == 'bs_update') {
				$nonce = $_POST['bs-options-nonce'];
				if (!wp_verify_nonce($nonce, 'bs-options-nonce')) die ( 'Security Check - If you receive this in error, log out and back in to WordPress');
                $message = $message_updated;       
				$upd_options['bs_bots'] = $_POST['bs_bots'];
				//$upd_options['bs_track_bots'] = $_POST['bs_track_bots'];
				$upd_options['bs_bots_list']= $_POST['bs_bots_list'];
				
                update_option('bs_options', $upd_options);
				$this->bs_options = get_option('bs_options'); 				
        }
        ?>
<?php if ($message) : ?>
<div id="message" class="updated fade"><p><?php echo $message; ?></p></div>
<?php endif; ?>
<div class="wrap">
<h2><?php _e('Bandwidth Saver Plugin Options', 'Bandwidth_Saver'); ?></h2>
<p>
<?php _e("This is version ", 'Bandwidth_Saver') ?><?php _e("$this->version ", 'Bandwidth_Saver') ?>
&nbsp;
| <a target="_blank" title="<?php _e('FAQ', 'Bandwidth Saver') ?>" href="http://indiafascinates.com/chennai/chennaicentral/"><?php _e('FAQ', 'Bandwidth_Saver') ?></a>
| <a target="_blank" title="<?php _e('Bandwidth Saver Plugin Feedback', 'Bandwidth_Saver') ?>" href="http://indiafascinates.com/chennai/chennaicentral/"><?php _e('Feedback', 'Bandwidth_Saver') ?></a>
| <a target="_blank" title="<?php _e('Donations for Bandwidth Saver Plugin', 'Bandwidth_Saver') ?>" href="https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=NZDQ4YSJ8VCA8&lc=IN&item_name=Chennaicentral&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted"><?php _e('Please Donate','Bandwidth_Saver') ?></a>
</p>
<script type="text/javascript">
<!--
    function toggleVisibility(id) {
       var e = document.getElementById(id);
       if(e.style.display == 'block')
          e.style.display = 'none';
       else
          e.style.display = 'block';
    }
//-->
</script>

<h3><?php _e('Click on option titles to get help!', 'Bandwidth_Saver') ?></h3>

<form name="dofollow" action="" method="post">
<table class="form-table">
<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'Bandwidth_Saver')?>" onclick="toggleVisibility('bs_bots_tip');">
<?php _e('Save bandwidth only for hits from bots:', 'Bandwidth_Saver')?>
</td>
<td>
<input type="checkbox" name="bs_bots" <?php if ($this->bs_options['bs_bots']) echo "checked=\"1\""; ?>/>
<div style="max-width:500px; text-align:left; display:none" id="bs_bots_tip">
<?php
_e('Check this if you want to Save bandwidth only for hits from bots.', 'Bandwidth_Saver');
 ?>
</div>
</td>
</tr>
<tr>
<th scope="row" style="text-align:right; vertical-align:top;">
<a style="cursor:pointer;" title="<?php _e('Click for Help!', 'Bandwidth_Saver')?>" onclick="toggleVisibility('bs_bots_list_tip');">
<?php _e('Enter list of bots (comma seperated and enclosed by doublequotes):', 'Bandwidth_Saver')?>
</a>
</td>
<td>
<textarea cols="117" rows="2" name="bs_bots_list"><?php echo stripcslashes($this->bs_options['bs_bots_list']); ?></textarea>
<div style="max-width:500px; text-align:left; display:none" id="bs_bots_list_tip">
<?php
_e('The comma seperated list of bots will be tracked and bandwith will be saved for any of their repeat visits, if they support Conditional Get.', 'Bandwidth_Saver');
 ?>
</div>
</td>
</tr>
</table>
<p class="submit">
<input type="hidden" name="action" value="bs_update" />
<input type="hidden" name="bs-options-nonce" value="<?php echo wp_create_nonce('bs-options-nonce'); ?>" />
<input type="submit" name="Submit" value="<?php _e('Update Options', 'Bandwidth_Saver')?> &raquo;" />
</p>
</form>
</div>
<?php

        } // options_panel
}

$bs_options = array(
		'bs_bots' => 0,
		'bs_bots_list' => 'Googlebot,Mediapartners,Slurp,MSNbot,Ask,Teoma,Bloglines,everyfeed,FeedFetcher,Gregarius'	
	);

add_option( 'bs_options', $bs_options);

$bs = new Bandwidth_Saver();

add_action('init', array($bs, 'init'));
add_action('admin_menu', array($bs, 'admin_menu'));
//add_action('pre_get_posts', 'send304header' );
add_action('wp',array($bs,'send304header'));

?>