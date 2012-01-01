<?php
class PluginCentral
{
    var $local_version;
    var $plugin_url;
    var $key;
    
    function PluginCentral()
    {
        $this->__construct();
    }
    
    function __construct()
    {
        $this->local_version = '2.4';
        $this->plugin_url = trailingslashit(get_bloginfo('wpurl')) . PLUGINDIR . '/' . dirname(plugin_basename(__FILE__));
        $this->key = 'plugin-central';
        
        global $wp_version;
        if (version_compare($wp_version, '2.5', '<'))
        {
            exit(sprintf(__('Plugin Central requires WordPress 2.5 or newer. <a href="%s" target="_blank">Please update first!</a>', 'dashspam'), 'http://codex.wordpress.org/Upgrading_WordPress'));
        }

        add_action('admin_menu', array($this, 'add_pages'));
        add_action('admin_head', array($this, 'head'));
        add_action('activity_box_end', array($this, 'dash'));
        add_filter('navbar_info', array($this, 'navbar_info'));
    }
    
    function get_trans()
    {
   
   	  global $wp_version;
    
    	if (version_compare($wp_version, '2.8', '<'))      
         return get_option('update_plugins');      
      
      else if (version_compare($wp_version, '3.0', '<'))
    	   return get_transient('update_plugins');
      
      else
    	   return get_site_transient('update_plugins');
            	
    }
    
    
    function handle_download($plugin_name, $package)
    {
        global $wp_version;
        
        if (version_compare($wp_version, '2.8', '<'))
        {
            $this->update_plugin_advanced($plugin_name, $package);
        }
        else  if (version_compare($wp_version, '3.0', '<'))
        {
           
            include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
                       
            $upgrader = new Plugin_Upgrader(); 
            
            $upgrader->install($package);
            
        	if ($upgrader->plugin_info())
        	{
        		echo '<a href="' . wp_nonce_url('plugins.php?action=activate&amp;plugin=' . $upgrader->plugin_info(), 'activate-plugin_' . $plugin_file) . '" title="' . esc_attr__('Activate this plugin') . '" target="_parent">' . __('Activate Plugin') . '</a>';
        	}
        
        }			
        else {
        	
        	include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';
                       
          $upgrader = new Plugin_Upgrader( new Plugin_Installer_Skin( compact('type', 'title', 'nonce', 'url') ) ); 
            
          $res=$upgrader->install($package);            
          
          if (!$upgrader->plugin_info())
        	{
        		echo $res;
        	}
          
       }
    }
    
    function update_plugin_advanced($plugin_name, $package)
    {
        echo "Downloading update from $package <br />";
        $file = download_url($package);
        
        if (is_wp_error($file)) 
        {
            echo 'Download failed: ' . $file->get_error_message();
            return;
        }
        
        echo 'Unpacking the plugin <br />';
        
        //plugin dir
        $result = $this->unzip($file, ABSPATH . PLUGINDIR . '/');
        
        // Once extracted, delete the package
        unlink($file);
        
        if ($result)
        {
            echo '<br /><strong>Plugin installed successfully.</strong><br /><br />';
        }
        else 
        {
            echo "<br />Error installing the plugin.<br /><br />You can try installing the plugin manually: <a href=\"$package\">$package</a><br /><br />";
        }
    }
    
    function unzip($file, $dir)
    {
        if (!current_user_can('edit_files')) 
        {
            echo 'Oops, sorry you are not authorized to do this';
            return false;
        }
        if (!class_exists('PclZip')) 
        {
            require_once(ABSPATH . 'wp-admin/includes/class-pclzip.php');
        }
        
        
        $unzip_archive = new PclZip($file);
        $list = $unzip_archive->properties();
        if (!$list['nb']) return false;
        
        echo 'Copying the files<br />';
        $result = $unzip_archive->extract(PCLZIP_OPT_PATH, $dir);
        if (!$result) 
        {
            echo 'Could not unarchive the file: ' . $unzip_archive->errorInfo(true) . ' <br />';
            return false;
        } 
          //print_r($result);
        foreach ($result as $item) 
        {
            if ($item['status'] != 'ok')
            {
                echo $item['stored_filename'] . ' ... ' . $item['status'] . '<br />';
            }
        }
        
        return true;
    }
    
