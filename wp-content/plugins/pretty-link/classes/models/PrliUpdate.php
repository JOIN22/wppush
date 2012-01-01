<?php
if(!defined('ABSPATH')) die('You are not allowed to call this page directly.');

/** Okay, this class is not a pure model -- it contains all the functions
  * necessary to successfully provide an update mechanism for Pretty Link Pro
  */
class PrliUpdate
{
  public $plugin_name;
  public $plugin_slug;
  public $plugin_url;
  public $pro_script;
  public $pro_mothership;
  public $get_started_page;
  
  public $pro_cred_store;
  public $pro_auth_store;
  
  public $pro_username_label;
  public $pro_password_label;
  
  public $pro_username_str;
  public $pro_password_str;
  
  public $pro_error_message_str;

  public $activation_order;
  
  public $pro_username;
  public $pro_password;
  public $pro_mothership_xmlrpc_url;

  function __construct()
  {
    // Where all the vitals are defined for this plugin
    $this->plugin_name            = 'pretty-link/pretty-link.php';
    $this->plugin_slug            = 'pretty-link';
    $this->plugin_url             = 'http://prettylinkpro.com';
    $this->pro_script             = PRLI_PATH . '/pro/pretty-link-pro.php';
    $this->pro_mothership         = 'http://prettylinkpro.com';
    $this->get_started_page       = 'pretty-link/prli-links.php';
    $this->pro_cred_store         = 'prlipro-credentials';
    $this->pro_auth_store         = 'prlipro_activated';
    $this->pro_username_label     = __('Pretty Link Pro Username', 'pretty-link');
    $this->pro_password_label     = __('Pretty Link Pro Password', 'pretty-link');
    $this->pro_error_message_str  = __('Your Pretty Link Pro Username or Password was Invalid', 'pretty-link');
    $this->activation_order       = 'dontcare';
    
    // Don't modify these variables
    $this->pro_username_str = 'proplug-username';
    $this->pro_password_str = 'proplug-password';
    $this->pro_mothership_xmlrpc_url = $this->pro_mothership . '/xmlrpc.php';
    
    add_filter( 'pre_set_site_transient_update_plugins', array( &$this, 'queue_update' ) );
    
    // Retrieve Pro Credentials
    $creds = get_option($this->pro_cred_store);
    if($creds and is_array($creds))
    {
      extract($creds);
      $this->pro_username = ((isset($username) and !empty($username))?$username:'');
      $this->pro_password = ((isset($password) and !empty($password))?$password:'');
    }

    if(isset($this->activation_order) and ($this->activation_order != 'dontcare'))
      add_action("activated_plugin", array(&$this,'reorder_activation'));
  }

  public function reorder_activation() {
    // ensure path to this file is via main wp plugin path
    $this_plugin = plugin_basename(trim($this->pro_script));
    $active_plugins = get_option('active_plugins');
    $this_plugin_key = array_search($this_plugin, $active_plugins);
    array_splice($active_plugins, $this_plugin_key, 1);

    if($this->activation_order == 'last')
      array_push($active_plugins, $this_plugin);
    else
      array_unshift($active_plugins, $this_plugin);
    
    update_option('active_plugins', $active_plugins);
  }

  public function pro_is_installed()
  {
    return file_exists($this->pro_script);
  }

  public function pro_is_authorized($force_check=false)
  {
    if( !empty($this->pro_username) and 
        !empty($this->pro_password) )
    {
      $authorized = get_option($this->pro_auth_store);
      
      if(!$force_check and isset($authorized) and $authorized and ($authorized=='true' or $authorized=='false'))
        return ($authorized=='true');
      else
      {
        $new_auth = $this->authorize_user($this->pro_username,$this->pro_password);
        $new_auth = ($new_auth?'true':'false');
        update_option($this->pro_auth_store, $new_auth);
        return ($new_auth=='true');
      }
    }

    return false;
  }

  public function pro_is_installed_and_authorized()
  {
    return ($this->pro_is_installed() and $this->pro_is_authorized());
  }

  public function authorize_user($username, $password)
  {
    include_once( ABSPATH . 'wp-includes/class-IXR.php' );

    $client = new IXR_Client( $this->pro_mothership_xmlrpc_url );

    if ( !$client->query( 'proplug.is_user_authorized', $username, $password ) )
      return false;

    return $client->getResponse();
  }

  public function user_allowed_to_download()
  {
    include_once( ABSPATH . 'wp-includes/class-IXR.php' );

    $client = new IXR_Client( $this->pro_mothership_xmlrpc_url );

    if ( !$client->query( 'proplug.is_user_allowed_to_download', $this->pro_username, $this->pro_password, get_option('home') ) )
      return false;

    return $client->getResponse();
  }

