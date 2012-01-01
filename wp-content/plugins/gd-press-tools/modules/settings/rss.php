<table class="form-table"><tbody>
<tr><th scope="row"><?php _e("RSS Feeds", "gd-press-tools"); ?></th>
    <td>
        <input type="checkbox" name="rss_disable" id="rss_disable"<?php if ($options["rss_disable"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="rss_disable"><?php _e("Disable all RSS feeds.", "gd-press-tools"); ?></label>
    </td>
</tr>
<tr><th scope="row"><?php _e("Delayed Posting", "gd-press-tools"); ?></th>
    <td>
        <table cellpadding="0" cellspacing="0" class="previewtable">
            <tr>
                <td width="400">
                <input type="checkbox" name="rss_delay_active" id="rss_delay_active"<?php if ($options["rss_delay_active"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="rss_delay_active"><?php _e("Delayed posting for RSS feeds", "gd-press-tools"); ?>:</label></td>
                <td>
                <input type="text" name="rss_delay_time" id="rss_delay_time" value="<?php echo $options["rss_delay_time"]; ?>" style="width: 100px; text-align: right" />
                (<?php _e("minutes", "gd-press-tools"); ?>)
                </td>
            </tr>
        </table>
        <div class="gdsr-table-split"></div>
        <?php _e("With this option active, when you publish new post this post will be part of the RSS feed after number of minutes specified.", "gd-press-tools"); ?> 
        <?php _e("This will give you enought time to discover potential errors in the post.", "gd-press-tools"); ?>
    </td>
</tr>
<tr><th scope="row"><?php _e("Expand RSS Items", "gd-press-tools"); ?></th>
    <td>
        <input type="checkbox" name="rss_header_enable" id="rss_header_enable"<?php if ($options["rss_header_enable"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="rss_header_enable"><?php _e("Insert content before RSS feed item", "gd-press-tools"); ?></label>:<br />
        <textarea style="width: 430px; height: 80px;" name="rss_header_contents"><?php echo wp_specialchars($options["rss_header_contents"]); ?></textarea>
        <div class="gdsr-table-split"></div>
        <input type="checkbox" name="rss_footer_enable" id="rss_footer_enable"<?php if ($options["rss_footer_enable"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="rss_footer_enable"><?php _e("Insert content after RSS feed item", "gd-press-tools"); ?></label>:<br />
        <textarea style="width: 430px; height: 80px;" name="rss_footer_contents"><?php echo wp_specialchars($options["rss_footer_contents"]); ?></textarea>
    </td>
</tr>
</tbody></table>
