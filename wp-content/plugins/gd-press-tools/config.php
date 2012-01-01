<?php

$gdpt_config_extra = dirname(dirname(__FILE__))."/gdpt-config.php";
if (file_exists($gdpt_config_extra)) require_once($gdpt_config_extra);

/**
 * Full path to wp-load file. Use only if the location of wp-content folder is changed.
 *
 * example: define('STARRATING_WPLOAD', '/home/path/to/wp-load.php');
 */
if (!defined('PRESSTOOLS_WPLOAD')) define('PRESSTOOLS_WPLOAD', '');

/**
 * Line ending used for generating the backup file.
 */
if (!defined('PRESSTOOLS_LINE_ENDING')) define('PRESSTOOLS_LINE_ENDING', "\r\n");

/*
 * File in which the debug and log data will be saved.
 */
define('PRESSTOOLS_LOG_PATH', dirname(__FILE__).'/debug.txt');

/*
 * Debug feature is active if set to true.
 */
define('PRESSTOOLS_DEBUG_ACTIVE', true);

/*
 * PHP Settings can be changed by the plugin if set to true.
 */
define('PRESSTOOLS_PHP_SETTINGS', true);

/*
 * Returns the path to wp-config.php file
 *
 * @return string wp-config.php path
 */
function get_gdpt_wpload_path() {
    if (PRESSTOOLS_WPLOAD == '') {
        $d = 0;
        while (!file_exists(str_repeat('../', $d).'wp-load.php'))
            if (++$d > 16) exit;
        $wpconfig = str_repeat('../', $d).'wp-load.php';
        return $wpconfig;
    } else return PRESSTOOLS_WPLOAD;
}

?>