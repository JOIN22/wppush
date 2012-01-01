<script lang="javascript">
function setbg(id, color) {
	document.getElementById(id).style.background=color;	
}


function AskConfirm() {

	return confirm ('Updates will be applied immediately! Press OK to Apply changes now.')
}

</script>

<div class="wrap" style="width:900px">
	
	<form name="optionsForm" method="post" action="<?php echo $_SERVER["REQUEST_URI"]; ?>">

	<h2>Easy Privacy Policy Setup v1.02</h2>
	
	<div style="border:2px solid #888888;margin-bottom:10px;background-color:#f1f1ff;padding:2px;">
	
		<strong style="margin-left:5px;">Instructions:</strong>
		<ul style="list-style:circle;margin-left:25px;">
			<li>Activate each section individually below to include them in your Policy. The checkboxes are not applied until you use the <strong>Update</strong> button.</li>
			<li>Use the <strong>Update</strong> buttons to apply changes to you privacy page <i>Immediately</i>.</li>
			<li>Use the keywords <strong>@blogname</strong> and <strong>@email</strong> for substitution within the privacy text. You can use them as many times as you like.</li>
			<li>The Page Url is the url that your Privacy Page will be set to, change this to make it more SEO friendly for your site.</li>
			<li>Click the <i>titles</i> and <i>text blocks</i> below to edit them, when you are finished editing press the <strong>Update</strong> button to apply changes.</li>
			<li>Urls of the format http://www.somesite.com will be automatically converted into a url link when published.</li>
		</ul>
		<div>
	&nbsp;
<a href = "https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=GU9E9ABXB2D5S" title="Please Donate to Support Easy Privacy Policy">
	<image src="https://www.paypal.com/en_US/GB/i/btn/btn_donateCC_LG.gif" border="0" alt="PayPal - The safer, easier way to pay online."></image>
