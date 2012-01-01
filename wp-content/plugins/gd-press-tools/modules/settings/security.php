<table class="form-table"><tbody>
<tr><th scope="row"><?php _e("Header", "gd-press-tools"); ?></th>
    <td>
        <input type="checkbox" name="remove_wp_version" id="remove_wp_version"<?php if ($options["remove_wp_version"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="integrate_dashboard"><?php _e("Remove WordPress version.", "gd-press-tools"); ?></label>
        <br />
        <input type="checkbox" name="remove_rds" id="remove_rds"<?php if ($options["remove_rds"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="integrate_dashboard"><?php _e("Remove Really Simple Discovery link from header.", "gd-press-tools"); ?></label>
        <br />
        <input type="checkbox" name="remove_wlw" id="remove_wlw"<?php if ($options["remove_wlw"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="integrate_dashboard"><?php _e("Remove Windows Live Writer link from header.", "gd-press-tools"); ?></label>
    </td>
</tr>
<tr><th scope="row"><?php _e("URL Request", "gd-press-tools"); ?></th>
    <td>
        <table cellpadding="0" cellspacing="0" class="previewtable">
            <tr>
                <td>
                <input type="checkbox" name="urlfilter_wpadmin_active" id="urlfilter_wpadmin_active"<?php if ($options["urlfilter_wpadmin_active"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="urlfilter_wpadmin_active"><?php _e("Use settings bellow to limit access to admin backend also.", "gd-press-tools"); ?></label></td>
                <td>
                </td>
            </tr>
        </table>
        <div class="gdsr-table-split"></div>
        <table cellpadding="0" cellspacing="0" class="previewtable">
            <tr>
                <td>
                <input type="checkbox" name="urlfilter_sqlqueries_active" id="urlfilter_sqlqueries_active"<?php if ($options["urlfilter_sqlqueries_active"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="urlfilter_sqlqueries_active"><?php _e("Limit access to website for URL requests containing SQL injection code.", "gd-press-tools"); ?></label></td>
                <td>
                </td>
            </tr>
            <tr>
                <td width="500">
                <input type="checkbox" name="urlfilter_requestlength_active" id="urlfilter_requestlength_active"<?php if ($options["urlfilter_requestlength_active"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="php_memory_limit_enabled"><?php _e("Limit access to website for URL requests longer than", "gd-press-tools"); ?></label></td>
                <td>
                <input type="text" name="urlfilter_requestlength_value" id="urlfilter_requestlength_value" value="<?php echo $options["urlfilter_requestlength_value"]; ?>" style="width: 100px; text-align: right" />
                <?php _e("characters", "gd-press-tools"); ?>.
                </td>
            </tr>
        </table>
        <div class="gdsr-table-split"></div>
        <?php _e("Setting low value for request length can cause problems if your website has long URL due to post names and other factors. Experiment until you find best value. Filter is not used for administrators.", "gd-press-tools"); ?>
    </td>
</tr>
<tr><th scope="row"><?php _e("Various", "gd-press-tools"); ?></th>
    <td>
        <input type="checkbox" name="remove_login_error" id="remove_login_error"<?php if ($options["remove_login_error"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="integrate_dashboard"><?php _e("Remove error messages from login screen.", "gd-press-tools"); ?></label>
        <br/>
        <input type="checkbox" name="auth_require_login" id="auth_require_login"<?php if ($options["auth_require_login"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="auth_require_login"><?php _e("Prevent website access if the user is not logged in.", "gd-press-tools"); ?></label>
    </td>
</tr>
<tr><th scope="row"><?php _e("Plugin Update", "gd-press-tools"); ?></th>
    <td>
        <input type="checkbox" name="update_report_usage" id="update_report_usage"<?php if ($options["update_report_usage"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="update_report_usage"><?php _e("Report basic usage data that will be used for statistical purposes only.", "gd-press-tools"); ?></label>
        <div class="gdsr-table-split"></div>
        <?php _e("This report will include your WordPress version and website URL. Report will be sent only when plugin needs to be updated.", "gd-press-tools"); ?>
    </td>
</tr>
</tbody></table>
