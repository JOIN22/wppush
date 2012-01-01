<table class="form-table"><tbody>
<tr><th scope="row"><?php _e("Admin Bar", "gd-press-tools"); ?></th>
    <td>
        <input type="checkbox" name="admin_bar_disable" id="admin_bar_disable"<?php if ($options["admin_bar_disable"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="admin_bar_disable"><?php _e("Disable Admin Bar.", "gd-press-tools"); ?> <strong>[<?php _e("WP 3.1 and higher.", "gd-press-tools"); ?>]</strong></label>
    </td>
</tr>
<tr><th scope="row"><?php _e("Grids Columns", "gd-press-tools"); ?></th>
    <td>
        <input type="checkbox" name="integrate_post_options" id="integrate_post_options"<?php if ($options["integrate_post_options"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="integrate_post_options"><?php _e("Add extra options for the post and page edit panel table.", "gd-press-tools"); ?></label>
        <div class="gdsr-table-split"></div>
        <input type="checkbox" name="integrate_post_id" id="integrate_post_id"<?php if ($options["integrate_post_id"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="integrate_post_id"><?php _e("Add ID column on the post and page edit panel table.", "gd-press-tools"); ?></label>
        <br />
        <input type="checkbox" name="integrate_post_sticky" id="integrate_post_sticky"<?php if ($options["integrate_post_sticky"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="integrate_post_sticky"><?php _e("Add sticky status column on the post and page edit panel table.", "gd-press-tools"); ?></label>
        <br />
        <input type="checkbox" name="integrate_post_views" id="integrate_post_views"<?php if ($options["integrate_post_views"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="integrate_post_views"><?php _e("Add views column on the post and page edit panel table.", "gd-press-tools"); ?></label>
        <br />
        <input type="checkbox" name="integrate_cat_id" id="integrate_cat_id"<?php if ($options["integrate_cat_id"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="integrate_cat_id"><?php _e("Add ID column on the post categories edit panel table.", "gd-press-tools"); ?> </label>
        <br />
        <input type="checkbox" name="integrate_tag_id" id="integrate_tag_id"<?php if ($options["integrate_tag_id"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="integrate_tag_id"><?php _e("Add ID column on the post tags edit panels tables.", "gd-press-tools"); ?> </label>
        <br />
        <input type="checkbox" name="integrate_comment_id" id="integrate_comment_id"<?php if ($options["integrate_comment_id"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="integrate_comment_id"><?php _e("Add ID column on the comments panel table.", "gd-press-tools"); ?></label>
        <div class="gdsr-table-split"></div>
        <input type="checkbox" name="integrate_media_id" id="integrate_media_id"<?php if ($options["integrate_media_id"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="integrate_post_id"><?php _e("Add ID column on the media edit panel table.", "gd-press-tools"); ?></label>
        <br />
        <input type="checkbox" name="integrate_links_id" id="integrate_links_id"<?php if ($options["integrate_links_id"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="integrate_post_id"><?php _e("Add ID column on the links edit panel table.", "gd-press-tools"); ?></label>
        <div class="gdsr-table-split"></div>
        <input type="checkbox" name="integrate_user_id" id="integrate_user_id"<?php if ($options["integrate_user_id"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="integrate_user_id"><?php _e("Add ID column on the users edit panel table.", "gd-press-tools"); ?> </label>
        <br />
        <input type="checkbox" name="integrate_user_comments" id="integrate_user_comments"<?php if ($options["integrate_user_comments"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="integrate_user_comments"><?php _e("Add column with comments count on the users edit panel table.", "gd-press-tools"); ?> </label>
        <br />
        <input type="checkbox" name="integrate_user_display" id="integrate_user_display"<?php if ($options["integrate_user_display"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="integrate_user_display"><?php _e("Add display name column on the users edit panel table.", "gd-press-tools"); ?> </label>
    </td>
</tr>
<tr><th scope="row"><?php _e("Descriptions", "gd-press-tools"); ?></th>
    <td>
        <?php _e("Allow saving HTML for tag, category and term descriptions, link description and notes and user description.", "gd-press-tools"); ?><br />
        <?php _e("By default WordPress strips all HTML elements from these fields.", "gd-press-tools"); ?>
        <div class="gdsr-table-split"></div>
        <input type="checkbox" name="html_desc_terms" id="html_desc_terms"<?php if ($options["html_desc_terms"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="html_desc_terms"><?php _e("Allow HTML in description for category, tag or custom taxonomy term.", "gd-press-tools"); ?></label>
        <br />
        <input type="checkbox" name="html_desc_links" id="html_desc_links"<?php if ($options["html_desc_links"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="html_desc_terms"><?php _e("Allow HTML in description for links.", "gd-press-tools"); ?></label>
        <br />
        <input type="checkbox" name="html_note_links" id="html_note_links"<?php if ($options["html_note_links"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="html_note_links"><?php _e("Allow HTML in note for links.", "gd-press-tools"); ?></label>
        <br />
        <input type="checkbox" name="html_desc_users" id="html_desc_users"<?php if ($options["html_desc_users"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="html_desc_users"><?php _e("Allow HTML in biographical info for user.", "gd-press-tools"); ?></label>
    </td>
</tr>
</tbody></table>
