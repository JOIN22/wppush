<div class="metabox-holder">
    <div id="gdpt_server" class="postbox gdrgrid frontleft">
        <h3 class="hndle"><span><?php _e("Plugin Quick Facts", "gd-press-tools"); ?></span></h3>
        <div class="inside">
            <div class="table">
                <table><tbody>
                    <tr class="first">
                        <td class="first b"><?php _e("Deleted revisions", "gd-press-tools"); ?>:</td>
                        <td class="t options"><?php echo $options["counter_total_revisions"]; ?></td>
                    </tr>
                    <tr>
                        <td class="first b"><?php _e("Saved on database cleanup", "gd-press-tools"); ?>:</td>
                        <td class="t options"><?php echo gdFunctionsGDPT::size_format($options["counter_total_overhead"]); ?></td>
                    </tr>
                </tbody></table>
            </div>
        </div>
    </div>
    <div id="gdpt_server" class="postbox gdrgrid frontleft">
        <h3 class="hndle"><span><?php _e("Server Status", "gd-press-tools"); ?></span></h3>
        <div class="inside">
            <div class="table">
                <table><tbody>
                    <tr class="first">
                        <td class="first b"><?php _e("Server", "gd-press-tools"); ?>:</td>
                        <td class="t options"><?php echo $_SERVER["SERVER_SOFTWARE"]; ?></td>
                    </tr>
                    <tr>
                        <td class="first b"><?php _e("Operating System", "gd-press-tools"); ?>:</td>
                        <td class="t options"><?php echo PHP_OS; ?></td>
                    </tr>
                    <tr>
                        <td class="first b"><?php _e("Hostname", "gd-press-tools"); ?>:</td>
                        <td class="t options"><?php echo $_SERVER['SERVER_NAME']; ?></td>
                    </tr>
                    <tr>
                        <td class="first b"><?php _e("IP and Port", "gd-press-tools"); ?>:</td>
                        <td class="t options"><?php echo $_SERVER['SERVER_ADDR'].":".$_SERVER['SERVER_PORT']; ?></td>
                    </tr>
                </tbody></table>
            </div>
        </div>
    </div>
    <div id="gdpt_server" class="postbox gdrgrid frontleft">
        <h3 class="hndle"><span><?php _e("PHP Status", "gd-press-tools"); ?></span></h3>
        <div class="inside">
            <div class="table">
                <table><tbody>
                    <tr class="first">
                        <td class="first b"><?php _e("Version", "gd-press-tools"); ?>:</td>
                        <td class="t options"><?php echo gdFunctionsGDPT::php_version(true); ?></td>
                    </tr>
                    <?php if (function_exists("memory_get_usage")) { ?>
                    <tr>
                        <td class="first b"><?php _e("Used Memory", "gd-press-tools"); ?>:</td>
                        <td class="t options"><?php echo gdFunctionsGDPT::size_format(memory_get_usage()); ?></td>
                    </tr>
                    <?php } ?>
                    <tr>
                        <td class="first b"><?php _e("Safe Mode", "gd-press-tools"); ?>:</td>
                        <td class="t options"><?php echo ini_get('safe_mode') ? __("On", "gd-press-tools") : __("Off", "gd-press-tools"); ?></td>
                    </tr>
                    <tr>
                        <td class="first b"><?php _e("Memory Limit", "gd-press-tools"); ?>:</td>
                        <td class="t options"><?php echo ini_get('memory_limit'); ?></td>
                    </tr>
                    <tr>
                        <td class="first b"><?php _e("Max POST Size", "gd-press-tools"); ?>:</td>
                        <td class="t options"><?php echo ini_get('post_max_size'); ?></td>
                    </tr>
                    <tr>
                        <td class="first b"><?php _e("Max Upload Size", "gd-press-tools"); ?>:</td>
                        <td class="t options"><?php echo ini_get('upload_max_filesize'); ?></td>
                    </tr>
                    <tr>
                        <td class="first b"><?php _e("Max Execution Time", "gd-press-tools"); ?>:</td>
                        <td class="t options"><?php echo ini_get('max_execution_time'); ?>s</td>
                    </tr>
                    <tr>
                        <td class="first b"><?php _e("Allow URL fopen", "gd-press-tools"); ?>:</td>
                        <td class="t options"><?php echo ini_get('allow_url_fopen') ? __("On", "gd-press-tools") : __("Off", "gd-press-tools"); ?></td>
                    </tr>
                    <tr>
                        <td class="first b"><?php _e("GD Library", "gd-press-tools"); ?>:</td>
                        <td class="t options"><?php echo GDPTDB::get_php_gd_version(); ?></td>
                    </tr>
                </tbody></table>
            </div>
        </div>
    </div>
    <div id="gdpt_server" class="postbox gdrgrid frontleft">
        <h3 class="hndle"><span><?php _e("mySQL Status", "gd-press-tools"); ?></span></h3>
        <div class="inside">
            <div class="table">
                <table><tbody>
                    <tr class="first">
                        <td class="first b"><?php _e("Version", "gd-press-tools"); ?>:</td>
                        <td class="t options"><?php echo gdFunctionsGDPT::mysql_version(true); ?></td>
                    </tr>
                    <tr>
                        <td class="first b"><?php _e("Full Database Size", "gd-press-tools"); ?>:</td>
                        <td class="t options"><?php echo GDPTDB::get_database_size(); ?></td>
                    </tr>
                    <tr>
                        <td class="first b"><?php _e("WordPress Tables Size", "gd-press-tools"); ?>:</td>
                        <td class="t options"><?php echo GDPTDB::get_tables_size(); ?></td>
                    </tr>
                </tbody></table>
            </div>
        </div>
    </div>
</div>
