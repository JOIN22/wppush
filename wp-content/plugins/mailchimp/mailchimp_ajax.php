<?php
ob_start();//start buffering for the ajax call just in case
include_once('../../../wp-blog-header.php');
include_once('mailchimp_includes.php');
ob_end_clean();ob_end_clean();ob_end_clean();//stop buffering and discard anything output
mailchimpSF_display_widget();
exit;
?>
