<table class="form-table"><tbody>
<tr><th scope="row"><?php _e("REAL Capital P", "gd-press-tools"); ?></th>
    <td>
        <input type="checkbox" name="real_capital_p_filter" id="footer_stats"<?php if ($options["real_capital_p_filter"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="real_capital_p_filter"><?php _e("Add filter for proper correction of word `WordPress` in post subject and post and comment content.", "gd-press-tools"); ?></label>
        <div class="gdsr-table-split"></div>
        <?php _e("This new filter uses regular expressions to match only `WordPress` that will not break URL's. It also matches any case of the word to get valid `WordPress`.", "gd-press-tools"); ?>
    </td>
</tr>
<tr><th scope="row"><?php _e("Broken Capital P", "gd-press-tools"); ?></th>
    <td>
        <input type="checkbox" name="remove_capital_p_filter" id="footer_stats"<?php if ($options["remove_capital_p_filter"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="remove_capital_p_filter"><?php _e("Disable default filters used to capitalize letter `p` in `WordPress`.", "gd-press-tools"); ?> <strong>[<?php _e("WP 3.0.", "gd-press-tools"); ?>]</strong></label>
        <div class="gdsr-table-split"></div>
        <?php _e("Matt Mullenweg added a filter that capitalizes letter `p` in word `WordPress` in post and comment content and post subject. But this filter tends to break URL's and enforces editorial control over what you write.", "gd-press-tools"); ?>
    </td>
</tr>
</tbody></table>
