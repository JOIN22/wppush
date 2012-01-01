<?php global $gdpt_robots_values, $gdpt_robots_insert; ?>
<form method="post" id="gdpt-meta">
<table cellpadding="0" cellspacing="0" class="previewtable robots" style="margin-bottom: 10px;">
    <thead>
        <tr>
            <th width="20%"></th>
            <th width="8%" class="active"><?php _e("active", "gd-press-tools"); ?></th>
            <?php foreach ($gdpt_robots_values as $value) echo '<th width="9%" class="'.$value.'">'.$value.'</th>'; ?>
        </tr>
    </thead>
    <tbody>
    <?php

    foreach ($gdpt_robots_insert as $row_name => $row_value) {
        echo '<tr class="row"><th>'.$row_value.':</th>';
        $i = 0;
        echo '<td class="active">';
        echo sprintf('<input%s class="gdpt-ch-active" type="checkbox" name="gdpt_meta_active[]" value="%s" />',
            isset($meta[$row_name]) && $meta[$row_name]["active"] == 1 ? ' checked' : '', $row_name);
        echo '</td>';
        foreach ($gdpt_robots_values as $value) {
            echo '<td class="'.$value.'">';
            echo sprintf('<input%s%s type="checkbox" id="gdptm-%s-%s" name="gdpt_meta[]" value="%s" />',
                isset($meta[$row_name]) && isset($meta[$row_name][$value]) && $meta[$row_name][$value] == 1 ? ' checked' : '',
                $i < 4 ? ' onchange="gdpt_robots_exclude(this)"' : '',
                $row_name, $value, $row_name."|".$value);
            echo '</td>';
        }
        echo '</tr>';
    }

    ?>
    </tbody>
</table>
<div style="float: left">
<input type="submit" class="inputbutton" value="<?php _e("Save", "gd-press-tools"); ?>" name="gdpt_saving_meta"/>
</div>
<div style="float: right">
<input type="submit" class="inputbutton" value="<?php _e("Load Defaults", "gd-press-tools"); ?>" name="gdpt_default_meta"/>
<input onclick="gdpt_clear_robots(document.getElementById('gdpt-meta'))" type="button" class="inputbutton" value="<?php _e("Clear", "gd-press-tools"); ?>" name="gdpt_clear_meta"/>
</div>
<div class="clearboth"></div>
</form>
