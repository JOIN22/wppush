<table class="form-table"><tbody>
<tr><th scope="row"><?php _e("Posts &amp; Pages Views", "gd-press-tools"); ?></th>
    <td>
        <input type="checkbox" name="posts_views_tracking" id="posts_views_tracking"<?php if ($options["posts_views_tracking"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="posts_views_tracking"><?php _e("Posts and Pages view tracking active.", "gd-press-tools"); ?></label>
        <div class="gdsr-table-split"></div>
        <table cellpadding="0" cellspacing="0" class="previewtable">
            <tr>
                <td width="200" valign="top">
                    <input type="checkbox" name="posts_views_tracking_posts" id="posts_views_tracking_posts"<?php if ($options["posts_views_tracking_posts"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="posts_views_tracking_posts"><?php _e("Track posts.", "gd-press-tools"); ?></label>
                </td>
                <td width="10"></td>
                <td valign="top">
                    <input type="checkbox" name="posts_views_tracking_pages" id="posts_views_tracking_pages"<?php if ($options["posts_views_tracking_pages"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="posts_views_tracking_pages"><?php _e("Track pages.", "gd-press-tools"); ?></label>
                </td>
            </tr>
            <tr>
                <td width="200" valign="top">
                    <input type="checkbox" name="posts_views_tracking_users" id="posts_views_tracking_users"<?php if ($options["posts_views_tracking_users"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="posts_views_tracking_users"><?php _e("Track registered users.", "gd-press-tools"); ?></label>
                </td>
                <td width="10"></td>
                <td valign="top">
                    <input type="checkbox" name="posts_views_tracking_visitors" id="posts_views_tracking_visitors"<?php if ($options["posts_views_tracking_visitors"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="posts_views_tracking_visitors"><?php _e("Track visitors.", "gd-press-tools"); ?></label>
                </td>
            </tr>
        </table>
        <div class="gdsr-table-split"></div>
        <table cellpadding="0" cellspacing="0" class="previewtable">
            <tr>
                <td width="200" valign="top">
                    <?php _e("Ignore Users", "gd-press-tools"); ?>:
                </td>
                <td width="10"></td>
                <td valign="top">
                    <input style="width: 300px;" type="text" name="posts_views_tracking_ignore" value="<?php echo $options["posts_views_tracking_ignore"]; ?>" /><br />
                    <span style="font-size: 9px; font-style: italic;"><?php _e("list comma separated users id's", "gd-press-tools") ?></span>
                </td>
            </tr>
        </table>
    </td>
</tr>
<tr><th scope="row"><?php _e("Users Browsing", "gd-press-tools"); ?></th>
    <td>
        <input type="checkbox" name="users_tracking" id="users_tracking"<?php if ($options["users_tracking"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="users_tracking"><?php _e("Track posts and pages registered users are visiting.", "gd-press-tools"); ?></label>
        <div class="gdsr-table-split"></div>
        <table cellpadding="0" cellspacing="0" class="previewtable">
            <tr>
                <td width="200" valign="top">
                    <input type="checkbox" name="users_tracking_posts" id="users_tracking_posts"<?php if ($options["users_tracking_posts"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="users_tracking_posts"><?php _e("Track posts.", "gd-press-tools"); ?></label>
                </td>
                <td width="10"></td>
                <td valign="top">
                    <input type="checkbox" name="users_tracking_pages" id="users_tracking_pages"<?php if ($options["users_tracking_pages"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="users_tracking_pages"><?php _e("Track pages.", "gd-press-tools"); ?></label>
                </td>
            </tr>
        </table>
        <div class="gdsr-table-split"></div>
        <table cellpadding="0" cellspacing="0" class="previewtable">
            <tr>
                <td width="200" valign="top">
                    <?php _e("Ignore Users", "gd-press-tools"); ?>:
                </td>
                <td width="10"></td>
                <td valign="top">
                    <input style="width: 300px;" type="text" name="users_tracking_ignore" value="<?php echo $options["users_tracking_ignore"]; ?>" /><br />
                    <span style="font-size: 9px; font-style: italic;"><?php _e("list comma separated users id's", "gd-press-tools") ?></span>
                </td>
            </tr>
        </table>
    </td>
</tr>
</tbody></table>
