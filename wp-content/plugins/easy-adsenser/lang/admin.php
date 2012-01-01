<?php
/*
Copyright (C) 2008 www.thulasidas.com

This file is part of the program "AdSense Now!"

AdSense Now! is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or (at
your option) any later version.

AdSense Now! is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

AdSense Now! is supported by ad space sharing. Unless you configure
the program (following the instructions on its admin page) and
explicitly turn off the sharing, you agree to run its developer's ads
on your site(s). By using the program, you are agreeing to this
condition, and confirming that your sites abide by Google's policies
and terms of service.
*/

echo '<script type="text/javascript" src="'. get_option('siteurl') . '/' . PLUGINDIR . '/' .  basename(dirname(__FILE__)) . '/wz_tooltip.js"></script>' ?>
<div class="wrap" style="width:850px">

<h2>AdSense Now! Setup <a href="http://validator.w3.org/" target="_blank"><img src="http://www.w3.org/Icons/valid-xhtml10" alt="Valid XHTML 1.0 Transitional" title="AdSense Now! Admin Page is certified Valid XHTML 1.0 Transitional" height="31" width="88" class="alignright"/></a></h2>
<table class="form-table">
<tr><th scope="row"><h3><?php _e('Instructions', 'easy-adsenser') ; ?></h3></th></tr>
<tr valign="top">
<td width="37%">
<ul style="padding-left:10px;list-style-type:circle; list-style-position:inside;" >
<li>
<a href="#" title="<?php _e('Click for help', 'easy-adsenser') ; ?>" onclick="TagToTip('help0',WIDTH, 270, TITLE, '<?php _e('How to Set it up', 'easy-adsenser') ; ?>', STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 15, 5])">
<?php
printf(__('A few easy steps to setup %s', 'easy-adsenser'),'<em>AdSense Now!</em>') ;
?></a><br />
</li>
<li>
<a href="#" title="<?php _e('Click for help', 'easy-adsenser') ; ?>" onclick="TagToTip('help1',WIDTH, 270, TITLE, '<?php _e('How to Control AdSense on Each Post', 'easy-adsenser') ; ?>', STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 15, 5])">
<?php _e('Need to control ad blocks on each post?', 'easy-adsenser') ;?></a><br />
</li>
<li>
<a href="#" title="<?php _e('Click for help', 'easy-adsenser') ; ?>" onclick="TagToTip('rate', TITLE, 'WordPress: AdSense Now!', STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [25, 25])">
<?php _e('Check out the FAQ and rate this plugin.', 'easy-adsenser') ;?></a><br />
</li>
</ul>
</td>

<?php @include (dirname (__FILE__).'/head-text.php'); ?>

</tr>
</table>

<form method="post" name="adsenser" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
<table class="form-table">
<tr><th scope="row"><h3><?php printf(__('Options (for the %s theme)', 'easy-adsenser'), $mThemeName); ?> </h3></th></tr>
</table>

