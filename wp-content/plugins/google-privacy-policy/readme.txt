=== Plugin Name ===
Contributors: Matthew Phillips
Donate link: http://smheart.org/donations
Tags: google, privacy policy, AdSense
Requires at least: 2.8
Tested up to: 2.9.1
Stable tag: trunk

 This plugin provides a direct and easy to include privacy policy to meet Google's requirements for websites using AdSense.  

== Description ==

Webmasters using Google AdSense are required to include a privacy policy on their site.  This plugin provides and easy way to add the necessary information.


== Installation ==

This section describes how to install the plugin and get it working.

1. Upload the folder 'google-privacy-policy' to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Add <!-- googleprivacypolicy --> to the Page or Post where the Privacy Policy should appear.




== Frequently Asked Questions ==

= How do I add the privacy policy to my site?

Add <!-- googleprivacypolicy --> to the Page or Post where you wish the privacy policy to display.

= I'm creating a theme how do I include the privacy policy? =

Add the following line wherever you wish the privacy policy to display
if (function_exists('google_privacy_policy_display'){echo google_privacy_policy_display();}

= Can I edit the privacy policy = 

You cannot edit the privacy policy at this time.  The existing text meets Google's requirements and editing may invalidate the privacy policy.

== Screenshots ==

1. This screenshot is of the existing configuration screen for the plugin.


== Changelog ==

= 1.0 =
* Initial Release 28 December 2009


== Known Bugs ==

= Major =
* none

= Minor =
* none

== Feature Roadmap ==

= UI Features = 
* add support for other languages

= Backend Features =
* add support for editing of the privacy policy
* update the privacy policy based on requirement changes from Google.

== License ==

* Google Privacy Policy is released under the GNU GPL version 3.0 or later.