    function dash()
    {
        global $wp_version;
    
        if (!current_user_can('edit_plugins')) 
        {
            return;
        }
        
        echo '<h4 style="padding-top:12px">Plugin Central</h4>';
        if ('upgrade-plugins' == $_GET['action']) 
        {
            $plugins = get_plugins();
    
           
            
            $update_plugins = $this->get_trans();
            
            echo '<ul>';
            foreach ($plugins as $file => $p) 
            {
                $result += $this->row($file, $p, 1, $update_plugins->response);
            }
            echo '</ul>';
      
            if ($result == 0) 
            {
                echo '<p>All plugins are up to date.</p>';
            }
      
            $this->dash_install();
            
            return;
        }
    
        if ('pc_delete' == $_GET['action']) 
        {
            $file = $_GET['file'];
            
            echo '<p>';
            $this->delete_plugin($file);
            echo '</p>';
        }
        
        if ('pc_ignore' == $_GET['action']) 
        {
            $file = $_GET['file'];
            $name = $_GET['name'];

            $options = get_option('pc_ignored_plugins');
            if ($options && !array_search($file, $options))
            {
                $options = array_merge($options, array($file));
            }
            elseif (!$options)
            {
                $options = array($file);
            }
            
            update_option('pc_ignored_plugins', $options);
            
            echo "<p>$name added to ignore list.</p>";
        }
              
        wp_update_plugins();

        $plugins = get_plugins();
    
        $result = 0;
        echo '<ul>';
    
       $update_plugins = $this->get_trans();

        
        $wp_29_on = !version_compare($wp_version, '2.9', '<');
        $options = get_option('pc_ignored_plugins');

        if ($wp_29_on)
        {
            echo '<form autocomplete="off" method="post" action="update-core.php?action=do-plugin-upgrade" name="upgrade-plugins" class="upgrade">';
            wp_nonce_field('upgrade-core');           
        }
        			
        if (!empty($update_plugins->response))
        {
            foreach ($plugins as $file => $p) 
            {
                // check to see if the plugin is ignored
                if (in_array($file, (array)$options)) continue;
                
                // we need to add something for 2.9 on batch upgrade feature
                if ($wp_29_on && $update_plugins->response[$file]->package)
                {
                    echo '<input type="checkbox" name="checked[]" value="' . esc_attr($file) . '" checked="checked" style="display:none" />';
                } 
    
                $result += $this->row($file, $p, 0, $update_plugins->response);
            }
        }
        echo '</ul>';
        
        if ($result == 0) 
        {
            echo '<p>All plugins are up to date.</p>';
        } 
        elseif ($result > 1) 
        {
            if ($wp_29_on)
            {
                echo '<p><input id="upgrade-plugins-2" class="button" type="submit" value="Update All" name="upgrade" /></p>';
            }
            else
            {
        	    echo '<p><a href="index.php?action=upgrade-plugins">Update All</a></p>';
            }
        }
        
        if ($wp_29_on)
        {
            echo '</form>';            
        }
            	               
        $this->dash_install();
    }
    
