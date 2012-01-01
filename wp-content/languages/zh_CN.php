<?php
// 0 upgrade 1 wpcng upgrade 2 no upgrade
$wpcng_update_core_sw_array = array('0', '1', '2');
$wpcng_update_core_sw = get_option('wpcng_update_core_sw');
if (!in_array($wpcng_update_core_sw, $wpcng_update_core_sw_array, true)) {
	delete_transient('update_core');
	delete_site_transient('update_core');
	add_option('wpcng_update_core_sw', '1');
	$wpcng_update_core_sw = '1';
}

// 添加中文版选项到管理菜单
add_action('admin_menu', 'wpcng_add_pages');
//add_action('core_upgrade_preamble', 'wpcng_add_upgrade_options');

function wpcng_add_pages() {
	add_options_page('WPCNG 中文版设置', 'WPCNG 中文版设置', 'update_core', 'wpcng-options', 'wpcng_options_admin_page');
}

function wpcng_options_admin_page() {
//function wpcng_add_upgrade_options() {
	if ($_POST['update-wpcng-options']) {
		$updated = false;
		if (get_option('wpcng_update_core_sw') !== $_POST['wpcng-update-core-sw']) {
			update_option('wpcng_update_core_sw', $_POST['wpcng-update-core-sw']);
			delete_transient('update_core');
			delete_site_transient('update_core');
			$updated = true;
		}
		if ($updated) {
			$wpcng_message = '设置成功更新。';
		} else {
			$wpcng_message = '设置没有变化。';
		}
	}
?>
	<h2>WPCNG 中文版设置</h2>
<?php if ($wpcng_message) { ?>
	<div id="message" class="updated fade"><p><b><?php echo $wpcng_message; ?></b></p></div>
<?php } ?>
	<form id="wpcng-form" method="POST" action="" name="wpcng-form">
		<table class="form-table">
			<tr valign="top">
				<th scope="row">核心程序更新方式：</th>
				<td>
					<select name="wpcng-update-core-sw">
						<option value="0"<?php if (get_option('wpcng_update_core_sw') === '0') echo ' selected="selected"'; ?>>检查官方版本更新</option>
						<option value="1"<?php if (get_option('wpcng_update_core_sw') === '1') echo ' selected="selected"'; ?>>检查 WPCNG 版本更新</option>
						<option value="2"<?php if (get_option('wpcng_update_core_sw') === '2') echo ' selected="selected"'; ?>>不检查更新</option>
					</select>
					<p style="color: #FF0000;">注意：如果你选择了“检查官方版本更新”并自动升级到了官方版本，你将无法再自动升级 WPCNG 的中文版了。除非，你重新下载 WPCNG 的中文包进行覆盖。</p>
				</td>
			</tr>
		</table>
		<p class="submit">
			<input type="submit" value="<?php _e('Update'); ?>" name="update-wpcng-options"/>
		</p>
	</form>
<?php
}

// 优化字体
if (!function_exists('admin_area_beautifier')) :

function admin_area_beautifier() { ?>
<style type="text/css">
.wrap h2,
#footer,
#footer a,
#dashboard_right_now p.sub,
.tablenav .displaying-num,
.inline-edit-row fieldset span.title,
.inline-edit-row fieldset span.checkbox-title,
.setting-description,
.form-wrap p,
#utc-time,
#local-time,
.howto,
#media-upload p.help,
#media-upload label.help {
	font-style: normal !important;
}

#screen-meta a.show-settings,
#favorite-actions a,
#adminmenu .wp-submenu a,
.submit input,
.button,
form.upgrade .button,
.button-primary,
.button-secondary,
div.tablenav .button-secondary,
.button-highlighted,
#postcustomstuff .submit input,
#the-comment-list .comment-item p.comment-actions,
.hndle a,
#dashboard_quick_press #media-buttons,
.subsubsub,
#wpcontent select,
.widefat td, .widefat th,
.inline-edit-row fieldset ul.cat-checklist label,
.inline-edit-row .catshow,
.inline-edit-row .cathide,
.inline-edit-row #bulk-titles div,
.form-wrap p,
.form-wrap label,
.widefat td p,
#poststuff .inside,
#poststuff .inside p,
.form-table td,
#wpbody-content .describe td,
kbd,
code,
#edithead .inside,
#edithead .inside input,
ul#widget-list li.widget-list-item div.widget-description,
.widget-control-edit,
li.widget-list-control-item div.widget-control,
.postbox p,
.postbox ul,
.postbox ol,
.postbox blockquote,
#wp-version-message {
	font-size:12px !important;
}

#wphead h1 a span {
	font-size:12px !important;
}

a {
	text-decoration:none;
}

#active-plugins-table .num, #posts-filter #author, #posts-filter #tags {
	min-width:50px !important;
}

.inline-editor .date input[name="aa"], #posts-filter #comments,#posts-filter #description{
	min-width:35px !important;
}

.tablenav select#cat {
	width:135px !important;
}

#adv-settings label,#posts-filter #name {
	min-width:10em !important;
}
#dashboard_right_now td.first {
	width:40px;
}
#dashboard_right_now td.last {
	width:60px;
}

