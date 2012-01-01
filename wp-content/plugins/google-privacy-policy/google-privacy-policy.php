<?php
/*
Plugin Name: Google Privacy Policy
Plugin URI: http://smheart.org/google-privacy-policy/
Description: This plugin provides a direct and easy to include privacy policy to meet Google's requirements for websites using AdSense.
Author: Matthew Phillips
Version: 1.0
Author URI: http://smheart.org


Copyright 2009 SMHeart Inc, Matthew Phillips  (email : matthew@smheart.org)

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

http://www.gnu.org/licenses/gpl.txt


*/

/*
Version
        1.0   28 December 2009

*/

add_action('admin_menu', 'google_privacy_policy_menu');
add_action('admin_head', 'google_privacy_policy_styles');
register_activation_hook(__FILE__, 'google_privacy_policy_install');
register_deactivation_hook(__FILE__, 'google_privacy_policy_remove');
add_filter('the_content', 'google_privacy_policy_insert');


function google_privacy_policy_install() {
	global $wpdb;
	if (!is_blog_installed()) return;
	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	}

function google_privacy_policy_remove() {
remove_filter('the_content', 'google_privacy_policy_insert');
}

function google_privacy_policy_menu() {
	add_options_page('Google Privacy Policy Options', 'Google Privacy Policy', 8, __FILE__, 'google_privacy_policy_options');
	}

function google_privacy_policy_styles() {
	?>
 	<link rel="stylesheet" href="/wp-content/plugins/google-privacy-policy/google-privacy-policy.css" type="text/css" media="screen" charset="utf-8"/>
	<?php
	}

function google_privacy_policy_insert($content) {
	if (strpos($content, "<!-- googleprivacypolicy -->") !== FALSE) {
		$content = preg_replace('/<p>\s*<!--(.*)-->\s*<\/p>/i', "<!--$1-->", $content);
		$content = str_replace('<!-- googleprivacypolicy -->', google_privacy_policy_display(), $content);
	}
	return $content;
}


function google_privacy_policy_display(){
$privacypolicy='<div id="google-privacy-policy"><p>Privacy Policy for <b>'.get_bloginfo('url').'</b></p>

<p>The privacy of our visitors to <b>'.get_bloginfo('url').'</b> is important to us.</p>

<p>At <b>'.get_bloginfo('url').'</b>, we recognize that privacy of your personal information is important. Here is information on what types of personal information we receive and collect when you use and visit <b>'.get_bloginfo('url').'</b>, and how we safeguard your information.  We never sell your personal information to third parties.</p>

<p><strong>Log Files</strong>
As with most other websites, we collect and use the data contained in log files.  The information in the log files include  your IP (internet protocol) address, your ISP (internet service provider, such as AOL or Shaw Cable), the browser you used to visit our site (such as Internet Explorer or Firefox), the time you visited our site and which pages you visited throughout our site.</p>

<p><strong>Cookies and Web Beacons</strong>
We do use cookies to store information, such as your personal preferences when you visit our site.  This could include only showing you a popup once in your visit, or the ability to login to some of our features, such as forums.</p>

<p>We also use third party advertisements on <b>'.get_bloginfo('url').'</b> to support our site.  Some of these advertisers may use technology such as cookies and web beacons when they advertise on our site, which will also send these advertisers (such as Google through the Google AdSense program) information including your IP address, your ISP , the browser you used to visit our site, and in some cases, whether you have Flash installed.  This is generally used for geotargeting purposes (showing New York real estate ads to someone in New York, for example) or showing certain ads based on specific sites visited (such as showing cooking ads to someone who frequents cooking sites).</p>

<p><strong>DoubleClick DART cookies</strong>
We also may use DART cookies for ad serving through Google\'s DoubleClick, which places a cookie on your computer when you are browsing the web and visit a site using DoubleClick advertising (including some Google AdSense advertisements).  This cookie is used to serve ads specific to you and your interests ("interest based targeting").  The ads served will be targeted based on your previous browsing history (For example, if you have been viewing sites about visiting Las Vegas, you may see Las Vegas hotel advertisements when viewing a non-related site, such as on a site about hockey).  DART uses "non personally identifiable information".  It does NOT track personal information about you, such as your name, email address, physical address, telephone number, social security numbers, bank account numbers or credit card numbers.  You can opt-out of this ad serving on all sites using this advertising by visiting <a href="http://www.doubleclick.com/privacy/dart_adserving.aspx">http://www.doubleclick.com/privacy/dart_adserving.aspx </a></p>

<p>You can choose to disable or selectively turn off our cookies or third-party cookies in your browser settings, or by managing preferences in programs such as Norton Internet Security.  However, this can affect how you are able to interact with our site as well as other websites.  This could include the inability to login to services or programs, such as logging into forums or accounts.</p>

<p>Deleting cookies does not mean you are permanently opted out of any advertising program.  Unless you have settings that disallow cookies, the next time you visit a site running the advertisements, a new cookie will be added.</p>

		

		</div>
';
	return $privacypolicy;
}


