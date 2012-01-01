<?php global $userdata; ?>

<?php if ($status != "") : ?>
<div id="message" class="updated fade" style="background-color: rgb(255, 251, 204);"><p><strong><?php echo $status; ?></strong></p></div>
<?php endif; ?>

<div class="gdsr"><div class="wrap">
<h2 class="gdptlogopage">GD Press Tools: <?php _e("Administration", "gd-press-tools"); ?></h2>
<?php gdpt_upgrade_notice(); ?>
<table class="form-table"><tbody>
<tr><th scope="row"><?php _e("Username", "gd-press-tools"); ?></th>
    <td>
        <form method="post">
        <?php _e("Change the username for your account. If the change is successful, you will be logged out and you need to login back using new username.", "gd-press-tools"); ?><br />
        <input type="text" name="gdpt_admin_username" value="<?php echo $userdata->user_login; ?>" />
        <input type="submit" class="inputbutton" value="<?php _e("Change", "gd-press-tools"); ?>" name="gdpt_admin_rename" id="gdpt_admin_rename" />
        <div class="gdsr-table-split"></div>
        <?php _e("Plugin will change username value in following tables", "gd-press-tools"); ?>:
        <strong>users</strong> <?php _e("and", "gd-press-tools"); ?> <strong>comments</strong>
        </form>
    </td>
</tr>
<tr><th scope="row"><?php _e("Cached RSS Feeds", "gd-press-tools"); ?></th>
    <td>
        <form method="post">
        <?php _e("Delete all RSS cached content.", "gd-press-tools"); ?><br />
        <input type="submit" class="inputbutton" value="<?php _e("Delete", "gd-press-tools"); ?>" name="gdpt_admin_rss_cache_reset" id="gdpt_admin_rss_cache_reset" />
        <div class="gdsr-table-split"></div>
        <?php _e("RSS feeds are stored into options database table. Number of feeds and their data can grow out of control, so it's safe to clean it from time to time.", "gd-press-tools"); ?><br />
        </form>
    </td>
</tr>
<tr><th scope="row"><?php _e("Reset Widgets", "gd-press-tools"); ?></th>
    <td>
        <form method="post">
        <?php _e("Reset all sidebars, and remove all widgets from them.", "gd-press-tools"); ?><br />
        <input type="submit" class="inputbutton" value="<?php _e("Reset", "gd-press-tools"); ?>" name="gdpt_admin_widget_reset" id="gdpt_admin_widget_reset" />
        <div class="gdsr-table-split"></div>
        <?php _e("Use this if you change themes with different number of sidebars and single instance widgets.", "gd-press-tools"); ?><br />
        </form>
    </td>
</tr>
<tr><th scope="row"><?php _e("Folder Protection", "gd-press-tools"); ?></th>
    <td>
        <form method="post">
        <?php echo sprintf(__("Add %s file with redirection to a home page as a mean of protection against direct access to some folders. File will be added to following folders if needed:", "gd-press-tools"), "<strong>index</strong>"); ?><br />
        <strong>wp-content, wp-content/plugins, wp-content/themes</strong><br />
        <input type="submit" class="inputbutton" value="<?php _e("Protect", "gd-press-tools"); ?>" name="gdpt_admin_folder_protect" id="gdpt_admin_folder_protect" />
        <div class="gdsr-table-split"></div>
        <?php _e("For this to work all these folders must be writable so the plugin can create new files.", "gd-press-tools"); ?><br />
        </form>
    </td>
</tr>
<tr><th scope="row"><?php _e("Rescan Avatars", "gd-press-tools"); ?></th>
    <td>
        <form method="post">
        <?php _e("Scan avatars folder to find new images.", "gd-press-tools"); ?>
        <?php _e("Avatars folder:", "gd-press-tools"); ?> <strong>wp-content/avatars</strong><br />
        <input type="submit" class="inputbutton" value="<?php _e("Rescan", "gd-press-tools"); ?>" name="gdpt_admin_avatar_scan" id="gdpt_admin_avatar_scan" />
        <div class="gdsr-table-split"></div>
        <?php _e("Use this if you added new avatar images. Plugin is performing auto scan when new version is installed.", "gd-press-tools"); ?><br />
        </form>
    </td>
</tr>
</tbody></table>
</div></div>
