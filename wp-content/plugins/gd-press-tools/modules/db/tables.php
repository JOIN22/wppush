    <table class="widefat">
    <thead>
        <tr>
            <th scope="col" style="width: 16px;"></th>
            <th scope="col"><?php _e("Name", "gd-press-tools"); ?></th>
            <th scope="col"><?php _e("Status", "gd-press-tools"); ?></th>
            <th scope="col"><?php _e("Collation", "gd-press-tools"); ?></th>
            <th scope="col" class="awidth" style="text-align: right;"><?php _e("Records", "gd-press-tools"); ?></th>
            <th scope="col" class="awidth" style="text-align: right;"><?php _e("Size", "gd-press-tools"); ?></th>
            <th scope="col" class="awidth" style="text-align: right;"><?php _e("Overhead", "gd-press-tools"); ?></th>
            <th scope="col" style="text-align: right;"><?php _e("Options", "gd-press-tools"); ?></th>
        </tr>
    </thead>
    <tbody>
    <?php

    $tables = GDPTDB::get_tables_status();
    $col_id = 0;
    foreach ($tables as $t) {
        echo '<tr>';
        echo '<td style="padding: 0 4px;">';
        echo '<div class="gdptdbtoggle on" id="ct'.$col_id.'"><img src="'.PRESSTOOLS_URL.'gfx/blank.gif" height="16" width="16" /></div>';
        echo '</td>';
        echo '<td><strong>'.$t->Name.'</strong></td>';
        echo '<td>';
            $status = GDPTDB::check_table($t->Name);
            echo $status->Msg_type.": ".$status->Msg_text;
        echo '</td>';
        echo '<td>'.$t->Collation.'</td>';
        echo '<td style="text-align: right;">'.$t->Rows.'</td>';
        echo '<td style="text-align: right;">'.gdFunctionsGDPT::size_format($t->Data_length).'</td>';
        echo '<td style="text-align: right;">';
        if ($t->Data_free > 0) {
            echo '<strong style="color: red">';
            echo gdFunctionsGDPT::size_format($t->Data_free);
            if ($t->Data_free > 0) echo '</strong>';
        } else echo '/';
        echo '</td>';
        echo '<td style="text-align: right;">';
        if (!in_array(strtolower($t->Name), $wp_tables)) {
            echo '<a onclick="return confirmDrop()" href="admin.php?page=gd-press-tools-database&gda=tpldrp&name='.$t->Name.'">'.__("drop", "gd-press-tools").'</a> | <a onclick="return confirmEmpty()" href="admin.php?page=gd-press-tools-database&gda=tblemp&name='.$t->Name.'">'.__("empty", "gd-press-tools").'</a>';
        }
        echo '</td>';
        echo '</tr>';
        echo '<tr id="cl'.$col_id.'" style="display: none"><td></td><td colspan="6">';

        ?>

        <table width="100%" cellpadding="0" cellspacing="0" class="previewtable columnstable">
        <thead>
            <tr>
                <th><?php _e("Name", "gd-press-tools"); ?></th>
                <th><?php _e("Type", "gd-press-tools"); ?></th>
                <th class="awidth"><?php _e("Key", "gd-press-tools"); ?></th>
                <th><?php _e("Default", "gd-press-tools"); ?></th>
                <th><?php _e("Extra", "gd-press-tools"); ?></th>
            </tr>
        </thead>
        <tbody>
        <?php

        $columns = gd_get_database_table_columns($t->Name);
        foreach ($columns as $column) {
            echo '<tr>';
            echo '<td><strong>'.$column->Field.'</strong></td>';
            echo '<td>'.$column->Type.'</td>';
            echo '<td>'.$column->Key.'</td>';
            echo '<td>'.$column->Default.'</td>';
            echo '<td>'.$column->Extra.'</td>';
            echo '</tr>';
        }

        ?>
        </tbody>
        </table>
        <?php echo '</td></tr>'; $col_id++;
    }

?>
</tbody>
</table>
<div class="gdsr-table-split"></div>
<?php _e("Default WordPress tables can't be emptied or droped.", "gd-press-tools"); ?>
