<table class="form-table"><tbody>
<tr><th scope="row"><?php _e("Access", "gd-press-tools"); ?></th>
    <td>
        <?php _e("Control what panels will be accessable by the individual blog users.", "gd-press-tools"); ?>
        <div class="gdsr-table-split"></div>
        <input type="checkbox" name="access_server" id="access_server"<?php if ($global["access_server"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="access_server"><?php _e("Environment Info", "gd-press-tools"); ?></label>
        <br />
        <input type="checkbox" name="access_hooks" id="access_hooks"<?php if ($global["access_hooks"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="access_hooks"><?php _e("WP Hooks", "gd-press-tools"); ?></label>
        <br />
        <input type="checkbox" name="access_database" id="access_database"<?php if ($global["access_database"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="access_database"><?php _e("Database", "gd-press-tools"); ?></label>
        <br />
        <input type="checkbox" name="access_cron" id="access_cron"<?php if ($global["access_cron"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="access_cron"><?php _e("Cron Scheduler", "gd-press-tools"); ?></label>
    </td>
</tr>
</tbody></table>
