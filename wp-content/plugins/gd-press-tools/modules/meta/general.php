<form method="post">
<table class="form-table"><tbody>
<tr><th scope="row"><?php _e("Default Robots Meta", "gd-press-tools"); ?></th>
    <td>
        <input type="checkbox" name="meta_wp_noindex" id="meta_wp_noindex"<?php if ($options["meta_wp_noindex"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="meta_wp_noindex"><?php _e("Try to disable standard Noindex meta tag added by WordPress.", "gd-press-tools"); ?></label>
        <div class="gdsr-table-split"></div>
        <?php _e("Enable this option if you want to use advanced robots meta tag generator.", "gd-press-tools"); ?>
    </td>
</tr>
<tr><th scope="row"><?php _e("Language Meta Tag", "gd-press-tools"); ?></th>
    <td>
        <input type="checkbox" name="meta_language_active" id="meta_language_active"<?php if ($options["meta_language_active"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="meta_language_active"><?php _e("Add content language meta tag site wide. You can specify more than one language for this tag.", "gd-press-tools"); ?></label>
        <table cellpadding="0" cellspacing="0" class="previewtable">
            <tr>
                <td width="200" valign="top">
                    <?php _e("List of languages to add", "gd-press-tools"); ?>:
                </td>
                <td width="10"></td>
                <td valign="top">
                    <input style="width: 300px;" type="text" name="meta_language_values" value="<?php echo $options["meta_language_values"]; ?>" /><br />
                    <span style="font-size: 9px; font-style: italic;"><?php _e("list comma separated 2 character codes", "gd-press-tools") ?></span>
                </td>
            </tr>
        </table>
        <div class="gdsr-table-split"></div>
        <?php _e("List of all supported 2 character language codes can be found here", "gd-press-tools"); ?>:
        <a href="http://www.loc.gov/standards/iso639-2/php/code_list.php" target="_blank">ISO 639-2: <?php _e("Codes for the Representation of Names of Languages", "gd-press-tools"); ?></a>
    </td>
</tr>
</tbody></table>
<input type="submit" class="inputbutton" value="<?php _e("Save Settings", "gd-press-tools"); ?>" name="gdpt_saving_meta_general"/>
</form>