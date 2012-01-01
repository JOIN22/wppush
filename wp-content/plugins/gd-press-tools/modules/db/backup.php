<form action="" method="post">
<table class="form-table"><tbody>
<tr><th scope="row"><?php _e("Settings", "gd-press-tools"); ?></th>
    <td>
        <?php _e("Database backup file will be saved into this folder", "gd-press-tools"); ?>: <strong>/wp-content/gdbackup/</strong>
        <?php if (!$db_backup_active) echo '<br /><strong>'.__("Plugin can't access the backup folder. Check the folder access rights before proceeding.", "gd-press-tools").'</strong>'; ?>
        <div class="gdsr-table-split"></div>
        <input checked type="checkbox" name="backup_compressed" id="backup_compressed" /><label style="margin-left: 5px;" for="backup_compressed"><?php _e("Save database backup as GZIP archive.", "gd-press-tools"); ?></label>
        <br />
        <input checked type="checkbox" name="backup_drop_exists" id="backup_drop_exists" /><label style="margin-left: 5px;" for="backup_drop_exists"><?php _e("Add DROP IF EXISTS query for each table.", "gd-press-tools"); ?></label>
        <br />
        <input type="checkbox" name="backup_structure_only" id="backup_structure_only" /><label style="margin-left: 5px;" for="backup_structure_only"><?php _e("Export only tables structure, no data.", "gd-press-tools"); ?></label>
        <div class="gdsr-table-split"></div>
        <input<?php if (!$db_backup_active) echo " disabled"; ?> type="submit" class="inputbutton" value="<?php _e("Backup", "gd-press-tools"); ?>" name="gdpt_backup_run" id="gdpt_backup_run" />
    </td>
</tr>
<tr><th scope="row"><?php _e("Backups", "gd-press-tools"); ?></th>
    <td>
        <?php _e("This is the list of available backups for download", "gd-press-tools"); ?>:
        <div class="gdsr-table-split"></div>
        <?php

        if (count($backups) == 0) _e("No backup files found in backup folder.", "gd-press-tools");
        else {
            foreach ($backups as $name => $url) {
                echo sprintf('<a href="%s">%s</a><br/>', $url, $name);
            }
        }

        ?>
    </td>
</tr>
</tbody></table>
</form>