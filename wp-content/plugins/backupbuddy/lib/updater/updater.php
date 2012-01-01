<?php
/*
 * PluginBuddy.com & iThemes.com
 * Author: Dustin Bolton < http://dustinbolton.com >
 * Created: 2-20-2010
 * Updated: 5-28-2010
 * Iteration: 33
 * 
 * Upgrade system for PluginBuddy and iThemes products.
 *
 */


// TODO: Implement version number into updater so it can be checked for compatability with backend.



if (!class_exists("iThemesUpdater")) {
	class iThemesUpdater {
		var $_updater_url = 'http://updater.ithemes.com/';
		var $_update_wait = '+10 minute';
		var $_version;
		var $_guid;
		var $_defaults = array(
			'key'			=>		'',
			'last_check'	=>		0,		// Timestamp of last server ping.
		);
		var $_checked = false;
		
		function iThemesUpdater(&$parent) {
			$this->_parent = &$parent;
			
			if ( empty( $this->_parent->_options ) ) {
				$this->_parent->load();
			}
			
			if (! array_key_exists('updater', $this->_parent->_options) ) {
				$this->_parent->_options['updater'] = $this->_defaults;
				$this->_parent->save();
			}
			
			// Generate GUID if needed.
			$this->_guid = get_option($this->_parent->_var.'-updater-guid');
			if ( $this->_guid == '' ) {
				$this->_guid = uniqid(''); // Empty string needed for PHP 4 compatability.
				add_option($this->_parent->_var.'-updater-guid', $this->_guid, '', false); // Create if needed.
				update_option($this->_parent->_var.'-updater-guid', $this->_guid); // Update.
			}
			
			add_action('wp_ajax_ithemes_updater', array(&$this, 'ajax') );
			
			//Plugin update actions
			add_action('update_option_update_plugins', array(&$this, 'server_ping')); // For WP 2.7.
			add_action('update_option__transient_update_plugins', array(&$this, 'server_ping')); // For WP 2.8.
			add_filter('pre_set_site_transient_update_plugins', array(&$this, 'server_ping')); //for WP 3.0 - Will they ever stop changing these?
			if( "plugins.php" == basename($_SERVER['PHP_SELF']) ) {
				add_action("admin_init", array(&$this, 'server_ping'));
				add_action('after_plugin_row_'.strtolower($this->_parent->_name).'/'.strtolower($this->_parent->_name).'.php', array(&$this, 'plugin_row') );
				add_action('plugin_action_links_'.strtolower($this->_parent->_name).'/'.strtolower($this->_parent->_name).'.php', array(&$this, 'plugin_links') );
			}
			add_action('install_plugins_pre_plugin-information', array(&$this, 'view_changelog'));
		}
		
		function ajax() {
			require_once( dirname( __FILE__ ) . '/get.php' );
			die();
		}
		
		function view_changelog() {
			if( $_GET["plugin"] != strtolower( $this->_parent->_name ) ) {
				return;
			}
			$data = $this->updater_post('action=changelog');
			echo $data['message'];
			die();
		}
		function server_ping() {
			//only check updates on the admin side
			if( !is_admin() ) {
				return;
			}
			
			if ( $this->_checked != true ) {
				$this->_checked = true;
				

				if ( !isset( $this->_check_status ) ) {
					$this->_check_status = $this->updater_post('action=check');
				}

				$plugin_name = strtolower($this->_parent->_name)."/".strtolower($this->_parent->_name).".php";
				$option = function_exists('get_transient') ? get_transient("update_plugins") : get_option("update_plugins");

				// If not already created.
				if(empty($option->response[$plugin_name])) {
					$option->response[$plugin_name] = new stdClass();
				}
				$this->_check_status['key_status'] = 'ok';
				// If key is invalid OR no update available...
				if ( isset( $this->_check_status['key_status'] ) ) {
					if( ($this->_check_status['key_status'] != 'ok') || ($this->_check_status['new_version'] == false) ){
						unset($option->response[$plugin_name]);
					} else {
						$option->response[$plugin_name]->url = $this->_updater_url;
						$option->response[$plugin_name]->slug = strtolower($this->_parent->_name);
						$option->response[$plugin_name]->package = $this->_check_status['download_url'];
						$option->response[$plugin_name]->new_version = $this->_check_status['latest_version'];
						$option->response[$plugin_name]->id = "0";
					}

					//Setting transient data (WP 2.8)
					if ( function_exists('set_transient') ){
						set_transient("update_plugins", $option);
					}
					//Setting option (WP 2.7)
					update_option("update_plugins", $option);
				}
			}
		}

		
		function plugin_links($val) {
			$this->_parent->load();
			if (array_key_exists('updater', $this->_parent->_options)) {
				if ( isset( $this->_parent->_options['updater']['key'] ) ) {
					$key = $this->_parent->_options['updater']['key'];
				} else {
					$key = '';
				}
			}
			
			$val[sizeof($val)] = '<a href="'.admin_url('admin-ajax.php').'?action=ithemes_updater&url='.urlencode('http://updater.ithemes.com/?action=licenses&product='.strtolower($this->_parent->_name).'&siteurl='.urlencode(get_option('siteurl')).'&key='.$key.'&guid='.$this->_guid.'&geturl='.admin_url('admin-ajax.php')).'&TB_iframe=true" class="thickbox" title="Manage Licenses"><img src="'.$this->_parent->_pluginURL.'/lib/updater/key.png" style="vertical-align: -3px;" /> Licenses</a>';
			
			//$val[sizeof($val)] = '<a href="'.$this->_parent->_pluginURL.'/lib/updater/get.php?url='.urlencode('http://updater.ithemes.com/?action=licenses&product='.strtolower($this->_parent->_name).'&siteurl='.urlencode(get_option('siteurl')).'&key='.$key.'&guid='.$this->_guid.'&geturl='.$this->_parent->_pluginURL.'/lib/updater/get.php').'&TB_iframe=true" class="thickbox" title="Manage Licenses"><img src="'.$this->_parent->_pluginURL.'/lib/updater/key.png" style="vertical-align: -3px;" /> Licenses</a>';
			return $val;
		}

		
		function plugin_row($plugin_name){
			if (strtolower($this->_parent->_name).'/'.strtolower($this->_parent->_name).'.php' != $plugin_name ) {
				return;
			}
		
			if ( !isset( $this->_check_status ) ) {
				$this->_check_status = $this->updater_post('action=check');
			}

			if ($this->_check_status['status']!='ok') {
				$this->output('ERROR checking update status: '.$this->_check_status['message']);
			} else {
				$print_text = "";
				$key_text = "";

				$this->_parent->load();
				if ( !isset( $this->_parent->_options ) ) {
					$this->_parent->load();
				}
				
				if (array_key_exists('updater', $this->_parent->_options)) {
					$key = $this->_parent->_options['updater']['key'];
				}
				//echo $key;
				$key_text='<span style="border-right: 1px solid #DFDFDF; margin-right: 5px;"><a href="'.admin_url('admin-ajax.php').'?action=ithemes_updater&url='.urlencode('http://updater.ithemes.com/?action=licenses&product='.strtolower($this->_parent->_name).'&siteurl='.urlencode(get_option('siteurl')).'&key='.$key.'&guid='.$this->_guid.'&geturl='.admin_url('admin-ajax.php')).'&TB_iframe=true" class="thickbox" title="Manage Licenses"><img src="'.$this->_parent->_pluginURL.'/lib/updater/key.png" style="vertical-align: -3px;" /> Manage Licenses</a> </span>';
				$this->_check_status['key_status'] = 'ok';
				if ($this->_check_status['key_status']!='ok') {
					if ( $this->_check_status['new_version'] == 'true' ) {
						$print_text .= 'There is a new version of this plugin available, '.$this->_check_status['latest_version'].'. ';
					} else {
						$print_text .= 'Plugin up to date. ';
					}
					$print_text .= 'No key set or invalid. Manage your license for automatic upgrades. ';
				}
				if (isset($this->_check_status['message'])) {
					$print_text .= $this->_check_status['message'];
				}
				if ( $print_text != '' ) {
					$this->output($key_text . $print_text);
				}
			}
		}
		
		function output($content) {
			echo '</tr>';
			
			wp_enqueue_script( 'thickbox' );
			wp_print_scripts( 'thickbox' );
			wp_print_styles( 'thickbox' );
			
			echo '<tr class="plugin-update-tr"><td colspan="5" class="plugin-update"><div class="update-message">'.$content.'</div></td>';
		}
		
		function updater_post($data) {
			if ( array_key_exists('updater', $this->_parent->_options) ) {
				if ( isset( $this->_parent->_options['updater']['key'] ) ) {
					$key = $this->_parent->_options['updater']['key'];
				}
			}
			
			// If recheck time has not passed, use cached response.
			if ( strtotime( $this->_update_wait, $this->_parent->_options['updater']['last_check']) > mktime() ) {
				// Need to update cached server response if current version appears equal or newer than cache!
				if ( version_compare($this->_parent->_options['updater']['last_check']['latest_version'], $this->_parent->_version) == 1) {
					return $this->_parent->_options['updater']['last_response'];
				}
			}
			
			if ( function_exists('curl_init') ) {
				$ch = curl_init($this->_updater_url.'?product='.strtolower($this->_parent->_name).'&version='.$this->_parent->_version.'&siteurl='.urlencode(get_option('siteurl')).'&key='.$key.'&guid='.$this->_guid.'&'.$data);
				curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
				$response = curl_exec($ch);
				if(curl_errno($ch)) {
					curl_close($ch);
					$response = array('status' => 'unable_to_connect');
				} else {
					curl_close($ch);
					if ($response != '') {
						$response = unserialize($response);
					} else {
						$response = array('status' => 'fail', 'message' => 'Invalid server response.');
					}
				}
			} else {
				$response = array('status' => 'fail', 'message' => 'This server does not support curl_init. Enable it in server PHP settings. Automatic upgrades unavailable.');
			}
			
			$this->_parent->_options['updater']['last_response'] = $response;
			$this->_parent->_options['updater']['last_check'] = mktime();
			$this->_parent->save();
			
			return $response;
		}
	}
}



$this->_updater = new iThemesUpdater($this);
add_action('after_plugin_row_backupbuddy/backupbuddy.php', array(&$this->_updater, 'plugin_row') );
?>