function google_privacy_policy_options() {
	?>
	<div class="wrap">
		<h2>Google Privacy Policy V1.0</h2>
		<div id="google_privacy_policy_main">
			<div id="google_privacy_policy_left_wrap">
				<div id="google_privacy_policy_left_inside">
					<h3>Donate</h3>
					<p><em>If you like this plugin and find it useful, help keep this plugin free and actively developed by clicking the <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=10836717" target="paypal"><strong>donate</strong></a> button or send me a gift from my <a href="http://amzn.com/w/11GK2Q9X1JXGY" target="amazon"><strong>Amazon wishlist</strong></a>.  Also follow me on <a href="http://twitter.com/kestrachern/" target="twitter"><strong>Twitter</strong></a>.</em></p>
					<a target="paypal" title="Paypal Donate"href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=10836717"><img src="/wp-content/plugins/google-privacy-policy/paypal.jpg" alt="Donate with PayPal" /></a>
					<a target="amazon" title="Amazon Wish List" href="http://amzn.com/w/11GK2Q9X1JXGY"><img src="/wp-content/plugins/google-privacy-policy/amazon.jpg" alt="My Amazon wishlist" /> </a>
					<a target="Twitter" title="Follow me on Twitter" href="http://twitter.com/kestrachern/"><img src="/wp-content/plugins/google-privacy-policy/twitter.jpg" alt="Twitter" /></a>	
				</div>
			</div>
			<div id="google_privacy_policy_right_wrap">
				<div id="google_privacy_policy_right_inside">
				<h3>About the Plugin</h3>
				<p>  This plugin provides a direct and easy to include privacy policy to meet Google's requirements for websites using AdSense.</p>
				</div>
			</div>
		</div>
	<div style="clear:both;"></div>
	<fieldset class="options"><legend>Google Privacy Policy Text</legend> 
			<fieldset class="selectors">
				<legend>Privacy Policy [<a class="google-privacy-policy_description" title="Click for Description!" onclick="toggleVisibility('privacy-policy-review');">Review</a>]</legend>
				<p> To include the Privacy Policy on your site add &lt;!-- googleprivacypolicy --&gt; to the page or post where you want the privacy policy to appear.</p>
				<div class="google-privacy-policy_description_text" id="privacy-policy-review">
					<?php echo google_privacy_policy_display(); ?>
				</div>
			</fieldset>
	</fieldset>
	<div style="clear:both;"></div>			
	<fieldset class="options"><legend>Feature Suggestion/Bug Report</legend> 
	<?php if ($_SERVER['REQUEST_METHOD'] != 'POST'){
      		$me = $_SERVER['PHP_SELF'].'?page=google-privacy-policy/google-privacy-policy.php';
		?>
		<form name="form1" method="post" action="<?php echo $me;?>">
		<table border="0" cellspacing="0" cellpadding="2">
		<tr>
			<td>
				Make a:
			</td>
			<td>
				<select name="MessageType">
				<option value="Feature Suggestion">Feature Suggestion</option>
				<option value="Bug Report">Bug Report</option>
				</select>
			</td>
		</tr>
		<tr>
			<td>
				Name:
			</td>
			<td>
				<input type="text" name="Name">
			</td>
		</tr>
		<tr>
			<td>
				Your email:
			</td>
			<td>
				<input type="text" name="Email" value="<?php echo(get_option('admin_email')) ?>" />
			</td>
		</tr>
		<tr>
			<td valign="top">
				Message:
			</td>
			<td>
				<textarea name="MsgBody">
				</textarea>
			</td>
		</tr>
		<tr>
			<td>
				&nbsp;
			</td>
			<td>
				<input type="submit" name="Submit" value="Send">
			</td>
		</tr>
	</table>
</form>
<?php
   } else {
      error_reporting(0);
	$recipient = 'support@smheart.org';
	$subject = stripslashes($_POST['MessageType']).'- Google Privacy Policy Plugin';
	$name = stripslashes($_POST['Name']);
	$email = stripslashes($_POST['Email']);
	if ($from == "") {
		$from = get_option('admin_email');
	}
	$header = "From: ".$name." <".$from.">\r\n."."Reply-To: ".$from." \r\n"."X-Mailer: PHP/" . phpversion();
	$msg = stripslashes($_POST['MsgBody']);
      if (mail($recipient, $subject, $msg, $header))
         echo nl2br("<h2>Message Sent:</h2>
         <strong>To:</strong> Google Privacy Policy Support
         <strong>Subject:</strong> $subject
         <strong>Message:</strong> $msg");
      else
         echo "<h2>Message failed to send</h2>";
	}
?>
	</fieldset>				
	</div>
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


	<?php
	}
?>