<table class="form-table" width="100%">
<tr valign="top">
<td width="400">
<b><?php _e('Ad Blocks in Your Posts', 'easy-adsenser') ; ?></b><br />
<?php _e('[Appears in your posts and pages]', 'easy-adsenser') ; ?><br />
<textarea cols="50" rows="15" name="adsNowText" style="width: 96%; height: 240px;"><?php echo(stripslashes(htmlspecialchars($adNwOptions['ad_text']))) ?></textarea>
</td>
<td>
<center>
<b><?php _e('Ad Alignment', 'easy-adsenser') ; ?></b>&nbsp;
<b><?php _e('(Where to show?)', 'easy-adsenser') ; ?></b>
</center>
<table bgcolor="white" width="450">
<tr align="center" valign="middle">
<td>&nbsp;</td><td><?php _e('Align Left', 'easy-adsenser') ; ?> </td><td><?php _e('Center', 'easy-adsenser') ; ?> </td><td><?php _e('Align Right', 'easy-adsenser') ; ?> </td><td><?php _e('Suppress', 'easy-adsenser') ; ?></td></tr>
<tr align="center" valign="middle">
<td><?php _e('Top', 'easy-adsenser') ; ?></td>
<td>
<input type="radio" id="adsNowShowLeadin_left" name="adsNowShowLeadin" value="float:left" <?php if ($adNwOptions['show_leadin'] == "float:left") { echo('checked="checked"'); }?> />
</td><td>
<input type="radio" id="adsNowShowLeadin_center" name="adsNowShowLeadin" value="text-align:center" <?php if ($adNwOptions['show_leadin'] == "text-align:center") { echo('checked="checked"'); }?> />
</td><td>
<input type="radio" id="adsNowShowLeadin_right" name="adsNowShowLeadin" value="float:right" <?php if ($adNwOptions['show_leadin'] == "float:right") { echo('checked="checked"'); }?> />
</td><td>
<input type="radio" id="adsNowShowLeadin_no" name="adsNowShowLeadin" value="no" <?php if ($adNwOptions['show_leadin'] == "no") { echo('checked="checked"'); }?> />
</td></tr>
<tr align="center" valign="middle">
<td><?php _e('Middle', 'easy-adsenser') ; ?></td>
<td>
<input type="radio" id="adsNowShowMidtext_left" name="adsNowShowMidtext" value="float:left" <?php if ($adNwOptions['show_midtext'] == "float:left") { echo('checked="checked"'); }?> />
</td><td>
<input type="radio" id="adsNowShowMidtext_center" name="adsNowShowMidtext" value="text-align:center" <?php if ($adNwOptions['show_midtext'] == "text-align:center") { echo('checked="checked"'); }?> />
</td><td>
<input type="radio" id="adsNowShowMidtext_right" name="adsNowShowMidtext" value="float:right" <?php if ($adNwOptions['show_midtext'] == "float:right") { echo('checked="checked"'); }?> />
</td><td>
<input type="radio" id="adsNowShowMidtext_no" name="adsNowShowMidtext" value="no" <?php if ($adNwOptions['show_midtext'] == "no") { echo('checked="checked"'); }?> />
</td></tr>
<tr align="center" valign="middle">
<td><?php _e('Bottom', 'easy-adsenser') ; ?></td>
<td>
<input type="radio" id="adsNowShowLeadout_left" name="adsNowShowLeadout" value="float:left" <?php if ($adNwOptions['show_leadout'] == "float:left") { echo('checked="checked"'); }?> />
</td><td>
<input type="radio" id="adsNowShowLeadout_center" name="adsNowShowLeadout" value="text-align:center" <?php if ($adNwOptions['show_leadout'] == "text-align:center") { echo('checked="checked"'); }?> />
</td><td>
<input type="radio" id="adsNowShowLeadout_right" name="adsNowShowLeadout" value="float:right" <?php if ($adNwOptions['show_leadout'] == "float:right") { echo('checked="checked"'); }?> />
</td><td>
<input type="radio" id="adsNowShowLeadout_no" name="adsNowShowLeadout" value="no" <?php if ($adNwOptions['show_leadout'] == "no") { echo('checked="checked"'); }?> />
</td>
</tr>
<tr><td colspan="5">
<br style="line-height: 7px;" />
<b><?php printf(__('Support %s by Donating Ad Space', 'easy-adsenser'), 'AdSense Now!') ; ?></b><br />
<?php _e('Percentage of ad slots to share [Default: 5%]:', 'easy-adsenser') ; ?> <input style="width:20px;text-align:center;" id="adNwMC" name="adNwMC" value="<?php echo(stripslashes(htmlspecialchars($adNwOptions['mc'])));?>" />%<br /><br style="line-height: 7px;" />
<b><?php _e('Suppress AdSense Ad Blocks on:', 'easy-adsenser') ; ?></b>&nbsp;&nbsp;
<input type="checkbox" id="adNwKillPages" name="adNwKillPages" value="true" <?php if ($adNwOptions['kill_pages']) { echo('checked="checked"'); }?> /> <a href="http://codex.wordpress.org/Pages" target="_blank" title="<?php _e('Click to see the difference between posts and pages', 'easy-adsenser') ; ?>"><?php _e('Pages (Ads only on Posts)', 'easy-adsenser') ; ?></a><br />
<label for="adNwKillAttach" title="<?php _e('Pages that show attachments', 'easy-adsenser') ; ?>">
<input type="checkbox" id="adNwKillAttach" name="adNwKillAttach" <?php if ($adNwOptions['kill_attach']) { echo('checked="checked"'); }?> /> <?php _e('Attachment Page', 'easy-adsenser') ; ?></label>&nbsp;&nbsp;
<label for="adNwKillHome" title="<?php _e('Home Page and Front Page are the same for most blogs', 'easy-adsenser') ; ?>">
<input type="checkbox" id="adNwKillHome" name="adNwKillHome" <?php if ($adNwOptions['kill_home']) { echo('checked="checked"'); }?> /> <?php _e('Home Page', 'easy-adsenser') ; ?></label>&nbsp;&nbsp;
<label for="adNwKillFront" title="<?php _e('Home Page and Front Page are the same for most blogs', 'easy-adsenser') ; ?>">
<input type="checkbox" id="adNwKillFront" name="adNwKillFront" <?php if ($adNwOptions['kill_front']) { echo('checked="checked"'); }?> /> <?php _e('Front Page', 'easy-adsenser') ; ?></label>&nbsp;&nbsp;
<br />
<label for="adNwKillCat" title="<?php _e('Pages that come up when you click on category names', 'easy-adsenser') ; ?>">
<input type="checkbox" id="adNwKillCat" name="adNwKillCat" <?php if ($adNwOptions['kill_cat']) { echo('checked="checked"'); }?> /> <?php _e('Category Pages', 'easy-adsenser') ; ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
<label for="adNwKillTag" title="<?php _e('Pages that come up when you click on tag names', 'easy-adsenser') ; ?>">
<input type="checkbox" id="adNwKillTag" name="adNwKillTag" <?php if ($adNwOptions['kill_tag']) { echo('checked="checked"'); }?> /> <?php _e('Tag Pages', 'easy-adsenser') ; ?></label>&nbsp;&nbsp;&nbsp;
<label for="adNwKillArchive" title="<?php _e('Pages that come up when you click on year/month archives', 'easy-adsenser') ; ?>">
<input type="checkbox" id="adNwKillArchive" name="adNwKillArchive" <?php if ($adNwOptions['kill_archive']) { echo('checked="checked"'); }?> /> <?php _e('Archive Pages', 'easy-adsenser') ; ?></label>&nbsp;&nbsp;
</td></tr>
</table>

