=== GD Press Tools ===
Contributors: gdragon
Donate link: http://d4p.me/gdpt
Version: 2.6.0
Tags: admin, database backup, auto tagger, tag, seo, url scan, tools, press, revisions, gdragon, post, page, security, tracking, administration, php, update, mysql, database, integration, cron
Requires at least: 2.9
Tested up to: 3.2
Stable tag: trunk

GD Press Tools is a collection of various administration, seo, maintenance, backup and security related tools that can help with everyday blog tasks and blog optimizations.

== Description ==
GD Press Tools is a collection of various administration, seo, maintenance, backup and security related tools. This tools can be integrated into the various WordPress admin panels, can perform maintenance operations, change some aspects of WordPress, see detailed server settings and information. Plugin can also track posts and pages views for various popularity lists. Some of the features don't work with every version of the WordPress. If you have some suggestion about potential features for this plugin, please leave a message.

Supported languages: english, serbian, french, spanish, danish, chinese, korean, belorussian

[List Of Features](http://www.dev4press.com/plugins/gd-press-tools/features/) |
[Tutorials](http://www.dev4press.com/category/tutorials/plugins-tutorials/gd-press-tools/) |
[Translations](http://info.dev4press.com/gd-press-tools/languages.html)
[Change Log](http://info.dev4press.com/gd-press-tools/changelog.html) |

= Other Important URL's =
[Plugin Home](http://www.dev4press.com/gd-press-tools/) |
[Forum](http://forum.gdragon.info/viewforum.php?f=24) |
[Feedburner](http://feeds2.feedburner.com/dev4press) |
[Twitter](http://twitter.com/milangd)

== Installation ==
= Requirements =
* PHP: 4.4.x or 5.x.x
* mySQL: 4.0, 4.1 or 5.x
* WordPress: 2.7 or newer

= Basic Installation =
* Plugin folder in the WordPress plugins folder must be `gd-press-tools`
* Upload folder `gd-press-tools` to the `/wp-content/plugins/` directory
* Activate the plugin through the 'Plugins' menu in WordPress

= Advanced Installation  =
* if you don't change location of `wp-content` folder, then you don't need to make any more changes.
* if your `wp-content` folder is moved out of default WordPress location, then you must edit plugins `config.php` file and set value with exact location of `wp-load.php` file for global constant `PRESSTOOLS_WPLOAD` in line 11.

== Frequently Asked Questions ==
= Does plugin works with WPMU and WordPress MultiSite installations? =
Lite version has only partial support for the multi site installations. Allowing access to plugins Database options is not reccomended for individual blogs, and SiteAdmin should disable this on the plugins General Settings tab. If you need full support for Multisite installations, upgrade plugin to PRO version: http://dv4p.com/gdpt.

= I need support for using this plugin and some of it's features? =
Lite version is regularly maintained, but it doesn't include any kind of support beyond bug fixing. If you need support, upgrade plugin to PRO edition and get much more additional features.

= I want to translate the plugin to my language, or to improve existing translations? =
You only need POEdit program that works on Windows, Linux and MacOS. Instructions on how to make or update translations are here: http://dv4p.com/wa.

= What capabilites are added by the plugin for various panels and modules? =
* presstools_dashboard: dashboard widget
* presstools_debug: debug info in the footer
* presstools_front: plugin front panel
* presstools_info: plugin info panels
* presstools_global: all other plugin panels

== Screenshots ==
1. Front panel
2. Settings panel
3. Administration panel
4. Meta Tags panel

== Upgrade Notice ==
= 2.6.0 =
Updates for the wordpress 3.2. Several minor changes and improvements. Removed support code for wp older than 2.9.

== Changelog ==
= 2.6.0 =
* Updates for the wordpress 3.2
* Several minor changes and improvements
* Removed support code for wp older than 2.9

= 2.5.9 =
* Replaced some more deprecated functions
* Removed some old and outdate code
* Disable admin bar improved
* Minor changes in some of the functions

= 2.5.8 =
* Option to disable admin bar in WordPress 3.1

= 2.5.7 =
* Minor compatibility changes for WordPress 3.1
* Updated POT file with some missing strings

= 2.5.6 =
* Use of capabilities for displaying plugin panels

= 2.5.5 =
* Missing id columns for categories and post tags in wp 3.0

= 2.5.4 =
* Fixed loading of unneeded scripts and styles

= 2.5.3 =
* Added working REAL Capital P filter
* Disabling core update removes the nag messages

= 2.5.2 =
* Option to disable Capital P filter in WordPress 3.0
* Some minor updates to plugin panel links

= 2.5.1 =
* URL scan for excessive length and SQL injection
* Expanding comments grid with comment ID
* Improvements to the plugin settings panel
* Updated Spanish and Danish translations

= 2.5.0 =
* Administration tweaking options: remove turbo, logo, favorites and help
* Several minor problems fixed

= 2.4.7 =
* Missing loading of file with redirection and other functions

= 2.4.6 =
* Auto tagger has more status messages and current processing time

= 2.4.5 =
* More WordPress 3.0 related improvements

= 2.4.4 =
* More changes for wordpress 3.0 related issues
* Small problem with site admin functions under wpmu

= 2.4.3 =
* Functions for post and user tracking results
* Few more improvements for wordpress 3.0

= 2.4.2 =
* Support for wordpress 3.0 multi site mode
* More wordpress 3.0 related improvements

= 2.4.1 =
* Added french translation
* Fixed invalid sql for delayed rss publish
* Fixed duplicate post duplicating quotes in content

= 2.4.0 =
* WordPress 3.0 compatibility fixes
* Setting plugin access rules through settings panel
* Few more typos and other minor errors fixed

= 2.3.8 =
* Updated translations
* Several minor changes

= 2.3.7 =
* Proper clear rss cache function for transient options
* Flash uploader problem when website is locked down

= 2.3.6 =
* Several improvements to auto tagger
* Some changes and fixes to running updated jobs

= 2.3.5 =
* Duplicate post sometimes breaks the content and invalid post_date_gmt value
* Many database backup improvements and notices added
* Several more minor changes fixes

= 2.3.2 =
* Fixed user level check for footer debug queries display
* Fixed generating long sql queries for db backup

= 2.3.1 =
* Enable auto database repair feature in WordPress 2.9
* Few changes for WordPress 2.9

= 2.3.0 =
* Option to disable theme update check
* Improved disable core update check
* Improved disable plugins update check

= 2.2.5 =
* Added chinese translation
* Improved loading and plugin init
* Expanded footer debug info
* Several warnings and notices

= 2.2.4 =
* Added korean translation
* Few minor fixes

= 2.2.3 =
* Added language meta tag options
* Filters for rss item footer and header
* Belorussian translation

= 2.2.2 =
* Added more meta related options
* Meta tags and database bugs fixed

= 2.2.1 =
* Allow HTML in term, user and link descriptions
* Fixed problem with creating of folders and setting wrong permissions

= 2.2.0 =
* Tool for database tables backup
* Showing DB tables collation
* Redesigned database panel

= 2.1.1 =
* Admin tool for scanning for new avatars
* Improved database tables installation
* Few updates and changes

= 2.1.0 =
* Global auto tagger tool to add tags using Yahoo
* Several changes and fixes for RSS features

= 2.0.1 =
* Some changes to menu items visibilty
* Minor ajax related loading problem

= 2.0.0 =
* Website based short url
* Panel with registered wordpress hooks
* Tracking last user login time
* Display SQL queries in footer
* Comments count on the users list panel
* Option to disable post auto save
* New dark color scheme replaced previous blue
* Rewritten loading of JS and CSS code
* Removed support for WP 2.5.x and 2.6.x
* Many changes and fixes

== Thanks To ==
* John Blackbourn(http://johnblackbourn.com/) for disable updates code
