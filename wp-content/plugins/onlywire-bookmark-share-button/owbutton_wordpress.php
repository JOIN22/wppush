<?php
/*
Plugin Name: OnlyWire for WordPress [OFFICIAL]
Plugin URI: http://onlywire.com/
Description: Easily post to millions of sites with one button. 
Version: 1.6.6
Author: OnlyWire Engineering
Author URI: http://onlywire.com/
*/

$wpURL = get_bloginfo('wpurl');

include ("postrequest.php");
include "jsonwrapper/jsonwrapper.php";

function ow_function($text) {
    global $post;

    $code = get_option('ow_script');
	$enable_button = get_option('ow_autopost_enable');

	if($enable_button == 'on')
	{
	    if($code) 
		{
			$text .= '<script type="text/javascript" class="owbutton" src="http://onlywire.com/btn/button_'.$code.'" title="'.$post->post_title.'" url="'.get_permalink($post->ID).'"></script>';
	    } 
		else 
		{
        	$text .= '<script type="text/javascript" class="owbutton" src="http://onlywire.com/button" title="'.$post->post_title.'" url="'.get_permalink($post->ID).'"></script>';
	    }
	}
    return $text;
}

add_filter('the_content', 'ow_function');

/**
 * Plugin activation.
 */
register_activation_hook(__FILE__, 'ow_activate');

function ow_activate()
{
	global $wpdb;
	add_option('ow_username');
	add_option('ow_password');
    add_option('ow_autopost');
    add_option('ow_autopost_revisions');
    add_option('ow_script');
	add_option('ow_autopost_enable');
	update_option('ow_autopost_enable', 'on');
}

/**
 * Post admin hooks
 */
add_action('admin_menu', "ow_adminInit");
add_action('publish_post', 'ow_post');
add_action('future_post','ow_post');
add_filter( 'plugin_action_links', 'ow_settings_link', 10, 2 );

/**
 * Adds an action link to the Plugins page
 */
function ow_settings_link($links, $file){
	static $this_plugin;
 
	if( !$this_plugin ) $this_plugin = plugin_basename(__FILE__);
 
	if( $file == $this_plugin ){
		$settings_link = '<a href="options-general.php?page=onlywireoptions">' . __('Settings') . '</a>';
		$links = array_merge( array($settings_link), $links); // before other links
	}
	return $links;
}

function ow_adminInit()
{
	if( function_exists("add_meta_box") )
		add_meta_box("onlywire-post", "OnlyWire Bookmark &amp; Share", "ow_posting", "post", "normal", "high");
	
	add_options_page('OnlyWire Settings', 'OnlyWire Settings', 8, 'onlywireoptions', 'ow_optionsAdmin');
}