</a>	
	<span style="font-size:10px;">&nbsp;&nbsp;&nbsp;Please Donate To Support Easy Privacy Policy * Many Thanks *</span>
		</div>	
	</div>
	
	<?php 

		if( get_option("easy_privacy_CreatePageId") == false) {
?>		
			<div class="updated">
				<table>
					<td>
						<div class="submit">
							<input type="submit" name="create_page" value="Create Privacy Page &raquo;" /> 
						</div>
					</td>
					<td>
						To start using <strong>Easy Privacy Policy</strong>, click the <i>Create Privacy Page</i> button. An initial page will be generated
						from the default settings laid out below ( you can edit these settings before creating the page if you like. )
					</td>
				</table>													
			</div>
			<br/>
<?php
		}	
	?>
	
	<table width="100%" border="0" cellspacing="0" cellpadding="6" style="border:1px solid #ddd;padding:7px;background-color:#f0f0f0;">
		<tr>
			<td align="right">@blogname&nbsp;</td>
			<td align="left" valign="middle">
				<input onfocus="setbg('easy_privacy_sitename','#d9ffd9');" onblur="setbg('easy_privacy_sitename','white');" class="edit" 
					name="easy_privacy_sitename" type="text" size="35" id="easy_privacy_sitename"
					value="<?php echo htmlspecialchars( get_option( 'easy_privacy_sitename' ) ); ?>" />
			</td>
			<td align="right">@email&nbsp;</td>
			<td align="left" valign="middle">
				<input onfocus="setbg('easy_privacy_email','#d9ffd9');" onblur="setbg('easy_privacy_email','white');" name="easy_privacy_email"
						type="text" size="35" id="easy_privacy_email"
						value="<?php echo htmlspecialchars( get_option( 'easy_privacy_email' ) ); ?>" />
		  </td>
		</tr>
		<tr>
			<td align="right">Page Title&nbsp;</td>
			<td align="left" valign="middle">
				<input onfocus="setbg('easy_privacy_pagetitle','#d9ffd9');" onblur="setbg('easy_privacy_pagetitle','white');" name="easy_privacy_pagetitle"
					type="text" size="35" id="easy_privacy_pagetitle"
					value="<?php echo htmlspecialchars( get_option( 'easy_privacy_pagetitle' ) ); ?>" />
			</td>
			<td align="right">Page Url&nbsp;</td>
			<td align="left" valign="middle">
				<input onfocus="setbg('easy_privacy_pageurl','#d9ffd9');" onblur="setbg('easy_privacy_pageurl','white');" name="easy_privacy_pageurl"
						type="text" size="35" id="easy_privacy_pageurl"
					value="<?php echo htmlspecialchars( get_option( 'easy_privacy_pageurl' ) ); ?>" />
		  </td>
		</tr>
		<tr><td colspan="4">&nbsp;</td></tr>
		<tr>			
			<td colspan="4">
				<table width="100%" border="0" cellspacing="6" cellpadding="6">
					<tr>
						<td width="140" valign="top"> 
							<input type="checkbox" name="easy_privacy_section_one_selected" id="easy_privacy_section_one_selected" 
								<?php echo ( get_option( 'easy_privacy_section_one_selected' ) == true ? "checked=\"checked\"" : "" ) ?>/> Section 1
							<div style="padding:6px;background-color:#b1b1b1;color:#fff;margin-top:2px;line-height:12px;font-size:9px;font-family:verdana,arial;">
								This is the basic privacy statement of intent that your blog supports. You will nearly always want to include this section.</div>
						</td>
						<td>
							<input style="font-weight:bold;" id="edit_title1" name="edit_title1" onfocus="setbg('edit_title1','#d9ffd9');" onblur="setbg('edit_title1','white');" value="<?php echo get_option( 'easy_privacy_section_one_title' ); ?>"></input></br>
							<textarea style="font-family: 'Lucida Grande', Verdana, Arial;font-size:11px;" id="edit_1" 
									  onfocus="setbg('edit_1','#d9ffd9');" onblur="setbg('edit_1','white');" rows="5" cols="105" name="edit_1"><?php echo get_option( 'easy_privacy_section_one' ); ?></textarea>
						</td>		
						<td valign="middle">
<?php 

						if( get_option("easy_privacy_CreatePageId") == true) {
?>					
							<div class="submit"><input type="submit" name="update_page" value="Update &raquo;" onclick="return AskConfirm()"/></div>
<?php 
						}
?>
						</td>	
					</tr>
					<tr>
						<td valign="top"> 
							<input type="checkbox" name="easy_privacy_section_two_selected" id="easy_privacy_section_two_selected" 
								<?php echo ( get_option( 'easy_privacy_section_two_selected' ) == true ? "checked=\"checked\"" : "" ) ?>/> Section 2
							<div style="padding:6px;background-color:#b1b1b1;color:#fff;margin-top:2px;line-height:12px;font-size:9px;font-family:verdana,arial;">
								If you are using Google Adsense you probably want to include this section or a variation of it. Not doing so will breach the current terms and conditions
								for adsense accounts.</div>
						</td>
						<td>
							<input size="50" type="text" style="font-weight:bold;" id="edit_title2" name="edit_title2" onfocus="setbg('edit_title2','#d9ffd9');" onblur="setbg('edit_title2','white');" value="<?php echo get_option( 'easy_privacy_section_two_title' ); ?>"></input></br>
							<textarea style="font-family: 'Lucida Grande', Verdana, Arial;font-size:11px;" 
									  onfocus="setbg('edit_2','#d9ffd9');" onblur="setbg('edit_2','white');" rows="14" cols="105" id="edit_2" name="edit_2"><?php echo get_option( 'easy_privacy_section_two' ); ?></textarea>
						</td>	
						<td valign="middle">
<?php 

						if( get_option("easy_privacy_CreatePageId") == true) {
?>					
							<div class="submit"><input type="submit" name="update_page" value="Update &raquo;" onclick="return AskConfirm()"/></div>
<?php 
						}
