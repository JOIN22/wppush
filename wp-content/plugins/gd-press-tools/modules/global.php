<?php if (isset($_GET['settings']) && $_GET['settings'] == "saved") { ?>
<div id="message" class="updated fade" style="background-color: rgb(255, 251, 204);"><p><strong><?php _e("Settings saved.", "gd-press-tools"); ?></strong></p></div>
<?php } ?>

<div class="gdsr"><div class="wrap">
<form method="post">
<h2 class="gdptlogopage">GD Press Tools: <?php _e("Global Settings", "gd-press-tools"); ?></h2>
<?php gdpt_upgrade_notice(); ?>
<div id="gdpt_tabs" class="gdpttabs">
<ul>
    <li><a href="#fragment-1"><span><?php _e("Plugin Panels", "gd-press-tools"); ?></span></a></li>
</ul>
<div style="clear: both"></div>
<div id="fragment-1">
<?php include PRESSTOOLS_PATH."modules/global/panels.php"; ?>
</div>
</div>
<input type="submit" class="inputbutton" value="<?php _e("Save Settings", "gd-press-tools"); ?>" name="gdpt_saving_global"/>
</form>
</div></div>