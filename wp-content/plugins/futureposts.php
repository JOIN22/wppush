<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta content="text/html; charset=utf-8" http-equiv="Content-Type" />
<title>Future Posts Preview</title>
<link rel="stylesheet" type="text/css" href="wp-content/plugins/future-posts/fut.css" media="screen" />
<link rel="stylesheet" type="text/css" href="wp-content/plugins/future-posts/sociable.css" media="screen" />
</head>
<body>
<?php
define('WP_USE_THEMES', false);
require('wp-blog-header.php');
ucp_show('Future Scheduled Posts','59','No posts','d.m.Y','true','true');
?>
</body>
</html>
