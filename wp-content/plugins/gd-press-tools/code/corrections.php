<style type="text/css">
#footer-upgrade { text-align: right; }
li.gdpt-go-pro a { font-weight: bold; color: #990000 !important; }

<?php if ($this->o["admin_interface_remove_logo"] == 1) { ?>#header-logo { display: none; }<?php echo PRESSTOOLS_LINE_ENDING; } ?>
<?php if ($this->o["admin_interface_remove_favorites"] == 1) { ?>#favorite-actions { display: none; }<?php echo PRESSTOOLS_LINE_ENDING; } ?>
</style>

<script type="text/javascript">
jQuery(document).ready(function() {
    jQuery("li#toplevel_page_gd-press-tools-gd-press-tools ul li:last").addClass("gdpt-go-pro");

<?php if ($this->admin_plugin && $this->admin_plugin_page != "settings" && $this->admin_plugin_page != "server") { ?>
    var htmlColor = jQuery("html").css("background-color");
    jQuery(".form-table td").css("border-bottom-color", htmlColor);
    jQuery(".form-table th").css("border-bottom-color", htmlColor);
<?php } ?>
<?php if ($this->o["admin_interface_remove_help"] == 1) { ?>
    jQuery('#contextual-help-link-wrap').remove();
<?php } ?>
<?php if ($this->o["admin_interface_remove_turbo"] == 1) { ?>
    jQuery('.turbo-nag').remove();
<?php } ?>
});
</script>
