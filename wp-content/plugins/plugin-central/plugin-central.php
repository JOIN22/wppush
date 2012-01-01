<?php
/*
Plugin Name: Plugin Central
Plugin URI: http://www.prelovac.com/vladimir/wordpress-plugins/plugin-central
Description: Automatically installs WordPress plugins by name and URL. Provides automatic update of multiple plugins.
Version: 2.4.2
Author: Vladimir Prelovac
Author URI: http://www.prelovac.com/vladimir/

Copyright 2008  Vladimir Prelovac
*/

if (isset($plugin_central)) return false;

require_once(dirname(__FILE__) . '/plugin-central.class.php');

$plugin_central = new PluginCentral();