    function row($file, $p, $update, $response)
    {
        $options = get_option('pc_ignored_plugins');      
        
        if (!isset($response[$file]) || ($options && in_array($file, $options)))
        {
            return 0;
        }
                
        $r = $response[$file];      
        
        if (empty($r->package))
        {
            printf(__('<li>%1$s %3$s <a href="%2$s">download</a> <em>(automatic update unavailable)</em>.</li>'), $p['Name'], $r->url, $r->new_version);
        }
        else 
        {
            if ($update == 1) 
            {
                //Check now, It'll be deactivated by the next line if it is,
                $was_activated = is_plugin_active($file);
                
                echo "<li><b>" . $p['Name'] . "</b>";
                echo '<iframe style="border:0" width="100%" height="170px" src="' . wp_nonce_url("update.php?action=upgrade-plugin&amp;plugin=$file&amp;navbar_noshow=1", 'upgrade-plugin_' . $file) . '"></iframe>';
                
                echo "</li>";
                $return = 2;
            } 
            else 
            {
                if (!in_array($file, get_option('active_plugins'))) 
                {
                    printf(__('<li>%1$s %3$s - <a href="%2$s">Download</a> | <a href="%4$s">Update</a> | <a href="javascript:verifyPlugin(\'%5$s\', \'Are you sure you want to ignore future updates of this plugin?\')">Ignore</a> | <a href="javascript:verifyPlugin(\'index.php?action=pc_delete&file=' . $file . '\', \'Are you sure you want to delete this plugin? Do this at your own risk!\')">Delete</a> | <a href="%6$s">Change Log</a></li>'), $p['Name'], $r->url, $r->new_version, wp_nonce_url("update.php?action=upgrade-plugin&amp;plugin=$file", 'upgrade-plugin_' . $file), urlencode("index.php?action=pc_ignore&file=" . $file . "&name=" . $p["Name"]), $r->url.'changelog/');
                } 
                else
                {
                    printf(__('<li><strong>%1$s %3$s</strong> - <a href="%2$s">Download</a> | <a href="%4$s">Update</a> | <a href="javascript:verifyPlugin(\'%5$s\', \'Are you sure you want to ignore future updates of this plugin?\')">Ignore</a> | <a href="%6$s">Changelog</a></li>'), $p['Name'], $r->url, $r->new_version, wp_nonce_url("update.php?action=upgrade-plugin&amp;plugin=$file", 'upgrade-plugin_' . $file), urlencode("index.php?action=pc_ignore&file=" . $file . "&name=" . $p["Name"]),$r->url.'changelog/');
                }
            }
        }
        
        $this->display_info_row($file);
        
        return 1;
    }
    
    function display_info_row($file)
    { 
        global $wp_version;
		$current =  $this->get_trans();

		if (!isset($current->response[$file])) return false;

		$output = '';

		$r = $current->response[$file];

		include_once( ABSPATH . 'wp-admin/includes/plugin-install.php' );
		$columns = version_compare($wp_version, '2.7.999', '>' ) ? 3 : 5;

        $api = @plugins_api('plugin_information', array('slug' => $r->slug, 'fields' => array('tested' => false, 'requires' => false, 'rating' => false, 'downloaded' => false, 'downloadlink' => false, 'last_updated' => false, 'homepage' => false, 'tags' => false, 'sections' => true) ));
        
        if (!is_wp_error($api) && current_user_can('update_plugins')) 
        {
        	$class = is_plugin_active($file) ? 'active' : 'inactive';
        
        	$class_tr = version_compare($wp_version, '2.7.999', '>' ) ? 
        		' class="plugin-update-tr second ' . $class . '"' : '';
        
        	if (isset($api->sections['changelog'])) 
        	{
        		$changelog = $api->sections['changelog'];

        		if(preg_match_all('/(<h4>|<p><strong>)(.*)(<\/h4>|<\/strong><\/p>)[\n|\r]{0,}<ul>(.*)<\/ul>[\w|\W]{0,}/isU', $changelog, $changelog_result)) 
        		{
        			$output .= '<div class="apu-changelog ' . $class . '">';
        			$output .= trim($changelog_result[0][0]);
        			$output .= '</div>';
        		}
        	}
        } 

		echo $output;
	}
	
    function dash_install()
    {
        echo '<p><a id="extra-toggle" href="#">Install new plugin</a></p>';
?>
<form id="extra" name="form_apu" method="post" action="plugins.php?page=plugin-central/plugin-central.class.php">
    <?php wp_nonce_field($this->key); ?>
	<div class="submit">
  		<p>
  			Enter the plugin Name or URL to the plugin zip installation file:<br />
  			<input id="multi_plugins"  name="multi_plugins" value="" size="70"/>     
  			<input type="submit" name="apu_update" value="Install Plugin &raquo;" />
  		</p>
  		<p><a href="plugins.php?page=plugin-central/plugin-central.class.php">Install multiple plugins</a></p>
	</div>
</form>
<?php
    }
    
    function navbar_info($content)
    {
        global $wp_version;
        $ret = '';
    
        if (!current_user_can('edit_plugins')) 
        {
            return;
        }                 
    
        wp_update_plugins();
        
        if (!function_exists('get_plugins'))
        {
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        } 
    		
        $plugins = get_plugins();
        $result = 0;
    
       $update_plugins = $this->get_trans();		      
    			
    	if (!empty($update_plugins->response))
    	{
            foreach ($plugins as $file => $p) 
            {
                $options = get_option('pc_ignored_plugins');      
                $r = $update_plugins->response[$file];                      
                if (empty($r->package) || !isset($update_plugins->response[$file]) || ($options && in_array($file, $options)))
                {
                }
                else
                { 	
            	    $result++;
                }     			
            }
    	}
    
        if ($result == 0) 
        {
            ;//$ret= "<p>Plugins up to date.</p>";
        } 
        else 
        {
            $ret='<p><a href="' . get_bloginfo('wpurl') . '/wp-admin/index.php?action=upgrade-plugins">Update ' . $result . ' plugins</a></p>';
        }            	                    
        
        return $content.$ret;
    }
    