?>
						</td>		
					</tr>
					<tr>
						<td valign="top"> 
							<input type="checkbox" name="easy_privacy_section_three_selected" id="easy_privacy_section_three_selected" 
								<?php echo ( get_option( 'easy_privacy_section_three_selected' ) == true ? "checked=\"checked\"" : "" ) ?>/> Section 3
							<div style="padding:6px;background-color:#b1b1b1;color:#fff;margin-top:2px;line-height:12px;font-size:9px;font-family:verdana,arial;">
								Is your website or blog suitable for people under the age of 18? Does it contain adult content or themes that are not suitable for children ?</div>
						</td>
						
						<td>
							<input size="50" type="text" style="font-weight:bold;" id="edit_title3" name="edit_title3" onfocus="setbg('edit_title3','#d9ffd9');" onblur="setbg('edit_title3','white');" value="<?php echo get_option( 'easy_privacy_section_three_title' ); ?>"></input></br>
							<textarea style="font-family: 'Lucida Grande', Verdana, Arial;font-size:11px;"
								       onfocus="setbg('edit_3','#d9ffd9');" onblur="setbg('edit_3','white');" rows="5" cols="105" id="edit_3" name="edit_3"><?php echo get_option( 'easy_privacy_section_three' ); ?></textarea>						
						</td>	
						<td valign="middle">
<?php 

						if( get_option("easy_privacy_CreatePageId") == true) {
?>					
							<div class="submit"><input type="submit" name="update_page" value="Update &raquo;" onclick="return AskConfirm()"/></div>
<?php 
						}
?>
						</td>							
					</tr>
					<tr>
						<td valign="top"> 
							<input type="checkbox" name="easy_privacy_section_four_selected" id="easy_privacy_section_four_selected" 
								<?php echo ( get_option( 'easy_privacy_section_four_selected' ) == true ? "checked=\"checked\"" : "" ) ?>/> Section 4
							<div style="padding:6px;background-color:#b1b1b1;color:#fff;margin-top:2px;line-height:12px;font-size:9px;font-family:verdana,arial;">
								This section states what information your site might record, typically IP addresses in web logs for example, or perhaps Google Analytics.</div>
						</td>
						<td>
							<input size="50" type="text" style="font-weight:bold;" id="edit_title4" name="edit_title4" onfocus="setbg('edit_title4','#d9ffd9');" onblur="setbg('edit_title4','white');" value="<?php echo get_option( 'easy_privacy_section_four_title' ); ?>"></input></br>
							<textarea style="font-family: 'Lucida Grande', Verdana, Arial;font-size:11px;"
										onfocus="setbg('edit_4','#d9ffd9');" onblur="setbg('edit_4','white');" rows="5" cols="105" id="edit_4" name="edit_4"><?php echo get_option( 'easy_privacy_section_four' ); ?></textarea>
						</td>	
						<td valign="middle">
<?php 

						if( get_option("easy_privacy_CreatePageId") == true) {
?>					
							<div class="submit"><input type="submit" name="update_page" value="Update &raquo;" onclick="return AskConfirm()"/></div>
<?php 
						}
?>
						</td>			
					</tr>
					<tr>
						<td valign="top"> 
							<input type="checkbox" name="easy_privacy_section_five_selected" id="easy_privacy_section_five_selected" 
								<?php echo ( get_option( 'easy_privacy_section_five_selected' ) == true ? "checked=\"checked\"" : "" ) ?>/> Section 5
							<div style="padding:6px;background-color:#b1b1b1;color:#fff;margin-top:2px;line-height:12px;font-size:9px;font-family:verdana,arial;">
								If you blog links to other sites, this section mitigates  you against whatever content might be contained by third party websites.</div>
						</td>
						<td>
							<input size="50" type="text" style="font-weight:bold;" id="edit_title5" name="edit_title5" onfocus="setbg('edit_title5','#d9ffd9');" onblur="setbg('edit_title5','white');" value="<?php echo get_option( 'easy_privacy_section_five_title' ); ?>"></input></br>
							<textarea style="font-family: 'Lucida Grande', Verdana, Arial;font-size:11px;"
										onfocus="setbg('edit_5','#d9ffd9');" onblur="setbg('edit_5','white');" rows="5" cols="105" id="edit_5" name="edit_5"><?php echo get_option( 'easy_privacy_section_five' ); ?></textarea>
						</td>	
						<td valign="middle">
