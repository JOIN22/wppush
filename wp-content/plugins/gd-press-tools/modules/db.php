<?php if ($status != "") : ?>
<div id="message" class="updated fade" style="background-color: rgb(255, 251, 204);"><p><strong><?php echo $status; ?></strong></p></div>
<?php endif;

global $wpdb;
$wp_tables = array(
    $wpdb->comments, $wpdb->links, $wpdb->options,
    $wpdb->postmeta, $wpdb->posts, $wpdb->terms,
    $wpdb->term_relationships, $wpdb->term_taxonomy,
    $wpdb->usermeta, $wpdb->users);

if (isset($wpdb->commentmeta)) $wp_tables[] = $wpdb->commentmeta;

?>

<script>
function confirmDrop() {
    return confirm("<?php _e("Are you sure that you want to DROP this table? Operation is not reversible.", "gd-press-tools"); ?>");
}

function confirmEmpty() {
    return confirm("<?php _e("Are you sure taht you want to EMPTY this table? Operation is not reversible.", "gd-press-tools"); ?>");
}
</script>
<div class="gdsr"><div class="wrap">
<h2 class="gdptlogopage">GD Press Tools: <?php _e("Database", "gd-press-tools"); ?></h2>
<?php gdpt_upgrade_notice(); ?>
<?php

$files = $backups = array();
if (is_dir(WP_CONTENT_DIR."/gdbackup/")) {
    $files = gdFunctionsGDPT::scan_dir(WP_CONTENT_DIR."/gdbackup/");
    foreach ($files as $fl) {
        if (substr($fl, 0, 10) == "db_backup_") {
            $backups[$fl] = WP_CONTENT_URL."/gdbackup/".$fl;
        }
    }
}

global $gdpt; $gdpt->backup_folder();
if (!is_dir(WP_CONTENT_DIR."/gdbackup/")) { ?>
<div id="gdnotice" class="gderror">
    <p><?php _e("For backup to work, plugin must be able to access backup folder. Plugin has tried to create folders needed and failed. Until you resolved this issue backup will not work.", "gd-press-tools"); ?>
    <?php _e("Either make <strong>wp-content</strong> folder writeable or create <strong>gdbackup</strong> folder in <strong>wp-content</strong> and make it writeable. Use 0755 chmod setting.", "gd-press-tools"); ?></p>
</div>
<?php }

$db_backup_active = is_dir(WP_CONTENT_DIR."/gdbackup/") && is_writable(WP_CONTENT_DIR."/gdbackup/");

?>

<div id="gdpt_tabs" class="gdpttabs">
<ul>
    <li><a href="#fragment-1"><span><?php _e("Tables", "gd-press-tools"); ?></span></a></li>
    <li><a href="#fragment-3"><span><?php _e("Backup", "gd-press-tools"); ?></span></a></li>
    <li><a href="#fragment-2"><span><?php _e("Tools", "gd-press-tools"); ?></span></a></li>
</ul>
<div style="clear: both"></div>
<div id="fragment-1">
<?php include PRESSTOOLS_PATH."modules/db/tables.php"; ?>
</div>
<div id="fragment-3">
<?php include PRESSTOOLS_PATH."modules/db/backup.php"; ?>
</div>
<div id="fragment-2">
<?php include PRESSTOOLS_PATH."modules/db/tools.php"; ?>
</div>
</div>

</div></div>