    function head()
    {
?>
<script type="text/javascript">
function verifyPlugin(url, text)
{
    if (confirm(text))
    {
    	document.location = url;
    }

	return void(0);
}


jQuery(function($){
	$("#extra").css("display","none");
	$("#extra-toggle").click(function(e){
		e.preventDefault();
		$("#extra").toggle("fast");
	});
});
</script>
<style type="text/css">
.apu-changelog 
{
	background-color:#FFFBE4;
	border-color:#DFDFDF;
	border-style:solid;
	border-width:1px;
	margin:5px;
	padding:3px 5px;
}		
</style>
    <?php
    }
    
    function get_plugin($plugin_name)
    {
        $name = $plugin_name;
        $plugin = $plugin_name;
        $description = '';
        $author = '';
        $version = '0.1';
        
        $plugin_file = "$name.php";
        
        return array(
        	'Name' => $name, 
        	'Title' => $plugin, 
        	'Description' => $description, 
        	'Author' => $author, 
        	'Version' => $version
        );
    }
    
    function get_packages($plugins_arr)
    {
        global $wp_version;
        
        if (!function_exists('fsockopen')) return false;
        
        foreach ($plugins_arr as $val) 
        {
            $val = trim($val);
            
            if (end(explode(".", $val)) == 'zip')
            {
               $this->handle_download("temp", $val);
            }
            else 
            {
                $plugins[plugin_basename($val . ".php")] = $this->get_plugin($val);
                $send = 1;
            }
        }
        
        //$plugins = get_plugins();
        
        if ($send) 
        {
            $to_send->plugins = $plugins;
            
            $send = serialize($to_send);
            
            $request = 'plugins=' . urlencode($send);
            $http_request = "POST /plugins/update-check/1.0/ HTTP/1.0\r\n";
            $http_request .= "Host: api.wordpress.org\r\n";
            $http_request .= "Content-Type: application/x-www-form-urlencoded; charset=" . get_option('blog_charset') . "\r\n";
            $http_request .= "Content-Length: " . strlen($request) . "\r\n";
            $http_request .= 'User-Agent: WordPress/' . $wp_version . '; ' . get_bloginfo('url') . "\r\n";
            $http_request .= "\r\n";
            $http_request .= $request;
            
            //echo $http_request."<br><br>";
            
            $response = '';
            if (false !== ($fs = @fsockopen('api.wordpress.org', 80, $errno, $errstr, 3)) && is_resource($fs)) 
            {
                fwrite($fs, $http_request);
                
                while (!feof($fs))
                {
                    // One TCP-IP packet
                    $response .= fgets($fs, 1160);
                }
                
                fclose($fs);
                //echo $response;
                $response = explode("\r\n\r\n", $response, 2);
            }
            
            
            $response = unserialize($response[1]);
            
            $i = 0;
            foreach ($plugins_arr as $val) 
            {
                ++$i;
                if ($plugins[plugin_basename("$val.php")]) 
                {
                    if ($response) 
                    {
                        $r = $response[plugin_basename("$val.php")];
                        if (!$r) 
                        {
                            echo '<p class="not-found">' . $i . '. <strong>' . $val . '</strong> not found. Try <a href="http://google.com/search?q=' . $val . ' +wordpress">manual</a> install.</p>';
                        } 
                        elseif ($r->package) 
                        {
                            $this->_flush("<p class=\"found\">$i. Found <strong>" .stripslashes($val). "</strong> ($r->slug, version $r->new_version). Processing installation...</strong></p>");
                            $this->handle_download($r->slug, $r->package);
                        } 
                        else
                        {
                           echo '<p class="not-found">' . $i . '. Package for <strong><em>' . $val . '</em></strong> not found. Try <a href="' . $r->url . '">manual</a> install.</p>';
                        }
                    } 
                    else
                    {
                        echo '<p class="not-found">' . $i . '. <strong>' . $val . '</strong> not found. Try <a href="http://google.com/search?q=' . $val . ' +wordpress">manual</a> install.</p>';
                    }
                }
            }
        }
    }
    