#site-title {
	font-size: 14px !important;
}
#wphead h1 a span {
	font-size: 14px !important;
}
#wphead #site-visit-button {
	font-size: 14px !important;
	line-height: 22px;
}
#post-status-info td#wp-word-count {
	font-size: 12px;
}
</style>
<?php
}
add_action('admin_head', 'admin_area_beautifier');

endif;

function login_area_beautifier() {
?>
<style type="text/css">
p.forgetmenot label,
p.message,
#nav a,
#backtoblog {
	font-style: normal !important;
	font-size:12px !important;
}
#login_error {
	font-size:12px !important;
}
</style>
<?php
}
add_action('login_head', 'login_area_beautifier');

function wp_version_check_mod() {
	if ( defined('WP_INSTALLING') )
		return;

	global $wpdb, $wp_local_package;
	include ABSPATH . WPINC . '/version.php'; // include an unmodified $wp_version
	$php_version = phpversion();

	$current = get_site_transient( 'update_core' );
	if ( ! is_object($current) ) {
		$current = new stdClass;
		$current->updates = array();
		$current->version_checked = $wp_version;
	}

	$locale = apply_filters( 'core_version_check_locale', get_locale() );

	// Update last_checked for current to prevent multiple blocking requests if request hangs
	$current->last_checked = time();
	set_site_transient( 'update_core', $current );

	if ( method_exists( $wpdb, 'db_version' ) )
		$mysql_version = preg_replace('/[^0-9.].*/', '', $wpdb->db_version());
	else
		$mysql_version = 'N/A';

	if ( is_multisite( ) ) {
		$user_count = get_user_count( );
		$num_blogs = get_blog_count( );
		$wp_install = network_site_url( );
		$multisite_enabled = 1;
	} else {
		$user_count = count_users( );
		$multisite_enabled = 0;
		$num_blogs = 1;
		$wp_install = home_url( '/' );
	}

	$local_package = isset( $wp_local_package )? $wp_local_package : '';
	$url = "http://api.wfans.org/check-wp-version/?version=$wp_version&php=$php_version&locale=$locale&mysql=$mysql_version&local_package=$local_package&blogs=$num_blogs&users={$user_count['total_users']}&multisite_enabled=$multisite_enabled";

	$options = array(
		'timeout' => ( ( defined('DOING_CRON') && DOING_CRON ) ? 30 : 3 ),
		'user-agent' => 'WordPress/' . $wp_version . '; ' . home_url( '/' ),
		'headers' => array(
			'wp_install' => $wp_install,
			'wp_blog' => home_url( '/' )
		)
	);

	$response = wp_remote_get($url, $options);

	if ( is_wp_error( $response ) || 200 != wp_remote_retrieve_response_code( $response ) )
		return false;

	$body = trim( wp_remote_retrieve_body( $response ) );
	if ( ! $body = maybe_unserialize( $body ) )
		return false;
	if ( ! isset( $body['offers'] ) )
		return false;
	$offers = $body['offers'];

	foreach ( $offers as &$offer ) {
		foreach ( $offer as $offer_key => $value ) {
			if ( 'packages' == $offer_key )
				$offer['packages'] = (object) array_intersect_key( array_map( 'esc_url', $offer['packages'] ),
					array_fill_keys( array( 'full', 'no_content', 'new_bundled', 'partial' ), '' ) );
			elseif ( 'download' == $offer_key )
				$offer['download'] = esc_url( $value );
			else
				$offer[ $offer_key ] = esc_html( $value );
		}
		$offer = (object) array_intersect_key( $offer, array_fill_keys( array( 'response', 'download', 'locale',
			'packages', 'current', 'php_version', 'mysql_version', 'new_bundled', 'partial_version' ), '' ) );
	}

	$updates = new stdClass();
	$updates->updates = $offers;
	$updates->last_checked = time();
	$updates->version_checked = $wp_version;
	set_site_transient( 'update_core',  $updates);
}

function _maybe_update_core_mod() {
	global $wp_version;

	$current = get_site_transient( 'update_core' );

	if ( isset( $current->last_checked ) &&
		43200 > ( time() - $current->last_checked ) &&
		isset( $current->version_checked ) &&
		$current->version_checked == $wp_version )
		return;

	wp_version_check_mod();
}

if ($wpcng_update_core_sw === '0') :
elseif ($wpcng_update_core_sw === '1') :
wp_clear_scheduled_hook('wp_version_check');
add_action('admin_init', create_function('$a', "remove_action('admin_init', '_maybe_update_core');"), 8);
add_action('wp_version_check', create_function( '$a', "remove_action( 'wp_version_check', 'wp_version_check' );"), 8);
add_action('admin_init', '_maybe_update_core_mod', 12);
add_action('wp_version_check', 'wp_version_check_mod', 12);
elseif ($wpcng_update_core_sw === '2') :
wp_clear_scheduled_hook('wp_version_check');
add_action('admin_init', create_function('$a', "remove_action('admin_init', '_maybe_update_core');"), 8);
add_action('wp_version_check', create_function( '$a', "remove_action( 'wp_version_check', 'wp_version_check' );"), 8);
endif;
?>