</td>
</tr>
</table>

<div class="submit">
<input type="submit" name="update_adsNowSettings" value="<?php _e('Save Changes', 'easy-adsenser') ?>" title="<?php _e('Save the changes as specified above', 'easy-adsenser') ?>" onmouseover="Tip('<?php _e('Save the changes as specified above', 'easy-adsenser') ?>',WIDTH, 240, TITLE, 'Save Settings')" onmouseout="UnTip()"/>
<input type="submit" name="reset_adsNowSettings"  value="<?php _e('Reset Options', 'easy-adsenser') ?>" onmouseover="TagToTip('help3',WIDTH, 240, TITLE, 'DANGER!', BGCOLOR, '#ffcccc', FONTCOLOR, '#800000',BORDERCOLOR, '#c00000')" onmouseout="UnTip()"/>
<input type="submit" name="clean_db"  value="<?php _e('Clean Database', 'easy-adsenser') ?>" onmouseover="TagToTip('help4',WIDTH, 280, TITLE, 'DANGER!', BGCOLOR, '#ffcccc', FONTCOLOR, '#800000',BORDERCOLOR, '#c00000')" onmouseout="UnTip()"/>
<input type="submit" name="kill_me"  value="<?php _e('Uninstall', 'easy-adsenser') ?>" onmouseover="TagToTip('help5',WIDTH, 280, TITLE, 'DANGER!', BGCOLOR, '#ffcccc', FONTCOLOR, '#800000',BORDERCOLOR, '#c00000')" onmouseout="UnTip()"/>
</div>
</form>

