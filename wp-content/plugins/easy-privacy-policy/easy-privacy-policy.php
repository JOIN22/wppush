<?php
/*
Plugin Name: Easy Privacy Policy
Plugin URI: http://europeancruiseadvisor.com/easy-privacy-policy
Description: Automatically adds a privacy policy page that includes an Adsense compliancy section. Configure it at <a href="options-general.php?page=easy-privacy-policy.php">Settings &rarr; Easy Privacy</a>.
Version: 1.02
Author: Kevin Sparrow
Author URI: http://www.europeancruiseadvisor.com/
*/

/*
Easy Privacy Policy is covered by the GPLv3 GNU General Public License
for more information visit http://www.gnu.org/licenses/gpl.html

*/

	
	
	if (!class_exists("EasyPrivacy")) {

	  class EasyPrivacy {		

		// -----------------------------------------------------------------------------------------
		
		var $easyPrivacyVersion = '1.02';

		// -----------------------------------------------------------------------------------------

		function EasyPrivacy() {
					
			add_option( 'easy_privacy_sitename', get_bloginfo( 'name' ));												
			add_option( 'easy_privacy_email', get_bloginfo( 'admin_email' ));															
			add_option( 'easy_privacy_pageurl', 'privacy-policy');			
			add_option( 'easy_privacy_acknowlegement', true);			
			add_option( 'easy_privacy_lastupdated', true);			
			add_option( 'easy_privacy_pagetitle', 'Privacy Policy');			
			add_option( 'easy_privacy_pageurl', 'privacy-policy');			
			
			
			add_option( 'easy_privacy_section_one_selected', true);		
			add_option( 'easy_privacy_section_one_title', 'Your Privacy');		
			add_option( 'easy_privacy_section_one', $this->loadFromFile('/meta-data/content-section-1.dat') );		
			
			add_option( 'easy_privacy_section_two_selected', true);		
			add_option( 'easy_privacy_section_two_title', 'Google Adsense and the DoubleClick DART Cookie');		
			add_option( 'easy_privacy_section_two', $this->loadFromFile('/meta-data/content-section-2.dat') );		
			
			add_option( 'easy_privacy_section_three_selected', false);		
			add_option( 'easy_privacy_section_three_title', 'Our Commitment To Childrens Privacy');	
			add_option( 'easy_privacy_section_three', $this->loadFromFile('/meta-data/content-section-3.dat') );		
			
			add_option( 'easy_privacy_section_four_selected', true);		
			add_option( 'easy_privacy_section_four_title', 'Collection of Personal Information');	
			add_option( 'easy_privacy_section_four', $this->loadFromFile('/meta-data/content-section-4.dat') );		
			
			add_option( 'easy_privacy_section_five_selected', true);		
			add_option( 'easy_privacy_section_five_title', 'Links to third party Websites');	
			add_option( 'easy_privacy_section_five', $this->loadFromFile('/meta-data/content-section-5.dat') );
			
			add_option( 'easy_privacy_section_six_selected', true);		
			add_option( 'easy_privacy_section_six_title', 'Changes to this Privacy Statement');	
			add_option( 'easy_privacy_section_six', $this->loadFromFile('/meta-data/content-section-6.dat') );
		}		  
		
		// -----------------------------------------------------------------------------------------
		
		function easyPrivacyOptionsPage(){
	
			if( isset( $_POST[ 'reset' ] ) ){
			
				// reset the settings
				$this->setDefaults();
				
				// update the data		
				$this->updatePrivacyPage();		
?>		
				<div class="updated"><p><strong><?php _e("Options set to default values", "easy privacy");?></strong></p></div>
<?php			
			}
			else if( isset( $_POST[ 'create_page' ] ) ){
			
				// make any changes permanent before creating the page
				// for the first time
				$this->updateOptions();
				
				// make the default page
				$this->createPrivacyPage();
			
			}						
			else if( isset( $_POST[ 'update_page' ] )){
									
				$this->updateOptions();
				
				// update the data		
				$this->updatePrivacyPage();		
			
			}				
			else if (isset($_POST['uninstall'])) {			
				
?>
			<div class="updated"><p><strong><?php _e("Database has been cleaned. All your options for this plugin (for all themes) have been removed.", "easy privacy");?></strong></p> </div>
<?php							
				$this->deletePrivacyPage();
				$this->deleteOptions();
								
				$this->cleanUp('easy_privacy');				
?>
				<div class="updated"><p><strong><?php _e("All options and settings removed.", "easy privacy");?></strong></div>
				<div class="updated"><p><strong><?php _e("The Privacy Page has been deleted.", "easy privacy");?></strong></div>
				<div class="updated"><p><strong><?php _e("This plugin has been deactivated.", "easy privacy");?></strong></div>
				<a href="plugins.php?deactivate=true"></a></strong></p></div>			
<?php
				return;
				
			} 
			
			// Show the settings Page
			$this->displayContent();
		}
		
		// -----------------------------------------------------------------------------------------
				
		function deletePrivacyPage() {
			
			$postId = get_option('easy_privacy_CreatePageId');
			if( !empty($postId)) {
			
				wp_delete_post($postId);			
			}		
		}
		
		// -----------------------------------------------------------------------------------------
		
		function createContent() {
			
			$content = '';
						
			if( get_option('easy_privacy_section_one_selected') == true ) {
			
				$content = $content.'<strong>'.$this->finalConversion('easy_privacy_section_one_title').'</strong><br/>';
				$content = $content.$this->finalConversion('easy_privacy_section_one').'<p/>';
			}
			if( get_option('easy_privacy_section_two_selected') == true ) {
			
				$content = $content.'<strong>'.$this->finalConversion('easy_privacy_section_two_title').'</strong><br/>';
				$content = $content.$this->finalConversion('easy_privacy_section_two').'<p/>';				
			}
			if( get_option('easy_privacy_section_three_selected') == true ) {
			
				$content = $content.'<strong>'.$this->finalConversion('easy_privacy_section_three_title').'</strong><br/>';
				$content = $content.$this->finalConversion('easy_privacy_section_three').'<p/>';				
			}
			if( get_option('easy_privacy_section_four_selected') == true ) {
			
				$content = $content.'<strong>'.$this->finalConversion('easy_privacy_section_four_title').'</strong><br/>';
				$content = $content.$this->finalConversion('easy_privacy_section_four').'<p/>';				
			}
			if( get_option('easy_privacy_section_five_selected') == true ) {
			
				$content = $content.'<strong>'.$this->finalConversion('easy_privacy_section_five_title').'</strong><br/>';
				$content = $content.$this->finalConversion('easy_privacy_section_five').'<p/>';				
			}			
			if( get_option('easy_privacy_section_six_selected') == true ) {
			
				$content = $content.'<strong>'.$this->finalConversion('easy_privacy_section_six_title').'</strong><br/>';
				$content = $content.$this->finalConversion('easy_privacy_section_six').'<p/>';				
			}
			if( get_option('easy_privacy_acknowlegement') == true ) {
				$content = $content.'<span style="font-size:11px;">This policy was generated by Easy Privacy Policy Plugin <a target="_blank" href="http://www.comparemycasino.co.uk">online casino</a> for WordPress.</span>';
			}
			if( get_option('easy_privacy_lastupdated') == true ) {
			
				$content = $content.'<div style="font-style:italic;font-size:10px;">Last updated ' . date('D, d M Y H:i').'</div>';			
			}
									
			return $content;
		}
		
		// -----------------------------------------------------------------------------------------
		
		function createPageTitle() {
		
			$title = stripslashes( (string) $_POST[ 'easy_privacy_pagetitle' ] );		
				
			return $title;
		}
		
		// -----------------------------------------------------------------------------------------
		
		function createPageUrl() {
				
			return stripslashes( (string) $_POST[ 'easy_privacy_pageurl' ] );		
		}		
		
		// -----------------------------------------------------------------------------------------
		
		function updatePrivacyPage() {
		
			try {
			
				$postID = get_option('easy_privacy_CreatePageId');
				if(!empty($postID)) {
				
					if($this->wp_exist_post($postID) == false) {
					
						// looks like the page was deleted
						$postID = $this->createPrivacyPage();		
						update_option('easy_privacy_CreatePageId', $postID);
?>
						<div class="error"><p><strong><?php _e("The privacy page had to be regenerated. To remove Easy Privacy Policy please use the <i>uninstall</i> button at the bottom of this page.", "easy privacy");?></strong></div>
<?php				
					}
					else {	
						$post_data = array();
						$post_data['ID'] = $postID;
						$post_data['post_title']   = $this->createPageTitle();
						$post_data['post_content'] = $this->createContent();
						$post_data['post_name']    = $this->createPageUrl();
						$post_data['comment_status'] = 'closed';
			
						wp_update_post($post_data);									
						
?>
						<div class="updated"><p><strong><?php _e("The privacy page has been updated.", "easy privacy");?></strong></div>
<?php			
					}
				}	
					else {
						$postID = $this->createPrivacyPage();
						update_option('easy_privacy_CreatePageId', $postID);
?>
						<div class="error"><p><strong><?php _e("The privacy page had to be regenerated. To remove Easy Privacy Policy please use the <i>uninstall</i> button at the bottom of this page.", "easy privacy");?></strong></div>
<?php	
					}
			}
			catch (Exception $e){
			
				echo '<div class="error"><p><strong>'.$e->getMessage().'</strong></div>';
			
			}
		}
		
		// -----------------------------------------------------------------------------------------
		
		function wp_exist_post($id) {
			
			// Amazingly this method doesn't exist in the wordpress libraries, unless i am blind ;)
		
			global $wpdb;
			return $wpdb->get_row("SELECT * FROM wp_posts WHERE id = '" . $id . "'", 'ARRAY_A');
		}
		
		// -----------------------------------------------------------------------------------------
		
		function wp_exist_post_by_title($title_str) {
			global $wpdb;
			return $wpdb->get_row("SELECT * FROM wp_posts WHERE post_title = '" . $title_str . "'", 'ARRAY_A');
		}
		
		// -----------------------------------------------------------------------------------------
		
		function createPrivacyPage() {
		
			try {
		
				if($this->wp_exist_post_by_title(get_option('easy_privacy_pagetitle')))
					throw new Exception('A Privacy Policy page already exists, you should delete it or remove its plugin before using Easy Privacy Policy!');
		
				echo '<div id="message" class="updated fade"><p><strong>';			
			
				$post_title   = $this->createPageTitle();
				$post_content = $this->createContent();
				$post_status = 'publish';
				$post_author = 1;
				$post_name = $this->createPageUrl();
				$post_type = 'page';
				$comment_status = 'closed';				
				$post_data = compact( 'post_title', 'post_content', 'post_status',
						  'post_author', 'post_name', 'post_type', 'comment_status' );

				$postID = wp_insert_post( $post_data );

				if( !$postID ){
					echo 'Privacy policy page could not be created';
				} else {
					echo 'Privacy policy page was created';
				}

				echo '</strong></p></div>';
				
				// mark as created
				add_option( 'easy_privacy_CreatePageId', $postID);
				
				return $postID;
			}
			catch (Exception $e){

				echo '<div class="error"><p><strong>'.$e->getMessage().'</strong></p></div>';		
			}
		}		
		
		// -----------------------------------------------------------------------------------------
		
		function loadFromFile($file) {
		
			$filepath = dirname (__FILE__).'/'.$file;
			
			$content = "";
			
			if (file_exists ($filepath)){
				$content = file_get_contents($filepath);		
			}
			
			return $content;
		}	
		
		// -----------------------------------------------------------------------------------------
				
		function displayContent() {
		
			if (file_exists (dirname (__FILE__).'/options.php'))
				include (dirname (__FILE__).'/options.php');
			else
				//echo '<font size="+1" color="red">' . __("Error locating the options page!\nEnsure options.php exists, or reinstall the plugin.", 'easy privacy') . '</font>' ;				
				echo '<p/><div class="error">'.__("Error locating the options page!\nEnsure options.php exists, or reinstall the plugin.", 'easy privacy').'</div>';
				
		}
		
		// -----------------------------------------------------------------------------------------
		
		function pluginAction($links, $file) {
			if ($file == plugin_basename(dirname(__FILE__).'/easy-privacy-policy.php')){
				$settings_link = "<a href='options-general.php?page=easy-privacy-policy.php'>" .
					__('Settings', 'easy-privacy-policy') . "</a>";
				array_unshift( $links, $settings_link );
			}
			return $links;
		}
				
		// -----------------------------------------------------------------------------------------
		
		function setdefaults() {
			
			update_option( 'easy_privacy_sitename', get_bloginfo( 'name' ));												
			update_option( 'easy_privacy_email', get_bloginfo( 'admin_email' ));															
			update_option( 'easy_privacy_pageurl', 'privacy-policy');		
			update_option( 'easy_privacy_acknowlegement', true);		
			update_option( 'easy_privacy_lastupdated', true);		
			update_option( 'easy_privacy_pagetitle', 'Privacy Policy');	
			update_option( 'easy_privacy_pageurl', 'privacy-policy');				
			
			update_option( 'easy_privacy_section_one_selected', true );				
			update_option( 'easy_privacy_section_one_title', 'Your Privacy' );		
			update_option( 'easy_privacy_section_one', $this->loadFromFile('/meta-data/content-section-1.dat') );	
	
			update_option( 'easy_privacy_section_two_selected', true );				
			update_option( 'easy_privacy_section_two_title', 'Google Adsense and the DoubleClick DART Cookie' );		
			update_option( 'easy_privacy_section_two', $this->loadFromFile('/meta-data/content-section-2.dat') );
		
			update_option( 'easy_privacy_section_three_selected', false );				
			update_option( 'easy_privacy_section_three_title', 'Our Commitment To Childrens Privacy' );		
			update_option( 'easy_privacy_section_three', $this->loadFromFile('/meta-data/content-section-3.dat') );	
	
			update_option( 'easy_privacy_section_four_selected', true );				
			update_option( 'easy_privacy_section_four_title', 'Collection of Personal Information' );		
			update_option( 'easy_privacy_section_four', $this->loadFromFile('/meta-data/content-section-4.dat') );	
	
			update_option( 'easy_privacy_section_five_selected', true );				
			update_option( 'easy_privacy_section_five_title', 'Links to third party Websites' );		
			update_option( 'easy_privacy_section_five', $this->loadFromFile('/meta-data/content-section-5.dat') );		
			
			update_option( 'easy_privacy_section_six_selected', true );				
			update_option( 'easy_privacy_section_six_title', 'Changes to this Policy' );		
			update_option( 'easy_privacy_section_six', $this->loadFromFile('/meta-data/content-section-6.dat') );	
		}
		
		// -----------------------------------------------------------------------------------------
		
		function updateOptions() {
			
			update_option( 'easy_privacy_sitename', stripslashes( (string) $_POST['easy_privacy_sitename' ] ));												
			update_option( 'easy_privacy_email', stripslashes( (string) $_POST['easy_privacy_email' ] ));															
			update_option( 'easy_privacy_pageurl', stripslashes( (string) $_POST['easy_privacy_pageurl' ] ));				
			update_option( 'easy_privacy_acknowlegement', (bool) $_POST['easy_privacy_acknowlegement'] );	
			update_option( 'easy_privacy_lastupdated', (bool) $_POST['easy_privacy_lastupdated'] );	
			update_option( 'easy_privacy_pagetitle', stripslashes( (string) $_POST['easy_privacy_pagetitle'] ));				
			
			update_option( 'easy_privacy_section_one_selected', (bool) $_POST['easy_privacy_section_one_selected'] );	
			update_option( 'easy_privacy_section_two_selected', (bool) $_POST['easy_privacy_section_two_selected'] );	
			update_option( 'easy_privacy_section_three_selected', (bool) $_POST['easy_privacy_section_three_selected'] );	
			update_option( 'easy_privacy_section_four_selected', (bool) $_POST['easy_privacy_section_four_selected'] );	
			update_option( 'easy_privacy_section_five_selected', (bool) $_POST['easy_privacy_section_five_selected'] );	
			update_option( 'easy_privacy_section_six_selected', (bool) $_POST['easy_privacy_section_six_selected'] );	
			
			update_option( 'easy_privacy_section_one_title', stripslashes( (string) $_POST['edit_title1' ] ));	
			update_option( 'easy_privacy_section_one', stripslashes( (string) $_POST['edit_1' ] ));	
			
			update_option( 'easy_privacy_section_two_title', stripslashes( (string) $_POST['edit_title2' ] ));	
			update_option( 'easy_privacy_section_two', stripslashes( (string) $_POST['edit_2' ] ));
			
			update_option( 'easy_privacy_section_three', stripslashes( (string) $_POST['edit_3' ] ));
			update_option( 'easy_privacy_section_three_title', stripslashes( (string) $_POST['edit_title3' ] ));	
			
			update_option( 'easy_privacy_section_four', stripslashes( (string) $_POST['edit_4' ] ));
			update_option( 'easy_privacy_section_four_title', stripslashes( (string) $_POST['edit_title4' ] ));	
			
			update_option( 'easy_privacy_section_five', stripslashes( (string) $_POST['edit_5' ] ));
			update_option( 'easy_privacy_section_five_title', stripslashes( (string) $_POST['edit_title5' ] ));	
			
			update_option( 'easy_privacy_section_six', stripslashes( (string) $_POST['edit_6' ] ));
			update_option( 'easy_privacy_section_six_title', stripslashes( (string) $_POST['edit_title6' ] ));	
		}
		
		// -----------------------------------------------------------------------------------------
		
		function deleteOptions() {
		
			delete_option('easy_privacy_sitename');		
			delete_option('easy_privacy_CreatePageId');		
			delete_option('easy_privacy_email');		
			delete_option('easy_privacy_pageurl');
			delete_option('easy_privacy_acknowlegement');			
			delete_option('easy_privacy_lastupdated');			
			delete_option('easy_privacy_pagetitle');	
			delete_option('easy_privacy_pageurl');	
			
			delete_option('easy_privacy_section_one_title');	
			delete_option('easy_privacy_section_one_selected');	
			delete_option('easy_privacy_section_one');	
			
			delete_option('easy_privacy_section_two_title');	
			delete_option('easy_privacy_section_two_selected');	
			delete_option('easy_privacy_section_two');	
			
			delete_option('easy_privacy_section_three_title');	
			delete_option('easy_privacy_section_three_selected');	
			delete_option('easy_privacy_section_three');	
			
			delete_option('easy_privacy_section_four_title');	
			delete_option('easy_privacy_section_four_selected');	
			delete_option('easy_privacy_section_four');	
			
			delete_option('easy_privacy_section_five_title');	
			delete_option('easy_privacy_section_five_selected');	
			delete_option('easy_privacy_section_five');	
			
			delete_option('easy_privacy_section_six_title');	
			delete_option('easy_privacy_section_six_selected');	
			delete_option('easy_privacy_section_six');	
		}		
		
		// -----------------------------------------------------------------------------------------
		
		function cleanUp($prefix) {
		
			global $wpdb ;
			$wpdb->query("DELETE FROM $wpdb->options WHERE option_name LIKE '$prefix%'") ;
			
			remove_action('admin_menu', 'EasyPrivacy_ap');
			
			deactivate_plugins('easy-privacy-policy/easy-privacy-policy.php', true);				
		}
		
		// -----------------------------------------------------------------------------------------
		
		function finalConversion($option) {
		
			$option = get_option($option);
			
			$option = ereg_replace("[[:alpha:]]+://[^<>[:space:]]+[[:alnum:]/]", "<a href=\"\\0\">\\0</a>", $option);
					
			$option  = str_replace("@blogname", get_option('easy_privacy_sitename'), $option);
			$option  = str_replace("@email", get_option('easy_privacy_email'), $option);
					
					
			return $option;		
		}		
		
		// -----------------------------------------------------------------------------------------
	  }

	} //End Class EasyPrivacy

	
		
	if (class_exists("EasyPrivacy")) {

		$dl_EasyPrivacy = new EasyPrivacy();
		
		 if (!function_exists("easyPrivacy_ap")) {
		 
			function easyPrivacy_ap() {

				global $dl_EasyPrivacy ;
				if (function_exists('add_options_page')) {
					add_options_page('Easy Privacy Policy', 'Easy Privacy Policy', 9,
					basename(__FILE__), array(&$dl_EasyPrivacy, 'easyPrivacyOptionsPage'));
				}
			}			  				
		}
					
		// Actions				
		add_action('admin_menu', 'easyPrivacy_ap');
						
		// Filters
		add_filter('plugin_action_links', array($dl_EasyPrivacy, 'pluginAction'), -10, 2);
	}

?>