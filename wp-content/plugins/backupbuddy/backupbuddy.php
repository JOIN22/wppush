<?php
/**
 *
 * Plugin Name: BackupBuddy
 * Plugin URI: http://pluginbuddy.com/
 * Description: BackupBuddy Backup, Restoration, & Migration Tool
 * Version: 1.2.1
 * Author: Dustin Bolton
 * Author URI: http://dustinbolton.com
 *
 * Installation:
 * 
 * 1. Download and unzip the latest release zip file.
 * 2. If you use the WordPress plugin uploader to install this plugin skip to step 4.
 * 3. Upload the entire Backup directory to your '/wp-content/plugins/' directory.
 * 4. Activate the plugin through the 'Plugins' menu in WordPress Administration.
 * 
 * Usage:
 * 
 * 1. Navigate to the new Backup menu in the Wordpress Administration Panel.
 *
 */
 
if (!class_exists("iThemesBackupBuddy")) {
	class iThemesBackupBuddy {
		
		var $_version = '1.2.1';
		
		var $_debug = false;						// Set true to enable debug messages.
		var $_var = 'ithemes-backupbuddy';
		var $_name = 'BackupBuddy';
		var $_timeformat = '%b %e, %Y, %l:%i%p';	// mysql time format
		var $_timestamp = 'M j, Y, g:iA';			// php timestamp format
		var $_usedInputs = array();
		var $_pluginPath = '';
		var $_pluginRelativePath = '';
		var $_pluginURL = '';
		var $_selfLink = '';
		var $_defaults = array(
			'password'				=>		'#PASSWORD#',
			'ftp_server'			=>		'',
			'ftp_user'				=>		'',
			'ftp_pass'				=>		'',
			'ftp_path'				=>		'',
			'ftp_type'				=>		'ftp',
			'last_run'				=>		0,
			'compression'			=>		1,
			'force_compatibility'	=>		0,
			'email_notify_scheduled' =>		1,
			'email_notify_manual'	=>		0,
			'backup_nonwp_tables'	=>		0,
			'integrity_check'		=>		1,
			'aws_ssl'				=>		1,
			'aws_directory'			=>		'backupbuddy',
			'schedules'				=>		array(),
		);
		var $_options = array();
		var $_sql_files = 0;
		var $_warning_time = 300;					// Minutes within which to warn user when beginning backup that one may already be running.
		var $_errors;
		
		/**
		 * iThemesBackupBuddy()
		 *
		 * Default Constructor
		 *
		 */
		function iThemesBackupBuddy() {
			$this->_pluginPath = dirname( __FILE__ );
			$this->_pluginRelativePath = ltrim( str_replace( '\\', '/', str_replace( rtrim( ABSPATH, '\\\/' ), '', $this->_pluginPath ) ), '\\\/' );
			$this->_pluginURL = get_option( 'siteurl' ) . '/' . $this->_pluginRelativePath;
			if ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] == 'on' ) {
				$this->_pluginURL = str_replace( 'http://', 'https://', $this->_pluginURL );
			}
			$this->_selfLink = array_shift( explode( '?', $_SERVER['REQUEST_URI'] ) ) . '?page=' . $this->_var;
			
			// Admin.
			if ( is_admin() ) {
				add_action('admin_menu', array(&$this, 'admin_menu')); // Add menu in admin.
				add_action('admin_init', array(&$this, 'init_admin' )); // Run on admin initialization.
				// When user activates plugin in plugin menu.
				//register_activation_hook(__FILE__, array(&$this, '_activate'));
				// Direct downloading of importbuddy.php using ajax portal.
				add_action('wp_ajax_backupbuddy_importbuddy', array(&$this, 'ajax_importbuddy') );
				// Test FTP
				add_action('wp_ajax_backupbuddy_ftptest', array(&$this, 'ajax_ftptest') );
				// Test Amazon S3
				add_action('wp_ajax_backupbuddy_awstest', array(&$this, 'ajax_awstest') );
				// Directory listing for exluding
				add_action('wp_ajax_backupbuddy_dirlist', array(&$this, 'ajax_dirlist') );
				
				require_once(dirname( __FILE__ ).'/lib/updater/updater.php');
			} else { // Non-Admin.
				//add_action('template_redirect', array(&$this, 'init_public'));
			}
			
			add_action($this->_var.'-cron_email', array(&$this, 'cron_email'), '', 4);
			add_action($this->_var.'-cron_ftp', array(&$this, 'cron_ftp'), '', 5);
			add_filter('cron_schedules', array(&$this, 'cron_add_schedules'));
			add_action($this->_var.'-cron_schedule', array(&$this, 'cron_schedule'), '', 5);
		}
		
		/**
		 *	alert()
		 *
		 *	Displays a message to the user at the top of the page when in the dashboard.
		 *
		 *	$message		string		Message you want to display to the user.
		 *	$error			boolean		OPTIONAL! true indicates this alert is an error and displays as red. Default: false
		 *	$error_code		int			OPTIONAL! Error code number to use in linking in the wiki for easy reference.
		 */
		function alert( $message, $error = false, $error_code = '' ) {
			echo '<div id="message" class="';
			if ( $error == false ) {
				echo 'updated fade';
			} else {
				echo 'error';
			}
			if ( $error_code != '' ) {
				$message .= '<p><a href="http://ithemes.com/codex/page/' . $this->_name . ':_Error_Codes#' . $error_code . '" target="_new"><i>' . $this->_name . ' Error Code ' . $error_code . ' - Click for more details.</i></a></p>';
			}
			echo '"><p><strong>'.$message.'</strong></p></div>';
		}
		
		/**
		 * iThemesBackupBuddy::init_admin()
		 *
		 * Initialize for admins.
		 *
		 */
		function init_admin() {
			// TODO: MAKE THIS ONLY RUN ON BACKUPBUDDY PAGES!
			//header('Keep-Alive: 7200');
			//header('Connection: keep-alive');
		}
		
		// PAGES //////////////////////////////

		
		function view_gettingstarted() {
			// Needed for fancy boxes...
			wp_enqueue_style('dashboard');
			wp_print_styles('dashboard');
			wp_enqueue_script('dashboard');
			wp_print_scripts('dashboard');
			// Load scripts and CSS used on this page.
			$this->admin_scripts();
			
			// If they clicked the button to reset plugin defaults...
			if (!empty($_POST['reset_defaults'])) {
				$this->_options = $this->_defaults;
				$this->save();
				$this->_showStatusMessage( 'Plugin settings have been reset to defaults.' );
			}
			?>
			
			<div class="wrap">
				<div class="postbox-container" style="width:70%;">
					<h2>Getting Started with <?php echo $this->_name; ?> v<?php echo $this->_version; ?></h2>
					
					
					
					
					
					BackupBuddy is an all-in-one solution for backups, restoration, and migration.  The single backup ZIP file created by the plugin
					can be used with the importer PHP script to quickly and easily restore your site on the same server or even migrate to a new host
					with different settings.  Whether you're an end user or a developer, this plugin is sure to bring you peace of mind and added safety
					in the event of data loss.  Our goal is to keep the backup, restoration, and migration processes easy, fast, and reliable.
					
					<br /><br />
					
					A full backup is required to fully restore your site or migrate.  However, a Database Only backup may be created as a faster, more
					regular backup solution.  When restoring your site or migrating, simply restore the latest Database Only backup (if newer than the
					full backup) followed by the Full Backup.
					<br /><br />
					
					Your backups are stored in <?php echo $this->_options['backup_directory']; ?> <a class="ithemes_tip" title=" - This is the local directory that backups are stored in. Backup files include random characters in their name for increased security. Verify that write permissions are available for this directory.">(?)</a>
					<br />
					<br />
					<p>
						<h3>Backup</h3>
						<ol>
							<!-- <li>Set a password in the <a href="<?php echo admin_url( "admin.php?page={$this->_var}-settings" ); ?>">Settings</a> section if you have not done so.</li> -->
							<li>Perform a full backup by clicking Begin Backup on the <a href="<?php echo admin_url( "admin.php?page={$this->_var}-backup" ); ?>">Backups</a> page. This may take several minutes.</li>
							<li>Perform database backups regularly to complement the full backup.<br />The database changes with every post & is much smaller than files so may be backed up more often.</li>
							<li>Download the backup importer, <a href="<?php echo admin_url('admin-ajax.php').'?action=backupbuddy_importbuddy&pass='.md5($this->_options['password']); ?>">importbuddy.php</a>, for use later when restoring or migrating.
						</ol>
					</p>
					<br />
					<p>
						<h3>Restoring, Migrating</h3>
						<ol>
							<li>Upload the backup file & <a href="<?php echo admin_url('admin-ajax.php').'?action=backupbuddy_importbuddy&pass='.md5($this->_options['password']); ?>">importbuddy.php</a> to the root web directory of the destination server.<br />
								<b>Do not install WordPress</b> on the destination server. The importbuddy.php script will restore all files, including WordPress.</li>
							<li>Navigate to <a href="<?php echo admin_url('admin-ajax.php').'?action=backupbuddy_importbuddy&pass='.md5($this->_options['password']); ?>">importbuddy.php</a> in your web browser on the destination server.</li>
							<li>Follow the importing instructions on screen. You will be asked whether you are restoring or migrating.<br />If applicable, you may restore an older Full Backup followed by a newer Database Only backup.</li>
						</ol>
					</p>
					
					<?php if ( stristr( PHP_OS, 'WIN' ) ) { ?>
						<br />
						<h3>Windows Server Performance Boost</h3>
						Windows servers may be able to significantly boost performance, IF the server allows executing .exe files, by adding native Zip compatibility executable files <a href="http://pluginbuddy.com/wp-content/uploads/2010/05/backupbuddy_windows_unzip.zip">available for download here</a>.
						Instructions are provided within the readme.txt in the package.  This package prevents Windows from falling back to Zip compatiblity mode and works for both BackupBuddy and importbuddy.php. This is particularly useful for local development on a Windows machine using a system like <a href="http://www.apachefriends.org/en/xampp.html">XAMPP</a>.
					<?php } ?>
					
					<br /><br />
					
					
					
					
					
					
					<h3>Version History</h3>
					<textarea rows="7" cols="65"><?php readfile( $this->_pluginPath . '/history.txt' ); ?></textarea>
					<br /><br />
					<script type="text/javascript">
						jQuery(document).ready(function() {
							jQuery("#pluginbuddy_debugtoggle").click(function() {
								jQuery("#pluginbuddy_debugtoggle_div").slideToggle();
							});
						});
					</script>
					
					<a id="pluginbuddy_debugtoggle" class="button secondary-button">Debugging Information</a>
					<div id="pluginbuddy_debugtoggle_div" style="display: none;">
						<h3>Debugging Information</h3>
						<?php
						echo '<textarea rows="7" cols="65">';
						echo 'Plugin Version = '.$this->_name.' '.$this->_version.' ('.$this->_var.')'."\n";
						echo 'WordPress Version = '.get_bloginfo("version")."\n";
						echo 'PHP Version = '.phpversion()."\n";
						global $wpdb;
						echo 'DB Version = '.$wpdb->db_version()."\n";
						echo "\n".serialize($this->_options);
						echo '</textarea>';
						?>
						<p>
						<form method="post" action="<?php echo $this->_selfLink; ?>">
							<input type="hidden" name="reset_defaults" value="true" />
							<input type="submit" name="submit" value="Reset Plugin Settings & Defaults" id="reset_defaults" class="button secondary-button" onclick="if ( !confirm('WARNING: This will reset all settings associated with this plugin to their defaults. Are you sure you want to do this?') ) { return false; }" />
						</form>
						</p>
					</div>
					<br /><br /><br />
					<a href="http://pluginbuddy.com" style="text-decoration: none;"><img src="<?php echo $this->_pluginURL; ?>/images/pluginbuddy.png" style="vertical-align: -3px;" /> PluginBuddy.com</a><br /><br />
				</div>
				<div class="postbox-container" style="width:20%; margin-top: 35px; margin-left: 15px;">
					<div class="metabox-holder">	
						<div class="meta-box-sortables">

							<div id="breadcrumbssupport" class="postbox">
								<div class="handlediv" title="Click to toggle"><br /></div>
								<h3 class="hndle"><span>Tutorials & Support</span></h3>
								<div class="inside">
									<p>See our <a href="http://pluginbuddy.com/tutorials/backupbuddy/">tutorials & videos</a> or visit our <a href="http://ithemes.com/support/backupbuddy/">support forum</a> for additional information and help.</p>
								</div>
							</div>
						
							<div id="breadcrumbslike" class="postbox">
								<div class="handlediv" title="Click to toggle"><br /></div>
								<h3 class="hndle"><span>Things to do...</span></h3>
								<div class="inside">
									<ul class="pluginbuddy-nodecor">
										<li>- <a href="http://twitter.com/home?status=<?php echo urlencode('Check out this awesome plugin, ' . $this->_name . '! ' . $this->_url . ' @pluginbuddy'); ?>" title="Share on Twitter" onClick="window.open(jQuery(this).attr('href'),'ithemes_popup','toolbar=0,status=0,width=820,height=500,scrollbars=1'); return false;">Tweet about this plugin.</a></li>
										<li>- <a href="http://pluginbuddy.com/purchase/">Check out PluginBuddy plugins.</a></li>
										<li>- <a href="http://pluginbuddy.com/purchase/">Check out iThemes themes.</a></li>
										<li>- <a href="http://secure.hostgator.com/cgi-bin/affiliates/clickthru.cgi?id=ithemes">Get HostGator web hosting.</a></li>
									</ul>
								</div>
							</div>

							<div id="breadcrumsnews" class="postbox">
								<div class="handlediv" title="Click to toggle"><br /></div>
								<h3 class="hndle"><span>Latest news from PluginBuddy</span></h3>
								<div class="inside">
									<p style="font-weight: bold;">PluginBuddy.com</p>
									<?php $this->get_feed( 'http://pluginbuddy.com/feed/', 5 );  ?>
									<p style="font-weight: bold;">Twitter @pluginbuddy</p>
									<?php
									$twit_append = '<li>&nbsp;</li>';
									$twit_append .= '<li><img src="'.$this->_pluginURL.'/images/twitter.png" style="vertical-align: -3px;" /> <a href="http://twitter.com/pluginbuddy/">Follow @pluginbuddy on Twitter.</a></li>';
									$twit_append .= '<li><img src="'.$this->_pluginURL.'/images/feed.png" style="vertical-align: -3px;" /> <a href="http://pluginbuddy.com/feed/">Subscribe to RSS news feed.</a></li>';
									$twit_append .= '<li><img src="'.$this->_pluginURL.'/images/email.png" style="vertical-align: -3px;" /> <a href="http://pluginbuddy.com/subscribe/">Subscribe to Email Newsletter.</a></li>';
									$this->get_feed( 'http://twitter.com/statuses/user_timeline/108700480.rss', 5, $twit_append, 'pluginbuddy: ' );
									?>
								</div>
							</div>
							
							
						</div>
					</div>
				</div>
			</div>
			<?php
		}
		
		function get_feed( $feed, $limit, $append = '', $replace = '' ) {
			require_once(ABSPATH.WPINC.'/feed.php');  
			$rss = fetch_feed( $feed );
			if (!is_wp_error( $rss ) ) {
				$maxitems = $rss->get_item_quantity( $limit ); // Limit 
				$rss_items = $rss->get_items(0, $maxitems); 
				
				echo '<ul class="pluginbuddy-nodecor">';

				$feed_html = get_transient( md5( $feed ) );
				if ( $feed_html == '' ) {
					foreach ( (array) $rss_items as $item ) {
						$feed_html .= '<li>- <a href="' . $item->get_permalink() . '">';
						$title =  $item->get_title(); //, ENT_NOQUOTES, 'UTF-8');
						if ( $replace != '' ) {
							$title = str_replace( $replace, '', $title );
						}
						if ( strlen( $title ) < 30 ) {
							$feed_html .= $title;
						} else {
							$feed_html .= substr( $title, 0, 32 ) . ' ...';
						}
						$feed_html .= '</a></li>';
					}
					set_transient( md5( $feed ), $feed_html, 300 ); // expires in 300secs aka 5min
				}
				echo $feed_html;
				
				echo $append;
				echo '</ul>';
			} else {
				echo 'Temporarily unable to load feed...';
			}
		}
		
		
		/**
		 * iThemesBackupBuddy::view_backup()
		 *
		 * Displays backup plugin page.
		 *
		 */
		function view_backup() {
			$this->load();
			
			// Load scripts and CSS used on this page.
			$this->admin_scripts();
			?>
			<div class="wrap">
				<h2>Create Backup</h2><br />
			<?php
			if (!empty($_GET['backup_step'])) {
				echo 'This may take several minutes. Do NOT use your back button or it may corrupt the backup. Please wait for this page to finish loading...<br /><br />';
				?>
				<div class="updated">
				<table style="width: 100%; border-collapse: collapse;">
					<tr>
						<td id="backupbuddy_step1" style="border-right: 1px solid #E6DB55; padding: 8px; font: italic 17px Georgia;">Starting Backup ...</td>
						<td id="backupbuddy_step2" style="color: #9F9F9F; background-color: #FFFFEF; border-right: 1px solid #E6DB55; padding: 8px; font: italic 17px Georgia;">Exporting Settings ...</td>
						<td id="backupbuddy_step3" style="color: #9F9F9F; background-color: #FFFFEF; border-right: 1px solid #E6DB55; padding: 8px; font: italic 17px Georgia;">Exporting Database ...</td>
						<td id="backupbuddy_step4" style="color: #9F9F9F; background-color: #FFFFEF; border-right: 1px solid #E6DB55; padding: 8px; font: italic 17px Georgia;">Saving Data to File ...</td>
						<td id="backupbuddy_step5" style="color: #9F9F9F; background-color: #FFFFEF; padding: 8px; font: italic 17px Georgia;">Finishing ...</td>
					</tr>
				</table>
			</div>
				<?php
				flush();
				
				$this->_backup( $_GET['type'] );
				
			} else {
				
			?>
				Backups stored in <?php echo $this->_options['backup_directory']; ?> <a class="ithemes_tip" title=" - This is the local directory that backups are stored in. Backup files include random characters in their name for increased security. Verify that write permissions are available for this directory.">(?)</a><br />
				<p>
					<ol>
						<li>Perform a full backup by clicking the button below. This may take several minutes.</li>
						<li>Download the backup importer: <a href="<?php echo admin_url('admin-ajax.php').'?action=backupbuddy_importbuddy&pass='.md5($this->_options['password']); ?>">importbuddy.php</a>
						<li>Upload the resulting backup zip file and <a href="<?php echo admin_url('admin-ajax.php').'?action=backupbuddy_importbuddy&pass='.md5($this->_options['password']); ?>">importbuddy.php</a> to the root web directory of the destination server.
						<li>If you performed database backups after the full backup, upload the latest database backup zip and import after the full backup.
						<li>Navigate to <a href="<?php echo admin_url('admin-ajax.php').'?action=backupbuddy_importbuddy&pass='.md5($this->_options['password']); ?>">importbuddy.php</a> in your webbrowser on the destination server.</li>
					</ol>
				</p>
				
				<?php
				/*
				if ($this->_options['password']=='#PASSWORD#') {
					echo '<br /><b>IMPORTANT:</b> Backing up disabled until a password is set for BackupBuddy. Set a password in the <a href="'.admin_url( "admin.php?page={$this->_var}-settings" ).'">Settings</a> section before you may make a backup.<br />';
					$this->_showErrorMessage( __( 'IMPORTANT: You have not set a password for BackupBuddy. You must set a password in the <a href="'.admin_url( "admin.php?page={$this->_var}-settings" ).'">Settings</a> section before you may make a backup.', $this->_var ) );
				} else {
				*/
				if ( !file_exists( $this->_options['backup_directory'] ) ) {
					$this->mkdir_recursive($this->_options['backup_directory']);
				}
				if ( !file_exists( $this->_options['backup_directory'] ) ) {
					$this->_showErrorMessage( __( 'Unable to create backup storage directory.', $this->_var ) );
				}
				if (is_writable($this->_options['backup_directory'])) {
					?>
					<br />
					<table width="400"><tr><td>
						<form method="post" action="<?php echo $this->_selfLink; ?>-backup&backup_step=1&type=full">
							<p class="submit"><?php $this->_addSubmit( 'backup', 'Full Backup' ); ?> <a class="ithemes_tip" title=" - Initial and occassional backup to store everything, including database and all files.">(?)</a>
						</form>
					</td><td>
						<form method="post" action="<?php echo $this->_selfLink; ?>-backup&backup_step=1&type=db">
							<?php $this->_addSubmit( 'db_backup', 'Database Only' ); ?> <a class="ithemes_tip" title=" - Perform this backup often to backup the database and all posts and settings. It is recommended that you create a Full Backup first and Database backups secondly on a more regular basis.  Upload the Database Backup ZIP in addition to the other files when restoring or migrating.">(?)</a></p>
						</form>
					</td></tr></table>
					<?php
				} else {
					echo 'ERROR: The backup directory is not writable. Please verify the directory has write permissions.';
				}
				?>
			
				<?php $this->show_backup_files(); ?>
			</div>
			<?php
			}
		}
		
		/**
		 * iThemesBackupBuddy::admin_scripts()
		 *
		 * Load scripts and styling for admin pages.
		 *
		 */	
		function admin_scripts() {
			//wp_enqueue_script( 'jquery' );
			
			wp_enqueue_script( 'jquery-ui-core' );
			wp_print_scripts( 'jquery-ui-core' );

			wp_enqueue_script( 'ithemes-custom-ui-js', $this->_pluginURL . '/js/jquery.custom-ui.js' );
			wp_print_scripts( 'ithemes-custom-ui-js' );
			
			wp_enqueue_script( 'ithemes-timepicker-js', $this->_pluginURL . '/js/timepicker.js' );
			wp_print_scripts( 'ithemes-timepicker-js' );
			echo '<link rel="stylesheet" href="'.$this->_pluginURL . '/css/ui-lightness/jquery-ui-1.7.2.custom.css" type="text/css" media="all" />';

			wp_enqueue_script( 'ithemes-filetree-js', $this->_pluginURL . '/js/filetree.js' );
			wp_print_scripts( 'ithemes-filetree-js' );
			echo '<link rel="stylesheet" href="'.$this->_pluginURL . '/css/filetree.css" type="text/css" media="all" />';
			
			wp_enqueue_script( 'ithemes-tooltip-js', $this->_pluginURL . '/js/tooltip.js' );
			wp_print_scripts( 'ithemes-tooltip-js' );
			wp_enqueue_script( 'ithemes-swiftpopup-js', $this->_pluginURL . '/js/swiftpopup.js' );
			wp_print_scripts( 'ithemes-swiftpopup-js' );
			wp_enqueue_script( 'ithemes-'.$this->_var.'-admin-js', $this->_pluginURL . '/js/admin.js' );
			wp_print_scripts( 'ithemes-'.$this->_var.'-admin-js' );
			echo '<link rel="stylesheet" href="'.$this->_pluginURL . '/css/admin.css" type="text/css" media="all" />';
		}
		
		/**
		 * iThemesBackupBuddy::view_settings()
		 *
		 * Displays settings form and values for viewing & editing.
		 *
		 */
		function view_settings() {
			$this->load();

			if (!empty($_POST['save'])) {
				$this->_saveSettings();
			}

			// Load scripts and CSS used on this page.
			$this->admin_scripts();
			
			echo '<div class="wrap">';
			?>
				<h2><?php echo $this->_name; ?> Settings</h2>
				
				<br /><i>BETA FEATURES: Please note that FTPs and Amazon S3 features are currently in BETA.</i><br /><br />
				
				
				
				<script type="text/javascript">
					jQuery(document).ready(function() {
						jQuery('#exlude_dirs').fileTree({ root: '/', multiFolder: false, script: '<?php echo admin_url('admin-ajax.php').'?action=backupbuddy_dirlist'; ?>' }, function(file) {
							//alert('file:'+file);
						}, function(directory) {
							if ( ( directory == '/wp-content/' ) || ( directory == '/wp-content/uploads/' ) || ( directory == '/wp-content/uploads/backupbuddy_backups/' ) ) {
								alert( 'You cannot exclude /wp-content/ or /wp-content/uploads/.  However, you may exclude subdirectories within these. The backupbuddy_backups directory is also automatically excluded and cannot be added to exclusion list.' );
							} else {
								jQuery('#exclude_dirs').val( directory + "\n" + jQuery('#exclude_dirs').val() );
							}
							
						});
					});
				</script>
				<style type="text/css">
					/* Core Styles */
					.jqueryFileTree LI.directory { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/directory.png') left top no-repeat; }
					.jqueryFileTree LI.expanded { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/folder_open.png') left top no-repeat; }
					.jqueryFileTree LI.file { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/file.png') left top no-repeat; }
					.jqueryFileTree LI.wait { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/spinner.gif') left top no-repeat; }
					/* File Extensions*/
					.jqueryFileTree LI.ext_3gp { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/film.png') left top no-repeat; }
					.jqueryFileTree LI.ext_afp { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/code.png') left top no-repeat; }
					.jqueryFileTree LI.ext_afpa { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/code.png') left top no-repeat; }
					.jqueryFileTree LI.ext_asp { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/code.png') left top no-repeat; }
					.jqueryFileTree LI.ext_aspx { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/code.png') left top no-repeat; }
					.jqueryFileTree LI.ext_avi { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/film.png') left top no-repeat; }
					.jqueryFileTree LI.ext_bat { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/application.png') left top no-repeat; }
					.jqueryFileTree LI.ext_bmp { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/picture.png') left top no-repeat; }
					.jqueryFileTree LI.ext_c { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/code.png') left top no-repeat; }
					.jqueryFileTree LI.ext_cfm { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/code.png') left top no-repeat; }
					.jqueryFileTree LI.ext_cgi { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/code.png') left top no-repeat; }
					.jqueryFileTree LI.ext_com { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/application.png') left top no-repeat; }
					.jqueryFileTree LI.ext_cpp { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/code.png') left top no-repeat; }
					.jqueryFileTree LI.ext_css { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/css.png') left top no-repeat; }
					.jqueryFileTree LI.ext_doc { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/doc.png') left top no-repeat; }
					.jqueryFileTree LI.ext_exe { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/application.png') left top no-repeat; }
					.jqueryFileTree LI.ext_gif { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/picture.png') left top no-repeat; }
					.jqueryFileTree LI.ext_fla { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/flash.png') left top no-repeat; }
					.jqueryFileTree LI.ext_h { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/code.png') left top no-repeat; }
					.jqueryFileTree LI.ext_htm { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/html.png') left top no-repeat; }
					.jqueryFileTree LI.ext_html { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/html.png') left top no-repeat; }
					.jqueryFileTree LI.ext_jar { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/java.png') left top no-repeat; }
					.jqueryFileTree LI.ext_jpg { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/picture.png') left top no-repeat; }
					.jqueryFileTree LI.ext_jpeg { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/picture.png') left top no-repeat; }
					.jqueryFileTree LI.ext_js { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/script.png') left top no-repeat; }
					.jqueryFileTree LI.ext_lasso { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/code.png') left top no-repeat; }
					.jqueryFileTree LI.ext_log { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/txt.png') left top no-repeat; }
					.jqueryFileTree LI.ext_m4p { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/music.png') left top no-repeat; }
					.jqueryFileTree LI.ext_mov { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/film.png') left top no-repeat; }
					.jqueryFileTree LI.ext_mp3 { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/music.png') left top no-repeat; }
					.jqueryFileTree LI.ext_mp4 { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/film.png') left top no-repeat; }
					.jqueryFileTree LI.ext_mpg { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/film.png') left top no-repeat; }
					.jqueryFileTree LI.ext_mpeg { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/film.png') left top no-repeat; }
					.jqueryFileTree LI.ext_ogg { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/music.png') left top no-repeat; }
					.jqueryFileTree LI.ext_pcx { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/picture.png') left top no-repeat; }
					.jqueryFileTree LI.ext_pdf { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/pdf.png') left top no-repeat; }
					.jqueryFileTree LI.ext_php { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/php.png') left top no-repeat; }
					.jqueryFileTree LI.ext_png { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/picture.png') left top no-repeat; }
					.jqueryFileTree LI.ext_ppt { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/ppt.png') left top no-repeat; }
					.jqueryFileTree LI.ext_psd { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/psd.png') left top no-repeat; }
					.jqueryFileTree LI.ext_pl { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/script.png') left top no-repeat; }
					.jqueryFileTree LI.ext_py { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/script.png') left top no-repeat; }
					.jqueryFileTree LI.ext_rb { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/ruby.png') left top no-repeat; }
					.jqueryFileTree LI.ext_rbx { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/ruby.png') left top no-repeat; }
					.jqueryFileTree LI.ext_rhtml { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/ruby.png') left top no-repeat; }
					.jqueryFileTree LI.ext_rpm { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/linux.png') left top no-repeat; }
					.jqueryFileTree LI.ext_ruby { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/ruby.png') left top no-repeat; }
					.jqueryFileTree LI.ext_sql { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/db.png') left top no-repeat; }
					.jqueryFileTree LI.ext_swf { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/flash.png') left top no-repeat; }
					.jqueryFileTree LI.ext_tif { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/picture.png') left top no-repeat; }
					.jqueryFileTree LI.ext_tiff { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/picture.png') left top no-repeat; }
					.jqueryFileTree LI.ext_txt { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/txt.png') left top no-repeat; }
					.jqueryFileTree LI.ext_vb { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/code.png') left top no-repeat; }
					.jqueryFileTree LI.ext_wav { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/music.png') left top no-repeat; }
					.jqueryFileTree LI.ext_wmv { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/film.png') left top no-repeat; }
					.jqueryFileTree LI.ext_xls { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/xls.png') left top no-repeat; }
					.jqueryFileTree LI.ext_xml { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/code.png') left top no-repeat; }
					.jqueryFileTree LI.ext_zip { background: url('<?php echo $this->_pluginURL; ?>/images/filetree/zip.png') left top no-repeat; }
				</style>
				
				
				<?php $this->_usedInputs=array(); ?>
				<form method="post" action="<?php echo $this->_selfLink; ?>-settings">
					<table class="form-table" style="max-width: 900px;">
					

						<tr><td colspan="3"><b>Notifications</b></td></tr>
						<tr>
							<td><label for="email">Notification Email Address <a class="ithemes_tip" title=" - [Example: foo@bar.com] - Email address to sent notifications and optional ZIP files to.">(?)</a>:</label></td>
							<td><?php $this->_addTextBox('email', array( 'size' => '45', 'maxlength' => '45', 'value' => $this->_options['email'] ) ); ?></td>
						</tr>
						<tr>
							<td><label for="email_notify_manual">Email on manual backup completion <a class="ithemes_tip" title=" - [Default: disabled] - Receive an email notification on the completion of manual backups.">(?)</a>:</label></td>
							<td><?php $this->_addCheckBox('email_notify_manual', array( 'value' => 1 ) ); ?> <label for="compression">Enable manual notifications</label></td>						
						</tr>
						<tr>
							<td><label for="email_notify_scheduled">Email on scheduled backup completion <a class="ithemes_tip" title=" - [Default: enabled] - Receive an email notification on the completion of scheduled backups. This is the default.">(?)</a>:</label></td>
							<td><?php $this->_addCheckBox('email_notify_scheduled', array( 'value' => 1 ) ); ?> <label for="compression">Enable scheduled notifications</label></td>						
						</tr>
						

						



						


						<tr><td colspan="3"><br /><b>Remote FTP / FTPs (BETA) Backup (optional)</b></td></tr>
						<tr>
							<td><label for="ftp_server">FTP Server Address <a class="ithemes_tip" title=" - [Example: ftp.yoursite.com] - Host / IP address of the FTP server to backup to.">(?)</a>:</label></td>
							<td><?php $this->_addTextBox('ftp_server', array( 'size' => '45', 'maxlength' => '45', 'value' => $this->_options['ftp_server'] ) ); ?></td>
						</tr>
						<tr>
							<td><label for="ftp_user">FTP Username <a class="ithemes_tip" title=" - [Example: foo] - Username to use when connecting to the FTP server.">(?)</a>:</label></td>
							<td><?php $this->_addTextBox('ftp_user', array( 'size' => '45', 'maxlength' => '45', 'value' => $this->_options['ftp_user'] ) ); ?></td>
						</tr>
						<tr>
							<td><label for="ftp_pass">FTP Password <a class="ithemes_tip" title=" - [Example: 1234] - Password to use when connecting to the FTP server.">(?)</a>:</label></td>
							<td><?php $this->_addPassBox('ftp_pass', array( 'size' => '45', 'maxlength' => '45', 'value' => $this->_options['ftp_pass'] ) ); ?></td>
						</tr>
						<tr>
							<td><label for="ftp_path">FTP Upload Path <a class="ithemes_tip" title=" - [Example: /public_html/backups/] - Remote path to place uploaded files into on the destination FTP server. Make sure this path is correct and that the directory already exists.">(?)</a>:</label></td>
							<td><?php $this->_addTextBox('ftp_path', array( 'size' => '45', 'maxlength' => '245', 'value' => $this->_options['ftp_path'] ) ); ?></td>
						</tr>
						<tr>
							<td><label for="ftp_path">FTP Connection Type <a class="ithemes_tip" title=" - [Default: FTP] - Select whether this connection is for FTP or FTPs (FTP over SSL). Note that FTPs is NOT the same as sFTP (FTP over SSH) and is not compatible with that.">(?)</a>:</label></td>
							<td><?php $this->_addDropDown( 'ftp_type', array( 'ftp' => 'FTP', 'ftps' => 'FTPs (SSL)' ) ); ?></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
								<input value="Test FTP Settings" class="button-secondary" type="submit" id="ithemes_backupbuddy_ftptest" alt="<?php echo admin_url('admin-ajax.php').'?action=backupbuddy_ftptest'; ?>" />
								<span id="ithemes_backupbuddy_ftpresponse"></span>
							</td>
						</tr>

						
						
						
						<tr><td colspan="3"><b>Remote Amazon S3 Backup (optional) (BETA)</b></td></tr>
						<tr>
							<td><label for="aws_accesskey">AWS Access Key <a class="ithemes_tip" title=" - [Example: BSEGHGSDEUOXSQOPGSBE] - Log in to your Amazon S3 AWS Account and navigate to Account: Access Credentials: Security Credentials.">(?)</a>:</label></td>
							<td><?php $this->_addTextBox('aws_accesskey', array( 'size' => '45', 'maxlength' => '45', 'value' => $this->_options['aws_accesskey'] ) ); ?></td>
						</tr>
						<tr>
							<td><label for="aws_secretkey">AWS Secret Key <a class="ithemes_tip" title=" - [Example: GHOIDDWE56SDSAZXMOPR] - Log in to your Amazon S3 AWS Account and navigate to Account: Access Credentials: Security Credentials.">(?)</a>:</label></td>
							<td><?php $this->_addPassBox('aws_secretkey', array( 'size' => '45', 'maxlength' => '45', 'value' => $this->_options['aws_secretkey'] ) ); ?></td>
						</tr>
						<tr>
							<td><label for="aws_bucket">Bucket Name <a class="ithemes_tip" title=" - [Example: wordpress_backups] - This bucket will be created for your automatically if it does not already exist.">(?)</a>:</label></td>
							<td><?php $this->_addTextBox('aws_bucket', array( 'size' => '45', 'maxlength' => '45', 'value' => $this->_options['aws_bucket'] ) ); ?></td>
						</tr>
						<tr>
							<td><label for="aws_directory">Directory Name <a class="ithemes_tip" title=" - [Example: backupbuddy] - Directory name to place the backup in within the bucket.">(?)</a>:</label></td>
							<td><?php $this->_addTextBox('aws_directory', array( 'size' => '45', 'maxlength' => '45', 'value' => $this->_options['aws_directory'] ) ); ?></td>
						</tr>
						<tr>
							<td><label for="aws_ssl">Use SSL Encryption <a class="ithemes_tip" title=" - [Default: enabled] - When enabled, all transfers will be encrypted with SSL encryption. Please note that encryption introduces overhead and may slow down the transfer. If Amazon S3 sends are failing try disabling this feature to speed up the process.  Note that 32-bit servers cannot encrypt transfers of 2GB or larger with SSL, causing large file transfers to fail.">(?)</a>:</label></td>
							<td><?php $this->_addCheckBox('aws_ssl', array( 'size' => '45', 'maxlength' => '45', 'value' => $this->_options['aws_ssl'] ) ); ?></td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>
								<input value="Test S3 Settings" class="button-secondary" type="submit" id="ithemes_backupbuddy_awstest" alt="<?php echo admin_url('admin-ajax.php').'?action=backupbuddy_awstest'; ?>" />
								<span id="ithemes_backupbuddy_awsresponse"></span>
							</td>
						</tr>
						
						
						
						
						
						<tr><td colspan="3"><b>Advanced Troubleshooting Options</b></td></tr>
						<!--
						<tr>
							<td><label for="first_name">Restore / Migrate Password <a class="ithemes_tip" title=" - [Example: 1234] - Enter the password you wish to use when restoring your site or migrating.">(?)</a>:</label></td>
							<td><?php $this->_addPassBox('password', array( 'size' => '45', 'maxlength' => '45', 'value' => $this->_options['password'] ) ); ?></td>
						</tr>
						-->
						<tr>
							<td><label for="compression">Use Zip Compression <a class="ithemes_tip" title=" - [Default: enabled] - ZIP compression decreases file sizes of stored backups. If you are encountering timeouts due to the script running too long, disabling compression may allow the process to complete faster.">(?)</a>:</label></td>
							<td><?php $this->_addCheckBox('compression', array( 'value' => 1, 'id' => 'compression' ) ); ?> <label for="compression">Enable ZIP compression</label></td>
						</tr>
						<tr>
							<td><label for="force_compatibility">Force Compatibility Mode <a class="ithemes_tip" title=" - [Default: disabled] - Under normal circumstances compatibility mode is automatically entered as needed without user intervention. However under some server configurations the native backup system is unavailable but is incorrectly reported as functioning by the server.  Forcing compatibility may fix problems in this situation by bypassing the native backup system check entirely.">(?)</a>:</label></td>
							<td><?php $this->_addCheckBox('force_compatibility', array( 'value' => 1, 'id' => 'force_compatibility' ) ); ?> <label for="force_compatibility">Enable forced compatibility for backups (slow)</label></td>
						</tr>
						<tr>
							<td><label for="backup_nonwp_tables">Backup non-WordPress database tables <a class="ithemes_tip" title=" - [Default: disabled] - Checking this box will result in ALL tables and data in the database being backed up, even database content not related to WordPress, its content, or plugins.  This is useful if you have other software installed on your hosting that stores data in your database.  This may also be useful for WordPress MU.">(?)</a>:</label></td>
							<td><?php $this->_addCheckBox('backup_nonwp_tables', array( 'value' => 1, 'id' => 'backup_nonwp_tables' ) ); ?> <label for="backup_nonwp_tables">Enable backing up non-WordPress database data</label></td>						
						</tr>
						<tr>
							<td><label for="integrity_check">Perform integrity check on backup files <a class="ithemes_tip" title=" - [Default: enabled] - By default each backup file is checked for integrity and completion the first time it is viewed on the Backup page.  On some server configurations this may cause memory problems as the integrity checking process is intensive.  If you are experiencing out of memory errors on the Backup file listing, you can uncheck this to disable this feature.">(?)</a>:</label></td>
							<td><?php $this->_addCheckBox('integrity_check', array( 'value' => 1, 'id' => 'integrity_check' ) ); ?> <label for="integrity_check">Enable integrity checking</label></td>						
						</tr>
					

						
						
						<tr><td colspan="3"><br /><b>Exclude Directories from Backup</b> <a class="ithemes_tip" title=" - If you would like specific directories excluded from your backups you may add them to the exclusion list.  This feature is only available in non-compatibility mode.">(?)</a></td></tr>
						<tr>
							<td>
								Click directories to navigate, <img src="<?php echo $this->_pluginURL; ?>/images/bullet_delete.png" style="vertical-align: -3px;" />to exclude directory. <a class="ithemes_tip" title=" - Click on a directory normally to navigate directories. Hold the control (Ctrl) button on your keyboard while clicking on a directory with your mouse to select a directory to exclude from being included in backups.  The selected directory will be added to the list to the right at the top of the list. /wp-content/ and /wp-content/uploads/ cannot be excluded.">(?)</a>:<br />
								<div id="exlude_dirs" class="jQueryOuterTree">
								</div>
								<small>Only available if your server doesn't require compatibility mode. <a class="ithemes_tip" title=" - If you receive notifications that your server is entering compatibility mode or that native zip functionality is unavailable then this feature will not be available due to technical limitations of the compatibility mode.  Ask your host to correct the problems causing compatibility mode or move to a new server.">(?)</a></small>
							</td>
							<td>
								Excluded directories (path relative to root <a class="ithemes_tip" title=" - List paths relative to root to be excluded from backups.  You may use the directory selector to the left to easily exclude directories by ctrl+clicking them.  Paths are relative to root. Ex: /wp-content/uploads/junk/">(?)</a>):<br />
								<?php
								if ( is_array( $this->_options['excludes'] ) ) {
									$exclude_dirs = implode( "\n", $this->_options['excludes'] );
								} else {
									$exclude_dirs = '';
								}
								$this->_addTextArea('exclude_dirs', array( 'wrap' => 'off', 'rows' => '4', 'cols' => '35', 'maxlength' => '9000', 'value' => $exclude_dirs ), true );
								?>
								<br /><small>List one path per line. Remove a line to remove exclusion.</small>
							</td>
						</tr>
						
						<tr>
							<td colspan="2" align="center">
								<p class="submit"><?php $this->_addSubmit( 'save', 'Save Settings' ); ?></p>
							</td>
						</tr>
												
						
					</table><br />
					
					<?php $this->_addUsedInputs(); ?>
					<?php wp_nonce_field( $this->_var . '-nonce' ); ?>
				</form>
			<?php
			echo '</div>';
		}

		
		/**
		 * iThemesBackupBuddy::view_scheduling()
		 *
		 * Displays settings form and values for viewing & editing.
		 *
		 */
		function view_scheduling() {
			
			$this->load();
			
			if (!empty($_POST['add_schedule'])) {
				$this->add_schedule();
			} elseif ( !empty( $_POST['delete_schedules'] ) ) {
				$this->delete_schedule();
			}
			
			// Load scripts and CSS used on this page.
			$this->admin_scripts();
			
			echo '<div class="wrap">';
			echo '<h2>Scheduling</h2>';
			
			$class = 'alternate';
			?>
			<br />
			<form id="posts-filter" enctype="multipart/form-data" method="post" action="<?php echo $this->_selfLink; ?>-scheduling">
			<?php $this->_addSubmit( 'delete_schedules', array( 'value' => 'Delete', 'class' => 'button-secondary delete' ) ); ?>
			
			<br /><br />
			<table class="widefat">
				<thead>
					<tr class="thead">
						<th scope="col" class="check-column"><input type="checkbox" class="check-all-entries" /></th>
						<th>Name</th>
						<th>Type</th>
						<th>Interval</th>
						<th>First Run</th>
						<th>Send File</th>
					</tr>
				</thead>
				<tfoot>
					<tr class="thead">
						<th scope="col" class="check-column"><input type="checkbox" class="check-all-entries" /></th>
						<th>Name</th>
						<th>Type</th>
						<th>Interval</th>
						<th>First Run</th>
						<th>Send File</th>
					</tr>
				</tfoot>
				<tbody>
			
			<?php

			$found_schedule = false;
			foreach ( (array) get_option('cron') as $id => $group ) {
				if (is_array($group) && array_key_exists('ithemes-backupbuddy-cron_schedule', $group)) { //( is_array($group['ithemes-backupbuddy-cron_schedule']) ) {
					foreach ( (array) $group['ithemes-backupbuddy-cron_schedule'] as $id2 => $group2 ) {
						$found_schedule = true;
						$scheduled = $this->_options['schedules'][$group2['args'][0]]; // This schedule item.
						?>
						<tr class="entry-row <?php echo $class; ?>" id="entry-<?php echo $id.'-'.$id2; ?>">
							<th scope="row" class="check-column">
								<input type="checkbox" name="schedules[]" class="entries" value="<?php echo $id.'-'.$id2; ?>" />
							</th>
							<td><?php echo $scheduled['name']; ?></td><td><?php echo $this->pretty_schedule_type($scheduled['type']); ?></td><td><?php echo $this->pretty_schedule_interval($scheduled['interval']); ?></td><td><?php echo $this->pretty_schedule_firstrun($scheduled['first_run']); ?></td>
							<td>
							<?php
								$got_ftp = false;
								$got_email = false;
								$got_aws = false;
								
								// DEPRECATED at v1.1.38
															if ( array_key_exists('send_ftp', $scheduled) && ($scheduled['send_ftp'] == '1') ) {
																echo 'FTP';
																$got_ftp = true;
															}
															if ( array_key_exists('send_email', $scheduled) && ($scheduled['send_email'] == '1') ) {
																if ($got_ftp == true) { echo ', '; }
																echo 'Email';
																$got_email = true;
															}
								// END DEPRECATED

								if ( array_key_exists('remote_send', $scheduled) ) {
									if ( $scheduled['remote_send'] == 'ftp' ) {
										echo 'FTP';
										$got_ftp = true;
									} elseif ( $scheduled['remote_send'] == 'aws' ) {
										echo 'Amazon S3';
										$got_aws = true;
									} elseif ( $scheduled['remote_send'] == 'email' ) {
										echo 'Email';
										$got_email = true;
									} else {
										echo 'None';
									}
								}
								
								
								if ( ($got_email != true) && ($got_ftp != true) && ($got_aws != true) ) {
									echo '<i>None</i>';
								}
								
								/*
								echo '<pre>';
								print_r( $scheduled );
								echo '</pre>';
								*/
							?>
							</td>
						</tr>
						<?php
						$class = ( $class === '' ) ? 'alternate' : '';
					}
				}
			}
			if ($found_schedule != true) {
				echo '<td><td colspan="6" align="center"><br /><i>There are currently no schedules backups.</i><br /><br /></td></tr>';
			}
			echo '</table><br />';
			$this->_addSubmit( 'delete_schedules', array( 'value' => 'Delete', 'class' => 'button-secondary delete' ) );
			echo '</form>';
			?>
			
			<br />
			<h2>Add New Scheduled Backup</h2>
				<img src="<?php echo $this->_pluginURL.'/images/bullet_error.png'; ?>" style="vertical-align: -3px;" /> Schedule backups without time overlap and during off-peak hours for optimal performance.<br /><br />
				<form enctype="multipart/form-data" method="post" action="<?php echo $this->_selfLink; ?>-scheduling">
					<?php wp_nonce_field( $this->_var . '-nonce' ); ?>
					<table class="form-table">
						<tr><th scope="row"><label for="name">Name for Backup Schedule:</label></th>
							<td><?php $this->_addTextBox( 'name' ); ?></td>
						</tr>
						<tr><th scope="row"><label for="type">Backup Type:</label></th>
							<td><?php $this->_addDropDown( 'type', array( 'db' => 'Database Only (default)', 'full' => 'Full Backup (Files + Database)' ) ); ?></td>
						</tr>
						<tr><th scope="row"><label for="interval">Backup Interval:</label></th>
							<td><?php $this->_addDropDown( 'interval', array( 'monthly' => 'Monthly', 'twicemonthly' => 'Twice Monthly', 'weekly' => 'Weekly', 'daily' => 'Daily', 'hourly' => 'Hourly' ) ); ?></td>
						</tr>
						<tr><th scope="row"><label for="name">Date/Time of First Run:</label></th>
							<td>						
								<input type="text" name="ithemes_datetime" id="ithemes_datetime" value="<?php echo date('m/d/Y h:i a',(time()+86400)); ?>"> Current server time: <?php echo date( 'm/d/Y h:i a e', time() ); ?>
								<br />
								<small>mm/dd/yyyy hh:mm [am/pm]</small>
							</td>
						</tr>
						<tr><th scope="row"><label for="send_ftp">Send file after backup*:</label></th>
							<td>
								<?php $this->_addDropDown( 'remote_send', array( 'none' => 'None', 'ftp' => 'FTP / FTPs', 'aws' => 'Amazon S3', 'email' => 'Email **' ) ); ?>
								<div id="ithemes-backupbuddy-deleteafter" style="display: none; background-color: #EAF2FA; border: 1px solid #E3E3E3; width: 250px; padding: 10px; margin: 5px; margin-left: 22px;">
									<?php $this->_addCheckBox( 'delete_after', '1' ); ?> Delete local backup file after sending.<br />
								</div>
							</td>
						</tr>
						

						
					</table>
	
					<p class="submit">
						<?php $this->_addSubmit( 'add_schedule', 'Add Schedule' ); ?>
					</p>
					
					<br /><br />
					
					<small>
						* Configure email and FTP in the <a href="<?php echo admin_url( "admin.php?page={$this->_var}-settings" ); ?>">Settings</a></a> section.<br />
						** Most email servers limit the file size of attachments to very small files. Full backups typically exceed this limit.<br />
					</small>
				</form>
			
			<?php
			echo '</div>';
		}

		
		/**
		 * iThemesBackupBuddy::pretty_schedule_type()
		 *
		 * Make this look pretty.
		 *
		 */
		function pretty_schedule_type($val) {
			if ($val == 'db') {
				return 'Database';
			} elseif ($val == 'full') {
				return 'Full';
			}
		}

		
		/**
		 * iThemesBackupBuddy::pretty_schedule_interval()
		 *
		 * Make this look pretty.
		 *
		 */
		function pretty_schedule_interval($val) {
			if ($val == 'monthly') {
				return 'Monthly';
			} elseif ($val == 'twicemonthly') {
				return 'Twice Monthly';
			} elseif ($val == 'weekly') {
				return 'Weekly';
			} elseif ($val == 'daily') {
				return 'Daily';
			} elseif ($val == 'hourly') {
				return 'Hourly';
			} else {
				return $val;
			}
		}

		
		/**
		 * iThemesBackupBuddy::pretty_schedule_firstrun()
		 *
		 * Make this look pretty.
		 *
		 */			
		function pretty_schedule_firstrun($val) {
			return date('m/d/Y h:i a',$val);
		}
	
	
		/**
		 * iThemesBackupBuddy::delete_schedule()
		 *
		 * Delete one or more scheduled backups
		 *
		 */			
		function delete_schedule() {
			$cron = get_option('cron');
			if ( ! empty( $_POST['schedules'] ) && is_array( $_POST['schedules'] ) ) {
				foreach ( (array) $_POST['schedules'] as $id_full ) {
					$id = explode('-', $id_full);
					// Remove from backupbuddy database.
					unset( $this->_options['schedules'][$cron[$id[0]]['ithemes-backupbuddy-cron_schedule'][$id[1]]['args'][0]] );
					$this->save();
					// Remote from CRON system in database.
					unset( $cron[$id[0]]['ithemes-backupbuddy-cron_schedule'][$id[1]] );
					update_option('cron', $cron); // Save Cron changes.
				}
			}
		}
		
		
		function add_schedule() {
			if ( empty( $_POST[$this->_var.'-name'] ) ) {
				$this->_errors[] = 'name';
				$this->_showErrorMessage( 'A name is required to create a new schedule.' );
			}
			/*elseif ( is_array( $this->_options['schedules'] ) ) {
				foreach ( (array) $this->_options['schedules'] as $id => $group ) {
					if ( $group['name'] == $name ) {
						$this->_errors[] = 'name';
						$this->_showErrorMessage( 'A schedule with that name already exists.' );
						break;
					}
				}
			} */
			
			//wp_schedule_event(time(), 'hourly', 'my_schedule_hook');
			//echo 'type: '.$_POST[$this->_var.'-type'];
			
			if ( isset( $this->_errors ) )
				$this->_showErrorMessage( 'Error: '.$this->_errors );
			else {
				if ( is_array( $this->_options['schedules'] ) && ! empty( $this->_options['schedules'] ) ) {
					$newID = max( array_keys( $this->_options['schedules'] ) ) + 1;
				} else {
					$newID = 0;
				}
				
				$first_run = strtotime($_POST['ithemes_datetime']);
				
				$this->_options['schedules'][$newID]['first_run'] = $first_run;
				$this->_options['schedules'][$newID]['name'] = $_POST[$this->_var . '-name'];
				$this->_options['schedules'][$newID]['interval'] = $_POST[$this->_var . '-interval'];
				$this->_options['schedules'][$newID]['type'] = $_POST[$this->_var . '-type'];
				
				if (!empty($_POST[$this->_var . '-delete_after'])) {
					$this->_options['schedules'][$newID]['delete_after'] = $_POST[$this->_var . '-delete_after'];
					$delete_after = 1;
				} else {
					$delete_after = 0;
				}
				
				// DEPRECATED at v1.1.38
										if (!empty($_POST[$this->_var . '-send_ftp'])) {
											$this->_options['schedules'][$newID]['send_ftp'] = $_POST[$this->_var . '-send_ftp'];
											$delete_after++;
										}
										if (!empty($_POST[$this->_var . '-send_email'])) {
											$this->_options['schedules'][$newID]['send_email'] = $_POST[$this->_var . '-send_email'];
											$delete_after++;
										}
				// END DEPRECATED
				
				$this->_options['schedules'][$newID]['remote_send'] = $_POST[$this->_var . '-remote_send'];
				if ( $_POST[$this->_var . '-remote_send'] != 'none' ) {
					$this->_options['schedules'][$newID]['delete_after'] = $_POST[$this->_var . '-delete_after'];
				}

				// If deleting after, change value to number of things that need sent before its triggered. FTP send or email send will decrement this number. Once number reaches 1, it deletes.
				if ( isset( $this->_options['schedules'][$newID]['delete_after'] ) && ($this->_options['schedules'][$newID]['delete_after'] == '1') ) {
					$this->_options['schedules'][$newID]['delete_after'] = $delete_after;
				}
				
				$this->save();
				
				wp_schedule_event($first_run, $_POST[$this->_var . '-interval'], $this->_var.'-cron_schedule', array($newID) );
			}
		}
		
		/**
		 * iThemesBackupBuddy::delete_files()
		 *
		 * Delete selected backup zip files(s).
		 *
		 */		
		function delete_files() {
			if ( ! empty( $_POST['files'] ) && is_array( $_POST['files'] ) ) {
				foreach ( (array) $_POST['files'] as $id ) {
					if ($id != '') {
						if ( file_exists( $this->_options['backup_directory'] . $id ) ) {
							unlink( $this->_options['backup_directory'] . $id );
							if ( file_exists( $this->_options['backup_directory'] . $id ) ) {
								$this->_errors[] = 'Deletion operation files on ' . $id . '.';
							}
						} else {
							//$this->_errors[] = 'File not found on ' . $id . '.';
						}
					}
				}
			}
			if ( is_array( $this->_errors ) ) {
				$this->_showErrorMessage( implode( '<br />', $this->_errors ) );
			} else {
				$this->_showStatusMessage( __( 'Selected files deleted.', $this->_var ) );
			}
		}

		
		/**
		 * iThemesBackupBuddy::show_backup_files()
		 *
		 * Displays listing of all backup files.
		 *
		 */		
		function show_backup_files() {
		
			if ( ! empty( $_POST['delete_file'] ) ) {
				$this->delete_files();
			} elseif ( ! empty( $_POST['email_zip'] ) ) {
				$attachments = array(WP_CONTENT_DIR . '/uploads/backupbuddy_backups/'.$_POST['file']);
				wp_schedule_single_event(time()+4, $this->_var.'-cron_email', array($_POST['email'], 'BackupBuddy ZIP File', 'Attached is the BackupBuddy generated ZIP file that you requested on the site '.get_option('siteurl'), $attachments));
				$this->_showStatusMessage('The file has been queued for emailing to '.htmlentities($_POST['email']).'.  You should receive it shortly.');
			} elseif ( ! empty( $_POST['ftp_zip'] ) ) {
				$attachments = array(WP_CONTENT_DIR . '/uploads/backupbuddy_backups/'.$_POST['file']);
				wp_schedule_single_event(time()+4, $this->_var.'-cron_ftp', array($_POST['ftp_server'], $_POST['ftp_user'], $_POST['ftp_pass'], $_POST['ftp_path'], $this->_options['backup_directory'].$_POST['file'], $_POST['ftp_type'] ));
				
				//$this->cron_ftp( $_POST['ftp_server'], $_POST['ftp_user'], $_POST['ftp_pass'], $_POST['ftp_path'], $_POST['file'] );
				
				$this->_showStatusMessage('The file has been queued for uploading to '.htmlentities($_POST['ftp_server']).' by ' . htmlentities( $_POST['ftp_type'] ) . '.  It should be uploaded shortly.');
			} elseif ( ! empty( $_POST['aws_zip'] ) ) {
				$attachments = array(WP_CONTENT_DIR . '/uploads/backupbuddy_backups/'.$_POST['file']);
				//wp_schedule_single_event(time()+4, $this->_var.'-cron_aws', array($_POST['aws_accessley'], $_POST['aws_secretkey'], $_POST['aws_bucket'], $this->_options['backup_directory'].$_POST['file']));
				//echo 'yo'.$this->_options['backup_directory'].$_POST['file'].'yoz';
				$this->cron_aws($_POST['aws_accesskey'], $_POST['aws_secretkey'], $_POST['aws_bucket'], $_POST['aws_directory'], $this->_options['backup_directory'].$_POST['file']);
				
				$this->_showStatusMessage('The file has been queued for uploading to '.htmlentities($_POST['ftp_server']).' by Amazon S3.  It should be uploaded shortly.');
			}
			?>
			<br />
			
			
				

							
							<?php
							if ( !file_exists($this->_options['backup_directory']) ) {
								$this->mkdir_recursive($this->_options['backup_directory']);

								if (is_writable($this->_options['backup_directory'])) {

						
									// Dummy files to prevent directory listings.
									$fh = fopen($this->_options['backup_directory'].'/index.php', 'a');
									fwrite($fh, '<html></html>');
									fclose($fh);
									unset($fh);
									
									$fh = fopen($this->_options['backup_directory'].'/index.htm', 'a');
									fwrite($fh, '<html></html>');
									fclose($fh);
									unset($fh);
								
								} else {
									echo 'ERROR: The backup directory is not writable. Please verify the directory has write permissions.';
								}
								
								
								$fh = fopen($this->_options['backup_directory'].'/.htaccess', 'a'); // Prevent directory listing.
								fwrite($fh, "IndexIgnore *\n");
								/*
								fwrite($fh, "AuthType Basic\n");
								fwrite($fh, 'AuthName "Restricted"\n');
								fwrite($fh, "AuthUserFile ".$this->_options['backup_directory'].".htpasswd\n");
								fwrite($fh, "Require valid-user\n");
								*/
								fclose($fh);
								unset($fh);
								/*
								$fh = fopen($this->_options['backup_directory'].'/.htpasswd', 'a');
								fwrite($fh, "backupbuddy:".$this->htpasswd($this->_options['password'])."\n");
								fclose($fh);
								unset($fh);
								*/

							}
							
							$this->_set_greedy_script_limits();
							
							$found_file = false;
							$handler = opendir($this->_options['backup_directory']);
							$file_i=0;
							$file_type = 'unknown';
							while ($file = readdir($handler)) {
								if ($file != '.' && $file != '..' && substr($file, 0, 6) == 'backup' ) {
									unset( $this->_errors );
									$found_file = true;
									
									if ( $this->_options['integrity_check'] != '1' ) {
										$this->_errors[] = 'unknown <a class="ithemes_tip" title=" - Backup File integrity checking is disabled on the Settings page.  Backup file status and type are unavailable while this option is disabled.">(?)</a>';
									} else {
										// Verify backup file integrity.
										if ( substr( $file, -1 ) != '-' ) {
											require_once( ABSPATH . 'wp-admin/includes/class-pclzip.php' );
											$zip = new PclZip($this->_options['backup_directory'] . '/' . $file);
											if (($list = $zip->listContent()) == 0) { // CORRUPT ZIP
												//die("Error : ".$zip->errorInfo(true));
												//$this->_showErrorMessage( __( 'Zip file error for '.$file.'. The backup may still be in progress for this file:<br />'. $zip->errorInfo(true), $this->_var ) );
												$this->_errors[] = 'Invalid Zip Format or In Progress <a class="ithemes_tip" title=" - This backup ZIP file was not able to be successfully read to check its integrity.  The backup may still be in process or the backup failed without completing the file. Please manually verify this file before relying on it.">(?)</a>';
											} else {
												$found_dat = false;
												$found_sql = false;
												$found_wpc = false;
												$file_type = '';
												
												$describe_dat = 'The BackupBuddy configuration file that is stored in the ZIP backup has a problem. The backup may have failed or is still backing up.';
												$describe_sql = 'The database file that is stored within this ZIP backup has a problem. This backup will not restore your database which includes post content, settings, etc.';
												$describe_wpc = 'The main WordPress configuration file, wp-config.php, stored within this ZIP backup has a problem. This backup may be missing required WordPress files and be highly unreliable for file restoration.';
												
												// TODO: After old zip file names are phased out then we can remove checking for posb. This is set to handle both old filenames and the new one where dashes separate sections for the url.
												$posa = strrpos($file,'_')+1;
												$posb = strrpos($file,'-')+1;
												if ( $posa < $posb ) {
													$zip_id = $posb;
													$zip_id = strrpos($file,'-')+1;
												} else {
													$zip_id = $posa;
													$zip_id = strrpos($file,'_')+1;
												}
												
												
												$zip_id = substr( $file, $zip_id, strlen($file)-$zip_id-4 );
												for ($i=0; $i<sizeof($list); $i++) {
													//echo 'loop';
													if ( $list[$i]['filename'] == 'wp-content/uploads/temp_'.$zip_id.'/backupbuddy_dat.php' ) {
														$found_dat = true;
														$file_type = 'full';
														if ( $list[$i]['size'] == 0 ) {
															$this->_errors[] = 'BackupBuddy DAT file is empty. (Err 2234534)<br />';
														}
													} elseif ( $list[$i]['filename'] == 'backupbuddy_dat.php' ) {
														$found_dat = true;
														$file_type = 'db';
														if ( $list[$i]['size'] == 0 ) {
															$this->_errors[] = 'BackupBuddy DAT file is empty. (Err 16892534)<br />';
														}
													} elseif ( $list[$i]['filename'] == 'wp-content/uploads/temp_'.$zip_id.'/db.sql' ) {
														$found_sql = true;
														$file_type = 'full';
														if ( $list[$i]['size'] == 0 ) {
															$this->_errors[] = 'Database SQL file is empty. (Err 9882534)<br />';
														}
													} elseif ( $list[$i]['filename'] == 'db.sql' ) {
														$found_sql = true;
														$file_type = 'db';
														if ( $list[$i]['size'] == 0 ) {
															$this->_errors[] = 'Database SQL file is empty. (Err 5492534)<br />';
														}
													} elseif ( $list[$i]['filename'] == 'wp-config.php' ) {
														$found_wpc = true;
														$file_type = 'full';
														if ( $list[$i]['size'] == 0 ) {
															$this->_errors[] = 'WordPress config file (wp-config.php) is empty. (Err 5492534)<br />';
														}
													}
												}
												if ( $found_dat == false ) { $this->_errors[] = 'BackupBuddy DAT file is missing. <a class="ithemes_tip" title=" - '.$describe_dat.'">(?)</a><br />'; }
												if ( $found_sql == false ) { $this->_errors[] = 'Database SQL file is missing. <a class="ithemes_tip" title=" - '.$describe_sql.'">(?)</a><br />'; }
												if ( $file_type == 'full' ) {
													if ( $found_wpc == false ) { $this->_errors[] = 'WordPress config file, wp-config.php, missing. <a class="ithemes_tip" title=" - '.$describe_wpc.'">(?)</a><br />'; }
												}
											}
											unset( $zip );
											//print_r( $this->_errors );
										}
										if ( $file_type == 'full' ) {
											$file_type = 'Full';
										} elseif ( $file_type == 'db' ) {
											$file_type = 'Database';
										} else {
											$file_type = 'Unknown';
										}
										// End file integrity check
									}
									
									
									// 0	=>	File modified time
									// 1	=>	Filename
									// 2	=>	Backup status
									// 3	=>	Backup type
									// 4	=>	Filesize
									$file_mod_time = filemtime($this->_options['backup_directory'].$file);
									$files[ $file_mod_time ] = array(
										date($this->_timestamp, $file_mod_time),
										$file,
										$this->_pretty_backup_status(),
										$file_type,
										number_format((filesize($this->_options['backup_directory'].$file) / 1048576),2),
									);
								}
							}
							closedir($handler);
							unset($handler);

							if ($found_file) {
								sort($files, SORT_NUMERIC);
								?>
								
								
								<form id="posts-filter" enctype="multipart/form-data" method="post" action="<?php echo $this->_selfLink; ?>-backup">
								<div class="tablenav">
									<div class="alignleft actions">
										<?php $this->_addSubmit( 'delete_file', array( 'value' => 'Delete', 'class' => 'button-secondary delete' ) ); ?>
									</div>
									<br class="clear" />
								</div>
								
								<table class="widefat" style="min-width: 600px;">
								<thead>
									<tr class="thead">
										<th scope="col" class="check-column"><input type="checkbox" id="check-all-groups" /></th>
										<th style="white-space: nowrap">Backup File</th>
										<th style="white-space: nowrap">Last Modified</th>
										<th style="white-space: nowrap">File Size</th>
										<th style="white-space: nowrap">Status</th>
										<th style="white-space: nowrap">Type</th>
										<th style="white-space: nowrap">Send*</th>
									</tr>
								</thead>
								<tfoot>
									<tr class="thead">
										<th scope="col" class="check-column"><input type="checkbox" id="check-all-groups" /></th>
										<th>Backup File</th>
										<th>Last Modified</th>
										<th>File Size</th>
										<th>Status</th>
										<th>Type</th>
										<th>Send*</th>
									</tr>
								</tfoot>
								<tbody>
									<tr style="background-color: #ffd000;">
									<?php
								
								foreach ($files as $file) {
									//$found_file = true;
									$file_i++;
									echo '<tr><th scope="row" class="check-column"><input type="checkbox" name="files[]" class="files" value="'.$file[1].'" id="file_name_'.$file_i.'" /></th><td style="white-space: nowrap"><a href="'.get_option('siteurl') . '/wp-content/uploads/backupbuddy_backups/' . $file[1] . '">'.$file[1].'</a></td><td style="white-space: nowrap">'.$file[0].'</td><td>'. $file[4] .' MB</td><td>' . $file[2] . '</td><td>' . $file[3] . '</td><td  style="white-space: nowrap"><a href="ithemes-ftp_pop" id="ftp_pop_'.$file_i.'" class="ithemes_pop ithemes_ftp_pop"><img src="'.$this->_pluginURL.'/images/server_go.png" style="vertical-align: -3px;" title="Send file by FTP" /></a> <a href="ithemes-aws_pop" id="aws_pop_'.$file_i.'" class="ithemes_pop ithemes_aws_pop"><img src="'.$this->_pluginURL.'/images/aws.gif" style="vertical-align: -3px;" title="Send file by Amazon S3" /></a> <a href="ithemes-email_pop" id="email_pop_'.$file_i.'" class="ithemes_pop ithemes_email_pop"><img src="'.$this->_pluginURL.'/images/email_go.png" style="vertical-align: -3px;" title="Send file by Email" /></a></td></tr>';
									//$this->_errors = array();
								}
								

								?>
											</tr>
										</tbody>
									</table>
									<small>* Large files are not compatible with all email servers & can cause PHP to time out based on server configuration.</small>
					
									<div class="tablenav">
										<div class="alignleft actions">
											<?php $this->_addSubmit( 'delete_file', array( 'value' => 'Delete', 'class' => 'button-secondary delete' ) ); ?>
										</div>
										<br class="clear" />
									</div>
									</form><br />
								<?php

								
								
								
							} else {
								echo '<i>You do not have any backup archives stored yet.</i><br /><br /><br />';
							}


							?>
						
				
			<a href="<?php echo admin_url('admin-ajax.php').'?action=backupbuddy_importbuddy&pass='.md5($this->_options['password']); ?>">Download importbuddy.php</a>
			<div id="ithemes-email_pop" class="ithemes-popup" style="text-align: center;">
				<center><h3>Email Address:</h3></center>
				<form method="post" action="admin.php?page=ithemes-backupbuddy-backup">
					<input type="hidden" name="email_zip" value="true" />
					<input type="hidden" name="file" value="NULL" id="email_file_name" />
					<input type="text" name="email" value="<?php echo $this->_options['email']; ?>" style="width: 200px;" />
					<p class="submit"><input value="Send File" type="submit" name="backup" class="button-primary" /></p>
				</form>
			</div>
			<div id="ithemes-ftp_pop" class="ithemes-popup" style="text-align: center;">
				<center><h3>FTP Connection:</h3></center>
				<form method="post" action="admin.php?page=ithemes-backupbuddy-backup">
					<input type="hidden" name="ftp_zip" value="true" />
					<input type="hidden" name="file" value="NULL" id="ftp_file_name" />
					
					<table class="form-table">
						<tr><td><label for="ftp_server">FTP Server:</label></td><td><input type="text" name="ftp_server" value="<?php echo $this->_options['ftp_server']; ?>" style="width: 200px;" /></td></tr>
						<tr><td><label for="ftp_user">FTP User:</label></td><td><input type="text" name="ftp_user" value="<?php echo $this->_options['ftp_user']; ?>" style="width: 200px;" /></td></tr>
						<tr><td><label for="ftp_pass">FTP Pass:</label></td><td><input type="password" name="ftp_pass" value="<?php echo $this->_options['ftp_pass']; ?>" style="width: 200px;" /></td></tr>
						<tr><td><label for="ftp_path">FTP Path:</label></td><td><input type="text" name="ftp_path" value="<?php echo $this->_options['ftp_path']; ?>" style="width: 200px;" /></td></tr>
						<tr><td><label for="ftp_path">FTP Type:</label></td><td>
							<select name="ftp_type" value="" style="width: 200px;" />
								<option value="ftp" <?php if ( isset( $this->_options['ftp_type'] ) && $this->_options['ftp_type'] == 'ftp' ) { echo 'selected'; } ?>>FTP</option>
								<option value="ftps" <?php if ( isset( $this->_options['ftp_type'] ) && $this->_options['ftp_type'] == 'ftp' ) { echo 'selected'; } ?>>FTPs (SSL)</option>
							</select>
						</td></tr>
					</table>
					
					<p class="submit"><input value="Send File to FTP" type="submit" name="backup" class="button-primary" /></p>
				</form>
			</div>
			<div id="ithemes-aws_pop" class="ithemes-popup" style="text-align: center;">
				<center><h3>Amazon S3 Connection:</h3></center>
				<form method="post" action="admin.php?page=ithemes-backupbuddy-backup">
					<input type="hidden" name="aws_zip" value="true" />
					<input type="hidden" name="file" value="NULL" id="aws_file_name" />
					
					<table class="form-table">
						<tr><td><label for="aws_accesskey">AWS Access Key:</label></td><td><input type="text" name="aws_accesskey" value="<?php echo $this->_options['aws_accesskey']; ?>" style="width: 200px;" /></td></tr>
						<tr><td><label for="aws_secretkey">AWS Secret Key:</label></td><td><input type="password" name="aws_secretkey" value="<?php echo $this->_options['aws_secretkey']; ?>" style="width: 200px;" /></td></tr>
						<tr><td><label for="aws_bucket">Bucket Name:</label></td><td><input type="text" name="aws_bucket" value="<?php echo $this->_options['aws_bucket']; ?>" style="width: 200px;" /></td></tr>
						<tr><td><label for="aws_directory">Directory Name:</label></td><td><input type="text" name="aws_directory" value="<?php echo $this->_options['aws_directory']; ?>" style="width: 200px;" /></td></tr>
					</table>
					
					<p class="submit"><input value="Send File to S3" type="submit" name="backup" class="button-primary" /></p>
				</form>
			</div>

			<?php
		}


		function _pretty_backup_status() {
			$string = '';
			if ( isset( $this->_errors ) ) {
				foreach( $this->_errors as $error ) {
					$string .= $error;
				}
				$string = '<span style="color: #DE4E21;">' . $string . '</span>';
			} else {
				$string = 'Good';
			}
			return $string;
			
			//return print_r($this->_errors);
		}
		
		/**
		 * iThemesBackupBuddy::cron_email()
		 *
		 * Begin process of backing up.
		 *
		 * @param	$to				string		Email address to send email to.
		 * @param	$subject		string		Subject of email.
		 * @param	$message		string		Email message body.
		 * @param	$attachments	string[]	Array of file paths of attachments to send.
		 * DEPRICATED:	@param	$from			string		Optional return email address.
		 *
		 */
		function cron_email($to, $subject, $message, $attachments, $delete_after_int = 0) {
			$headers = 'From: BackupBuddy <' . get_option('admin_email') . '>' . "\r\n\\";
			wp_mail($to, $subject, $message, $headers, $attachments);
			
			// Decrement this each sending operation. If we reach 1 then time to delete file since last send is done.
			$delete_after_int = $delete_after_int - 1;
			if ( $delete_after_int == 1 ) {
				unlink( $attachments );
			}
		}

		/**
		 * iThemesBackupBuddy::cron_add_schedules()
		 *
		 * Add additional scheduling intervals to WordPress cron system.
		 *
		 */
		function cron_add_schedules( $schedules = array() ) {
			$schedules['weekly'] = array( 'interval' => 604800, 'display' => 'Once Weekly' );
			$schedules['twicemonthly'] = array( 'interval' => 604800, 'display' => 'Twice Monthly' );
			$schedules['monthly'] = array( 'interval' => 2592000, 'display' => 'Once Monthly' );
			return $schedules;
		}
		
		
		/**
		 * iThemesBackupBuddy::cron_schedule()
		 *
		 * Handle schedules jobs.
		 *
		 */
		function cron_schedule($item) {
			$this->load();
			
			if ( is_array($this->_options['schedules'][$item]) ) {
				$scheduled = $this->_options['schedules'][$item];
				$this->_isScheduled = true; // Set this variable so manual email notification doesnt get sent.
				
				$this->_options['last_backup'] = 0; // Clear this so scheduled backups dont get blocked.
				
				$this->_backup( $scheduled['type'] );
				
				if ( ( isset( $this->_options['email_notify_scheduled'] ) ) && ( $this->_options['email_notify_scheduled'] ) ) {
					//if ( ( !isset( $this->_options['email_notify_manual'] ) ) || ( !$this->_options['email_notify_manual'] ) ) { // If manual backups isnt set OR manual backups are off then send schedule email.  Manual emailing is always triggered so only send schedule email if manual emails is not also checked.
					$this->mail_notice('Scheduled backup "'.$scheduled['name'].'" completed.');
					//}
				}

				if ( isset( $scheduled['delete_after'] ) ) {
					$delete_after_int = $scheduled['delete_after'];
				} else {
					$delete_after_int = 0; // dont delete file
				}
				
				
				// NEW AT v1.1.38
						$got_ftp = false;
						$got_email = false;
						$got_aws = false;
						
						// DEPRECATED at v1.1.38
													if ( array_key_exists('send_ftp', $scheduled) && ($scheduled['send_ftp'] == '1') ) {
														$got_ftp = true;
													}
													if ( array_key_exists('send_email', $scheduled) && ($scheduled['send_email'] == '1') ) {
														if ($got_ftp == true) { echo ', '; }
														$got_email = true;
													}
						// END DEPRECATED

						if ( array_key_exists('remote_send', $scheduled) ) {
							if ( $scheduled['remote_send'] == 'ftp' ) {
								$got_ftp = true;
							} elseif ( $scheduled['remote_send'] == 'aws' ) {
								$got_aws = true;
							} elseif ( $scheduled['remote_send'] == 'email' ) {
								$got_email = true;
							} else {
								echo 'None';
							}
						}
				// END NEW
				
				
				
				if ( $got_email ) {
					//function cron_email($to, $subject, $message, $attachments) {
					$this->cron_email($this->_options['email'],'Scheduled backup file.','Attached to this email is the backup file for the scheduled backup "'.$scheduled['name'].'"' . "\n\nFile: ".$this->zip_file."\n\nBackupBuddy by http://pluginbuddy.com", $this->zip_file, $scheduled['delete_after']);
				}
				if ( $got_ftp ) {
					if ( array_key_exists('ftp_type', $scheduled) ) {
						$ftp_type = $this->_options['ftp_type'];
					} else {
						$ftp_type = 'ftp'; // handle deprecated old schedules
					}
					$this->cron_ftp($this->_options['ftp_server'], $this->_options['ftp_user'], $this->_options['ftp_pass'], $this->_options['ftp_path'], $this->zip_file, $ftp_type, $scheduled['delete_after']);
				}
				if ( $got_aws ) {
					//cron_aws($aws_accesskey, $aws_secretkey, $aws_bucket, $file, $delete_after_int = 0)
					$this->cron_aws($this->_options['aws_accesskey'], $this->_options['aws_secretkey'], $this->_options['aws_bucket'], $this->_options['aws_directory'], $this->zip_file, $scheduled['delete_after']);
				}
			}
		}
		
		
		function _generate_serial() {
			return $this->rand_string( 10 );
		}
		
		
		function _get_zip_name( $serial ) {
			$siteurl = get_bloginfo("siteurl");
			$siteurl = str_replace( 'http://', '', $siteurl );
			$siteurl = str_replace( 'https://', '', $siteurl );
			$siteurl = str_replace( '/', '_', $siteurl );
			$siteurl = str_replace( '\\', '_', $siteurl );
			$siteurl = str_replace( '.', '_', $siteurl );
			//echo 'url:'. $siteurl . '!<br />';
			return $this->_options['backup_directory'] . 'backup-' . $siteurl . '-' . str_replace( '-', '_', date( 'Y-m-d' ) ) . "-$serial.zip";
		}
		
		
		function _get_storage_directory( $serial, $create_new = false ) {
			//$upload_dir = wp_upload_dir();
			//$upload_path = $upload_dir['basedir'];
			
			// Sadly we currently must use this location for uploads and ignore custom uploads folders due to importbuddy limitations.
			$upload_path = ABSPATH . 'wp-content/uploads';
			
			$storage_directory = "$upload_path/temp_$serial";
			
			if ( ! file_exists( $storage_directory ) ) {
				if ( false === $this->mkdir_recursive( $storage_directory ) ) {
					echo '<br />Unable to create temporary storage directory (' . $storage_directory . '). Error #234x43.<br />';
					return new WP_Error( 'write_failure', 'Unable to create temporary storage directory' );
				}
			}
			
			return $storage_directory;
		}
		
		
		function _pre_backup( $type ) {
		
			// Give the script enough memory and time to work properly
			$this->_set_greedy_script_limits();
			
			// Set up backup variables
			$serial = $this->_generate_serial();
			$zip_file = $this->_get_zip_name( $serial );
			$storage_directory = $this->_get_storage_directory( $serial, true );
			
			// Verify that the temporary storage directory exists
			if ( is_wp_error( $storage_directory ) ) {
				// Without this, this just fails with all kinds of errors.  Added this alert as a quick fix for this issue.
				// TODO: Better error handling here.
				echo 'Unknown Error #35565654. Contact PluginBuddy support.';
				die();
				return $storage_directory;
			} elseif ( ! file_exists( $storage_directory ) ) {
				return new WP_Error( 'write_failure', 'Unable to create temporary storage directory' );
			}
			
			// Create the backup directory if it doesn't exist
			if ( ! file_exists( $this->_options['backup_directory'] ) ) {
				if ( false === $this->mkdir_recursive( $this->_options['backup_directory'] ) )
					return new WP_Error( 'write_failure', 'Unable to create backup directory' );
			}
			
			// Verify that the backup zip file can be written to
			if ( false === ( $file_handle = fopen( $zip_file, 'w' ) ) )
				return new WP_Error( 'write_failure', 'Unable to create backup file' );
			fclose( $file_handle );
			
			
			// TODO: Add code to handle incomplete processes
			unset( $this->_options['backup_status'] );
			
			
			// Set up the backup status information
			if ( empty( $this->_options['backup_status'] ) ) {
				$this->_options['backup_status'] = array(
					'type'				=> $type,
					'serial'			=> $serial,
					'zip_file'			=> $zip_file,
					'storage_directory'	=> $storage_directory,
					'processes'			=> array(),
					'start_time'		=> time(),
					'last_update_time'	=> time(),
					'last_process'		=> '',
					'errors'			=> array(),
				);
				
				$this->save();
			}
			
			// Set up the processes that will be run based on type
			$this->_processes = array(
				'settings'	=> array(
					'name'				=> 'Settings Backup',
					'description'		=> 'Creating site settings backup...',
					'function'			=> '_create_settings_file',
					'time_limit'		=> 5 * 60, // 5 minutes
					'number'			=> 2,
				),
				'database'	=> array(
					'name'				=> 'Database Backup',
					'description'		=> 'Creating database backup...',
					'function'			=> '_create_database_backup_file',
					'time_limit'		=> 3 * 60 * 60, // 3 hours
					'number'			=> 3,
				),
			);
			
			if ( 'db' === $type ) {
				$this->_processes['archive'] = array(
					'name'				=> 'Database Backup',
					'description'		=> 'Storing database backup to a single archive...',
					'function'			=> '_create_database_backup',
					'time_limit'		=> 4 * 60 * 60, // 4 hours
					'number'			=> 4,
				);
			}
			else {
				$this->_processes['archive'] = array(
					'name'				=> 'Full Backup',
					'description'		=> 'Storing full backup to a single archive...',
					'function'			=> '_create_full_backup',
					'time_limit'		=> 8 * 60 * 60, // 8 hours
					'number'			=> 4,
				);
			}
			
			// Set up the process statuses
			$this->_statuses = array(
				'pre_run'		=> array(
					'description'	=> 'Process has not started yet',
					'error'			=> false,
				),
				'running'		=> array(
					'description'	=> 'Process is still running',
					'error'			=> false,
				),
				'complete'		=> array(
					'description'	=> 'Process completed successfully',
					'error'			=> false,
				),
				'timed_out'		=> array(
					'description'	=> 'Process took too long to complete',
					'error'			=> true,
				),
				'failed'		=> array( // This is a temp status message and should be replaced by more specific status codes and messages
					'description'	=> 'Process failed to complete successfully',
					'error'			=> true,
				),
			);
		}
		
		
		function _post_backup( $type ) {
			// Remove the temporary storage directory
			if ( ! empty( $this->_options['backup_status']['storage_directory'] ) && file_exists( $this->_options['backup_status']['storage_directory'] ) ) {
				if ( false === $this->_delete_directory( $this->_options['backup_status']['storage_directory'] ) ) {
					echo "<p class='bb-error'>Unable to remove temporary storage directory</p>\n";
					echo "<p class='bb-error'>The following directory should be manually removed: <strong><code>{$this->_options['backup_status']['storage_directory']}</code></strong></p>\n";
				}
			}
			
			// Remove the backup status so that another backup can run
			if ( isset( $this->_options['backup_status'] ) ) {
				$this->zip_file = $this->_options['backup_status']['zip_file']; // Set last filename for use for cron mailing, etc.
				unset( $this->_options['backup_status'] );
				
				$this->save();
			}
		}
		
		
		/**
		 * iThemesBackupBuddy::backup()
		 *
		 * Begin process of backing up.
		 *
		 *	@param	$step	integer	Numeric step number.
		 *	@param	$type	string	Optional backup type for CRON jobs. Use querystring for http based backups.
		 *	@param	$get_zip_id	int	ID number if passed in querystring.
		 *
		 */
		function _backup( $type ) {
			if ( ! empty( $_GET['reset_last_backup'] ) ) {
				$this->_options['last_backup'] = 0; // Reset last backup time to allow it to run.
			}
			
			// Set up everything needed for the backup processes
			$this->_pre_backup( $type );
			
			// Run backup processes
			$this->_run_backup_processes();
			
			// Clean up after the backup processes
			$this->_post_backup( $type );
			
			// Show backed-up files
			$this->show_backup_files();
		}
		
		function _run_backup_processes() {
//			$this->_start_time = time();
			
			foreach ( (array) $this->_processes as $process => $process_options ) {
				if ( empty( $this->_options['backup_status']['processes'][$process] ) ) {
					$this->_options['backup_status']['processes'][$process] = array(
						'status'	=> 'pre_start',
					);
				}
			}
			
			?>
			<script type="text/javascript">
				backupbuddy_timer = window.setTimeout('backupbuddy_timeout()', 1800000);
				function backupbuddy_timeout() { alert('WARNING! The backup is taking an excessive amount of time (30+ minutes).  Verify your browser is still actively loading the page since the backup may have failed.'); }
			</script>
			<?php

			
			foreach ( (array) $this->_processes as $process => $process_options ) {
				$current_time = time();
				
				$this->_options['backup_status']['last_update_time'] = $current_time;
				$this->_options['backup_status']['last_process'] = $process;
				
				if ( 'pre_start' === $this->_options['backup_status']['processes'][$process]['status'] ) {
					$this->_options['backup_status']['processes'][$process] = array(
						'start_time'		=> $current_time,
						'last_update_time'	=> $current_time,
						'status'			=> 'running',
					);
					
				}
				
				// If process status is not 'running', skip to next process
				if ( 'running' !== $this->_options['backup_status']['processes'][$process]['status'] ) {
					$this->save();
					
					continue;
				}
				
				$this->_options['backup_status']['processes'][$process]['last_update_time'] = $current_time;
				
				$total_time = $current_time - $this->_options['backup_status']['processes'][$process]['start_time'];
				
				// Don't permit the process to exceed its time limit
				if ( isset( $process_options['time_limit'] ) && ( $process_options['time_limit'] > 0 ) && ( $total_time > $process_options['time_limit'] ) ) {
					$this->_options['backup_status']['processes'][$process]['status'] = 'timed_out';
					$this->save();
					
					continue;
				}
				
				
				//echo 'num:'.$process_options['number'];
				echo '<script type="text/javascript">';
				// Starting this step, color its background and text
				echo "	jQuery('#backupbuddy_step".$process_options['number']."').css({'color':'#000000','background-color':'transparent'});";
				echo "	jQuery('#backupbuddy_step".$process_options['number']."').append('&nbsp;<img id=\"backupbuddy_loading".$process_options['number']."\" src=\"".$this->_pluginURL."/images/loading.gif\" style=\"vertical-align: 0px;\" />');";
				echo "\n";
				// Finished previous step, add checkmark.
				echo "	jQuery('#backupbuddy_loading". ($process_options['number']-1) ."').hide();";
				echo "	jQuery('#backupbuddy_step". ($process_options['number']-1) ."').append('&nbsp;<img src=\"".$this->_pluginURL."/images/tick.png\" style=\"vertical-align: -3px;\" />');";
				
				
				
				echo '</script>';
				
				echo "<p>{$process_options['description']}</p>\n";
				flush();
				
				// Run the process
				call_user_func_array( array( &$this, $process_options['function'] ), array( $process ) );
				
				// Save any changes added by process
				$this->save();
			}
			
			echo '<script type="text/javascript">';
			// finished. Color done step.
			echo "	jQuery('#backupbuddy_step5').css({'color':'#000000','background-color':'transparent'});";
			echo "\n";
			// Finished. Checkmark previous and done step.
			echo "	jQuery('#backupbuddy_loading4').hide();";
			echo "	jQuery('#backupbuddy_step4').append('&nbsp;<img src=\"".$this->_pluginURL."/images/tick.png\" style=\"vertical-align: -3px;\" />');";
			echo "	jQuery('#backupbuddy_step5').append('&nbsp;<img src=\"".$this->_pluginURL."/images/tick.png\" style=\"vertical-align: -3px;\" />');";
			
			echo '	clearTimeout(backupbuddy_timer);';
			
			echo '</script>';
			
			$this->_showStatusMessage( __( 'Backup complete.', $this->_var ) );
			if ( !isset( $this->_isScheduled ) ) {
				if ( ( isset( $this->_options['email_notify_manual'] ) ) && ( $this->_options['email_notify_manual'] ) ) {
					$this->mail_notice('Manual backup completed on site ' . get_option( 'siteurl' ) . '.');
				}
			}
		}
		
		/**
		 * iThemesBackupBuddy::_create_settings_file()
		 *
		 * Create .dat (now .php for security) file holding meta information.
		 *
		 */
		function _create_settings_file( $process ) {
			global $wpdb;
			
			
			$storage_directory = $this->_options['backup_status']['storage_directory'];
			$settings_file = "$storage_directory/backupbuddy_dat.php";
			
			
			$settings = array(
				// Store information about the plugin version and time
				'backupbuddy_version'		=> $this->_version,
				'backup_time'				=> date( 'Y-m-d H:i:s' ),
				'backup_type'				=> $_GET['type'],
				
				// Save details about site's WordPress setup
				'abspath'					=> ABSPATH,
				'siteurl'					=> get_option( 'siteurl' ),
				'home'						=> get_option( 'home' ),
				'blogname'					=> get_option( 'blogname' ),
				'blogdescription'			=> get_option( 'blogdescription' ),
				
				// Add the database details
				'db_name'					=> DB_NAME,
				'db_user'					=> DB_USER,
				'db_prefix'					=> $wpdb->prefix,
				'db_server'					=> DB_HOST,
				'db_password'				=> DB_PASSWORD,		// TODO: If mcrypt is installed, then encrypt this
			);
			
			
			if ( false === ( $file_handle = fopen( $settings_file, 'w' ) ) ) {
				$this->_options['backup_status']['processes'][$process]['status'] = 'failed';
				
				$error =& new WP_Error( 'write_failure', 'Unable to write Settings File' );
				
				echo '<div class="bb-error">' . $error->get_error_message() . "</div>\n";
				return $error;
			}
			
			fwrite( $file_handle, serialize( $settings ) );
			fclose( $file_handle );
			
			
			$this->_options['backup_status']['processes'][$process]['status'] = 'complete';
			
			return true;
		}
		
		
		/**
		 * iThemesBackupBuddy::backup_db_create_database_backup_file()
		 *
		 * Backup database.
		 *
		 */
		function _create_database_backup_file( $process ) {
			global $wpdb;
			
			
			$storage_directory = $this->_options['backup_status']['storage_directory'];
			$database_file = "$storage_directory/db.sql";
			
			
			if ( false === ( $file_handle = fopen( $database_file, 'w' ) ) ) {
				$this->_options['backup_status']['processes'][$process]['status'] = 'failed';
				
				$error =& new WP_Error( 'write_failure', 'Unable to write Database File' );
				
				echo '<div class="bb-error">' . $error->get_error_message() . "</div>\n";
				return $error;
			}
			
			
			flush();
			$server = DB_HOST;
			$user = DB_USER;
			$pass = DB_PASSWORD;
			$db = DB_NAME;
			
			$_count = 0;
			global $wpdb;
			
			
			mysql_connect($server, $user, $pass);
			mysql_select_db($db);
			$tables = mysql_list_tables($db);
			$insert_sql = "";
			$_char_count = 0;
			
			if ( (isset($this->_options['backup_nonwp_tables'])) && ($this->_options['backup_nonwp_tables'] == 1) ) {
				$backup_nonwp_tables = 1;
				echo 'Including non-WordPress database tables in backup...<br />';
			} else {
				$backup_nonwp_tables = 0;
			}
			
			while ($td = mysql_fetch_array($tables)) {
				$table = $td[0];
				if ( (substr($table, 0, strlen($wpdb->prefix)) == $wpdb->prefix) || ( $backup_nonwp_tables == 1 ) ) { // Only backup this wordpress installations database.
					$r = mysql_query("SHOW CREATE TABLE `$table`");
					if ($r) {
						
						$d = mysql_fetch_array($r);
						$d[1] .= ";";
						$insert_sql .= str_replace("\n", "", $d[1]) . "\n";
						
						$table_query = mysql_query("SELECT * FROM `$table`") or $this->alert('Unable to read database table ' . $table . '. Your backup will not include data from this table (you may ignore this warning if you do not need this specific data). This is due to the following error: ' . mysql_error(), true, '9001');
						$num_fields = mysql_num_fields($table_query);
						while ($fetch_row = mysql_fetch_array($table_query)) {
							$insert_sql .= "INSERT INTO $table VALUES(";
							for ($n=1;$n<=$num_fields;$n++) {
								$m = $n - 1;
								$insert_sql .= "'".mysql_real_escape_string($fetch_row[$m])."', ";
							}
							$insert_sql = substr($insert_sql,0,-2);
							$insert_sql .= ");\n";
							
							fwrite($file_handle, $insert_sql);
							unset($insert_sql);
							$insert_sql = "";
							
							$_count++;
							if ($_count >= 200) {
								echo '.';
								flush();
								$_count = 0;
								$_char_count++;
								if ($_char_count >= 60) {
									echo '<br />';
									$_char_count = 0;
								}
							}
						}
						echo '.';
					}
				}
			}
			
			fclose( $file_handle );
			unset( $file_handle );
			
			
			return true;
		}
		
		
		/**
		 * iThemesBackupBuddy::_create_database_backup()
		 *
		 * Backup web files.
		 *
		 */
		function _create_database_backup( $process ) {
			$storage_directory = $this->_options['backup_status']['storage_directory'];
			
			$options = array(
				'remove_path'	=> $storage_directory,
				'overwrite'		=> true,
				'append'		=> false,
			);
			
			/*
			if ( isset( $this->_options['compression'] ) && ( $this->_options['compression'] != 1 ) ) {
				// Compression specifically disabled.
				$options['no_compress'] = true;
			}
			*/
			
			require_once( dirname( __FILE__ ) . '/lib/zip/zip.php' );
			$pluginBuddyZip = new PluginBuddyZip();
			
			if ($this->_options['compression'] == 0) {
				$disable_compression = true;
			} else {
				$disable_compression = false;
			}
			if ( isset( $this->_options['force_compatibility'] ) && ( $this->_options['force_compatibility'] == 1) ) {
				$options['force_compatibility'] = true;
			} else {
				$options['force_compatibility'] = false;
			}
			$result = $pluginBuddyZip->add_directory_to_zip( $this->_options['backup_status']['zip_file'], $storage_directory, $options, $disable_compression );
			
			if ( true !== $result ) {
				$this->_options['backup_status']['processes'][$process]['status'] = 'failed';
				
				$error =& new WP_Error( 'archive_failure', 'Unable to create backup: ' . $result['error'] );
				
				echo '<div class="bb-error">' . $error->get_error_message() . "</div>\n";
				return $error;
			}
			
			
			$this->_options['backup_status']['processes'][$process]['status'] = 'complete';
			
			return true;
		}
		
		
		/**
		 * iThemesBackupBuddy::_create_full_backup()
		 *
		 * Backup web files.
		 *
		 */
		function _create_full_backup( $process ) {
			$exclude = ltrim( str_replace( rtrim( ABSPATH, '\\\/' ), '', $this->_options['backup_directory'] ), ' \\/' );
			if ( is_array( $this->_options['excludes'] ) ) {
				$exclude = array_merge( (array)$exclude, $this->_options['excludes'] );
				//echo 'gotexcludes';
			}
			//echo $exclude;
			// Exclude dir format: wp-content/uploads/backupbuddy_backups/
			$options = array(
				'remove_path'	=> ABSPATH,
				'excludes'		=> $exclude,
				'overwrite'		=> true,
				'append'		=> false,
			);
			
			/*
			if ( isset( $this->_options['compression'] ) && ( $this->_options['compression'] != 1 ) ) {
				// Compression specifically disabled.
				$options['no_compress'] = true;
			}
			*/
			
			require_once( dirname( __FILE__ ) . '/lib/zip/zip.php' );
			$pluginBuddyZip = new PluginBuddyZip();
			
			//echo 'yo'.$this->_options['backup_directory'].'!';
			
			if ($this->_options['compression'] == 0) {
				$disable_compression = true;
			} else {
				$disable_compression = false;
			}
			if ( isset( $this->_options['force_compatibility'] ) && ( $this->_options['force_compatibility'] == 1) ) {
				$options['force_compatibility'] = true;
			} else {
				$options['force_compatibility'] = false;
			}
			$result = $pluginBuddyZip->add_directory_to_zip( $this->_options['backup_status']['zip_file'], ABSPATH, $options, $disable_compression );
			
			if ( true !== $result ) {
				$this->_options['backup_status']['processes'][$process]['status'] = 'failed';
				
				$error =& new WP_Error( 'archive_failure', 'Unable to create backup: ' . $result['error'] );
				
				echo '<div class="bb-error">' . $error->get_error_message() . "</div>\n";
				return $error;
			}
			
			
			$this->_options['backup_status']['processes'][$process]['status'] = 'complete';
			
			return true;
		}
		
		function ajax_importbuddy() {
			if ($_GET['pass'] == '') {
				echo 'ERROR #6612: Missing password.';
			} else {
				$output = file_get_contents( dirname( __FILE__ ) . '/importbuddy.php' );
				$output = preg_replace('/#PASSWORD#/', $_GET['pass'], $output, 1 ); // Only replaces first instance due to last parameter.
				$output = preg_replace('/#VERSION#/', $this->_version, $output, 1 ); // Only replaces first instance due to last parameter.

				header( 'Content-Description: File Transfer' );
				header( 'Content-Type: text/plain; name=importbuddy.php' );
				header( 'Content-Disposition: attachment; filename=importbuddy.php' );
				header( 'Expires: 0' );
				header( 'Content-Length: ' . strlen( $output ) );

				//ob_clean();
				flush();

				//str_replace( readfile( dirname( __FILE__ ) . '/importbuddy.php' ) );

				echo $output;
			}
			die();
		}
		
		function ajax_dirlist() {
			$root = ABSPATH;
			$_POST['dir'] = urldecode($_POST['dir']);
			if( file_exists($root . $_POST['dir']) ) {
				$files = scandir($root . $_POST['dir']);
				natcasesort($files);
				if( count($files) > 2 ) { /* The 2 accounts for . and .. */
					echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
					// All dirs
					foreach( $files as $file ) {
						if( file_exists($root . $_POST['dir'] . $file) && $file != '.' && $file != '..' && is_dir($root . $_POST['dir'] . $file) ) {
							echo "<li class=\"directory collapsed\"><a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $file) . "/\">" . htmlentities($file) . " <img src=\"" . $this->_pluginURL . "/images/bullet_delete.png\" style=\"vertical-align: -3px;\" /></a></li>";
						}
					}
					// All files
					/*
					foreach( $files as $file ) {
						if( file_exists($root . $_POST['dir'] . $file) && $file != '.' && $file != '..' && !is_dir($root . $_POST['dir'] . $file) ) {
							$ext = preg_replace('/^.*\./', '', $file);
							echo "<li class=\"file ext_$ext\"><a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . $file) . "\">" . htmlentities($file) . "</a></li>";
						}
					}
					*/
					echo "</ul>";
				} else {
					echo "<ul class=\"jqueryFileTree\" style=\"display: none;\">";
					echo "<li><a href=\"#\" rel=\"" . htmlentities($_POST['dir'] . 'NONE') . "\"><i>Empty Directory ...</i></a></li>";
					echo '</ul>';
				}
			} else {
				echo 'Unable to read site root.';
			}
			
			die();
		}
		
		
		function ajax_ftptest() {
			$ftp_server = $_POST['server'];
			$ftp_user = $_POST['user'];
			$ftp_pass = $_POST['pass'];
			$ftp_path = $_POST['path'];
			
			// can remove this check once ftps implemented...
			if ( isset( $_POST['type'] ) ) {
				$ftp_type = $_POST['type'];
			} else {
				$ftp_type = 'ftp';
			}
			
			
			if ( ( $ftp_server == '' ) || ( $ftp_user == '' ) || ( $ftp_pass == '' ) ) {
				die('Missing required input.');
			}
			
			
			if ( $ftp_type == 'ftp' ) {
				$conn_id = ftp_connect( $ftp_server ) or die( 'Unable to connect to FTP (check address).' );
			} elseif ( $ftp_type == 'ftps' ) {
				if ( function_exists( 'ftp_ssl_connect' ) ) {
					$conn_id = ftp_ssl_connect( $ftp_server ) or die('Unable to connect to FTPS  (check address/FTPS support).'); 
					if ( $conn_id === false ) {
						die( 'Destination server does not support FTPS?' );
					}
				} else {
					die( 'Your web server doesnt support FTPS.' );
				}
			}
			
			//echo 'user'.$ftp_user;
			$login_result = @ftp_login($conn_id, $ftp_user, $ftp_pass);
			if ((!$conn_id) || (!$login_result)) {
			   echo 'Unable to login. Bad user/pass.';
			} else {
				$tmp = tmpfile(); // Write tempory text file to stream.
				fwrite($tmp, 'Upload test for BackupBuddy');
				rewind($tmp);
				$upload = @ftp_fput($conn_id, $ftp_path.'/backupbuddy.txt', $tmp, FTP_BINARY);
				fclose($tmp);
				if (!$upload) {
					echo 'Failure uploading. Check path & permissions.';
				} else {
					echo 'Success testing ' . $ftp_type . '!';
					ftp_delete($conn_id, $ftp_path.'/backupbuddy.txt');
				}
			}
			ftp_close($conn_id);
			
			die();
		}

		
		function ajax_awstest() {
			$aws_accesskey = $_POST['aws_accesskey'];
			$aws_secretkey = $_POST['aws_secretkey'];
			$aws_bucket = $_POST['aws_bucket'];
			$aws_directory = $_POST['aws_directory'];
			$aws_ssl = $_POST['aws_ssl'];
			
			require_once(dirname( __FILE__ ).'/lib/s3/s3.php');
			$s3 = new S3( $aws_accesskey, $aws_secretkey);
			
			if ( $this->_options['aws_ssl'] != '1' ) {
				S3::$useSSL = false;
			}

			$s3->putBucket( $aws_bucket, S3::ACL_PUBLIC_READ );
			
			if ( $s3->putObject( 'Upload test for BackupBuddy for Amazon S3', $aws_bucket, $aws_directory . '/backupbuddy.txt', S3::ACL_PRIVATE) ) {
				// Success... just delete temp test file now...
			} else {
				die( 'Unable to upload. Check bucket & permissions.' );
			}

			if ( S3::deleteObject( $aws_bucket, $aws_directory . '/backupbuddy.txt' ) ) {
				die( 'Success!' );
			} else {
				die( 'Partial success. Could not delete temp file.' );
			}
			
			die();
		}
		
		// Recursively make directory. If a parent directory is missing, create it until we can create the final deepest dir.
		function mkdir_recursive($pathname) {
			is_dir(dirname($pathname)) || $this->mkdir_recursive(dirname($pathname));
			return is_dir($pathname) || mkdir($pathname);
		}		
		
		/**
		 * iThemesBackupBuddy::mail_user()
		 *
		 * Send an email to a specified user, no matter what.
		 *
		 * @param	$user			string / int	Name or uid number of user to email.
		 * @param	$subject		string			Email subject.
		 * @param	$body			string			Email body.
		 *
		 */			
		function mail_user($user, $subject, $body) {
			if ( is_numeric($user) ) {
				$sqlwhere="ID='".$user."'";
			} else { // Already numeric.
				$sqlwhere="user_login='".$user."'";
			}
			global $wpdb;

			$query = $wpdb->get_results("SELECT user_email FROM ".$wpdb->prefix."users WHERE ".$sqlwhere." LIMIT 1");
			if ( empty($query) ) {
				echo 'ERROR #45445454543: Unable to find specified user for email.';
				return 0;
			} else {
				// Cannot use wp_mail because it will not let us alter the return address (bug?).
				mail($query[0]->user_email, $subject, $body, 'From: '.$this->_options['email_reply']."\r\n".'Reply-To: '.$this->_options['email_reply']."\r\n");
			}
			unset($query);
		}


		/**
		 * iThemesBackupBuddy::mail_notice()
		 *
		 * Send an email to the admin of the site with notice information.
		 *
		 * @param	$user			string / int	Name or uid number of user to email.
		 * @param	$subject		string			Email subject.
		 * @param	$body			string			Email body.
		 *
		 */			
		function mail_notice($message) {
			mail(get_option('admin_email'), "BackupBuddy Status", "An action occurred with BackupBuddy on " . date(DATE_RFC822) . " for the site ". get_option( 'siteurl' ) . ".  The notice is displayed below:\n\n".$message, 'From: '.get_option('admin_email')."\r\n".'Reply-To: '.get_option('admin_email')."\r\n");
		}
		
		// OPTIONS STORAGE //////////////////////
		
		
		function save() {
			add_option($this->_var, $this->_options, '', 'no'); // 'No' prevents autoload if we wont always need the data loaded.
			update_option($this->_var, $this->_options);
			return true;
		}
		
		
		function load() {
			$this->_options=get_option($this->_var);
			$options = array_merge( $this->_defaults, (array)$this->_options );

			if ( $options !== $this->_options ) {
				// Defaults existed that werent already in the options so we need to update their settings to include some new options.
				$this->_options = $options;
				$this->save();
			}

			$this->_options['backup_directory'] = WP_CONTENT_DIR . '/uploads/backupbuddy_backups/';
			
			return true;
		}
		
		// ADMIN MENU FUNCTIONS /////////////////


		/** admin_menu()
		 *
		 * Initialize menu for admin section.
		 *
		 */
		function admin_menu() {
			// Add main menu (default when clicking top of menu)
			add_menu_page('Getting Started', $this->_name, 'administrator', $this->_var, array(&$this, 'view_gettingstarted'), $this->_pluginURL.'/images/pluginbuddy.png');
			// Add sub-menu items (first should match default page above)
			add_submenu_page($this->_var, 'Getting Started with '.$this->_name, 'Getting Started', 'administrator', $this->_var, array(&$this, 'view_gettingstarted'));
			add_submenu_page($this->_var, $this->_name.' Backups', 'Backups', 'administrator', $this->_var.'-backup', array(&$this, 'view_backup'));
			//add_submenu_page($this->_var, $this->_name.' Importing', 'Importing', 'administrator', $this->_var.'-importing', array(&$this, 'view_importing'));
			add_submenu_page($this->_var, $this->_name.' Scheduling', 'Scheduling', 'administrator', $this->_var.'-scheduling', array(&$this, 'view_scheduling'));
			add_submenu_page($this->_var, $this->_name.' Settings', 'Settings', 'administrator', $this->_var.'-settings', array(&$this, 'view_settings'));
		}

		
		// MISC DUSTIN FUNCTIONS ////////////////
		
		function rand_string($length = 32, $chars = 'abcdefghijkmnopqrstuvwxyz1234567890') {
			$chars_length = (strlen($chars) - 1);
			$string = $chars{rand(0, $chars_length)};
			for ($i = 1; $i < $length; $i = strlen($string)) {
				$r = $chars{rand(0, $chars_length)};
				if ($r != $string{$i - 1}) $string .=  $r;
			}
			return $string;
		}

		
		
		function cron_ftp($ftp_server, $ftp_user, $ftp_pass, $ftp_path, $file, $ftp_type, $delete_after_int = 0) {
			$ftp_pass = html_entity_decode ( $ftp_pass ); // In case html entities set for some reason... Something has been running htmlentities() on some inputs...

			$details = "";
			$details .= "Server: ".$ftp_server."\n";
			$details .= "User: ".$ftp_user."\n";
			if ($this->_debug) {
				$details .= "Pass: ".$ftp_pass."\n";
			} else {
				$details .= "Pass: *hidden*\n";
			}
			$details .= "Remote Path: ".$ftp_path."\n";
			//$details .= "Full File: ".$file."\n";
			$details .= "Local File & Path: ".$this->_options['backup_directory'].'/'.basename($file)."\n";
			$details .= "Filename: ".basename($file)."\n";
			
			//$this->mail_notice("TRYING FTP:\n\n".$details);
			
			//$conn_id = ftp_connect($ftp_server);
			if ( $ftp_type == 'ftp' ) {
				$conn_id = ftp_connect( $ftp_server ) or die( 'Unable to connect to FTP (check address).' );
			} elseif ( $ftp_type == 'ftps' ) {
				if ( function_exists( 'ftp_ssl_connect' ) ) {
					$conn_id = ftp_ssl_connect( $ftp_server ) or die('Unable to connect to FTPS  (check address/FTPS support).'); 
					if ( $conn_id === false ) {
						die( 'Destination server does not support FTPS?' );
					}
				} else {
					die( 'Your web server doesnt support FTPS.' );
				}
			}
			
			$login_result = ftp_login($conn_id, $ftp_user, $ftp_pass);
			if ((!$conn_id) || (!$login_result)) {
			   $this->mail_notice("ERROR #5544. FTP connection failed. ".$login_result."\n\n".$details);
			   exit;
		   } else {
			   echo "Sending backup via FTP ...";
		   }
			$upload = ftp_put($conn_id, $ftp_path.'/'.basename($file), $file, FTP_BINARY);
			if (!$upload) {
			   $this->mail_notice("ERROR #745567! FTP upload failed. Details: ".$upload."\n\n".$details);
			} else {
			   echo " Done uploading $file to $ftp_server".'.<br />';
			}
			ftp_close($conn_id);
			
			// Decrement this each sending operation. If we reach 1 then time to delete file since last send is done.
			$delete_after_int = $delete_after_int - 1;
			if ( $delete_after_int == 1 ) {
				unlink( $file );
			}
		}
		
		function cron_aws($aws_accesskey, $aws_secretkey, $aws_bucket, $aws_directory, $file, $delete_after_int = 0) {
			$details = "";
			$details .= "AWS Access Key: ".$aws_accesskey."\n";
			if ($this->_debug) {
				$details .= "AWS Secret Key: ".$aws_secretkey."\n";
			} else {
				$details .= "AWS Secret Key: *hidden*\n";
			}
			$details .= "AWS Bucket: ".$aws_bucket."\n";
			$details .= "AWS Directory: ".$aws_directory."\n";
			$details .= "Local File & Path: ".$this->_options['backup_directory'].'/'.basename($file)."\n";
			$details .= "Filename: ".basename($file)."\n";

			/*
			echo '<pre>';
			print_r( $details );
			echo '</pre>';
			*/
			
			require_once(dirname( __FILE__ ).'/lib/s3/s3.php');
			$s3 = new S3( $aws_accesskey, $aws_secretkey);
			
			if ( $this->_options['aws_ssl'] != '1' ) {
				S3::$useSSL = false;
			}
			
			$s3->putBucket( $aws_bucket, S3::ACL_PUBLIC_READ );
			if ( $s3->putObject( S3::inputFile( $file ), $aws_bucket, $aws_directory . '/' . basename($file), S3::ACL_PRIVATE) ) {
				// success
			} else {
				$this->mail_notice('ERROR #9002! Failed sending file to Amazon S3. Details:' . "\n\n" . $details);
			}
			
			if ( $delete_after_int == 1 ) {
				unlink( $file );
			}
		}

		/**
		 * iThemesBackupBuddy::_set_greedy_script_limits()
		 *
		 * Set up PHP parameters to allow for extended time limits
		 *
		 */
		function _set_greedy_script_limits() {
			// Don't abort script if the client connection is lost/closed
			@ignore_user_abort( true );
			
			// 2 hour execution time limits
			@ini_set( 'default_socket_timeout', 60 * 60 * 2 );
			@set_time_limit( 60 * 60 * 2 );
			
			// Increase the memory limit
			$current_memory_limit = trim( @ini_get( 'memory_limit' ) );
			
			if ( preg_match( '/(\d+)(\w*)/', $current_memory_limit, $matches ) ) {
				$current_memory_limit = $matches[1];
				$unit = $matches[2];
				
				// Up memory limit if currently lower than 256M
				if ( 'g' !== strtolower( $unit ) ) {
					if ( ( $current_memory_limit < 256 ) || ( 'm' !== strtolower( $unit ) ) )
						@ini_set('memory_limit', '256M');
				}
			}
			else {
				// Couldn't determine current limit, set to 256M to be safe
				@ini_set('memory_limit', '256M');
			}
		}
		
		
		/**
		 * iThemesBackupBuddy::_delete_directory()
		 *
		 * Delete a directory and all its contents
		 *
		 */
		function _delete_directory( $directory ) {
			$directory = preg_replace( '|[/\\\\]+$|', '', $directory );
			
			$files = glob( $directory . '/*', GLOB_MARK );
			
			foreach( $files as $file ) {
				if( '/' === substr( $file, -1 ) )
					$this->_delete_directory( $file );
				else
					unlink( $file );
			}
			
			if ( is_dir( $directory ) ) rmdir( $directory );
			
			if ( is_dir( $directory ) )
				return false;
			return true;
		}
		
		
		/////////////////////////////////////////////
		// CHRIS' FORM CREATION FUNCTIONS: //////////
		/////////////////////////////////////////////


		function _saveSettings() {
		
			$errorCount = 0;
		
			check_admin_referer( $this->_var . '-nonce' );
			
			foreach ( (array) explode( ',', $_POST['used-inputs'] ) as $name ) {
				$is_array = ( preg_match( '/\[\]$/', $name ) ) ? true : false;
				
				$name = str_replace( '[]', '', $name );
				$var_name = preg_replace( '/^' . $this->_var . '-/', '', $name );
				
				if ( $is_array && empty( $_POST[$name] ) )
					$_POST[$name] = array();
				
				if ( isset( $_POST[$name] ) && ! is_array( $_POST[$name] ) )
					$this->_options[$var_name] = stripslashes( $_POST[$name] );
				else if ( isset( $_POST[$name] ) )
					$this->_options[$var_name] = $_POST[$name];
				else
					$this->_options[$var_name] = '';
			}

			// Strip protocol prefix in case user enters it.
			if ( isset( $_POST[$this->_var.'-ftp_server'] ) ) {
				$this->_options['ftp_server'] = str_replace( 'http://', '', $this->_options['ftp_server'] );
				$this->_options['ftp_server'] = str_replace( 'ftp://', '', $this->_options['ftp_server'] );
			}
			// Convert excluded directories into format we like
			if ( isset( $_POST[$this->_var.'-exclude_dirs'] ) ) {
			
				if ( strstr( $_POST[$this->_var.'-exclude_dirs'], '/wp-content/' . "\r\n" ) || strstr( $_POST[$this->_var.'-exclude_dirs'], '/wp-content/uploads/' . "\r\n" ) ) {
					$this->_showErrorMessage( 'You may not exclude the /wp-content/ or /wp-content/uploads/ directories as they are needed by BackupBuddy. You may exclude other subdirectories within these however.' );
					$errorCount++;
				} else {
			
					$_POST[$this->_var.'-exclude_dirs'] = explode( "\n", trim( $_POST[$this->_var.'-exclude_dirs'] ) );
					$this->_options['excludes'] = $_POST[$this->_var.'-exclude_dirs'];
			
					unset( $_POST[$this->_var.'-exclude_dirs'] );
				}
			}
			
			
			
			// ERROR CHECKING OF INPUT
			if ( $errorCount < 1 ) {
				if ( $this->save() )
					$this->_showStatusMessage( __( 'Settings updated', $this->_var ) );
				else
					$this->_showErrorMessage( __( 'Error while updating settings', $this->_var ) );
			}
			else {
				$this->_showErrorMessage( 'Your settings have NOT been updated. Please correct any errors listed.' );
			}
		}
		
		function _newForm() {
			$this->_usedInputs = array();
		}
		
		function _addSubmit( $var, $options = array(), $override_value = true ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'submit';
			$options['name'] = $var;
			$options['class'] = ( empty( $options['class'] ) ) ? 'button-primary' : $options['class'];
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addButton( $var, $options = array(), $override_value = true ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'button';
			$options['name'] = $var;
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addPassBox( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'password';
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addTextBox( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'text';
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addTextArea( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'textarea';
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addFileUpload( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'file';
			$options['name'] = $var;
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addCheckBox( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'checkbox';
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addMultiCheckBox( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'checkbox';
			$var = $var . '[]';
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addRadio( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'radio';
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addDropDown( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array();
			else if ( ! isset( $options['value'] ) || ! is_array( $options['value'] ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'dropdown';
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addHidden( $var, $options = array(), $override_value = false ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['type'] = 'hidden';
			$this->_addSimpleInput( $var, $options, $override_value );
		}
		
		function _addHiddenNoSave( $var, $options = array(), $override_value = true ) {
			if ( ! is_array( $options ) )
				$options = array( 'value' => $options );
			
			$options['name'] = $var;
			$this->_addHidden( $var, $options, $override_value );
		}
		
		function _addDefaultHidden( $var ) {
			$options = array();
			$options['value'] = $this->defaults[$var];
			
			$var = "default_option_$var";
			$this->_addHiddenNoSave( $var, $options );
		}
		
		function _addUsedInputs() {
			$options['type'] = 'hidden';
			$options['value'] = implode( ',', $this->_usedInputs );
			$options['name'] = 'used-inputs';
			$this->_addSimpleInput( 'used-inputs', $options, true );
		}
		
		function _addSimpleInput( $var, $options = false, $override_value = false ) {
			if ( empty( $options['type'] ) ) {
				echo "<!-- _addSimpleInput called without a type option set. -->\n";
				return false;
			}

			$scrublist['textarea']['value'] = true;
			$scrublist['file']['value'] = true;
			$scrublist['dropdown']['value'] = true;
			$defaults = array();
			$defaults['name'] = $this->_var . '-' . $var;
			$var = str_replace( '[]', '', $var );
			
			if ( 'checkbox' === $options['type'] )
				$defaults['class'] = $var;
			else
				$defaults['id'] = $var;
			
			$options = $this->_merge_defaults( $options, $defaults );
			
			if ( ( false === $override_value ) && isset( $this->_options[$var] ) ) {
				if ( 'checkbox' === $options['type'] ) {
					if ( $this->_options[$var] == $options['value'] )
						$options['checked'] = 'checked';
				}
				elseif ( 'dropdown' !== $options['type'] )
					$options['value'] = $this->_options[$var];
			}
			
			if ( ( preg_match( '/^' . $this->_var . '/', $options['name'] ) ) && ( ! in_array( $options['name'], $this->_usedInputs ) ) )
				$this->_usedInputs[] = $options['name'];
			
			$attributes = '';
			
			if ( false !== $options )
				foreach ( (array) $options as $name => $val )
					if ( ! is_array( $val ) && ( ! isset( $scrublist[$options['type']][$name] ) || ( true !== $scrublist[$options['type']][$name] ) ) )
						if ( ( 'submit' === $options['type'] ) || ( 'button' === $options['type'] ) )
							$attributes .= "$name=\"$val\" ";
						else
							$attributes .= "$name=\"" . htmlspecialchars( $val ) . '" ';
			
			if ( 'textarea' === $options['type'] )
				echo '<textarea ' . $attributes . '>' . $options['value'] . '</textarea>';
			elseif ( 'dropdown' === $options['type'] ) {
				echo "<select  $attributes>\n";
				foreach ( (array) $options['value'] as $val => $name ) {
				
					$selected = ( $this->_options[$var] == $val ) ? ' selected="selected"' : '';
					echo "<option value=\"$val\"$selected>$name</option>\n";
				}
				
				echo "</select>\n";
			}
			else
				echo '<input ' . $attributes . '/>';
		}
		
		function _merge_defaults( $values, $defaults, $force = false ) {
			if ( ! $this->_is_associative_array( $defaults ) ) {
				if ( ! isset( $values ) ) {
					return $defaults;
				}
				if ( false === $force ) {
					return $values;
				}
				if ( isset( $values ) || is_array( $values ) )
					return $values;
				return $defaults;
			}
			
			foreach ( (array) $defaults as $key => $val ) {
				if ( ! isset( $values[$key] ) ) {
					$values[$key] = null;
				}
				$values[$key] = $this->_merge_defaults($values[$key], $val, $force );
			}
			return $values;
		}
		
		function _is_associative_array( &$array ) {
			if ( ! is_array( $array ) || empty( $array ) ) {
				return false;
			}
			$next = 0;
			foreach ( $array as $k => $v ) {
				if ( $k !== $next++ ) {
					return true;
				}
			}
			return false;
		}
		
		// PUBLIC DISPLAY OF MESSAGES ////////////////////////
		
		function _showStatusMessage( $message ) {
			echo '<div id="message" class="updated fade"><p><strong>'.$message.'</strong></p></div>';			
		}
		function _showErrorMessage( $message ) {
			echo '<div id="message" class="error"><p><strong>'.$message.'</strong></p></div>';
		}
		
		// SORTING FUNCTION(S) //////////////////////////////////
		
		function _sortGroupsByName( $a, $b ) {
			if ( $this->_options['groups'][$a]['name'] < $this->_options['groups'][$b]['name'] )
				return -1;
			
			return 1;
		}

    } // End class


	
	$iThemesBackupBuddy = new iThemesBackupBuddy(); // Create instance
}
?>