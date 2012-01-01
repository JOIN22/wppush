<?php
/*
Copyright (C) 2008 www.thulasidas.com

This file is part of the programs "Easy AdSense", "AdSense Now!",
"Theme Tweaker", "Easy LaTeX", "More Money" and "Easy Translator".

These programs are free software; you can redistribute them and/or
modify it under the terms of the GNU General Public License as
published by the Free Software Foundation; either version 3 of the
License, or (at your option) any later version.

These programs are distributed in the hope that they will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
General Public License for more details.

You should have received a copy of the GNU General Public License
along with the programs.  If not, see <http://www.gnu.org/licenses/>.

Some of these programs are supported by ad space sharing. Unless you
configure them (following the instructions on its admin page) and
explicitly turn off the sharing, you agree to run its developer's ads
on your site(s). By using any of these programs, you are agreeing to
this condition, and confirming that your sites abide by Google's
policies and terms of service.
*/

$plgname = basename(dirname(__FILE__)) ;
if ($plgname == 'easy-adsenser'|| $plgname == 'adsense-now') { ?>

<td width="30%">
<div style="background-color:#ffffaa;padding:5px;border: solid 1px" id="support">
<b><?php _e('Support this Plugin!', 'easy-adsenser') ; ?></b><br />
<span style='text-decoration:underline' onmouseover="TagToTip('share', WIDTH, 230, TITLE, 'Ad Space Sharing',STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 5, 5])" onmouseout="UnTip()"><?php _e('Share a small fraction of your ad space', 'easy-adsenser') ; ?></span>,
<em style="text-decoration:underline" onmouseover="TagToTip('unreal', WIDTH, 230, TITLE, 'Buy &lt;em&gt;The Unreal Universe&lt;/em&gt;',STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 5, 5])"><?php _e('Buy a book, <b>The Unreal Universe</b>', 'easy-adsenser') ; ?></em>, or
<span style='text-decoration:underline' onmouseover="TagToTip('donate', WIDTH, 230, TITLE, 'Donation',STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 5, 5])"><?php _e('Make a donation', 'easy-adsenser') ; ?></span>.
</div>

<div id="share" style="padding:5px">
<?php
printf(__("Starting version %3.1f, <em>%s</em> has an ad space sharing option, if you would like to support its future development. It gives you an option to share a small fraction of your ad slots (default is 5%%) to show the author's ads. Use the option (in \"Support %s by Donating Ad Space\") below to change the value, or turn it off (by entering 0%%).", 'easy-adsenser'), 2.5, 'Easy AdSense', 'Easy AdSense') ;
?>
</div>

<div id="donate">
<?php _e('If you find yourself making a lot of money using this plugin, consider making a donation.', 'easy-adsener'); ?>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick"/>
<center><input type="image" src="https://www.paypal.com/en_US/i/btn/btn_donate_SM.gif" name="submit" alt="Support my plugin efforts"/></center>
<img alt="" border="0" src="https://www.paypal.com/en_US/i/scr/pixel.gif" width="1" height="1"/>
<input type="hidden" name="encrypted" value="-----BEGIN PKCS7-----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-----END PKCS7-----
"/></form>
</div>
</td>
<?php } ?>

<td width="30%">
<div style="background-color:#ccffff;padding:15px;border: solid 1px">
<center>
<big style="color:#a48;font-variant: small-caps;text-decoration:underline" onmouseover="TagToTip('unreal', WIDTH, 230, TITLE, 'Buy &lt;em&gt;The Unreal Universe&lt;/em&gt;',STICKY, 1, CLOSEBTN, true, CLICKCLOSE, true, FIX, [this, 5, 5])"><b>The Unreal Universe</b></big><br />
A Remarkable Book on<br />Physics and Philosophy<br />
</center>
</div>

<div id="unreal">
<center>
Of Life, Universe and Everything<br />
<big><b><em>The Unreal Universe</em></b></big><br />
Only $9.95 for the eBook version!
</center>
<form action="https://www.paypal.com/cgi-bin/webscr" method="post">
<input type="hidden" name="cmd" value="_s-xclick"/>
<input type="hidden" name="hosted_button_id" value="1216642"/>
<center><input type="image" src="/img/btn_buynow_SM.gif"  name="submit" title="The Unreal Universe eBook for only $9.95!" alt="[PayPal]"/></center>
<img alt="" border="0" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1"/>
</form>
<table border="0" cellpadding="0" cellspacing="0" summary="" width="100%">
<tr>
<td colspan="2" align="center"><a href="http://www.thulasidas.com/about/about-my-book" target="_blank">Want to know more?</a></td></tr><tr><td>
<small>
Pages: 292<br />
(282 in eBook)<br />
Trimsize: 6" x 9" <br />
Illustrations: 34<br />
(9 in color in eBook)<br />
Tables: 8 <br />
Bibliography: Yes<br />
Index: Yes<br />
ISBN: 9789810575946&nbsp;&nbsp;&nbsp;
</small>
</td>
<td align="right"><a href="http://www.thulasidas.com/buy"><img class="alignright" src="http://www.thulasidas.com/img/unreal.gif" alt="TheUnrealUniverse" title="Check out the beuatifully printed paperback!" /></a></td>
</tr>
</table>
</div>

<?php echo '</td>' ; ?>
