<?php
if(!defined('ABSPATH'))
  die('You are not allowed to call this page directly.');

require_once(PRLI_MODELS_PATH.'/PrliLink.php');
require_once(PRLI_MODELS_PATH.'/PrliClick.php');
require_once(PRLI_MODELS_PATH.'/PrliGroup.php');
require_once(PRLI_MODELS_PATH.'/PrliUtils.php');
require_once(PRLI_MODELS_PATH.'/PrliUrlUtils.php');
require_once(PRLI_MODELS_PATH.'/PrliLinkMeta.php');
require_once(PRLI_MODELS_PATH.'/PrliUpdate.php');

global $prli_link;
global $prli_link_meta;
global $prli_click;
global $prli_group;
global $prli_utils;
global $prli_url_utils;
global $prli_update;

$prli_link      = new PrliLink();
$prli_link_meta = new PrliLinkMeta();
$prli_click     = new PrliClick();
$prli_group     = new PrliGroup();
$prli_utils     = new PrliUtils();
$prli_url_utils = new PrliUrlUtils();
$prli_update    = new PrliUpdate();

global $prli_db_version;
global $prlipro_db_version;

$prli_db_version = 12; // this is the version of the database we're moving to
$prlipro_db_version = 3; // this is the version of the database we're moving to

// Load Controller(s)
require_once( PRLI_CONTROLLERS_PATH.'/PrliAppController.php');

global $prli_app_controller;

$prli_app_controller = new PrliAppController();

function prli_get_main_message($message='',$expiration=1800) // Get new messages every 1/2 hour
{
  global $prli_update;

  // Set the default message
  if(empty($message)) {
    $message = __( "Get started by <a href=\"?page=pretty-link/prli-links.php&action=new\">" .
                   "adding a URL</a> that you want to turn into a pretty link.<br/>" .
                   "Come back to see how many times it was clicked." , 'pretty-link');
  }

  $messages = get_site_transient('_prli_messages');

  // if the messages array has expired go back to the mothership
  if(!$messages)
  {
	$remote_controller = $prli_update->pro_is_installed_and_authorized() ? 'prlipro' : 'prli';
	$message_mothership = "http://prettylinkpro.com/index.php?controller={$remote_controller}&action=json_messages";

    if( !class_exists( 'WP_Http' ) )
      include_once( ABSPATH . WPINC . '/class-http.php' );
	
    $http = new WP_Http;
    $response = $http->request( $message_mothership );
    
    if( isset($response) and
        is_array($response) and // if response is an error then WP_Error will be returned
        isset($response['body']) and
        !empty($response['body']))
      $messages = json_decode($response['body']);
    else
      $messages = array($message);
    
    set_site_transient("_prli_messages", $messages, $expiration);
  }

  if(empty($messages) or !$messages or !is_array($messages))
    return $message;
  else
    return $messages[array_rand($messages)];
}
