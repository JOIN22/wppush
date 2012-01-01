<div class="wrap"><div class="gdsr">
    <div id="gdptlogo">
        <div class="gdpttitle">
            <div class="gdtitle">GD Press Tools</div>
            <div class="edition <?php echo $options["edition"]; ?>"></div>
            <span><?php echo $options["version"]; ?><?php echo $options["status"] == "Stable" ? "" : " ".$options["status"]; ?></span>
            <div class="clear"></div>
        </div>
        <h3><?php _e("a wordpress administration addon", "gd-press-tools"); ?></h3>
    </div>
<?php gdpt_upgrade_notice(); ?>
<table><tr><td valign="top">
<?php include PRESSTOOLS_PATH."modules/front/infos.php"; ?>
</td><td style="width: 20px"> </td><td valign="top">
<?php include PRESSTOOLS_PATH."modules/front/gdragon.php"; ?>
</td></tr></table>

</div></div>