    function copy_files($dir_from, $dir_to, $include_sub_dirs = true)
    {
        if (!current_user_can('edit_files')) 
        {
            echo 'Oops sorry you are not authorized to do this';
            return false;
        }
        
        //check if we have both the directories
        //older versions may now have new dirs so create them
        if (!$dir = opendir($dir_to)) 
        {
            mkdir($dir_to . '/', 0757);
            closedir($dir_to);
        }
        
        if (@opendir($dir_from)) 
        {
            closedir($dir_from);
            $dir = dir($dir_from);
            chmod($dir_from, 0757);
            
            while ($item = $dir->read()) 
            {
                if ((is_dir("$dir_from/$item") && $item != '.' && $item != '..') && $include_sub_dirs) 
                {
                    // recursive
                    $this->copy_files("$dir_from/$item", "$dir_to/$item", $include_sub_dirs);
                } 
                else 
                {
                    if ($item != '.' && $item != '..' && !is_dir("$dir_from/$item")) 
                    {
                        echo "Copying $item <br />";
                        if (@copy("$dir_from/$item", "$dir_to/$item")) 
                        {
                            echo "Overwriting file $item to $dir_to <br />";
                        } 
                        else 
                        {
                            echo "ERROR: Could not copy $dir_from/$item to $dir_to / <br />";
                            return false;
                        }
                    }
                }
            }
        } 
        else 
        {
            echo "ERROR: Could not read either the source directory $dir_from or the target directory $dir_to. <br />";
            return false;
        }
        
        return true;
    }
    
    function update_plugin($plugin_name, $package)
    {
        global $wp_filesystem;
        
        if (!$wp_filesystem || !is_object($wp_filesystem))
        {
            WP_Filesystem($credentials);
        }
        
        if (!is_object($wp_filesystem)) 
        {
            echo '<strong><em>Could not access filesystem.</strong></em><br /><br />';
            return;
        }
        
        if ($wp_filesystem->errors->get_error_code()) 
        {
            echo '<strong><em>Filesystem error ' . $wp_filesystem->errors->get_error_message() . '</strong></em><br /><br />';
            return;
        }
        
        //Get the Base folder
        $base = $wp_filesystem->get_base_dir();
        
        if (empty($base)) 
        {
            echo '<strong><em>Unable to locate WordPress directory.</strong></em><br /><br />';
            return;
        }
        
        $this->_flush("Downloading file from $package<br />");

        $file = download_url($package);
        
        if (is_wp_error($file)) 
        {
            echo '<strong><em>Download failed : ' . $file->get_error_message() . '</strong></em><br /><br />';
            return;
        }
        
        $working_dir = "{$base}wp-content/upgrade/$plugin_name";
        
        // Clean up working directory
        if ($wp_filesystem->is_dir($working_dir))
        {
            $wp_filesystem->delete($working_dir, true);
        }
        
        $this->_flush('Unpacking the plugin<br />');
        
        // Unzip package to working directory
        $result = unzip_file($file, $working_dir);
        if (is_wp_error($result)) 
        {
            unlink($file);
            $wp_filesystem->delete($working_dir, true);
            echo '<strong><em>Unpack failed : ' . $result->get_error_message() . '</strong></em><br /><br />';
            return;
        }
        
        // Once extracted, delete the package
        unlink($file);
        $this->_flush('Installing the plugin<br />');

        // Copy new version of plugin into place.
        if (!copy_dir($working_dir, $base . PLUGINDIR)) 
        {
            // TODO: Uncomment? This DOES mean that the new files are available in the upgrade folder if it fails.
            $wp_filesystem->delete($working_dir, true);
            echo '<strong><em>Installation failed (plugin already installed?)</strong></em><br /><br />';
            return;
        }
        
        // Get a list of the directories in the working directory before we delete it, 
        // We need to know the new folder for the plugin
        $filelist = array_keys($wp_filesystem->dirlist($working_dir));
        
        // Remove working directory
        $wp_filesystem->delete($working_dir, true);
        
        
        echo '<strong>Plugin installed successfully!</strong><br /><br />';
        return;
    }
    
