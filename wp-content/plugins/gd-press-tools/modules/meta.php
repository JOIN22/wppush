<?php if (isset($_GET['settings']) && $_GET['settings'] == "saved") : ?>
<div id="message" class="updated fade" style="background-color: rgb(255, 251, 204);"><p><strong><?php _e("Meta tags settings are saved.", "gd-press-tools"); ?></strong></p></div>
<?php endif; ?>

<div class="gdsr"><div class="wrap">
<h2 class="gdptlogopage">GD Press Tools: <?php _e("Meta Tags", "gd-press-tools"); ?></h2>
<?php gdpt_upgrade_notice(); ?>
<div id="gdpt_tabs" class="gdpttabs">
<ul>
    <li><a href="#fragment-1"><span><?php _e("General", "gd-press-tools"); ?></span></a></li>
    <li><a href="#fragment-2"><span><?php _e("Robots", "gd-press-tools"); ?></span></a></li>
</ul>
<div class="clearboth"></div>
<div id="fragment-1">
<?php include PRESSTOOLS_PATH."modules/meta/general.php"; ?>
</div>
<div id="fragment-2">
<?php include PRESSTOOLS_PATH."modules/meta/robots.php"; ?>
</div>
</div>

</div></div>
