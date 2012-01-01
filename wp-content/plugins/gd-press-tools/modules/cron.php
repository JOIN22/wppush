<script>
function confirmUnsc() {
    return confirm("<?php _e("Are you sure that you want to do this?", "gd-press-tools"); ?>");
}
</script>
<div class="gdsr"><div class="wrap">
<h2 class="gdptlogopage">GD Press Tools: <?php _e("Cron Scheduler", "gd-press-tools"); ?></h2>
<?php gdpt_upgrade_notice(); ?>
<table class="form-table"><tbody>
<tr><th scope="row"><?php _e("Jobs", "gd-press-tools"); ?></th>
    <td>
        <table width="95%" cellpadding="0" cellspacing="0" class="previewtable">
            <thead>
                <tr>
                    <th><?php _e("Time", "gd-press-tools"); ?></th>
                    <th><?php _e("Function", "gd-press-tools"); ?></th>
                    <th style="width: 10%"><?php _e("Schedule", "gd-press-tools"); ?></th>
                    <th class="aright awidth"><?php _e("Interval", "gd-press-tools"); ?></th>
                    <th style="text-align: right"><?php _e("Options", "gd-press-tools"); ?></th>
                </tr>
            </thead>
            <tbody>
        <?php

            $tr_class = "";
            $time_slots = _get_cron_array();
            foreach ($time_slots as $key => $jobs) {
                foreach ($jobs as $job => $value) {
                    echo '<tr>';
                    echo '<td>'.date("r", $key).'</td>';
                    echo '<td><strong>'.$job.'</strong></td>';
                    $schedule = $value[key($value)];
                    echo '<td>'.(isset($schedule["schedule"]) ? $schedule["schedule"] : "").'</td>';
                    echo '<td class="aright">'.(isset($schedule["interval"]) ? $schedule["interval"] : "").'</td>';
                    echo '<td class="aright">';
                    echo '<a onclick="return confirmUnsc()" href="admin.php?page=gd-press-tools-cron&gda=unsevt&time='.$key.'&job='.$job.'&key='.key($value).'">'.__("unschedule", "gd-press-tools").'</a>';
                    echo ' | <a href="admin.php?page=gd-press-tools-cron&gda=runevt&job='.$job.'">'.__("run now", "gd-press-tools").'</a>';
                    echo '</td>';
                    echo '</tr>';
                    if ($tr_class == "")
                        $tr_class = "alternate ";
                    else
                        $tr_class = "";
                }
            }

        ?>
            </tbody>
        </table>
        <div class="gdsr-table-split"></div>
        <?php _e("Deleting cron jobs is not reccomended if you are not sure what exactly are these jobs used for. Usually, job removal is not reversible.", "gd-press-tools"); ?><br />
        <?php _e("In some cases plugins can leave cron jobs after plugin removal, and these jobs are safe to remove.", "gd-press-tools"); ?>
    </td>
</tr>
</tbody></table>
</div></div>