function ow_optionsAdmin()
{
?>
    <script>
function getFrameDocument(theId) { 
    
    if(document.getElementById(theId)) {
        var iframe_document = null;
        theId = document.getElementById(theId);
        if (theId.contentDocument) {
            iframe_document = theId.contentDocument;
        }
        else if (theId.contentWindow) {
            iframe_document = theId.contentWindow.document;
        }
        else if (theId.document) {
            iframe_document = theId.document;
        }
        else {
            throw(new Error("Cannot access iframe document."));
        }
        
        return iframe_document;
    }

}
function ajaxObject(url, callbackFunction) {
  var that=this;      
  this.updating = false;
  this.abort = function() {
    if (that.updating) {
      that.updating=false;
      that.AJAX.abort();
      that.AJAX=null;
    }
  }
  this.update = function(passData,postMethod) { 
    if (that.updating) { return false; }
    that.AJAX = null;                          
    if (window.XMLHttpRequest) {              
      that.AJAX=new XMLHttpRequest();              
    } else {                                  
      that.AJAX=new ActiveXObject("Microsoft.XMLHTTP");
    }                                             
    if (that.AJAX==null) {                             
      return false;                               
    } else {
      that.AJAX.onreadystatechange = function() {  
        if (that.AJAX.readyState==4) {             
          that.updating=false;                
          that.callback(that.AJAX.responseText,that.AJAX.status,that.AJAX.responseXML);        
          that.AJAX=null;                                         
        }                                                      
      }                                                        
      that.updating = new Date();                              
      if (/post/i.test(postMethod)) {
        var uri=urlCall+'?'+that.updating.getTime();
        that.AJAX.open("POST", uri, true);
        that.AJAX.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
        that.AJAX.setRequestHeader("Content-Length", passData.length);
        that.AJAX.send(passData);
      } else {
        var uri=urlCall+'?'+passData+'&timestamp='+(that.updating.getTime()); 
        that.AJAX.open("GET", uri, true);                             
        that.AJAX.send(null);                                         
      }              
      return true;                                             
    }                                                                           
  }
  var urlCall = url;        
  this.callback = callbackFunction || function () { };
}
function obj2query(obj, forPHP, parentObject){
   if( typeof obj != 'object' ) return '';

   if (arguments.length == 1)
      forPHP = /\.php$/.test(document.location.href);
   
   var rv = '';
   for(var prop in obj) if (obj.hasOwnProperty(prop) ) {

      var qname = parentObject
         ? parentObject + '.' + prop
         : prop;

      // Expand Arrays
      if (obj[prop] instanceof Array)
         for( var i = 0; i < obj[prop].length; i++ )
            if( typeof obj[prop][i] == 'object' )
               rv += '&' + obj2query( obj[prop][i], forPHP, qname );
            else
               rv += '&' + encodeURIComponent(qname) + (forPHP ? '[]' : '')
                    + '=' + encodeURIComponent( obj[prop][i] );

      // Expand Dates
      else if (obj[prop] instanceof Date)
         rv += '&' + encodeURIComponent(qname) + '=' + obj[prop].getTime();

      // Expand Objects
      else if (obj[prop] instanceof Object)
         // If they're String() or Number() etc
         if (obj.toString && obj.toString !== Object.prototype.toString)
            rv += '&' + encodeURIComponent(qname) + '=' + encodeURIComponent( obj[prop].toString() );
         // Otherwise, we want the raw properties
         else
            rv += '&' + obj2query(obj[prop], forPHP, qname);

      // Output non-object
      else
         rv += '&' + encodeURIComponent(qname) + '=' + encodeURIComponent( obj[prop] );

   }
   return rv.replace(/^&/,'');
}


function serialize(form)
{
    var obj = {};
    for(var i=0; i<form.elements.length; i++) {
        obj[form.elements[i].name] = form.elements[i].value;
    }
    return obj2query(obj);
}
function processData(responseText) {
    // update the hidden ow_script input box with responseText, and submit form
    //console.log(responseText);
    document.getElementById("ow_script").value = responseText; 
    document.getElementById("ow_form").submit();
}

/**
 * Verify the user wants to turn on the "Auto Post All Article Revisions" option -- it is recommended to leave this turned off
 */
function verifyAutoRevisions() {
    
    if (document.getElementById("ow_autopost_revisions").checked) {
        confirm("Enabling this option may cause you to be banned from bookmarking services for excessive submissions.\n\n\'Cancel\' to stop, \'OK\' to enable it.") ? document.getElementById("ow_autopost_revisions").checked = true : document.getElementById("ow_autopost_revisions").checked = false;
    }
}
function auth() {
	 var ow_username = document.getElementById("ow_username").value;
	 var ow_password = document.getElementById("ow_password").value;
	 var url = "<?php echo site_url()?>/wp-content/plugins/onlywire-bookmark-share-button/http_auth_call.php?auth_user="+encodeURIComponent(ow_username.trim())+"&auth_pw="+encodeURIComponent(ow_password.trim());
	 var xmlhttp;
	
	if (window.XMLHttpRequest)
  	{// code for IE7+, Firefox, Chrome, Opera, Safari
 		xmlhttp=new XMLHttpRequest();
  	}
	else
  	{	// code for IE6, IE5
  		xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  	}
  
	xmlhttp.onreadystatechange=function()
  	{   		
  		if (xmlhttp.readyState==4 && xmlhttp.status == 200)
    	{ 
			if(xmlhttp.responseText == 1){
				 func();
				 return false; 
			} else {
				alert("Invalid Username or Password. \nPlease provide correct login Information.");
				 return false;
			}
    	}
  	}

	xmlhttp.open("GET",url,true);
	xmlhttp.send();
}
function func() {
    var ow_iframe_doc = getFrameDocument("ow_iframe"); 
    var ow_iframe_win = document.getElementById("ow_iframe").contentWindow;
    
    var buttonform = ow_iframe_doc.getElementById("thebuttonform");
    //we must call this javascript from within the iframe, this builds the POST data values
    ow_iframe_win.$('selections').value = ow_iframe_win.buildButtonId();
    
    // call the local buttonid.php (ajax) file to make a request with the data from the form to onlywire, and get back the buttonid
    var s = serialize(buttonform);
    var myRequest = new ajaxObject("<?php echo site_url()?>/wp-content/plugins/onlywire-bookmark-share-button/buttonid.php", processData);
    myRequest.update(s);  // Server is contacted here.
}
    </script>
	<div class="wrap">
	<h2>OnlyWire Settings</h2>
	
		<form id="ow_form" method="post" action="options.php" onSubmit="auth(); return false;">
			<?php wp_nonce_field('update-options'); ?>
	
            <input id="ow_script" type="hidden" name="ow_script" value="<?php echo get_option('ow_script'); ?>" />

			<table class="form-table">
				<tr valign="top">
					<th style="white-space:nowrap;" scope="row"><label for="ow_username"><?php _e("OnlyWire username"); ?>:</label></th>
					<td><input id="ow_username" type="text" name="ow_username" value="<?php echo get_option('ow_username'); ?>" /></td>
					<td style="width:100%;">The username you use to login on OnlyWire.com.</td>
				</tr>
				
				<tr valign="top">
					<th style="white-space:nowrap;" scope="row"><label for="ow_password"><?php _e("OnlyWire password"); ?>:</label></th>
					<td><input id="ow_password" type="password" name="ow_password" value="<?php echo get_option('ow_password'); ?>" /></td>
					<td style="width:100%;">The password you use to login on OnlyWire.com.</td>
				</tr>
				<tr valign="top">
					<th style="white-space:nowrap;" scope="row"><label for="ow_autopost"><?php _e("Auto Post All Articles"); ?>:</label></th>
					<td><input id="ow_autopost" type="checkbox" name="ow_autopost" <?php if(get_option('ow_autopost') == 'on') { echo 'checked="true"'; }?> /></td>
					<td style="width:100%;"></td>
				</tr>
				<tr valign="top">
					<th style="white-space:nowrap;" scope="row"><label for="ow_autopost_revisions"><?php _e("Auto Post All Article Revisions"); ?>:</label></th>
					<td style="vertical-align: bottom;"><input id="ow_autopost_revisions" type="checkbox" name="ow_autopost_revisions" onclick="verifyAutoRevisions()" <?php echo get_option('ow_autopost_revisions')=='on'?'checked="checked"':''; ?> /></td>
					<td style="width:100%;"><font style="color:red">&#42;</font>&nbsp;OnlyWire <strong>does <em>not</em></strong> recommend enabling this option.</td>
				</tr>
				<tr valign="top">
					<th style="white-space:nowrap;" scope="row"><label for="ow_autopost_enable"><?php _e("Show Bookmark & Share Button"); ?>:</label></th>
					<td style="vertical-align: bottom;"><input id="ow_autopost_enable" type="checkbox" name="ow_autopost_enable" <?php if(get_option('ow_autopost_enable') == 'on') { echo 'checked="true"'; }?> /></td>
					<td style="width:100%;"></td>
				</tr>
			</table>
            <iframe id="ow_iframe" src="<?php echo site_url()."/wp-content/plugins/onlywire-bookmark-share-button/iframe.php"?>" style="width: 100%; height: 710px;" ></iframe>
	
			<input type="hidden" name="action" value="update" />
			<input type="hidden" name="page_options" value="ow_username,ow_password,ow_autopost,ow_autopost_revisions,ow_script,ow_autopost_enable" />
	
			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>
		</form>
	</div>

<?php
}

