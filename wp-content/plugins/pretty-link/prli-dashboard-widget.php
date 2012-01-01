<?php
if(!defined('ABSPATH'))
  die('You are not allowed to call this page directly.');

  require_once('prli-config.php');
  require_once(PRLI_MODELS_PATH . '/models.inc.php');

  global $prli_group,$prli_link,$prli_blogurl;

  $groups = $prli_group->getAll('',' ORDER BY name');
  $values = setup_new_vars($groups);

  require_once(PRLI_VIEWS_PATH . "/prli-dashboard-widget/widget.php");
