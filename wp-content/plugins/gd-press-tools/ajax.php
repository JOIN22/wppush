<?php

require_once(dirname(__FILE__)."/config.php");
$wpload = get_gdpt_wpload_path();
require($wpload);

$options = get_option('gd-press-tools');
$action = $_GET["action"];
$value = $_GET["value"];

switch ($action) {
    case "hide":
        $options["dashboard_handler_".$value] = 0;
        break;
    case "show":
        $options["dashboard_handler_".$value] = 1;
        break;
}

update_option('gd-press-tools', $options);

?>