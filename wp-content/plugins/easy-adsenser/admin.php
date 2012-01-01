<?php
/*
Copyright (C) 2008 www.thulasidas.com

This file is part of the program "Easy AdSense."

Easy AdSense is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 3 of the License, or (at
your option) any later version.

Easy AdSense is distributed in the hope that it will be useful, but
WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program.  If not, see <http://www.gnu.org/licenses/>.

Easy AdSense is supported by ad space sharing. Unless you configure
the program (following the instructions on its admin page) and
explicitly turn off the sharing, you agree to run its developer's ads
on your site(s). By using the program, you are agreeing to this
condition, and confirming that your sites abide by Google's policies
and terms of service.
*/

echo '<script type="text/javascript" src="'. get_option('siteurl') . '/' . PLUGINDIR . '/' .  basename(dirname(__FILE__)) . '/wz_tooltip.js"></script>' ;
if (isset($this->ezTran)) {
  echo '<div class="wrap" style="width:900px">' ;
  echo '<form method="post" action="' . $_SERVER["REQUEST_URI"] . '">' ;
  $this->ezTran->printAdminPage() ;
  echo "</form>\n</div>" ;
}
else {
?>

<div class="wrap" id="wrapper" style="width:900px">
<h2 title="<?php echo $this->info(false) ?>">Easy AdSense Setup
<a href="http://validator.w3.org/" target="_blank"><img src="http://www.w3.org/Icons/valid-xhtml10" alt="Valid XHTML 1.0 Transitional" title="Easy AdSense Admin Page is certified Valid XHTML 1.0 Transitional" height="31" width="88" class="alignright"/></a>
</h2>

<table class="form-table">
<tr><th scope="row"><h3><?php _e('Instructions', 'easy-adsenser') ; ?></h3></th></tr>
<tr valign="middle">
<td width="40%">

<ul style="padding-left:10px;list-style-type:circle; list-style-position:inside;" >
<li>
<a href="#" title="<?php _e('Click for help', 'easy-adsenser') ; ?>" onclick="TagToTip('help0',WIDTH, 300, TITLE, '<?php _e('How to Set it up', 'easy-adsenser') ; ?>', STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 15, 5])">
<?php
printf(__('A few easy steps to setup %s', 'easy-adsenser'),'<em>Easy AdSense</em>') ;
?></a><br />
</li>
<li>
<a href="#" title="<?php _e('Click for help', 'easy-adsenser') ; ?>" onclick="TagToTip('help1',WIDTH, 300, TITLE, '<?php _e('How to Control AdSense on Each Post', 'easy-adsenser') ; ?>', STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 15, 5])">
<?php _e('Need to control ad blocks on each post?', 'easy-adsenser') ;?></a><br />
</li>
<li>
<a href="#" title="<?php _e('Click for help', 'easy-adsenser') ; ?>" onclick="TagToTip('help1a',WIDTH, 300, TITLE, '<?php _e('All-in-One AdSense Control', 'easy-adsenser') ; ?>', STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 15, 5])">
<?php _e('Sidebar Widgets, Link Units or Google Search', 'easy-adsenser') ;?></a><br />
</li>
<li>
<a href="#" title="<?php _e('Click for help', 'easy-adsenser') ; ?>" onclick="TagToTip('rate', TITLE, 'WordPress: Easy AdSense', STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [25, 25])">
<?php _e('Check out the FAQ and rate this plugin.', 'easy-adsenser') ;?></a><br />
</li>
</ul>
</td>

<?php @include (dirname (__FILE__).'/head-text.php'); ?>

</tr>
</table>

<form method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">
<br />

<?php
      if ($ezAdOptions['policy'] == 'unknown') {
?>
<div style="background-color:#fdd;border: solid 1px #f00; padding:5px" id="tnc">
<p>Does your website conform to Google terms and conditions? In particular, do you cofirm that your website does not contain any pornographic, adult-oriented, pirated or gambling content? </p>
<span onmouseover="Tip('<?php _e('Your repsonse only affects what kind of ads are displayed by default and in shared ad slots, if you enable ad-slot sharing.', 'easy-adsenser') ?>', WIDTH, 240, TITLE, '<?php _e('Policy Compliance', 'easy-adsenser') ?>')" onmouseout="UnTip()">
<input type = "button" id = "ybutton" value = "Yes" onclick = "buttonwhich('yes')">
<input type = "button" id = "nbutton" value = "No" onclick = "buttonwhich('no')">
<input type="hidden" id="ezAdSensePolicy" name="ezAdSensePolicy" value="<?php echo($ezAdOptions['policy']);?>" /></span>

<script type = "text/javascript">
function hideTnC() {
  document.getElementById("tnc").style.display = 'none';
}
function buttonwhich(message) {
  document.getElementById("ezAdSensePolicy").value = message;
  document.getElementById("ybutton").style.display = 'none';
  document.getElementById("nbutton").disabled = 'true';
  document.getElementById("nbutton").value = 'Thank you for confirming!';
  setTimeout('hideTnC()', 6000);
}
</script>
</div>
      <?php } ?>

<table class="form-table">
<tr><th scope="row"><h3><?php printf(__('Options (for the %s theme)', 'easy-adsenser'), $mThemeName); ?> </h3></th></tr>
</table>

<table width="100%">
<tr>
<td width="50%" height="50px">

<table class="form-table">
<tr>
<td width="50%" height="40px">
<b><u><?php _e('Ad Blocks in Your Posts', 'easy-adsenser') ; ?></u></b><br />
<?php _e('[Appears in your posts and pages]', 'easy-adsenser') ; ?>
</td>
</tr>
</table>
</td>

<td width="50%" height="50px">
<table class="form-table">
<tr>
<td width="50%" height="40px">
<b><u><?php _e('Widgets for Your Sidebars', 'easy-adsenser') ; ?></u></b><br />
<?php _e('[See <a href="widgets.php"> Appearance (or Design) &rarr; Widgets</a>]', 'easy-adsenser') ; ?>
</td>
</tr>
</table>
</td>
</tr>
</table>

<table width="100%">
<tr valign="top">
<td width="50%">
<table class="form-table">
<tr valign="top">
<td width="50%" height="220px" valign="middle">
<b><?php _e('Lead-in AdSense Text', 'easy-adsenser') ; ?></b>&nbsp;
<?php _e('(Appears near the beginning of the post)', 'easy-adsenser') ; ?><br />
<textarea cols="50" rows="15" name="ezAdSenseTextLeadin" style="width: 95%; height: 130px;"><?php echo(stripslashes(htmlspecialchars($ezAdOptions['text_leadin']))) ?></textarea>
<br />
<b><?php _e('Ad Alignment', 'easy-adsenser') ; ?></b>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span onmouseover="Tip('<?php _e('Suppress this ad block if the post is not at least this many words long. Enter 0 or a small number if you do not want to suppress ads based on the number of words in the page/post.', 'easy-adsenser') ?>', WIDTH, 240, TITLE, '<?php _e('Min. Word Count', 'easy-adsenser') ?>')" onmouseout="UnTip()"><?php _e('Min. Word Count', 'easy-adsenser') ; ?>: <input style="width:40px;text-align:center;" id="ezLeadInWC" name="ezLeadInWC" value="<?php echo(stripslashes(htmlspecialchars($ezAdOptions['wc_leadin'])));?>" /></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<span onmouseover="Tip('<?php _e('Use the margin setting to trim margins. Decreasing the margin moves the ad block left and up. Margin can be negative.', 'easy-adsenser') ?>', WIDTH, 240, TITLE, '<?php _e('Tweak Margins', 'easy-adsenser') ?>')" onmouseout="UnTip()"><?php _e('Margin:', 'easy-adsenser') ; ?> <input style="width:30px;text-align:center;" id="ezLeadInMargin" name="ezLeadInMargin" value="<?php echo(stripslashes(htmlspecialchars($ezAdOptions['margin_leadin'])));?>" />px</span>
<br />

<label for="ezHeaderLeadin" onmouseover="Tip('<?php _e('Select where you would like to show the lead-in ad block. A placement above or below the blog header would be suitable for a wide AdSense block.', 'easy-adsenser') ; echo (htmlspecialchars('<br />Note that <b>Below Header</b> and <b>End of Page</b> options are hacks that may not be compatible with the WordPress default widget for <b>Recent Posts</b> or anything else that may use DB queries or loops. If you have problems with your sidebars and/or font sizes, please choose some other <b>Postion</b> option.')) ; ?>', WIDTH, 240, TITLE, '<?php _e('(Where to show?)', 'easy-adsenser') ?>')" onmouseout="UnTip()">
<?php _e('Position:', 'easy-adsenser') ; ?>
<select style="width:30%;" id="ezHeaderLeadin" name="ezHeaderLeadin">
<option <?php if ($ezAdOptions['header_leadin'] == 'send_headers') { echo('selected="selected"'); }?> value ="send_headers"><?php _e('Above Header', 'easy-adsenser') ?></option>
<option <?php if ($ezAdOptions['header_leadin'] == 'the_content') { echo('selected="selected"'); }?> value ="the_content"><?php _e('Below Header', 'easy-adsenser') ?></option>
<option <?php if ($ezAdOptions['header_leadin'] == '') { echo('selected="selected"'); }?> value =""><?php _e('Beginning of Post', 'easy-adsenser') ?></option>
</select>
</label>
&nbsp;
<label for="ezAdSenseShowLeadin" onmouseover="Tip('<?php _e('Decide whether to show this AdSense block, and specify how to align it.', 'easy-adsenser') ?>', WIDTH, 240, TITLE, '<?php _e('(Where to show?)', 'easy-adsenser') ?>')" onmouseout="UnTip()">
<?php _e('Show:', 'easy-adsenser') ; ?>
<select style="width:42%;" id="ezAdSenseShowLeadin" name="ezAdSenseShowLeadin">
<option <?php if ($ezAdOptions['show_leadin'] == 'no') { echo('selected="selected"'); }?> value ="no"><?php _e('Suppress Lead-in Ad', 'easy-adsenser') ?></option>
<option <?php if ($ezAdOptions['show_leadin'] == 'float:left') { echo('selected="selected"'); }?> value ="float:left"><?php _e('Align Left', 'easy-adsenser'); echo ', ' ; _e('Text-wrapped', 'easy-adsenser'); ?></option>
<option <?php if ($ezAdOptions['show_leadin'] == 'text-align:left') { echo('selected="selected"'); }?> value ="text-align:left"><?php _e('Align Left', 'easy-adsenser'); echo ', ' ; _e('No wrap', 'easy-adsenser'); ?></option>
<option <?php if ($ezAdOptions['show_leadin'] == 'text-align:center') { echo('selected="selected"'); }?> value ="text-align:center"><?php _e('Center', 'easy-adsenser') ?></option>
<option <?php if ($ezAdOptions['show_leadin'] == 'float:right') { echo('selected="selected"'); }?> value ="float:right"><?php _e('Align Right', 'easy-adsenser'); echo ', ' ; _e('Text-wrapped', 'easy-adsenser'); ?></option>
<option <?php if ($ezAdOptions['show_leadin'] == 'text-align:right') { echo('selected="selected"'); }?> value ="text-align:right"><?php _e('Align Rigth', 'easy-adsenser'); echo ', ' ; _e('No wrap', 'easy-adsenser'); ?></option>
</select>
</label>
<br />
</td>
</tr>
<tr valign="top">
<td width="50%" height="220px" valign="middle">
<b><?php _e('Mid-Post AdSense Text', 'easy-adsenser') ; ?></b>&nbsp;
<?php _e('(Appears near the middle of the post)', 'easy-adsenser') ; ?><br />
<textarea cols="50" rows="15" name="ezAdSenseTextMidtext" style="width: 95%; height: 130px;"><?php echo(stripslashes(htmlspecialchars($ezAdOptions['text_midtext']))) ?></textarea>
<br />
<b><?php _e('Ad Alignment', 'easy-adsenser') ; ?></b>&nbsp;
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span onmouseover="Tip('<?php _e('Suppress this ad block if the post is not at least this many words long. Enter 0 or a small number if you do not want to suppress ads based on the number of words in the page/post.', 'easy-adsenser') ?>', WIDTH, 240, TITLE, '<?php _e('Min. Word Count', 'easy-adsenser') ?>')" onmouseout="UnTip()"><?php _e('Min. Word Count', 'easy-adsenser') ; ?>: <input style="width:40px;text-align:center;" id="ezMidTextWC" name="ezMidTextWC" value="<?php echo(stripslashes(htmlspecialchars($ezAdOptions['wc_midtext'])));?>" /></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<span onmouseover="Tip('<?php _e('Use the margin setting to trim margins. Decreasing the margin moves the ad block left and up. Margin can be negative.', 'easy-adsenser') ?>', WIDTH, 240, TITLE, '<?php _e('Tweak Margins', 'easy-adsenser') ?>')" onmouseout="UnTip()"><?php _e('Margin:', 'easy-adsenser') ; ?> <input style="width:30px;text-align:center;" id="ezMidTextMargin" name="ezMidTextMargin" value="<?php echo(stripslashes(htmlspecialchars($ezAdOptions['margin_midtext'])));?>" />px</span>
<br />
<label for="ezForceMidAd" onmouseover="Tip('<?php _e('Force mid-text ad (if enabled) even in short posts.', 'easy-adsenser') ?>', WIDTH, 240, TITLE, '<?php _e('Force Mid-post Ad', 'easy-adsenser') ?>')" onmouseout="UnTip()">
<input type="checkbox" id="ezForceMidAd" name="ezForceMidAd"  <?php if ($ezAdOptions['force_midad']) { echo('checked="checked"'); }?> /> <?php _e('Force Mid-post Ad', 'easy-adsenser') ; ?></label>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<label for="ezAdSenseShowMidtext" onmouseover="Tip('<?php _e('Decide whether to show this AdSense block, and specify how to align it.', 'easy-adsenser') ?>', WIDTH, 240, TITLE, '<?php _e('(Where to show?)', 'easy-adsenser') ?>')" onmouseout="UnTip()">
<?php _e('Show:', 'easy-adsenser') ; ?>
<select style="width:42%;" id="ezAdSenseShowMidtext" name="ezAdSenseShowMidtext">
<option <?php if ($ezAdOptions['show_midtext'] == 'no') { echo('selected="selected"'); }?> value ="no"><?php _e('Suppress Mid-post Ad', 'easy-adsenser') ?></option>
<option <?php if ($ezAdOptions['show_midtext'] == 'float:left') { echo('selected="selected"'); }?> value ="float:left"><?php _e('Align Left', 'easy-adsenser'); echo ', ' ; _e('Text-wrapped', 'easy-adsenser'); ?></option>
<option <?php if ($ezAdOptions['show_midtext'] == 'text-align:left') { echo('selected="selected"'); }?> value ="text-align:left"><?php _e('Align Left', 'easy-adsenser'); echo ', ' ; _e('No wrap', 'easy-adsenser'); ?></option>
<option <?php if ($ezAdOptions['show_midtext'] == 'text-align:center') { echo('selected="selected"'); }?> value ="text-align:center"><?php _e('Center', 'easy-adsenser') ?></option>
<option <?php if ($ezAdOptions['show_midtext'] == 'float:right') { echo('selected="selected"'); }?> value ="float:right"><?php _e('Align Right', 'easy-adsenser'); echo ', ' ; _e('Text-wrapped', 'easy-adsenser'); ?></option>
<option <?php if ($ezAdOptions['show_midtext'] == 'text-align:right') { echo('selected="selected"'); }?> value ="text-align:right"><?php _e('Align Rigth', 'easy-adsenser'); echo ', ' ; _e('No wrap', 'easy-adsenser'); ?></option>
</select>
</label>

</td>
</tr>
<tr valign="top">
<td width="50%" height="250px" valign="middle">
<b><?php _e('Post Lead-out AdSense Text', 'easy-adsenser') ; ?></b>&nbsp;
<?php _e('(Appears near the end of the post)', 'easy-adsenser') ; ?><br />
<textarea cols="50" rows="15" name="ezAdSenseTextLeadout" style="width: 95%; height: 162px;"><?php echo(stripslashes(htmlspecialchars($ezAdOptions['text_leadout']))) ?></textarea>
<br />
<b><?php _e('Ad Alignment', 'easy-adsenser') ; ?></b>
&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<span onmouseover="Tip('<?php _e('Suppress this ad block if the post is not at least this many words long. Enter 0 or a small number if you do not want to suppress ads based on the number of words in the page/post.', 'easy-adsenser') ?>', WIDTH, 240, TITLE, '<?php _e('Min. Word Count', 'easy-adsenser') ?>')" onmouseout="UnTip()"><?php _e('Min. Word Count', 'easy-adsenser') ; ?>: <input style="width:40px;text-align:center;" id="ezLeadOutWC" name="ezLeadOutWC" value="<?php echo(stripslashes(htmlspecialchars($ezAdOptions['wc_leadout'])));?>" /></span>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<span onmouseover="Tip('<?php _e('Use the margin setting to trim margins. Decreasing the margin moves the ad block left and up. Margin can be negative.', 'easy-adsenser') ?>', WIDTH, 240, TITLE, '<?php _e('Tweak Margins', 'easy-adsenser') ?>')" onmouseout="UnTip()"><?php _e('Margin:', 'easy-adsenser') ; ?> <input style="width:30px;text-align:center;" id="ezLeadOutMargin" name="ezLeadOutMargin" value="<?php echo(stripslashes(htmlspecialchars($ezAdOptions['margin_leadout'])));?>" />px</span>
<br />

<label for="ezFooterLeadout" onmouseover="Tip('<?php _e('Select where you would like to show the lead-out ad block. A placement above or below the blog footer would be suitable for a wide AdSense block.', 'easy-adsenser') ; echo (htmlspecialchars('<br />Note that <b>Below Header</b> and <b>End of Page</b> options are hacks that may not be compatible with the WordPress default widget for <b>Recent Posts</b> or anything else that may use DB queries or loops. If you have problems with your sidebars and/or font sizes, please choose some other <b>Position</b> option.')) ;  ?>', WIDTH, 240, TITLE, '<?php _e('(Where to show?)', 'easy-adsenser') ?>')" onmouseout="UnTip()">
<?php _e('Position:', 'easy-adsenser') ; ?>
<select style="width:30%;" id="ezFooterLeadout" name="ezFooterLeadout">
<option <?php if ($ezAdOptions['footer_leadout'] == '') { echo('selected="selected"'); }?> value =""><?php _e('End of Post', 'easy-adsenser') ?></option>
<option <?php if ($ezAdOptions['footer_leadout'] == 'loop_end') { echo('selected="selected"'); }?> value ="loop_end"><?php _e('End of Page', 'easy-adsenser') ?></option>
<option <?php if ($ezAdOptions['footer_leadout'] == 'get_footer') { echo('selected="selected"'); }?> value ="get_footer"><?php _e('Above Footer', 'easy-adsenser') ?></option>
<option <?php if ($ezAdOptions['footer_leadout'] == 'wp_footer') { echo('selected="selected"'); }?> value ="wp_footer"><?php _e('Below Footer', 'easy-adsenser') ?></option>
</select>
</label>
&nbsp;
<label for="ezAdSenseShowLeadout" onmouseover="Tip('<?php _e('Decide whether to show this AdSense block, and specify how to align it.', 'easy-adsenser') ?>', WIDTH, 240, TITLE, '<?php _e('(Where to show?)', 'easy-adsenser') ?>')" onmouseout="UnTip()">
<?php _e('Show:', 'easy-adsenser') ; ?>
<select style="width:42%;" id="ezAdSenseShowLeadout" name="ezAdSenseShowLeadout">
<option <?php if ($ezAdOptions['show_leadout'] == 'no') { echo('selected="selected"'); }?> value ="no"><?php _e('Suppress Lead-out Ad', 'easy-adsenser') ?></option>
<option <?php if ($ezAdOptions['show_leadout'] == 'float:left') { echo('selected="selected"'); }?> value ="float:left"><?php _e('Align Left', 'easy-adsenser'); echo ', ' ; _e('Text-wrapped', 'easy-adsenser'); ?></option>
<option <?php if ($ezAdOptions['show_leadout'] == 'text-align:left') { echo('selected="selected"'); }?> value ="text-align:left"><?php _e('Align Left', 'easy-adsenser'); echo ', ' ; _e('No wrap', 'easy-adsenser'); ?></option>
<option <?php if ($ezAdOptions['show_leadout'] == 'text-align:center') { echo('selected="selected"'); }?> value ="text-align:center"><?php _e('Center', 'easy-adsenser') ?></option>
<option <?php if ($ezAdOptions['show_leadout'] == 'float:right') { echo('selected="selected"'); }?> value ="float:right"><?php _e('Align Right', 'easy-adsenser'); echo ', ' ; _e('Text-wrapped', 'easy-adsenser'); ?></option>
<option <?php if ($ezAdOptions['show_leadout'] == 'text-align:right') { echo('selected="selected"'); }?> value ="text-align:right"><?php _e('Align Rigth', 'easy-adsenser'); echo ', ' ; _e('No wrap', 'easy-adsenser'); ?></option>
</select>
</label>
<br />
</td>
</tr>
</table>

<table class="form-table">
<tr valign="top">
<td width="50%" height="250px" valign="middle">
<b><?php _e('Option on Google Policy', 'easy-adsenser') ; ?></b>
<font size="-2"><?php _e('(Google policy allows no more than three ad blocks and three link units per page)', 'easy-adsenser') ; ?></font>
<br />
<label for="ezAdSenseMax3">
<input type="radio" id="ezAdSenseMax3" name="ezAdSenseMax" value="3" <?php if ($ezAdOptions['max_count'] == 3) { echo('checked="checked"'); }?> /> <?php _e('Three ad blocks (including the side bar widget, if enabled).', 'easy-adsenser') ; ?></label><br />
<label for="ezAdSenseMax2">
<input type="radio" id="ezAdSenseMax2" name="ezAdSenseMax" value="2" <?php if ($ezAdOptions['max_count'] == 2) { echo('checked="checked"'); }?> /> <?php _e('Two ad blocks.', 'easy-adsenser') ; ?></label>
<label for="ezAdSenseMax1">
<input type="radio" id="ezAdSenseMax1" name="ezAdSenseMax" value="1" <?php if ($ezAdOptions['max_count'] == 1) { echo('checked="checked"'); }?> /> <?php _e('One ad block.', 'easy-adsenser') ; ?></label>
<label for="ezAdSenseMax0">
<input type="radio" id="ezAdSenseMax0" name="ezAdSenseMax" value="0" <?php if ($ezAdOptions['max_count'] == 0) { echo('checked="checked"'); }?> /> <?php _e('No ad blocks in posts.', 'easy-adsenser') ; ?></label><br />
<label for="ezAdSenseMax9">
<input type="radio" id="ezAdSenseMax9" name="ezAdSenseMax" value="99" <?php if ($ezAdOptions['max_count'] == 99) { echo('checked="checked"'); }?> /> <?php _e('Any number of ad blocks (At your own risk!)', 'easy-adsenser') ; ?></label><br />

<?php if (get_bloginfo('version') < 2.8) {_e('Number of Link Units widgets (&le; 3) [Google serves only three]:', 'easy-adsenser') ; ?> <input style="width:30px;text-align:center;" id="ezLimitLU" name="ezLimitLU" value="<?php echo(stripslashes(htmlspecialchars($ezAdOptions['limit_lu'])));?>" /><br /><br style="line-height: 7px;" /> <?php } else echo '<br style="line-height: 7px;" />' ;?>

<b><?php printf(__('Support %s by Donating Ad Space', 'easy-adsenser'), 'Easy AdSense') ; ?></b><br />
<?php _e('Percentage of ad slots to share [Default: 5%]:', 'easy-adsenser') ; ?> <input style="width:30px;text-align:center;" id="ezMC" name="ezMC" value="<?php echo(stripslashes(htmlspecialchars($ezAdOptions['mc'])));?>" />%<br /><br style="line-height: 7px;" />

<b><?php _e('Suppress AdSense Ad Blocks on:', 'easy-adsenser') ; ?></b>&nbsp;&nbsp;
<input type="checkbox" id="ezKillPages" name="ezKillPages" value="true" <?php if ($ezAdOptions['kill_pages']) { echo('checked="checked"'); }?> /> <a href="http://codex.wordpress.org/Pages" target="_blank" title="<?php _e('Click to see the difference between posts and pages', 'easy-adsenser') ; ?>"><?php _e('Pages (Ads only on Posts)', 'easy-adsenser') ; ?></a><br />
<label for="ezKillAttach" title="<?php _e('Pages that show attachments', 'easy-adsenser') ; ?>">
<input type="checkbox" id="ezKillAttach" name="ezKillAttach" <?php if ($ezAdOptions['kill_attach']) { echo('checked="checked"'); }?> /> <?php _e('Attachment Page', 'easy-adsenser') ; ?></label>&nbsp;&nbsp;
<label for="ezKillHome" title="<?php _e('Home Page and Front Page are the same for most blogs', 'easy-adsenser') ; ?>">
<input type="checkbox" id="ezKillHome" name="ezKillHome" <?php if ($ezAdOptions['kill_home']) { echo('checked="checked"'); }?> /> <?php _e('Home Page', 'easy-adsenser') ; ?></label>&nbsp;&nbsp;
<label for="ezKillFront" title="<?php _e('Home Page and Front Page are the same for most blogs', 'easy-adsenser') ; ?>">
<input type="checkbox" id="ezKillFront" name="ezKillFront" <?php if ($ezAdOptions['kill_front']) { echo('checked="checked"'); }?> /> <?php _e('Front Page', 'easy-adsenser') ; ?></label>&nbsp;&nbsp;
<br />
<label for="ezKillCat" title="<?php _e('Pages that come up when you click on category names', 'easy-adsenser') ; ?>">
<input type="checkbox" id="ezKillCat" name="ezKillCat" <?php if ($ezAdOptions['kill_cat']) { echo('checked="checked"'); }?> /> <?php _e('Category Pages', 'easy-adsenser') ; ?></label>&nbsp;&nbsp;&nbsp;&nbsp;
<label for="ezKillTag" title="<?php _e('Pages that come up when you click on tag names', 'easy-adsenser') ; ?>">
<input type="checkbox" id="ezKillTag" name="ezKillTag" <?php if ($ezAdOptions['kill_tag']) { echo('checked="checked"'); }?> /> <?php _e('Tag Pages', 'easy-adsenser') ; ?></label>&nbsp;&nbsp;&nbsp;
<label for="ezKillArchive" title="<?php _e('Pages that come up when you click on year/month archives', 'easy-adsenser') ; ?>">
<input type="checkbox" id="ezKillArchive" name="ezKillArchive" <?php if ($ezAdOptions['kill_archive']) { echo('checked="checked"'); }?> /> <?php _e('Archive Pages', 'easy-adsenser') ; ?></label>&nbsp;&nbsp;
</td>
</tr>
</table>

</td>
<td width="50%">

<table class="form-table">
<tr valign="top">
<td width="50%" height="220px" valign="middle">
<b><?php _e('AdSense Widget Text', 'easy-adsenser') ; ?></b>&nbsp;
<?php _e('(Appears in the Sidebar as a Widget)', 'easy-adsenser') ; ?><br />
<textarea cols="50" rows="15" name="ezAdSenseTextWidget" style="width: 95%; height: 110px;"><?php echo(stripslashes(htmlspecialchars($ezAdOptions['text_widget']))) ?></textarea>
<br />
<b><?php _e('Ad Alignment', 'easy-adsenser') ; ?></b>&nbsp;
<?php _e('(Where to show?)', 'easy-adsenser') ; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<span onmouseover="Tip('<?php _e('Use the margin setting to trim margins. Decreasing the margin moves the ad block left and up. Margin can be negative.', 'easy-adsenser') ?>', WIDTH, 240, TITLE, '<?php _e('Tweak Margins', 'easy-adsenser') ?>')" onmouseout="UnTip()"><?php _e('Margin:', 'easy-adsenser') ; ?> <input style="width:30px;text-align:center;" id="ezWidgetMargin" name="ezWidgetMargin" value="<?php echo(stripslashes(htmlspecialchars($ezAdOptions['margin_widget'])));?>" />px</span>
<br />
<label for="ezAdSenseShowWidget_left">
<input type="radio" id="ezAdSenseShowWidget_left" name="ezAdSenseShowWidget" value="text-align:left" <?php if ($ezAdOptions['show_widget'] == "text-align:left") { echo('checked="checked"'); }?> /> <?php _e('Align Left', 'easy-adsenser') ; ?> </label>&nbsp;
<label for="ezAdSenseShowWidget_center">
<input type="radio" id="ezAdSenseShowWidget_center" name="ezAdSenseShowWidget" value="text-align:center" <?php if ($ezAdOptions['show_widget'] == "text-align:center") { echo('checked="checked"'); }?> /> <?php _e('Center', 'easy-adsenser') ; ?> </label>&nbsp;
<label for="ezAdSenseShowWidget_right">
<input type="radio" id="ezAdSenseShowWidget_right" name="ezAdSenseShowWidget" value="text-align:right" <?php if ($ezAdOptions['show_widget'] == "text-align:right") { echo('checked="checked"'); }?> /> <?php _e('Align Right', 'easy-adsenser') ; ?> </label>&nbsp;
<label for="ezAdSenseShowWidget_no">
<input type="radio" id="ezAdSenseShowWidget_no" name="ezAdSenseShowWidget" value="no" <?php if ($ezAdOptions['show_widget'] == "no") { echo('checked="checked"'); }?> /> <?php _e('Suppress Widget', 'easy-adsenser') ; ?></label><br />
<label for="ezAdWidgetTitle"><b><?php _e('Widget Title:', 'easy-adsenser') ; ?></b>&nbsp; <input style="width:200px" id="ezAdWidgetTitle" name="ezAdWidgetTitle" type="text" value= "<?php echo(stripslashes(htmlspecialchars($ezAdOptions['title_widget']))) ?>" /></label>&nbsp;
<label for="ezAdKillWidgetTitle"><input type="checkbox" id="ezAdKillWidgetTitle" name="ezAdKillWidgetTitle" <?php if ($ezAdOptions['kill_widget_title']) { echo('checked="checked"'); }?> /> <?php _e('Hide Title', 'easy-adsenser') ; ?> </label>
</td>
</tr>
<tr valign="top">
<td width="50%" height="220px" valign="middle">
<b><?php _e('AdSense Link-Units Text', 'easy-adsenser') ; ?></b>&nbsp;
<?php _e('(Appears in the Sidebar as  Widgets)', 'easy-adsenser') ; ?><br />
<textarea cols="50" rows="15" name="ezAdSenseTextLU" style="width: 95%; height: 110px;"><?php echo(stripslashes(htmlspecialchars($ezAdOptions['text_lu']))) ?></textarea>
<br />
<b><?php _e('Ad Alignment', 'easy-adsenser') ; ?></b>&nbsp;
<?php _e('(Where to show?)', 'easy-adsenser') ; ?>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
<span onmouseover="Tip('<?php _e('Use the margin setting to trim margins. Decreasing the margin moves the ad block left and up. Margin can be negative.', 'easy-adsenser') ?>', WIDTH, 240, TITLE, '<?php _e('Tweak Margins', 'easy-adsenser') ?>')" onmouseout="UnTip()"><?php _e('Margin:', 'easy-adsenser') ; ?> <input style="width:30px;text-align:center;" id="ezLUMargin" name="ezLUMargin" value="<?php echo(stripslashes(htmlspecialchars($ezAdOptions['margin_lu'])));?>" />px</span>
<br />
<label for="ezAdSenseShowLU_left">
<input type="radio" id="ezAdSenseShowLU_left" name="ezAdSenseShowLU" value="text-align:left" <?php if ($ezAdOptions['show_lu'] == "text-align:left") { echo('checked="checked"'); }?> /> <?php _e('Align Left', 'easy-adsenser') ; ?> </label>&nbsp;
<label for="ezAdSenseShowLU_center">
<input type="radio" id="ezAdSenseShowLU_center" name="ezAdSenseShowLU" value="text-align:center" <?php if ($ezAdOptions['show_lu'] == "text-align:center") { echo('checked="checked"'); }?> /> <?php _e('Center', 'easy-adsenser') ; ?> </label>&nbsp;
<label for="ezAdSenseShowLU_right">
<input type="radio" id="ezAdSenseShowLU_right" name="ezAdSenseShowLU" value="text-align:right" <?php if ($ezAdOptions['show_lu'] == "text-align:right") { echo('checked="checked"'); }?> /> <?php _e('Align Right', 'easy-adsenser') ; ?> </label>&nbsp;
<label for="ezAdSenseShowLU_no">
<input type="radio" id="ezAdSenseShowLU_no" name="ezAdSenseShowLU" value="no" <?php if ($ezAdOptions['show_lu'] == "no") { echo('checked="checked"'); }?> /> <?php _e('Suppress Link Units', 'easy-adsenser') ; ?></label><br />
<label for="ezAdLUTitle"><b><?php _e('Link Unit Title:', 'easy-adsenser') ; ?></b>&nbsp; <input style="width: 200px;" id="ezAdLUTitle" name="ezAdLUTitle" type="text" value= "<?php echo(stripslashes(htmlspecialchars($ezAdOptions['title_lu']))) ?>" /></label>
<label for="ezAdKillLUTitle"><input type="checkbox" id="ezAdKillLUTitle" name="ezAdKillLUTitle" <?php if ($ezAdOptions['kill_lu_title']) { echo('checked="checked"'); }?> /> <?php _e('Hide Title', 'easy-adsenser') ; ?> </label>
</td>
</tr>
<tr valign="top">
<td width="50%" height="250px" valign="middle">
<b><?php _e('Google Search Widget', 'easy-adsenser') ; ?></b>&nbsp;
<?php _e('(Adds a Google Search Box to your sidebar)', 'easy-adsenser') ; ?><br />
<textarea cols="50" rows="15" name="ezAdSenseTextGSearch" style="width: 95%; height: 110px;"><?php echo(stripslashes(htmlspecialchars($ezAdOptions['text_gsearch']))) ?></textarea>
<br />
<b><?php _e('Search Title', 'easy-adsenser') ; ?></b>&nbsp;
<?php _e('(Title of the Google Search Widget)', 'easy-adsenser') ; ?>&nbsp;&nbsp;&nbsp;&nbsp;
<span onmouseover="Tip('<?php _e('Use the margin setting to trim margins. Decreasing the margin moves the ad block left and up. Margin can be negative.', 'easy-adsenser') ?>', WIDTH, 240, TITLE, '<?php _e('Tweak Margins', 'easy-adsenser') ?>')" onmouseout="UnTip()"><?php _e('Margin:', 'easy-adsenser') ; ?> <input style="width:30px;text-align:center;" id="ezSearchMargin" name="ezSearchMargin" value="<?php echo(stripslashes(htmlspecialchars($ezAdOptions['margin_gsearch'])));?>" />px</span>
<br />
<label for="ezAdSenseShowGSearch_dark">
<input type="radio" id="ezAdSenseShowGSearch_dark" name="ezAdSenseShowGSearch" value="dark" <?php if ($ezAdOptions['title_gsearch'] == "dark") { echo('checked="checked"'); }?> />&nbsp; <?php echo '<img src=" ' . $this->plugindir . '/google-dark.gif" border="0" alt="Google (dark)" style="background:black;vertical-align:-40%;"'; ?> /> </label>&nbsp;
<label for="ezAdSenseShowGSearch_light">
<input type="radio" id="ezAdSenseShowGSearch_light" name="ezAdSenseShowGSearch" value="light" <?php if ($ezAdOptions['title_gsearch'] == "light") { echo('checked="checked"'); }?> />&nbsp; <?php echo '<img src=" ' . $this->plugindir . '/google-light.gif" border="0" alt="Google (light)" style="background:white;vertical-align:-40%;"'; ?> /> </label>&nbsp;
<label for="ezAdSenseShowGSearch_no">
<input type="radio" id="ezAdSenseShowGSearch_no" name="ezAdSenseShowGSearch" value="no" <?php if ($ezAdOptions['title_gsearch'] == "no") { echo('checked="checked"'); }?> /> <?php _e('Suppress Search Box', 'easy-adsenser') ; ?></label><br /><br />
<label for="ezAdSenseShowGSearch_text">
<input type="radio" id="ezAdSenseShowGSearch_text" name="ezAdSenseShowGSearch" value="text" <?php $title = $ezAdOptions['title_gsearch'] ; if ($title != 'dark' && $title != 'light' && $title != 'no') { echo('checked="checked"'); }?> /> <b><?php _e('Custom Title:', 'easy-adsenser') ; ?></b></label>&nbsp;
<label for="ezAdSearchTitle">
<input style="width: 200px;" id="ezAdSearchTitle" name="ezAdSearchTitle" type="text" value= "<?php echo(stripslashes(htmlspecialchars($ezAdOptions['title_gsearch']))) ?>" /></label>
<label for="ezAdKillSearchTitle"><input type="checkbox" id="ezAdKillSearchTitle" name="ezAdKillSearchTitle" <?php if ($ezAdOptions['kill_gsearch_title']) { echo('checked="checked"'); }?> /> <?php _e('Hide Title', 'easy-adsenser') ; ?> </label>
</td>
</tr>
</table>

<table class="form-table">
<tr valign="top">
<td width="50%" height="250px" valign="middle">
<b><?php _e('Other Options', 'easy-adsenser') ; ?></b><br />
<label for="ezAllowFeeds">
<input type="checkbox" id="ezAllowFeeds" name="ezAllowFeeds"  <?php if ($ezAdOptions['allow_feeds']) { echo('checked="checked"'); }?> /> <?php _e('Allow ad blocks in feeds. [Please report any problems with this option.]', 'easy-adsenser') ; ?></label><br />
<label for="ezForceWidget">
<input type="checkbox" id="ezForceWidget" name="ezForceWidget"  <?php if ($ezAdOptions['force_widget']) { echo('checked="checked"'); }?> /> <?php _e('Prioritize sidebar widget. (Always shows the widget, if enabled.)', 'easy-adsenser') ; ?></label><br />
<label for="ezShowBorders"  onmouseover="Tip('<?php _e('Google Policy says that you may not direct user attention to the ads via arrows or other graphical gimmicks. Please convince yourself that showing a mouseover decoration does not violate this Google statement before enabling this option.', 'easy-adsenser') ?>',WIDTH, 240, TITLE, 'Your call')" onmouseout="UnTip()" >
<input type="checkbox" id="ezShowBorders" name="ezShowBorders" <?php if ($ezAdOptions['show_borders']) { echo('checked="checked"'); }?> /> <?php _e('Show a border around the ads?', 'easy-adsenser') ; ?></label>&nbsp;
<label for="ezBorderWidget" title="<?php _e('Show the same border on the sidebar widget as well?', 'easy-adsenser') ; ?>">
<input type="checkbox" id="ezBorderWidget" name="ezBorderWidget" <?php if ($ezAdOptions['border_widget']) { echo('checked="checked"'); }?> /> <?php _e('Widget?', 'easy-adsenser') ; ?></label>&nbsp;&nbsp;
<label for="ezBorderLU" title="<?php _e('Show the same border on the link units too?', 'easy-adsenser') ; ?>">
<input type="checkbox" id="ezBorderLU" name="ezBorderLU" <?php if ($ezAdOptions['border_lu']) { echo('checked="checked"'); }?> /> <?php _e('Link Units?', 'easy-adsenser') ; ?></label><br />&nbsp;&nbsp;&nbsp;&nbsp;
Width: <input style="width:25px;text-align:center;" id="ezBorderWidth" name="ezBorderWidth" value="<?php echo(stripslashes(htmlspecialchars($ezAdOptions['border_width'])));?>" />px&nbsp;&nbsp;
Colors:&nbsp; Normal:#<input style="width:55px;text-align:center;" id="ezBorderNormal" name="ezBorderNormal" value="<?php echo(stripslashes(htmlspecialchars($ezAdOptions['border_normal'])));?>" />&nbsp;&nbsp; Hover:#<input style="width:55px;text-align:center;" id="ezBorderColor" name="ezBorderColor" value="<?php echo(stripslashes(htmlspecialchars($ezAdOptions['border_color'])));?>" /><br />

<label for="ezKillInLine"  onmouseover="Tip('<?php _e('All &lt;code&gt;&amp;lt;div&amp;gt;&lt;/code&gt;s that &lt;em&gt;Easy AdSense&lt;/em&gt; creates have the class attribute &lt;code&gt;adsense&lt;/code&gt;. Furthermore, they have class attributes like &lt;code&gt;adsense-leadin&lt;/code&gt;, &lt;code&gt;adsense-midtext&lt;/code&gt;, &lt;code&gt;adsense-leadout&lt;/code&gt;, &lt;code&gt;adsense-widget&lt;/code&gt; and &lt;code&gt;adsense-lu&lt;/code&gt; depending on the type. You can set the style for these classes in your theme &lt;code&gt;style.css&lt;/code&gt; to control their appearance.&lt;br /&gt;If this is all Greek to you, please leave the option unchecked.', 'easy-adsenser'); ?>',WIDTH, 290, TITLE, 'CSS vs. In-Line')" onmouseout="UnTip()" >
<input type="checkbox" id="ezKillInLine" name="ezKillInLine"  <?php if ($ezAdOptions['kill_inline']) { echo('checked="checked"'); }?> /> <?php _e('Suppress in-line styles (Control ad-blocks using style.css)', 'easy-adsenser') ; ?></label><br />
<br style="line-height: 5px;" />

<b><?php _e('Link-backs to', 'easy-adsenser') ; ?> <a href="http://www.Thulasidas.com" target="_blank">Unreal Blog</a></b>
<?php _e('(Consider showing at least one link.)', 'easy-adsenser') ; ?><br />
<label for="ezAdSenseLinkMax99">
<input type="radio" id="ezAdSenseLinkMax99" name="ezAdSenseLinkMax" value="99" <?php if ($ezAdOptions['max_link'] == 99) { echo('checked="checked"'); }?> /> <?php _e('Show a link under every ad block.', 'easy-adsenser') ; ?></label><br />
<label for="ezAdSenseLinkMax1">
<input type="radio" id="ezAdSenseLinkMax1" name="ezAdSenseLinkMax" value="1" <?php if ($ezAdOptions['max_link'] == 1) { echo('checked="checked"'); }?> /> <?php _e('Show the link only under the first ad block.', 'easy-adsenser') ; ?></label><br />
<label for="ezAdSenseLinkMax-1">
<input type="radio" id="ezAdSenseLinkMax-1" name="ezAdSenseLinkMax" value="-1" <?php if ($ezAdOptions['max_link'] == -1) { echo('checked="checked"'); }?> /> <?php _e('Show the link at the bottom of your blog page.', 'easy-adsenser') ; ?></label><br />
<label for="ezAdSenseLinkMax0">
<input type="radio" id="ezAdSenseLinkMax0" name="ezAdSenseLinkMax" value="0" <?php if ($ezAdOptions['max_link'] == 0) { echo('checked="checked"'); }?> /> <?php _e('Show no links to my blog anywhere (Are you sure?!)', 'easy-adsenser') ; ?></label>
</td>
</tr>
</table>

</td>
</tr>
</table>

<div class="submit">
<input type="submit" name="update_ezAdSenseSettings" value="<?php _e('Save Changes', 'easy-adsenser') ?>" title="<?php _e('Save the changes as specified above', 'easy-adsenser') ?>" onmouseover="Tip('<?php _e('Save the changes as specified above', 'easy-adsenser') ?>',WIDTH, 240, TITLE, '<?php _e('Save Changes', 'easy-adsenser') ?>')" onmouseout="UnTip()"/>
<input type="submit" name="reset_ezAdSenseSettings" value="<?php _e('Reset Options', 'easy-adsenser') ?>" title="<?php _e('Discard all your changes and load defaults. (Are you quite sure?)', 'easy-adsenser') ?>"  onmouseover="TagToTip('help3',WIDTH, 240, TITLE, 'DANGER!', BGCOLOR, '#ffcccc', FONTCOLOR, '#800000',BORDERCOLOR, '#c00000')" onmouseout="UnTip()"/>
<input type="submit" name="clean_db"  value="<?php _e('Clean Database', 'easy-adsenser') ?>" onmouseover="TagToTip('help4',WIDTH, 280, TITLE, 'DANGER!', BGCOLOR, '#ffcccc', FONTCOLOR, '#800000',BORDERCOLOR, '#c00000')" onmouseout="UnTip()"/>
<input type="submit" name="kill_me"  value="<?php _e('Uninstall', 'easy-adsenser') ?>" onmouseover="TagToTip('help5',WIDTH, 280, TITLE, 'DANGER!', BGCOLOR, '#ffcccc', FONTCOLOR, '#800000',BORDERCOLOR, '#c00000')" onmouseout="UnTip()"/>
<?php echo $this->invite ;
if ($this->locale != "en_US") {?>
&nbsp;&nbsp;&nbsp;&nbsp;<input type="image" title="Switch to English temporarily" src="<?php echo $this->plugindir ;?>/english.gif" style="vertical-align:-15px;" name="english" value="english" />
<?php } ?>
<hr />
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
4. <?php
_e('Take a look at the Google policy option, and other options. The defaults should work.', 'easy-adsenser') ;
?>
<br />
5.
<?php
printf(__('If you want to use the widgets, drag and drop them at %s Appearance (or Design) &rarr; Widgets %s', 'easy-adsenser'), '<a href="widgets.php">', '</a>.') ;
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

<span id="help1a">
<?php _e('<em>Easy AdSense</em> gives you widgets to embelish your sidebars. You can configure them here (on the right hand side of the Options table below) and place them on your page using <a href="widgets.php"> Appearance (or Design) &rarr; Widgets</a>.
<br />
<br />
1. <b>AdSense Widget</b> is an ad block widget that you can place any where on the sidebar. Typically, you would put a skyscraper block (160x600px, for instance) on your sidebar, but you can put anything -- not necessarily AdSense code.<br />
<br />
2. <b>AdSense Link Units</b>, if enabled, give you multiple widgets to put <a href="https://www.google.com/adsense/support/bin/answer.py?hl=en&amp;answer=15817" target="_blank">link units</a> on your sidebars. You can display three of them according to Google AdSense policy, and you can configure the number of widgets you need.<br /><br />
3. <b>Google Search Widget</b> gives you another widget to place a <a href="https://www.google.com/adsense/support/bin/answer.py?hl=en&amp;answer=17960" target="_blank">custom AdSense search box</a> on your sidebar. You can customize the look of the search box and its title by configuring them on this page.', 'easy-adsenser') ;?>
</span>

<span id="rate">
<iframe src="http://wordpress.org/extend/plugins/easy-adsenser/faq/" width="1000px" height="750px">
</iframe>
</span>

<span id="help3">
<font color="red"><?php _e('This <b>Reset Options</b> button discards all your changes and loads the default options. This is your only warning!', 'easy-adsenser') ; ?></font><br />
<b><?php _e('Discard all your changes and load defaults. (Are you quite sure?)', 'easy-adsenser') ?></b></span>

<span id="help4">
<font color="red"><?php _e('The <b>Database Cleanup</b> button discards all your AdSense settings you have saved so far for <b>all</b> the themes, including the current one. Use it only if you know that you won\'t be using these themes. Please be careful with all database operations -- keep a backup.', 'easy-adsenser') ; ?></font><br />
<b><?php _e('Discard all your changes and load defaults. (Are you quite sure?)', 'easy-adsenser') ?></b></span>

<span id="help5">
<font color="red"><?php printf(__('The <b>Uninstall</b> button really kills %s after cleaning up all the options it wrote in your database. This is your only warning! Please be careful with all database operations -- keep a backup.', 'easy-adsenser'), '<em>Easy AdSense</em>') ; ?></font><br />
<b><?php _e('Kill this plugin. (Are you quite sure?)', 'easy-adsenser') ?></b></span>

<?php @include (dirname (__FILE__).'/tail-text.php'); ?>

<table class="form-table" >
<tr><th scope="row"><h3><?php _e('Credits', 'easy-adsenser'); ?></h3></th></tr>
<tr><td>
<ul style="padding-left:10px;list-style-type:circle; list-style-position:inside;" >
<li>
<?php printf(__('%s uses the excellent Javascript/DHTML tooltips by %s', 'easy-adsenser'), '<b>Easy Adsenser</b>', '<a href="http://www.walterzorn.com" target="_blank" title="Javascript, DTML Tooltips"> Walter Zorn</a>.') ;
?>
</li>
</ul>
</td>
</tr>
</table>

</div>
<?php
   }
?>