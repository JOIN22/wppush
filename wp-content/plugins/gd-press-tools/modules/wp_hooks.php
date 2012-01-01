<div class="gdsr"><div class="wrap">
<h2 class="gdptlogopage">GD Press Tools: <?php _e("WordPress Hooks", "gd-press-tools"); ?></h2>
<?php gdpt_upgrade_notice(); ?>
<?php

global $wp_filter;

$render = array();
foreach ($wp_filter as $tag => $p) {
    $hook_elements = array();
    foreach ($p as $priority => $functions) {
        foreach ($functions as $name => $props) {
            $el = array(
                "priority" => $priority,
                "arguments" => $props["accepted_args"]
            );
            if (is_array($props["function"])) {
                $el["object"] = is_object($props["function"][0]) ? get_class($props["function"][0]) : $props["function"][0];
                $el["function"] = $props["function"][1];
            } else $el["function"] = $props["function"];
            $hook_elements[] = $el;
        }
    }
    $render[$tag] = $hook_elements;
}

?>

<table class="widefat">
    <thead>
        <tr>
            <th scope="col" style=""><?php _e("Hook", "gd-press-tools"); ?></th>
            <th scope="col" style=""><?php _e("Object", "gd-press-tools"); ?></th>
            <th scope="col" style=""><?php _e("Function", "gd-press-tools"); ?></th>
            <th scope="col" style="width: 60px; text-align: right;"><?php _e("Priority", "gd-press-tools"); ?></th>
            <th scope="col" style="width: 60px; text-align: right;"><?php _e("Arguments", "gd-press-tools"); ?></th>
        </tr>
    </thead>
    <tbody>

<?php

    foreach ($render as $hook => $funcs) {
        $rowspan = count($funcs);
        $i = 0;
        foreach ($funcs as $f) {
?>

<tr id="hook-<?php echo $hook."-".$i; ?>" class="author-self status-publish" valign="top">
    <?php if ($i == 0) { ?>
    <td rowspan="<?php echo $rowspan; ?>"><strong style="color: #cc0000;"><?php echo $hook; ?></strong></td>
    <?php } ?>
    <td><?php echo isset($f["object"]) ? $f["object"] : "/"; ?></td>
    <td><?php echo $f["function"]; ?></td>
    <td style="text-align: right;"><?php echo $f["priority"]; ?></td>
    <td style="text-align: right;"><?php echo $f["arguments"]; ?></td>
</tr>

<?php

            $i++;
        }
    }


?>

    </tbody>
</table>

</div></div>
