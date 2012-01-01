<div class="gdsr"><div class="wrap">
<h2 class="gdptlogopage">GD Press Tools: <?php _e("Full Server Info", "gd-press-tools"); ?></h2>
<?php gdpt_upgrade_notice(); ?>
<div id="gdpt_tabs" class="gdpttabs">
<ul>
    <li><a href="#fragment-1"><span><?php _e("PHP Info", "gd-press-tools"); ?></span></a></li>
    <li><a href="#fragment-2"><span><?php _e("PHP Constants", "gd-press-tools"); ?></span></a></li>
    <li><a href="#fragment-3"><span><?php _e("mySQL Info", "gd-press-tools"); ?></span></a></li>
</ul>
<div style="clear: both"></div>
<div id="fragment-1" class="gdrgrid">
<?php

ob_start();
phpinfo();
$php_info = ob_get_contents();
ob_end_clean();

$php_info = strip_tags($php_info, '<table><tbody><tr><th><td><h1><h2>');
$ix = strpos($php_info, "</table>");
$php_info = substr($php_info, $ix + 8);
$php_info = str_replace("<table border=\"0\" cellpadding=\"3\" width=\"600\">\n<tr>", '<div class="table phpinfo"><table><tbody><tr class="first">', $php_info);
$php_info = str_replace('<table border="0" cellpadding="3" width="600">', '<div class="table phpinfo"><table><tbody>', $php_info);
$php_info = str_replace('</table>', '</tbody></table></div>', $php_info);
$php_info = str_replace('<tr class="h"><th>', '<tr class="first"><th class="first">', $php_info);
$php_info = str_replace('<td class="e">', '<td class="first b">', $php_info);
$php_info = str_replace('<td class="v">', '<td class="t">', $php_info);
$ix = strpos($php_info, "</div>");
$iy = strpos($php_info, "<h1>Configuration");
$php_info = substr($php_info, 0, $ix + 6).substr($php_info, $iy + 22);
$ix = strpos($php_info, "<h2>PHP License");
$php_info = substr($php_info, 0, $ix);
$php_info = str_replace('<h2>', '<h2 class="phpsection">', $php_info);
echo $php_info;

?>
</div>
<div id="fragment-2" class="gdrgrid">
    <div class="table">
        <table><tbody>
<?php

$constants = @get_defined_constants();
ksort($constants);
$first = true;
foreach ($constants as $key => $value) {
    echo sprintf('<tr%s><td class="first b">%s:</td><td class="t">%s</td></tr>',
        $first ? ' class="first"' : '', $key, $value
    );
    $first = false;
}

?>
        </tbody></table>
    </div>
</div>
<div id="fragment-3" class="gdrgrid">
    <div class="table">
        <table><tbody>
<?php

$mysql_info = GDPTDB::get_mysql_server_info();
$first = true;
foreach ($mysql_info as $info) {
    echo sprintf('<tr%s><td class="first b">%s:</td><td class="t">%s</td></tr>',
        $first ? ' class="first"' : '', $info->Variable_name, htmlspecialchars($info->Value)
    );
    $first = false;
}

?>
        </tbody></table>
    </div>
</div>
</div>

</div></div>
