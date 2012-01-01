<table class="form-table"><tbody>
<tr><th scope="row"><?php _e("Memory Limits", "gd-press-tools"); ?></th>
    <td>
        <table cellpadding="0" cellspacing="0" class="previewtable">
            <tr>
                <td width="400">
                <input type="checkbox" name="php_memory_limit_enabled" id="php_memory_limit_enabled"<?php if ($options["php_memory_limit_enabled"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="php_memory_limit_enabled"><?php _e("Memory allocated by PHP", "gd-press-tools"); ?>:</label></td>
                <td>
                <input type="text" name="php_memory_limit" id="php_memory_limit" value="<?php echo $options["php_memory_limit"]; ?>" style="width: 100px; text-align: right" />
                (php.ini: <strong>memory_limit</strong> [<?php echo ini_get('memory_limit'); ?>])
                </td>
            </tr>
        </table>
        <div class="gdsr-table-split"></div>
        <?php _e("Modifying these settings can have negative impact on your server and website performance. If you are not sure what you doing, don't change these values.", "gd-press-tools"); ?>
    </td>
</tr>
</tbody></table>