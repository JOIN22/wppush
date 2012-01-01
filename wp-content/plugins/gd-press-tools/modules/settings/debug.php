<table class="form-table"><tbody>
<tr><th scope="row"><?php _e("Log File", "gd-press-tools"); ?></th>
    <td>
        <input type="checkbox" name="debug_sql" id="debug_sql"<?php if ($options["debug_sql"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="debug_sql"><?php _e("Log SQL queries executed by the plugin.", "gd-press-tools"); ?></label>
        <div class="gdsr-table-split"></div>
        <?php echo sprintf(__("For logging to work %s in file %s must be set to %s.", "gd-press-tools"), "<strong>PRESSTOOLS_DEBUG_ACTIVE</strong>", "<strong>gd-press-config.php</strong>", "<strong>true</strong>"); ?><br />
        <?php echo sprintf(__("Debug data is saved into file set with %s variable in this config file. Default location is %s file in plugin folder, so this file needs to be writable.", "gd-press-tools"), "<strong>PRESSTOOLS_LOG_PATH</strong>", "<strong>debug.txt</strong>"); ?>
    </td>
</tr>
<tr><th scope="row"><?php _e("SQL Queries", "gd-press-tools"); ?></th>
    <td>
        <input type="checkbox" name="debug_queries_global" id="debug_queries_global"<?php if ($options["debug_queries_global"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="debug_queries_global"><?php _e("Enable logging of SQL queries. This option must be active to all options bellow to work.", "gd-press-tools"); ?></label>
        <div class="gdsr-table-split"></div>
        <table cellpadding="0" cellspacing="0" class="previewtable">
            <tr>
                <td width="440"><input type="checkbox" name="debug_queries_admin" id="debug_queries_admin"<?php if ($options["debug_queries_admin"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="debug_queries_admin"><?php _e("Add SQL queries at the end of the admin pages.", "gd-press-tools"); ?></label></td>
                <td><input type="text" name="debug_queries_admin_level" id="debug_queries_admin_level" value="<?php echo $options["debug_queries_admin_level"]; ?>" style="width: 50px; text-align: right;" /> [1 - 10]</td>
            </tr>
            <tr>
                <td width="440"><input type="checkbox" name="debug_queries_blog" id="debug_queries_blog"<?php if ($options["debug_queries_blog"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="debug_queries_blog"><?php _e("Add SQL queries at the end of the blog pages.", "gd-press-tools"); ?></label></td>
                <td><input type="text" name="debug_queries_blog_level" id="debug_queries_blog_level" value="<?php echo $options["debug_queries_blog_level"]; ?>" style="width: 50px; text-align: right;" /> [1 - 10]</td>
            </tr>
        </table>
        <div class="gdsr-table-split"></div>
        <?php _e("Set minimal user level to see all the executed queries by WordPress.", "gd-press-tools"); ?>
    </td>
</tr>
</tbody></table>