<span id="help0">
1.
<?php
_e('Generate AdSense code (from http://adsense.google.com &rarr; AdSense Setup &rarr; Get Ads).', 'easy-adsenser') ;
?>
<br />
2.
<?php
_e('Cut and paste the AdSense code into the boxes below, deleting the existing text.', 'easy-adsenser') ;
?>
<br />
3.
<?php
_e('Decide how to align and show the code in your blog posts.', 'easy-adsenser') ;
?>
<br />
<b>
<?php
_e('Save the options, and you are done!', 'easy-adsenser') ;
?>
</b>
</span>

<span id="help1">
<?php _e('If you want to suppress AdSense in a particular post or page, give the <b><em>comment </em></b> "&lt;!--noadsense--&gt;" somewhere in its text.
<br />
<br />
Or, insert a <b><em>Custom Field</em></b> with a <b>key</b> "adsense" and give it a <b>value</b> "no".<br />
<br />
Other <b><em>Custom Fields</em></b> you can use to fine-tune how a post or page displays AdSense blocks:<br />
<b>Keys</b>:<br />
adsense-top,
adsense-middle,
adsense-bottom,
adsense-widget,
adsense-search<br />
<b>Values</b>:<br />
left,
right,
center,
no', 'easy-adsenser') ;?>
</span>

<span id="rate">
<iframe src="http://wordpress.org/extend/plugins/adsense-now/faq/" width="1000px" height="750px">
</iframe>
</span>

<span id="help3">
<font color="red"><?php _e('This <b>Reset Options</b> button discards all your changes and loads the default options. This is your only warning!', 'easy-adsenser') ; ?></font><br />
<b><?php _e('Discard all your changes and load defaults. (Are you quite sure?)', 'easy-adsenser') ?></b></span>
<span id="help4">
<font color="red"><?php _e('The <b>Database Cleanup</b> button discards all your AdSense settings you have saved so far for <b>all</b> the themes, including the current one. Use it only if you know that you won\'t be using these themes. Please be careful with all database operations -- keep a backup.', 'easy-adsenser') ; ?></font><br />
<b><?php _e('Discard all your changes and load defaults. (Are you quite sure?)', 'easy-adsenser') ?></b></span>
<span id="help5">
<font color="red"><?php printf(__('The <b>Uninstall</b> button really kills %s after cleaning up all the options it wrote in your database. This is your only warning! Please be careful with all database operations -- keep a backup.', 'easy-adsenser'), '<em>AdSense Now!</em>') ; ?></font><br />
<b><?php _e('Kill this plugin. (Are you quite sure?)', 'easy-adsenser') ?></b></span>
<hr />

<?php @include (dirname (__FILE__).'/tail-text.php'); ?>

<table class="form-table" >
<tr><th scope="row"><h3><?php _e('Credits', 'easy-adsenser'); ?></h3></th></tr>
<tr><td>
<ul style="padding-left:10px;list-style-type:circle; list-style-position:inside;" >
<li>
<?php printf(__('%s uses the excellent Javascript/DHTML tooltips by %s', 'easy-adsenser'), '<b>Adsense Now!</b>', '<a href="http://www.walterzorn.com" target="_blank" title="Javascript, DTML Tooltips"> Walter Zorn</a>.') ;
?>
</li>
</ul>
</td>
</tr>
</table>

<?php echo '</div>' ; ?>