<?php 

						if( get_option("easy_privacy_CreatePageId") == true) {
?>					
							<div class="submit"><input type="submit" name="update_page" value="Update &raquo;" onclick="return AskConfirm()"/></div>
<?php 
						}
?>
						</td>			
					</tr>
					<tr>
						<td valign="top"> 
							<input type="checkbox" name="easy_privacy_section_six_selected" id="easy_privacy_section_six_selected" 
								<?php echo ( get_option( 'easy_privacy_section_six_selected' ) == true ? "checked=\"checked\"" : "" ) ?>/> Section 6
							<div style="padding:6px;background-color:#b1b1b1;color:#fff;margin-top:2px;line-height:12px;font-size:9px;font-family:verdana,arial;">
								Put any other information here, for example state that changes may be made to the policy at any time.</div>
						</td>
						<td>
							<input size="50" type="text" style="font-weight:bold;" id="edit_title6" name="edit_title6" onfocus="setbg('edit_title6','#d9ffd9');" onblur="setbg('edit_title6','white');" value="<?php echo get_option( 'easy_privacy_section_six_title' ); ?>"></input></br>
							<textarea style="font-family: 'Lucida Grande', Verdana, Arial;font-size:11px;"
										onfocus="setbg('edit_6','#d9ffd9');" onblur="setbg('edit_6','white');" rows="5" cols="105" id="edit_6" name="edit_6"><?php echo get_option( 'easy_privacy_section_six' ); ?></textarea>
						</td>	
						<td valign="middle">
<?php 

						if( get_option("easy_privacy_CreatePageId") == true) {
?>					
							<div class="submit"><input type="submit" name="update_page" value="Update &raquo;" onclick="return AskConfirm()"/></div>
<?php 
						}
?>
						</td>		
					</tr>
				</table>
			</td>
			
		</tr>
	</table>
	<br/>
	<?php echo ( get_option( 'easy_privacy_lastupated' )); ?>
	<hr/>
	<strong>Options</strong><br/>
	 <input type="checkbox" name="easy_privacy_lastupdated"
            id="easy_privacy_lastupdated"
            <?php echo ( get_option( 'easy_privacy_lastupdated' ) == true ? "checked=\"checked\"" : "" ) ?> />
			Include a last updated date stamp ?
	 <br/>
	 <input type="checkbox" name="easy_privacy_acknowlegement"
            id="easy_privacy_acknowlegement"
            <?php echo ( get_option( 'easy_privacy_acknowlegement' ) == true ? "checked=\"checked\"" : "" ) ?> />
			Include a credit link for the <a target="_blank" title="Easy Privacy Policy Homepage" href="http://europeancruiseadvisor.com/easy-privacy-policy">Easy Privacy Plugin</a> (thanks!)
	<div class="submit">
	 
<?php 

		if( get_option("easy_privacy_CreatePageId") == true) {
?>
		
			<input type="submit" name="update_page" value="Update Privacy Page &raquo;" onclick="return AskConfirm()"/>
			<input type="submit" name="reset" value="Reset Defaults" onclick="return confirm ('Are you sure you want to Reset to defaults ? This will reset your Privacy Policy page at the same time!')"/>
			<input type="submit" name="uninstall" value="Uninstall"/>
<?php

		}
?>			
	</div>

	</form>

</div>