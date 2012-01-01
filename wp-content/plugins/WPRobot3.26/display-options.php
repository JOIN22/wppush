<div class="wrap">
<h2><?php _e("WP Robot Options","wprobot") ?></h2>
<style type="text/css">
table.addt {padding:5px;background:#F5F5F5;border:1px dotted #F0F0F0;}
table.addt:hover {background:#F2F2F2;border:1px dotted #d9d9d9;}
div.expld {padding:5px;margin-bottom:10px;background:#fffff0;border:1px dotted #e5dd83;}
div.expld:hover {background:#ffffe5;border:1px dotted #e5db6c;} 
<?php if($options['wpr_help'] == "Yes") { // FFFFE0 // E6DB55 ?>
a.tooltip {background:#ffffff;font-weight:bold;text-decoration:none;padding:2px 6px;}
a.tooltip:hover {background:#ffffff; text-decoration:none;} /*BG color is a must for IE6*/
a.tooltip span {display:none;font-weight:normal; padding:2px 3px; margin-left:8px; width:230px;}
a.tooltip:hover span{display:inline; position:absolute; background:#ffffff; border:1px solid #cccccc; color:#6c6c6c;}
<?php } else {?>
a.tooltip {display:none;}
<?php } ?>
</style>
	<div style="width:25%;float:right;";>
	
		<div class="expld">
			<strong><?php _e("Documentation","wprobot") ?></strong><br/>
			<?php _e('Have <a href="http://wprobot.net/documentation/#8">a look at the <b>documentation</b></a> to view an explanation of all available settings.',"wprobot") ?>
		</div>			
	
		<div class="expld">
			<?php _e('<strong>Affiliate and API keys</strong> highlighted in red have to be entered for the plugin to function correctly!',"wprobot") ?>
		</div>	
	
		<div class="expld">
			<strong><?php _e("Quick Links","wprobot") ?></strong><br/>
			<?php _e('- <a target="_blank" href="http://wprobot.net/">WP Robot</a><br/>- <a target="_blank" href="http://cc.wprobot.net/">WP Robot Control Center</a><br/>- <a target="_blank" href="http://wprobot.net/documentation">Online Documentation</a><br/>- <a target="_blank" href="http://wprobot.net/forum">Support Forum</a><br/>- <a target="_blank" href="http://wprobot.net/robotpal/sendnew.php">New Download Link</a>',"wprobot") ?>
		</div>			

		<div class="expld">
			<strong><?php _e("Other Services","wprobot") ?></strong><br/>
			<?php _e('<a target="_blank" href="http://wpshoppingpages.com/"><b>WP Shopping Pages</b></a><br/>Wordpress affiliate shop plugin.<br/><br/><a target="_blank" href="http://blogthemesclub.com/"><b>BlogThemesClub</b></a><br/>Affordable premium Wordpress themes.<br/><br/><a target="_blank" href="http://wpscoop.com/"><b>WP Scoop</b></a><br/>Submit your favorite Wordpress news and stories!',"wprobot") ?>
		</div>		
		
	</div>
	<div style="width:70%;">		
	<form method="post" id="wpr_options">	
	
	<p class="submit"><input class="button-primary" type="submit" name="wpr_options_save" value="<?php _e("Save Options","wprobot") ?>" /></p>
	
	
	<h3 style="text-transform:uppercase;border-bottom: 1px solid #ccc;"><?php _e("License Options","wprobot") ?></h3>	
		<table class="addt" width="100%" cellspacing="2" cellpadding="5" class="editform"> 	
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Your Paypal Email:","wprobot") ?></td> 
				<?php if($options['wpr_email'] != "demo") {$wemail = substr_replace($options['wpr_email'], '***', 0, 3);} else {$wemail = $options['wpr_email'];} ?>
				<td><input id="wpr_email" size="40" type="text" value="<?php echo $wemail; ?>" name="wpr_email" <?php if($options['wpr_core']!='developer' && $options['wpr_email']!="demo") {echo "disabled";} ?> />
				 <?php if($options['wpr_core']=='developer' || $options['wpr_email']=="demo") { ?><input class="button" type="submit" name="wpr_update_email" value="<?php _e("Update","wprobot") ?>" /><?php } ?>
				<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('If you own the Developer License of WP Robot you can edit the Paypal email in order to insert a sublicense for your clients.',"wprobot") ?></span></a>
				</td> 
			</tr>		
		</table>		

	<h3 style="text-transform:uppercase;border-bottom: 1px solid #ccc;"><?php _e("General Options","wprobot") ?></h3>	
		<table class="addt" width="100%" cellspacing="2" cellpadding="5" class="editform"> 		
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Enable Simple Mode:","wprobot") ?></td> 
				<td>
				<input name="wpr_simple" type="checkbox" id="wpr_simple" value="Yes" <?php if ($options['wpr_simple']=='Yes') {echo "checked";} ?>/> <?php _e("Yes","wprobot") ?>		
				<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('In Simple Mode certain steps of the campaign creation in WP Robot have been simplified but in exchange campaigns are less powerful and flexible.<br/><br/>Specifically the "Post Templates" are hidden from the campaigns and instead you can only enable or disable single modules for your campaign (mostly similar to how WP Robot 2 worked).<br/><br/>You can switch between Simple and Advanced Mode at any time without affecting your campaigns.',"wprobot") ?></span></a>
				</td> 
			</tr>		
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("New Post Status:","wprobot") ?></td>
				<td>
				<select name="wpr_poststatus" id="wpr_poststatus">
					<option value="published" <?php if ($options['wpr_poststatus']=='published') {echo 'selected';} ?>><?php _e("published","wprobot") ?></option>
					<option value="draft" <?php if ($options['wpr_poststatus']=='draft') {echo 'selected';} ?>><?php _e("draft","wprobot") ?></option>
				</select>
				</td> 
			</tr>	
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Reset Post Counter:","wprobot") ?></td>
				<td>
				<select name="wpr_resetcount" id="wpr_resetcount">
					<option value="no" <?php if ($options['wpr_resetcount']=='no') {echo 'selected';} ?>><?php _e("no","wprobot") ?></option>
					<option <?php if ($options['wpr_resetcount']=='50') {echo 'selected';} ?>>50</option>					
					<option <?php if ($options['wpr_resetcount']=='75') {echo 'selected';} ?>>75</option>					
					<option <?php if ($options['wpr_resetcount']=='100') {echo 'selected';} ?>>100</option>
					<option <?php if ($options['wpr_resetcount']=='150') {echo 'selected';} ?>>150</option>
					<option <?php if ($options['wpr_resetcount']=='200') {echo 'selected';} ?>>200</option>					
				</select>
				<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('Will reset the internal search counter for a keyword after a certain amount of created posts. The effect is that search and posting will start from the beginning of the search results again and thus add new and relevant posts.',"wprobot") ?></span></a>
				</td> 
			</tr>	
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Enable Help Tooltips:","wprobot") ?></td> 
				<td>
				<input name="wpr_help" type="checkbox" id="wpr_help" value="Yes" <?php if ($options['wpr_help']=='Yes') {echo "checked";} ?>/> <?php _e("Yes","wprobot") ?>		
				<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('If disabled all help tooltips like the one you are reading right now will disappear.',"wprobot") ?></span></a>
				</td> 
			</tr>	
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Enable Old Duplicate Check:<br/><small>(for WP Robot 2 backwards compatibility)</small>","wprobot") ?></td> 
				<td>
				<input name="wpr_check_unique_old" type="checkbox" id="wpr_check_unique_old" value="Yes" <?php if ($options['wpr_check_unique_old']=='Yes') {echo "checked";} ?>/> <?php _e("Yes","wprobot") ?>		
				<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('If disabled the old duplicate check from WP Robot 2 (comparing post titles) will be used besides the new method in WP Robot 3.<br/><br/><b>This should only be enabled if you were using WP Robot 2 or previous versions on this blog. If you were and do not disable this option you might get duplicate posts.</b>',"wprobot") ?></span></a>
				</td> 
			</tr>				
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Randomize Post Times:","wprobot") ?></td> 
				<td>
				<input name="wpr_randomize" type="checkbox" id="wpr_randomize" value="Yes" <?php if ($options['wpr_randomize']=='Yes') {echo "checked";} ?>/> <?php _e("Yes","wprobot") ?>		
				</td> 
			</tr>
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Randomize Number of Comments:","wprobot") ?></td> 
				<td>
				<input name="wpr_randomize_comments" type="checkbox" id="wpr_randomize_comments" value="Yes" <?php if ($options['wpr_randomize_comments']=='Yes') {echo "checked";} ?>/> <?php _e("Yes","wprobot") ?>		
				<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('If enabled only 50-100% of available comments for a post will be randomly added in order to vary the number of comments per post.',"wprobot"); ?></span></a>				
				</td> 
			</tr>			
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Cloak Affiliate Links:","wprobot") ?></td> 
				<td>
				<input name="wpr_cloak" type="checkbox" id="wpr_cloak" value="Yes" <?php if (get_option('wpr_cloak')=='Yes') {echo "checked";} ?>/> <?php _e("Yes","wprobot") ?>	
				<!--Tooltip--><a class="tooltip" target="_blank" href="http://wprobot.net/blog/how-to-set-up-wp-robot-link-cloaking/">?<span><?php _e('<b>Important:</b> Additional steps are required to enable link cloaking. If you only check this box and don\'t finish the setup your links will not work!<br/><br/><b>Click to view setup instructions.</b>',"wprobot") ?></span></a>	
				</td> 
			</tr>				
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Automatically create Tags:","wprobot") ?></td>
				<td>
				<input name="wpr_autotag" type="checkbox" id="wpr_autotag" value="Yes" <?php if ($options['wpr_autotag']=='Yes') {echo "checked";} ?>/> <?php _e("Yes","wprobot") ?>
				</td> 
			</tr>
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Save Images to Server:","wprobot") ?></td> 
				<td>
				<input name="wpr_save_images" type="checkbox" id="wpr_save_images" value="Yes" <?php if ($options['wpr_save_images']=='Yes') {echo "checked";} ?>/> <?php _e("Yes","wprobot") ?>		
				<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('If enabled all images from WP Robot posts will be saved to your server instead of hotlinked. <b>Important:</b> For this option to work you need to make the "WPRobot3/images" directory writeable by the server (chmod 666).',"wprobot"); ?></span></a>				
				</td> 
			</tr>				
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Exclude from Tags:<br/><small>Words with 3 characters and less are automatically excluded</small>","wprobot") ?></td> 
				<td>			
					<textarea name="wpr_badwords" rows="2" cols="30"><?php echo $options['wpr_badwords'];?></textarea>	
				</td> 
			</tr>	
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Global Exclude Keywords:","wprobot") ?></td> 
				<td>			
					<textarea name="wpr_global_exclude" rows="2" cols="30"><?php echo $options['wpr_global_exclude'];?></textarea>	
					<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('Enter one keyword or phrase per line.<br/><br/>The keywords will be excluded from <b>all</b> campaigns and posts. If any one is found the post is skipped.<br/><br/>This is in addition to the exclude keywords you can set up for each campaign individually.',"wprobot") ?></span></a>
				</td> 
			</tr>							
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Author:","wprobot") ?></td> 
				<td><?php _e("Post as User ID","wprobot") ?> <input id="wpr_authorid" size="14" class="small-text" type="text" value="<?php echo $options['wpr_authorid']; ?>" name="wpr_authorid"/>
				<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('Enter the user ID you want to use for all WP Robot posts.<br/><br/>You can enter <strong>several user IDs</strong> separated by ";" (without quotes) to select a random user from the list for each post.',"wprobot") ?></span></a>
				</td> 
			</tr>	
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Open Links:","wprobot") ?></td>
				<td>
				<select name="wpr_openlinks" id="wpr_openlinks">
					<option value="yes" <?php if ($options['wpr_openlinks']=='yes') {echo 'selected';} ?>><?php _e("In a new window","wprobot") ?></option>
					<option value="no" <?php if ($options['wpr_openlinks']=='no') {echo 'selected';} ?>><?php _e("In the same window","wprobot") ?></option>								
				</select>
				</td> 
			</tr>				
		</table>

	<?php
		foreach($wpr_modules as $module) {
			$function = "wpr_".$module."_options";
			if(function_exists($function)) {
				$function($options);
			}
		}
	?>		
				
	<h3 style="text-transform:uppercase;border-bottom: 1px solid #ccc;"><?php _e("Error Handling","wprobot") ?></h3>
	<p><i><?php _e("Important: Only edit the options below if you know what you are doing!","wprobot") ?></i></p>
		<table class="addt" width="100%" cellspacing="2" cellpadding="5" class="editform"> 		
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Maximum Retries","wprobot") ?></td> 
				<td>
					<input size="4" name="wpr_err_retries" type="text" id="wpr_err_retries" value="<?php echo $options['wpr_err_retries'] ;?>"/>
					<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('This option defines how many times the creation of a post is retried if a previous attempt failed.',"wprobot") ?></span></a>
				</td> 
			</tr>	
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Maximum Errors","wprobot") ?></td> 
				<td>
					<input size="4" name="wpr_err_maxerr" type="text" id="wpr_err_maxerr" value="<?php echo $options['wpr_err_maxerr'] ;?>"/>
					<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('The maximum amount of errors that can be encountered for modules in a template before skipping a post.<br/><br/><b>Example</b><br/>If set to 0 a post will be skipped as soon as one of the modules in the template returned an error, no matter how many other modules were there that worked.',"wprobot") ?></span></a>
				</td> 
			</tr>
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Minimum Modules","wprobot") ?></td> 
				<td>
					<input size="4" name="wpr_err_minmod" type="text" id="wpr_err_minmod" value="<?php echo $options['wpr_err_minmod'] ;?>"/>
					<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('The minimum number of modules necessary for a post to get created.<br/><br/><b>Example</b><br/>If set to 2 and the template contains three modules (i.e. {amazon}{article}{ebay}) at least two modules have to work for a post to get created.<br/><br/><b>Warning:</b> Do not set this to zero as otherwise you could get empty posts. If you set this option to a higher value than the number of modules in your template you will never get any posts.',"wprobot") ?></span></a>
				</td> 
			</tr>
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Disable Keywords","wprobot") ?></td> 
				<td>
					<?php _e("...after creating a post for it failed","wprobot") ?> <input size="4" name="wpr_err_disable" type="text" id="wpr_err_disable" value="<?php echo $options['wpr_err_disable'] ;?>"/> <?php _e("times.","wprobot") ?>
					<!--Tooltip--><a class="tooltip" href="#">?<span><?php _e('Select how many times a keyword may return an error until it is disabled. Disabled keywords will not get selected anymore in the campaign they are in.',"wprobot") ?></span></a>
				</td> 
			</tr>				
		</table>
		

	<h3 style="text-transform:uppercase;border-bottom: 1px solid #ccc;"><?php _e("Rewriting","wprobot") ?></h3>
	<p><a href="http://www.generateuniquecontent.com/?refer=wprobot"><?php _e("Rewriter Details and Signup","wprobot") ?></a></p>	
		<table class="addt" width="100%" cellspacing="2" cellpadding="5" class="editform"> 		
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Enable Rewriter:","wprobot") ?></td> 
				<td>
				<input name="wpr_rewrite_active" type="checkbox" id="wpr_rewrite_active" value="Yes" <?php if ($options['wpr_rewrite_active']=='Yes') {echo "checked";} ?>/> <?php _e("Yes","wprobot") ?>		
				</td> 
			</tr>	
			<tr <?php if($options['wpr_rewrite_email'] == "") {echo 'style="background:#F8E0E0;"';} ?> valign="top"> 
				<td width="40%" scope="row"><?php _e("Registered Email:","wprobot") ?></td> 
				<td><input size="40" name="wpr_rewrite_email" type="text" id="wpr_rewrite_email" value="<?php echo $options['wpr_rewrite_email'] ;?>"/>
					<a class="tooltip" href="http://www.generateuniquecontent.com/?refer=wprobot">?<span><?php _e('Click this link to get to the sign up page of the external rewriting API supported by WP Robot.',"wprobot") ?></span></a>						
			</td> 
			</tr>	
			<tr <?php if($options['wpr_rewrite_key'] == "") {echo 'style="background:#F8E0E0;"';} ?> valign="top"> 
				<td width="40%" scope="row"><?php _e("Rewriter API Key:","wprobot") ?></td> 
				<td><input size="40" name="wpr_rewrite_key" type="text" id="wpr_rewrite_key" value="<?php echo $options['wpr_rewrite_key'] ;?>"/>
			</td> 
			</tr>	
			<tr valign="top"> 
				<td width="40%" scope="row"><?php _e("Rewrite Level:","wprobot") ?></td>
				<td>
				<select name="wpr_rewrite_level" id="wpr_rewrite_level">
					<option value="r" <?php if ($options['wpr_rewrite_level']=='r') {echo 'selected';} ?>><?php _e("Random","wprobot") ?></option>
					<option value="1" <?php if ($options['wpr_rewrite_level']=='1') {echo 'selected';} ?>><?php _e("Low","wprobot") ?></option>
					<option value="2" <?php if ($options['wpr_rewrite_level']=='2') {echo 'selected';} ?>><?php _e("Medium","wprobot") ?></option>	
					<option value="3" <?php if ($options['wpr_rewrite_level']=='3') {echo 'selected';} ?>><?php _e("High","wprobot") ?></option>
					<option value="4" <?php if ($options['wpr_rewrite_level']=='4') {echo 'selected';} ?>><?php _e("Highest","wprobot") ?></option>						
				</select>
					<a class="tooltip" href="#">?<span><?php _e('A higher rewrite level means the resulting content will be more unique but usually less readable.',"wprobot") ?></span></a>				
				</td> 
			</tr>				
		</table>		

		
		
		<p class="submit"><input class="button-primary" type="submit" name="wpr_options_save" value="<?php _e("Save Options","wprobot") ?>" /></p>
	</div>
	
	<div class="updated" style="margin-top: 20px;">
	<h3><?php _e("Resetting and Uninstalling","wprobot") ?></h3>		
	<p class="submit"><input onclick="return confirm('<?php _e("This will reset all options to their default values. Continue?","wprobot") ?>')" class="button" type="submit" name="wpr_options_default" value="<?php _e("Reset Options to Defaults","wprobot") ?>" /> 
	<input onclick="return confirm('<?php _e("This will reset all templates to their default values and delete any changes or additions you have made. Continue?","wprobot") ?>')" class="button" type="submit" name="wpr_templates_default" value="<?php _e("Reset Templates","wprobot") ?>" /> 
	<input onclick="return confirm('<?php _e("This will attempt to update your WP Robot Core. Only continue if you have upgraded to a better Core Version after installing WP Robot on this blog.","wprobot") ?>')" class="button" type="submit" name="wpr_update_core" value="<?php _e("Update Core","wprobot") ?>" /> 	
	<input onclick="return confirm('<?php _e("This will clear the WP Robot log of all messages and errors. Continue?","wprobot") ?>')" class="button" type="submit" name="wpr_clear_log" value="<?php _e("Clear Log","wprobot") ?>" /> 
	<input onclick="return confirm('<?php _e("This will clear the WP Robot post history and thus all posts in the history could get posted again. Continue?","wprobot") ?>')" class="button" type="submit" name="wpr_clear_posts" value="<?php _e("Clear Post History","wprobot") ?>" /> 
	<input onclick="return confirm('<?php _e("Warning: This will uninstall WP Robot and delete all settings. Continue?","wprobot") ?>')" class="button" type="submit" name="wpr_uninstall" value="<?php _e("Uninstall WP Robot","wprobot") ?>" /></p>	

	<?php if(get_option('ma_poststatus') != "") {?>
	<h3><?php _e("Import Settings from WP Robot2","wprobot") ?></h3>		
	<p class="submit"><input onclick="return confirm('<?php _e("This will import your settings from WP Robot2. Continue?","wprobot") ?>')" class="button" type="submit" name="wpr_import" value="<?php _e("Import","wprobot") ?>" /></p>	
	<?php } ?>	
	</div>
	
	<div class="updated" style="margin-top: 20px;">	
		<h3><?php _e("Unix Cron Job","wprobot") ?></h3>
		<p><?php _e('You can set up an Unix cron job in your servers control panel as an alternative to the Wordpress pseodo-cron jobs. See the <a href="http://wprobot.net/documentation/#94a">documentation</a> for setup instructions and advanced options.',"wprobot") ?>
		<br/><br/>
		<strong><?php _e("Cron Url for this Weblog:","wprobot") ?></strong><br/><i><?php echo WPR_URLPATH ."cron.php?code=".get_option("wpr_cron"); ?></i>
		<br/><br/>
		<strong><?php _e("Sample Cron Command:","wprobot") ?></strong><br/><i>wget -O /dev/null <?php echo WPR_URLPATH ."cron.php?code=".get_option("wpr_cron"); ?></i>
		</p>
	</div>	

	</form>	
</div>
