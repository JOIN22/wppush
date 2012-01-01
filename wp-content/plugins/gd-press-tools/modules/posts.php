<?php if ($status != "") : ?>
<div id="message" class="updated fade" style="background-color: rgb(255, 251, 204);"><p><strong><?php echo $status; ?></strong></p></div>
<?php endif; ?>

<div class="gdsr"><div class="wrap">
<h2 class="gdptlogopage">GD Press Tools: <?php _e("Posts", "gd-press-tools"); ?></h2>
<?php gdpt_upgrade_notice(); ?>
<table class="form-table"><tbody>
<tr><th scope="row"><?php _e("Comments", "gd-press-tools"); ?></th>
    <td>
        <form method="post">
        <?php _e("Close comments and/or pings on posts older than selected date.", "gd-press-tools"); ?><br />
        <input type="checkbox" name="gdpt_cmm_comments" id="gdpt_cmm_comments" /><label style="margin-left: 5px;" for="gdpt_cmm_comments"><?php _e("Close comments.", "gd-press-tools"); ?></label>
        <br />
        <input type="checkbox" name="gdpt_cmm_pings" id="gdpt_cmm_pings" /><label style="margin-left: 5px;" for="gdpt_cmm_pings"><?php _e("Close pings.", "gd-press-tools"); ?></label>
        <table cellpadding="0" cellspacing="0" class="previewtable">
            <tr>
                <td width="200" height="25"><?php _e("For posts published before", "gd-press-tools"); ?>:</td>
                <td height="25"><input type="text" id="gdpt_cmm_date" name="gdpt_cmm_date" value="" /></td>
            </tr>
        </table>
        <input type="submit" class="inputbutton" value="<?php _e("Close", "gd-press-tools"); ?>" name="gdpt_cmm_set" id="gdpt_cmm_set" />
        </form>
    </td>
</tr>
<tr><th scope="row"><?php _e("Revisions", "gd-press-tools"); ?></th>
    <td>
        <form method="post">
        <?php _e("Remove all revision for published posts and pages.", "gd-press-tools"); ?><br />
        <?php _e("Total number of revisions found", "gd-press-tools"); ?>: <strong><?php echo gd_count_revisions_total(); ?></strong><br />
        <input type="submit" class="inputbutton" value="<?php _e("Delete", "gd-press-tools"); ?>" name="gdpt_revisions_delete" id="gdpt_revisions_delete" />
        <div class="gdsr-table-split"></div>
        <?php _e("Last revisions removal", "gd-press-tools"); ?>: <strong><?php echo $options["tool_revisions_removed"]; ?></strong>
        </form>
    </td>
</tr>
<tr><th scope="row"><?php _e("Delete Posts", "gd-press-tools"); ?></th>
    <td>
        <form method="post" onsubmit="return areYouSure()">
        <?php _e("Delete all posts older than selected date. This will delete posts, post terms relationships, post meta data, comments for those posts and revisions.", "gd-press-tools"); ?><br />
        <table cellpadding="0" cellspacing="0" class="previewtable">
            <tr>
                <td width="200" height="25"><?php _e("Delete posts published before", "gd-press-tools"); ?>:</td>
                <td height="25"><input type="text" id="gdpt_delposts_date" name="gdpt_delposts_date" value="" /></td>
            </tr>
        </table>
        <input type="submit" class="inputbutton inputred" value="<?php _e("Delete", "gd-press-tools"); ?>" name="gdpt_posts_delete" id="gdpt_posts_delete" />
        <div class="gdsr-table-split"></div>
        <?php _e("Operation is not reversible. Backup your data before proceeding.", "gd-press-tools"); ?>
        </form>
    </td>
</tr>
</tbody></table>
</div></div>