  public function pro_cred_form()
  {
    if(isset($_POST) and
       isset($_POST['process_cred_form']) and
       $_POST['process_cred_form'] == 'Y')
    {
      if($this->process_pro_cred_form())
      {
        if(!$this->pro_is_installed())
        {
          $inst_install_url = wp_nonce_url('update.php?action=upgrade-plugin&plugin=' . $this->plugin_name, 'upgrade-plugin_' . $this->plugin_name);

          ?>
<div id="message" class="updated fade">
<strong><?php printf(__('Your Username & Password was accepted<br/>Now you can %1$sUpgrade Automatically!%2$s', 'pretty-link'), "<a href=\"{$inst_install_url}\">","</a>"); ?></strong>
</div>
          <?php
        }
        else
        {
          $plugin_url = wp_nonce_url("admin.php?page={$this->get_started_page}", "configure-plugin_{$this->get_started_page}");

          ?>
<div id="message" class="updated fade">
<strong><?php printf(__('Your Username & Password was accepted<br/>Now you can %1$sGet Started!%2$s', 'pretty-link'), "<a href=\"{$plugin_url}\">","</a>"); ?></strong>
</div>
          <?php
        }
      }
      else
      {
        ?>
<div class="error">
  <ul>
    <li><strong><?php _e('ERROR', 'pretty-link'); ?></strong>: <?php echo $this->pro_error_message_str; ?></li>
  </ul>
</div>
        <?php
      }
    }

    $this->display_pro_cred_form();
  }

  public function display_pro_cred_form()
  {
    // Yah, this is the view for the credentials form -- this class isn't a true model
    $this_uri = preg_replace('#&.*?$#', '', str_replace( '%7E', '~', $_SERVER['REQUEST_URI']));
    extract($this->get_pro_cred_form_vals());
    ?>
<form name="cred_form" method="post" action="<?php echo $this_uri; ?>">
  <input type="hidden" name="process_cred_form" value="Y">
  <?php wp_nonce_field('cred_form'); ?>

  <table class="form-table">
    <tr class="form-field">
      <td valign="top" width="15%"><?php echo $this->pro_username_label; ?>:</td>
      <td width="85%">
        <input type="text" name="<?php echo $this->pro_username_str; ?>" value="<?php echo $username; ?>"/>
      </td>
    </tr>
    <tr class="form-field">
      <td valign="top" width="15%"><?php echo $this->pro_password_label; ?>:</td>
      <td width="85%">
        <input type="password" name="<?php echo $this->pro_password_str; ?>" value="<?php echo $password; ?>"/>
      </td>
    </tr>
  </table>
  <p class="submit">
    <input type="submit" name="Submit" value="<?php _e('Save', 'pretty-link'); ?>" />
  </p>
</form>
    <?php
  }

  public function process_pro_cred_form()
  {
    $creds = $this->get_pro_cred_form_vals();
    $user_authorized = $this->authorize_user($creds['username'], $creds['password']);

    if(!empty($user_authorized) and $user_authorized)
    {
      update_option($this->pro_cred_store, $creds);
      update_option($this->pro_auth_store, ($user_authorized?'true':'false'));

      extract($creds);
      $this->pro_username = ((isset($username) and !empty($username))?$username:'');
      $this->pro_password = ((isset($password) and !empty($password))?$password:'');

      if(!$this->pro_is_installed())
        $this->manually_queue_update();
    }

    return $user_authorized;
  }

  public function get_pro_cred_form_vals()
  {
    $username = ((isset($_POST[$this->pro_username_str]))?$_POST[$this->pro_username_str]:$this->pro_username);
    $password = ((isset($_POST[$this->pro_password_str]))?$_POST[$this->pro_password_str]:$this->pro_password);

    return compact('username','password');
  }

  public function get_download_url($version)
  {
    include_once( ABSPATH . 'wp-includes/class-IXR.php' );

    $client = new IXR_Client( $this->pro_mothership_xmlrpc_url );

    if( !$client->query( 'proplug.get_download_url', $this->pro_username, $this->pro_password, $version ) )
      return false;

    return $client->getResponse();
  }

  public function get_current_version()
  {
    include_once( ABSPATH . 'wp-includes/class-IXR.php' );

    $client = new IXR_Client( $this->pro_mothership_xmlrpc_url );

    if( !$client->query( 'proplug.get_current_version' ) )
      return false;

    return $client->getResponse();
  }

  public function queue_update($transient, $force=false)
  {
    if($this->pro_is_authorized())
    {
      // If pro is authorized but not installed then we need to force an upgrade
      if(!$this->pro_is_installed())
        $force=true;
      
      $curr_version = $this->get_current_version();
      $installed_version = $transient->checked[$this->plugin_name];
	  
      if( $force or version_compare( $curr_version, $installed_version, '>' ) )
      {
        $download_url = $this->get_download_url($curr_version);
        
        if(!empty($download_url) and $download_url and $this->user_allowed_to_download())
        {
          if(isset($transient->response[$this->plugin_name]))
            unset($transient->response[$this->plugin_name]);
          
          $transient->response[$this->plugin_name]              = new stdClass();
          $transient->response[$this->plugin_name]->id          = '0';
          $transient->response[$this->plugin_name]->slug        = $this->plugin_slug;
          $transient->response[$this->plugin_name]->new_version = $curr_version;
          $transient->response[$this->plugin_name]->url         = $this->plugin_url;
          $transient->response[$this->plugin_name]->package     = $download_url;
        }
      }
      else
      {
        if(isset($transient->response[$this->plugin_name]))
          unset($transient->response[$this->plugin_name]);
      }
    }

    return $transient;
  }

  public function manually_queue_update() {
    $transient = get_site_transient("update_plugins");
    set_site_transient("update_plugins",$this->queue_update($transient, true));
  }
}
