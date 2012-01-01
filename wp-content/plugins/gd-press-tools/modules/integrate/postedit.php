<?php $disabled = $meta_robots["active"] == 1 ? ' disabled="disabled"' : ''; ?>
<div class="gdpt-widget">
    <input type="hidden" id="gdpt_post_edit" name="gdpt_post_edit" value="edit" />
    <input type="hidden" id="gdpt_post_type" name="gdpt_post_type" value="<?php echo $post->post_type; ?>" />
    <?php if ($this->o["shorturl_active"] == 1) { ?>
    <h4 class="gdpt-section-title"><?php _e("Short URL", "gd-press-tools"); ?>:</h4>
    <input style="width: 99%; margin-bottom: 3px;" type="text" value="<?php echo $this->get_shortlink($post->ID); ?>" readonly />
    <?php } ?>
    <h4 class="gdpt-section-title"><?php _e("Robots Meta Tag", "gd-press-tools"); ?>:</h4>
    <input onchange="gdpt_postedit_disabling(this, 'gdpt-table-robots')" type="checkbox" class="gdpt_check" name="gdpt_meta_robots" id="gdpt_meta_robots"<?php if ($meta_robots["active"] == 1) echo " checked"; ?> /><label style="margin-left: 5px; font-size: 12px;" for="gdpt_meta_robots"><?php _e("Use global meta tag settings", "gd-press-tools"); ?></label>
    <div class="gdpt-table-split-edit"></div>
    <div class="gdpt-table-robots<?php echo $meta_robots["active"] == 1 ? " gdpt-disabled" : ""; ?>">
        <table width="100%" style="">
            <tr>
                <td>
                    <label style="font-size: 12px;" for="gdsr_review"><?php _e("Standard", "gd-press-tools"); ?>:</label>
                </td>
                <td align="right" valign="baseline">
                    <?php GDPTDB::render_meta_robots("gdpt_meta_robots_standard", isset($meta_robots["standard"]) ? $meta_robots["standard"] : "", $disabled); ?>
                </td>
            </tr>
            <tr>
                <td>
                    <input<?php echo $disabled; ?> type="checkbox" class="gdpt_check" name="gdpt_meta_robots_extra[noydir]" id="gdpt_meta_robots_noydir"<?php if (isset($meta_robots["noydir"]) && $meta_robots["noydir"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="gdpt_meta_robots_noydir"><?php _e("No Yahoo", "gd-press-tools"); ?></label>
                </td>
                <td align="right" valign="baseline">
                    <label style="margin-right: 5px;" for="gdpt_meta_robots_noarchive"><?php _e("No Archive", "gd-press-tools"); ?></label><input<?php echo $disabled; ?> type="checkbox" class="gdpt_check" name="gdpt_meta_robots_extra[noarchive]" id="gdpt_meta_robots_noarchive"<?php if (isset($meta_robots["noarchive"]) && $meta_robots["noarchive"] == 1) echo " checked"; ?> />
                </td>
            </tr>
            <tr>
                <td>
                    <input<?php echo $disabled; ?> type="checkbox" class="gdpt_check" name="gdpt_meta_robots_extra[noodp]" id="gdpt_meta_robots_noodp"<?php if (isset($meta_robots["noodp"]) && $meta_robots["noodp"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="gdpt_meta_robots_noodp"><?php _e("No ODP", "gd-press-tools"); ?></label>
                </td>
                <td align="right" valign="baseline">
                    <label style="margin-right: 5px;" for="gdpt_meta_robots_nosnippet"><?php _e("No Snippet", "gd-press-tools"); ?></label><input<?php echo $disabled; ?> type="checkbox" class="gdpt_check" name="gdpt_meta_robots_extra[nosnippet]" id="gdpt_meta_robots_nosnippet"<?php if (isset($meta_robots["nosnippet"]) && $meta_robots["nosnippet"] == 1) echo " checked"; ?> />
                </td>
            </tr>
            <tr>
                <td>
                    <input<?php echo $disabled; ?> type="checkbox" class="gdpt_check" name="gdpt_meta_robots_extra[noimageindex]" id="gdpt_meta_robots_noimageindex"<?php if (isset($meta_robots["noimageindex"]) && $meta_robots["noimageindex"] == 1) echo " checked"; ?> /><label style="margin-left: 5px;" for="gdpt_meta_robots_noimageindex"><?php _e("No Image Index", "gd-press-tools"); ?></label>
                </td>
                <td align="right" valign="baseline">
                    <label style="margin-right: 5px;" for="gdpt_meta_robots_notranslate"><?php _e("No Translate", "gd-press-tools"); ?></label><input<?php echo $disabled; ?> type="checkbox" class="gdpt_check" name="gdpt_meta_robots_extra[notranslate]" id="gdpt_meta_robots_notranslate"<?php if (isset($meta_robots["notranslate"]) && $meta_robots["notranslate"] == 1) echo " checked"; ?> />
                </td>
            </tr>
        </table>
    </div>
</div>
