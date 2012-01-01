=== Plugin Name ===
Contributors: janhvizdak
Donate link: http://www.janhvizdak.com/make-donation-cross-linker-plugin-wordpress.php
Tags: hyperlink, words, automatically, links
Requires at least: 3.1.4
Tested up to: 3.1.4
Stable tag: 1.4.4

A plugin which automatically hyperlinks defined words to defined URLs.

== Description ==

This plugin automatically hyperlinks selected words to defined URLs. The hyperlinking process works in posts and comments (given as an option) currently. If you are upgrading from any other previous version, then DO NOT deactivate the original installation of this plugin. See the installation instructions for more details.

== Installation ==

1. Upload downloaded files to the  '/wp-content/plugins/' directory. So that you should see something like '/wp-content/plugins/cross-linker/' there.
1. If you aren't using any of previous versions yet, activate the plugin through the 'Plugins' menu in WordPress. If you're already using some of the previous versions, then there is no need to activate it. Deactivation is not necessary for upgrading to a newer version.

== Frequently Asked Questions ==

= Does this plugin hyperlink all words? =

All words which fit specified criteria are hyperlinked. Firstly, the word must be a real "word". So it must be divided from other characters clearly. Say that you want to hyperlink 'design' only. In such case, all 'design' words will be hyperlinked, but 'designers' not (if they're not hyperlinked too). All characters which divide words can be modified.

= Which MySQL tables belong to this plugin? =

All which contain _interlinker in their name. However, we are not responsible for others plugins which may use these names (although not likely). Since version 1.2 all tables are created after activation and they are NOT deleted later. This is because some people deactivated this plugin and so they lost all data within. Later after re-activating it they (people) were missing such data.

= Does this plugin hyperlink existing links? =

No.

= Is it possible to tell the plugin to ignore some words or some tags? =

Yes.

= Does this plugin work with comments? =

Yes.

= How can I manage the plugin? =

Simply check the snapshot which is mentioned below, or click on "Tools->Cross-Linker" in your WordPress admin panel after activating this plugin.

= Is it possible to delete words which have been cross-linked already? =

Yes. Since the version 1.1.

== Screenshots ==

1. [http://www.janhvizdak.com/images/snapshot.png](http://www.janhvizdak.com/images/snapshot.png)
