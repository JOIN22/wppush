<div class="gdsr"><div class="wrap">
<h2 class="gdptlogopage">GD Press Tools: <?php _e("Auto Tagger", "gd-press-tools"); ?></h2>
<?php gdpt_upgrade_notice(); ?>
<table class="form-table"><tbody>
<tr><th scope="row"><?php _e("Tagger", "gd-press-tools"); ?></th>
    <td>
        <form action="" method="post" onsubmit="return areYouSureSimple()">
        <?php _e("Parse posts and/or pages and search for new tags using Yahoo Term Extraction API.", "gd-press-tools"); ?><br />
        <?php _e("Operation will run as a scheduled job in the background because it will take a lot of time depending on the number of posts.", "gd-press-tools"); ?><br />
        <?php _e("After you start the process you don't need to be on this page, you can check back from time to time for the status.", "gd-press-tools"); ?><br />
        <div class="gdsr-table-split"></div>
        <table cellpadding="0" cellspacing="0" class="previewtable">
            <tr>
                <td width="250" height="25"><input type="checkbox" name="gdpt_tagger_post" id="gdpt_tagger_post" checked /><label style="margin-left: 5px;" for="gdpt_tagger_post"><?php _e("Search and tag posts.", "gd-press-tools"); ?></label></td>
                <td height="25"><input type="checkbox" name="gdpt_tagger_page" id="gdpt_tagger_page" checked /><label style="margin-left: 5px;" for="gdpt_tagger_page"><?php _e("Search and tag pages.", "gd-press-tools"); ?></label></td>
            </tr>
            <tr>
                <td width="250" height="25"><?php _e("Maximum number of tags to add", "gd-press-tools"); ?>:</td>
                <td height="25"><input style="text-align: right;" type="text" id="gdpt_tagger_limit" name="gdpt_tagger_limit" value="10" /></td>
            </tr>
            <tr>
                <td width="250" height="25"><?php _e("Post ID range to process", "gd-press-tools"); ?>:</td>
                <td height="25">
                    <input style="text-align: right; width: 60px;" type="text" id="gdpt_tagger_start" name="gdpt_tagger_start" value="0" />
                    &nbsp;<?php _e("to", "gd-press-tools"); ?>&nbsp;
                    <input style="text-align: right; width: 60px;" type="text" id="gdpt_tagger_end" name="gdpt_tagger_end" value="0" />
                </td>
            </tr>
            <tr>
                <td width="250" height="25"></td>
                <td height="25"><em><?php _e("Leave 0 to ignore range", "gd-press-tools"); ?></em></td>
            </tr>
        </table>
        <div class="gdsr-table-split"></div>
        <?php if (function_exists('curl_init')) { $disabled = isset($s["tagger"]) && ($s["tagger"]["status"] == "running" || $s["tagger"]["status"] == "scheduled"); ?>
        <input<?php if ($disabled) echo " disabled"; ?> type="submit" class="inputbutton <?php echo $disabled ? "inputdisabled" : "inputred"; ?>" value="<?php echo $disabled ? __("Running...", "gd-press-tools") : __("Run", "gd-press-tools"); ?>" name="gdpt_tagger_run" id="gdpt_tagger_run" />
        <?php } else { ?>
        <?php _e("Due to the complexity of the requests for results, this tool requires cURL.", "gd-press-tools"); ?>
        <?php } ?>
        </form>
    </td>
</tr>
<tr><th scope="row"><?php _e("Status", "gd-press-tools"); ?></th>
    <td>
        <?php 
            if (!isset($s["tagger"]) || $s["tagger"]["status"] == "idle") {
                _e("Auto tagger is currently inactive and not scheduled.", "gd-press-tools");
                if (isset($s["tagger"]["status"]) && $s["tagger"]["status"] == "idle") {
                    echo '<br />';
                    _e("Last tagging started at", "gd-press-tools");
                    echo ": <strong>".date("r", $s["tagger"]["started"])."</strong><br />";
                    _e("Last tagging ended at", "gd-press-tools");
                    echo ": <strong>".date("r", $s["tagger"]["ended"])."</strong><br />";
                    _e("Last post ID processed", "gd-press-tools");
                    echo ": <strong>".$s["tagger"]["last_id"]."</strong><br />";
                }
            } else {
                if ($s["tagger"]["status"] == "scheduled") {
                    _e("Auto tagger is scheduled to run. Process will start in 30 seconds.", "gd-press-tools");
                    echo '<br />';
                    _e("You can safely leave this page, process will run in the background. If you want to check the status later, open this page again.", "gd-press-tools");
                }
                if ($s["tagger"]["status"] == "running") {
                    _e("Auto tagger is currently running in the background.", "gd-press-tools");
                    echo '<br />';
                    _e("Data displayed bellow are not current. To get latest status of the process, refresh this page.", "gd-press-tools");
                    echo '<div class="gdsr-table-split"></div>';
                    _e("Maximum tags to add per post", "gd-press-tools");
                    echo ": <strong>".$s["tagger"]["limit"]."</strong><br />";
                    _e("Operation started at", "gd-press-tools");
                    echo ": <strong>".date("r", $s["tagger"]["started"])."</strong><br />";
                    _e("Total posts to process", "gd-press-tools"); 
                    echo ": <strong>".$s["tagger"]["total"]."</strong><br />";
                    _e("Processed posts so far", "gd-press-tools");
                    echo ": <strong>".$s["tagger"]["processed"]."</strong><br />";
                    _e("Last post ID processed", "gd-press-tools");
                    echo ": <strong>".$s["tagger"]["last_id"]."</strong><br />";
                    _e("Last post processed at", "gd-press-tools");
                    echo ": <strong>".date("r", $s["tagger"]["latest"])."</strong><br />";
                    _e("Number of tags retrieved", "gd-press-tools");
                    echo ": <strong>".$s["tagger"]["tags_found"]."</strong><br />";
                    ?>
                        <form action="" method="post" onsubmit="return areYouSureSimple()">
                            <input type="submit" class="inputbutton inputred" value="<?php _e("Abort", "gd-press-tools"); ?>" name="gdpt_tagger_stop" id="gdpt_tagger_stop" />
                        </form>
                    <?php
                }
            }
        ?>
    </td>
</tr>
<tr><th scope="row"><?php _e("Maintenance", "gd-press-tools"); ?></th>
    <td>
        <?php _e("In some rare cases, tagger cron job can be stuck during execution, preventing you to run another one.", "gd-press-tools"); ?><br/>
        <?php _e("In that case you can use this forced cleanup option, but only if the current process is stuck for longer than few hours.", "gd-press-tools"); ?>
        <div class="gdsr-table-split"></div>
        <form action="" method="post">
            <input type="submit" class="inputbutton inputred" value="<?php _e("Force Cleanup", "gd-press-tools"); ?>" name="gdpt_tagger_forced" id="gdpt_tagger_forced" />
        </form>
    </td>
</tr>
</tbody></table>
</div></div>
