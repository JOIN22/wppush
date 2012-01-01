<table class="form-table"><tbody>
<tr><th scope="row"><?php _e("Optimize Database", "gd-press-tools"); ?></th>
    <td>
        <form method="post">
        <?php _e("Optimize, remove overhead and repair all tables if needed.", "gd-press-tools"); ?><br />
        <input type="submit" class="inputbutton" value="<?php _e("Clean", "gd-press-tools"); ?>" name="gdpt_db_clean" id="gdpt_db_clean" />
        <div class="gdsr-table-split"></div>
        <?php _e("Overhead can have negative impact to WordPress performance, and is best to have database optimized from time to time.", "gd-press-tools"); ?>
        </form>
    </td>
</tr>
<tr><th scope="row"><?php _e("Delete Backups", "gd-press-tools"); ?></th>
    <td>
        <form method="post">
        <?php _e("Delete all database backup files from the backup folder.", "gd-press-tools"); ?><br />
        <input type="submit" class="inputbutton" value="<?php _e("Delete", "gd-press-tools"); ?>" name="gdpt_dbbackup_delete" id="gdpt_dbbackup_delete" />
        <div class="gdsr-table-split"></div>
        <?php _e("Currently available backup files", "gd-press-tools"); ?>: <strong><?php echo count($backups); ?></strong>
        </form>
    </td>
</tr>
</tbody></table>