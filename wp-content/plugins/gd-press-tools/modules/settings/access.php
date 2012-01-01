<table class="form-table"><tbody>
<tr><th scope="row"><?php _e("User Levels", "gd-press-tools"); ?></th>
    <td>
        <table cellpadding="0" cellspacing="0" class="previewtable">
            <tr>
                <td width="400"><?php _e("Main access level to plugin panels", "gd-press-tools"); ?>:</td>
                <td><input type="text" name="access_level_main" id="access_level_main" value="<?php echo $options["access_level_main"]; ?>" style="width: 100px; text-align: right" /></td>
            </tr>
            <tr>
                <td width="400"><?php _e("Access level for environment info panel", "gd-press-tools"); ?>:</td>
                <td><input type="text" name="access_level_info" id="access_level_info" value="<?php echo $options["access_level_info"]; ?>" style="width: 100px; text-align: right" /></td>
            </tr>
            <tr>
                <td width="400"><?php _e("Access level for plugin front panel", "gd-press-tools"); ?>:</td>
                <td><input type="text" name="access_level_front" id="access_level_front" value="<?php echo $options["access_level_front"]; ?>" style="width: 100px; text-align: right" /></td>
            </tr>
        </table>
        <div class="gdsr-table-split">
        <?php _e("Level must be between 1 and 10. Subscribers level 0 is not allowed.", "gd-press-tools"); ?></div>
    </td>
</tr>
</tbody></table>