    function del_tree($directory)
    {
        if (substr($directory, -1) == '/') 
        {
            $directory = substr($directory, 0, -1);
        }
        
        if (!file_exists($directory) || !is_dir($directory)) 
        {
            echo "'$directory' : Path doesn't exist or isn't a directory!<br />";
            return false;
        } 
        
        $this->_flush("Processing directory '$directory'...<br />");
        
        $handle = opendir($directory);
        while (false !== ($item = @readdir($handle))) 
        {
            if (($item != '.') && ($item != '..')) 
            {
                $path = $directory . '/' . $item;
                if (is_dir($path) && !is_link($path)) 
                {
                    if (!$this->del_tree($path)) 
                    {
                        return false;
                    }
                } 
                else 
                {
                    $this->_flush("Deleting file $path<br />");
                    if (!unlink($path)) {
                        echo "Can't delete file '$path'<br />";
                        return false;
                    }
                }
            }
        }
        
        closedir($handle);
        
        $this->_flush("Deleting directory '$directory'<br />");
        if (!@rmdir($directory)) 
        {
            echo "Can't delete directory '$directory'<br />";
            return false;
        }
        
        return true;
    }
    
    function delete_plugin($plugin_file)
    {
        if (empty($plugin_file)) 
        {
            return;
        }
        
        $plugin_dir = realpath(ABSPATH . PLUGINDIR . '/');
        
        // It seems that on some systems realpath() will strip out the last slash, so I'll add it here. 
        if ((substr($plugin_dir, -1) != '/') && (substr($plugin_dir, -1) != '\\')) {
            $plugin_dir .= '/';
        }
        
        $this->_flush("Deleting the plugin '$plugin_file'<br />");
        
        $parts = preg_split('/[\\/]/', $plugin_file);
        $parts = array_filter($parts);
        if (count($parts) > 1) 
        {
            //the plugin is in a subfolder, so kill the folder
            $directory = $plugin_dir . $parts[0];
            $this->_flush("Deleting directory '$directory'...<br />");
          
            if (!$this->del_tree($directory)) 
            {
                echo "Can't delete the directory <strong>$parts[0]</strong><br />";
            } 
            else
            {
                echo "Plugin deleted successfully.";
            }
            
            return;
        }
         
        //it seems to be a single file inside wp-content/plugins
        $this->_flush("Deleting file '$plugin_file'<br />");
        if (!unlink($plugin_dir . $plugin_file)) 
        {
            //error!
            echo "Failed. ";
            echo "Can't delete <strong>$plugin_file</strong><br />";
        }
        echo 'Plugin deleted successfully.<br />';
        
        //  wp_redirect(get_option('siteurl').'/wp-admin/plugins.php');
    }
    
    
    function _flush($s)
    {
        echo $s;
        flush();
    }
    
    function add_pages()
    {
        add_submenu_page('plugins.php', 'Plugin Central', 'Plugin Central', 10, __FILE__, array($this, 'options_page'));
    }
    