/**
 * Function taken from Revision Control WordPress Plugin
 * http://wordpress.org/extend/plugins/revision-control/
 * Determines the post/page's ID based on the 'post' and 'post_ID' POST/GET fields.
 */
function ow_get_page_id() {
	foreach ( array( 'post_ID', 'post' ) as $field )
		if ( isset( $_REQUEST[ $field ] ) )
			return absint($_REQUEST[ $field ]);

	if ( isset($_REQUEST['revision']) )
		if ( $post = get_post( $id = absint($_REQUEST['revision']) ) )
			return absint($post->post_parent);

	return false;
}

/**
 * Code for the meta box.
 * the post of this goes to the function ow_post()
 */
function ow_posting()
{
    global $post_ID;
    
    $ow_post_type_id = get_post(ow_get_page_id());
    $ow_post_type = $ow_post_type_id->post_status;

    //Check to see if it's a revision ("auto-draft" or "draft" return type is a new post)
    if ( ($ow_post_type != 'auto-draft') && ($ow_post_type != 'draft') ){	
?>
    <label for="ow_post">
        <input type="checkbox" <?php echo get_option('ow_autopost_revisions')=='on'?'checked="checked"':''; ?> id="ow_post" name="ow_post" /> Post this revision to OnlyWire	
    </label>
    
<?php	
    } else {
?>	    
   <label for="ow_post">
        <input type="checkbox" <?php echo get_option('ow_autopost')=='on'?'checked="checked"':''; ?> id="ow_post" name="ow_post" /> Post this to OnlyWire	
    </label>
<?php
    }
}

