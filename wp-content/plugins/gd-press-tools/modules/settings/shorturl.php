<?php $short_url = '<em>'.trailingslashit(get_option("home")).'<strong>prefix</strong>123</em>'; ?>
<table class="form-table"><tbody>
<tr><th scope="row"><?php _e("Shortening URL's", "gd-press-tools"); ?></th>
    <td>
        <input type="checkbox" name="shorturl_active" id="shorturl_active"<?php if ($options["shorturl_active"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="shorturl_active"><?php _e("Activate shortening URL's service.", "gd-press-tools"); ?></label>
        <div class="gdsr-table-split"></div>
        <?php echo sprintf(__("For this to work, you must use permalinks feature build in the Wordpress.", "gd-press-tools"), "<strong>PRESSTOOLS_DEBUG_ACTIVE</strong>", "<strong>gd-press-config.php</strong>", "<strong>true</strong>"); ?>
    </td>
</tr>
<tr><th scope="row"><?php _e("URL format", "gd-press-tools"); ?></th>
    <td>
        <table cellpadding="0" cellspacing="0" class="previewtable">
            <tr>
                <td width="200" valign="top">
                    <?php _e("Prefix for creating URL", "gd-press-tools"); ?>:
                </td>
                <td valign="top">
                    <input style="width: 200px;" type="text" name="shorturl_prefix" value="<?php echo $options["shorturl_prefix"]; ?>" />
                     [ <?php _e("only letters are allowed.", "gd-press-tools"); ?> ]<br />
                    <?php _e("URL will look like this", "gd-press-tools"); ?>: <?php echo $short_url; ?>
                </td>
            </tr>
        </table>
    </td>
</tr>
</tbody></table>