    function options_page()
    {
        $imgpath = "{$this->plugin_url}/i";
        $action_url = $_SERVER['REQUEST_URI'];	
?>
<link rel="stylesheet" type="text/css" href="<?php echo $this->plugin_url ?>/style.css" />
<div class="wrap pc-wrap" >
	<div class="icon32" id="icon-plugins"><br></div>
	<h2>Plugin Central</h2>
	<div id="poststuff" style="margin-top:10px;">
         <div id="sideblock" style="float:right;width:270px;margin-left:10px;"> 

		 <iframe width=270 height=800 frameborder="0" src="http://www.prelovac.com/plugin/news.php?id=7&utm_source=plugin&utm_medium=plugin&utm_campaign=Plugin%2BCentral"></iframe>

 	</div>

      
        <div id="mainblock" style="width:710px">
       
        <div class="dbx-content">
            <form name="form_apu" method="post" action="<?php echo $_SERVER['REQUEST_URI'] ?>">
<?php
    wp_nonce_field($this->key);

    if (!current_user_can('edit_plugins')) 
    {
        echo "You do not have sufficient permissions to manage plugins on this blog.<br>";
        return;
    }

    $result = '';

    if (!defined('PHP_EOL'))
    {
        define('PHP_EOL', strtoupper(substr(PHP_OS, 0, 3) == 'WIN') ? "\r\n" : "\n");
    }
    
    // If form was submitted
    if (isset($_POST['apu_update']) && trim($_POST['multi_plugins']))
    {
        check_admin_referer($this->key);
        
        echo '<h2>Plugin installation</h2>';
        
        $plugin_install = !isset($_POST['multi_plugins']) ? '' : $_POST['multi_plugins'];
        
        if ($plugin_install != '') 
        {
            $plugin_install = str_replace(array("\r\r\r", "\r\r", "\r\n", "\n\r", "\n\n\n", "\n\n"), "\n", $plugin_install);
            $options = explode("\n", $plugin_install);
          
            $this->get_packages($options);
        }
        
        echo '<br /><br />';
    } 
    elseif (isset($_POST['apu_all'])) 
    {
        check_admin_referer($this->key);
        $active = get_option('active_plugins');
        $plugins = get_plugins();
      
        $result = '<ul class="pc-plugin-list">';
        
        foreach ($plugins as $file => $p) 
        {
            if (!in_array($file, $active))
            {
                // TODO: read readme.txt and extract correct plugin name!!
                $result .= "<li class=\"inactive\" title=\"Inactive\">{$p['Name']}</p>";
            }
            else
            {
                // TODO: read readme.txt and extract correct plugin name!!
                $result .= "<li class=\"active\" title=\"Active\">{$p['Name']}</li>";
            }
        }
        
        $result .= '</ul>';
    } 
    elseif (isset($_POST['apu_active'])) 
    {
        check_admin_referer($this->key);
        $active = get_option('active_plugins');
        $plugins = get_plugins();
        
        $result = '<ul class="pc-plugin-list">';
        
        foreach ($plugins as $file => $p) 
        {
            if (in_array($file, $active))
            {
                $result .= "<li class=\"active\" title=\"Active\">{$p['Name']}</li>";
            }
        }
        
        $result .= '</ul>';
    } 
    elseif (isset($_GET['pc_no_ignore'])) 
    {
        if (!wp_verify_nonce($_GET['_wpnonce'], $this->key)) 
        {
            die('Unauthorized access.');
        }
        
        // TODO: !!! merge options!!!
        $options = get_option('pc_ignored_plugins');
        
        if ($options) 
        {
            unset($options[array_search($_GET['pc_no_ignore'], $options)]);
            update_option('pc_ignored_plugins', $options);
        }
    }
    
    
    $options = get_option('pc_ignored_plugins');
    
    $ignored = '';
    
    if ($options)
    {
        $nonce = wp_create_nonce($this->key);
        $ignored = '<ul>';
        foreach ($options as $item) 
        {
            $ignored .= sprintf('<li>
            <a href="plugins.php?page=plugin-central/plugin-central.class.php&_wpnonce=%s&pc_no_ignore=%s">%s</a>
            </li>', $nonce, $item, $item);
        }
        $ignored .= '</ul>';
    }
?>
                 	<h2>Options</h2>
                	<input class="button" type="submit" name="apu_all" value="List all plugins" class="button-primary"/>
                	<input class="button" type="submit" name="apu_active" value="List active plugins" class="button-primary"/>
                     <?php echo $result ?>
                
                	<div class="submit">  
                  		<h2>Easy plugin Installation</h2>
                  		<p>Enter the list of plugins to install.<br />
                  		You can specify either the Name or URL of the plugin zip installation file.</p>
                  		<textarea style="border:1px solid #D1D1D1;width:600px;" name="multi_plugins" id="multi_plugins" cols="40" rows="10"></textarea>
                  		<p><input class="button" type="submit" name="apu_update" value="Install plugins &raquo;" class="button-primary"/></p>
                	</div>
                
                	<div>  
                  		<h2>Ignored Plugins</h2>
                  		<p>This is the list of currently ignored plugins for update checks. 
                  		Click the link to restore normal status.</p>
                     	<?php echo $ignored ?>
                	</div>
    			</form>
    		</div>
    	</div>
    </div>
</div>
<h5 class="author">Another fine plugin by <a href="http://www.prelovac.com/vladimir/">Vladimir Prelovac</a></h5>

<?php
    }
}