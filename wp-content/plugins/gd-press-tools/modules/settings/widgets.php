<table class="form-table"><tbody>
<tr><th scope="row"><?php _e("Dashboard", "gd-press-tools"); ?></th>
    <td>
        <table cellpadding="0" cellspacing="0" class="previewtable">
            <tr>
                <td width="440"><input type="checkbox" name="integrate_dashboard" id="integrate_dashboard"<?php if ($options["integrate_dashboard"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="integrate_dashboard"><?php _e("Widget with some additional statistical info.", "gd-press-tools"); ?></label></td>
                <td><input type="text" name="integrate_dashboard_level" id="integrate_dashboard_level" value="<?php echo $options["integrate_dashboard_level"]; ?>" style="width: 50px; text-align: right;" /> [1 - 10]</td>
            </tr>
        </table>
        <div class="gdsr-table-split"></div>
        <?php _e("Set minimal user level to see the widget for each widget individually.", "gd-press-tools"); ?>
    </td>
</tr>
<tr><th scope="row"><?php _e("Post Editor", "gd-press-tools"); ?></th>
    <td>
        <input type="checkbox" name="integrate_postedit_widget" id="integrate_postedit_widget"<?php if ($options["integrate_postedit_widget"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="integrate_postedit_widget"><?php _e("Add options widget to post and page edit panel.", "gd-press-tools"); ?></label>
        <br />
    </td>
</tr>
</tbody></table>