/**
 * Return a random tag if none are supplied in the post
 */
function getDefaultTag() {
    $tags = array("bookmark","favorite","blog","social","web","internet","share","organize","manage","reference","tag","save");
    $rand_keys = array_rand($tags,2);
    
    return $tags[$rand_keys[0]];
}

/**
 * @param The post ID
 * Posts this post to OnlyWire
 */
function ow_post( $postID )
{
	global $wpdb;

	// Get the correct post ID if revision.
	if ( $wpdb->get_var("SELECT post_type FROM $wpdb->posts WHERE ID=$postID")=='revision') {
		$postID = $wpdb->get_var("SELECT post_parent FROM $wpdb->posts WHERE ID=$postID");
	}

    
    if(isset($_POST['ow_post']) && $_POST['ow_post'] == 'on') {
        // the checkbox is selected, let's post to onlywire with user credentials
        $username = get_option('ow_username');
        $password = get_option('ow_password');
        if($username && $password) {
            // we have credentials, let's login on Onlywire with this account and post the $postID
            $password = array($username, md5($password));
            $data = array();
            $data['token'] = implode('%26', $password);

            // get the services
            $gservices = GetRequest("http://onlywire.com/widget/getWidgetData.php?token=".$data['token']);
            // gservices is not "jsonp(..);" let's remove "jsonp(" and ");"
            $gservices = str_replace('jsonp(','',$gservices[1]);
            $gservices = str_replace(');','',$gservices);
            $jservices = json_decode($gservices,true);

            // $jservices->services is an array of objects
            $service_ids = array();
            foreach($jservices['services'] as $jobj) {
                array_push($service_ids, $jobj['pk_id']);
            }
            $services =  implode(',',$service_ids);

            $data['service'] = $services; 

            $post = get_post($postID); 
            $tags = get_the_tags($postID);
            if($tags) {
                 $tagarr = array();
                 // build tags string
                 foreach($tags as $tag) {
                     array_push($tagarr, str_replace(' ', '-', $tag->name));
                 }
                 $tagstring = implode(' ', $tagarr);
            } else {
                   $tagstring = getDefaultTag();
            }
			
			$categories = get_the_category($postID);
			$categorystring = '';
			if($categories) {
				$categoryarr = array();
				// build category string
				foreach($categories as $category)
				{
					array_push($categoryarr, str_replace(' ', '-', $category->name));
				}
				
				$categorystring = implode(' ', $categoryarr);
			}
			
			if(trim($categorystring) != '') {
				$tagstring = $tagstring.' '.$categorystring;
			}	
						
            $data['url'] = urlencode(get_permalink($postID));
            $data['title'] = urlencode($post->post_title);
            $data['tags'] = $tagstring;
            $d = 'm\/d\/Y H\.i T';
            $data['scheduledtime'] = get_post_time($d,true,$postID,false);

            $a = PostRequest("http://onlywire.com/b/saveurl2.php","", $data);

        }
    }

}

?>
