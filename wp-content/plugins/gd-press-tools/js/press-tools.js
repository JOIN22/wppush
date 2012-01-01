jQuery(document).ready(function() {
    jQuery("#gdpt_cmm_date").datepicker({duration: "fast", dateFormat: "yy-mm-dd"});
    jQuery("#gdpt_delposts_date").datepicker({duration: "fast", dateFormat: "yy-mm-dd"});
    jQuery("#ui-datepicker-div").css("display", "none");
    jQuery(".gdptdbtoggle").click(function() {
        var id = jQuery(this).attr("id");
        var trid = "cl" + id.substring(2);
        var visible = jQuery("#" + trid).css("display");
        if (visible != "none") {
            jQuery("#" + trid).css("display", "none");
            jQuery(this).removeClass("off");
            jQuery(this).addClass("on");
        }
        else {
            jQuery("#" + trid).css("display", "");
            jQuery(this).removeClass("on");
            jQuery(this).addClass("off");
        }
    });
});

function gdpt_robots_exclude(el) {
    if (jQuery(el).is(":checked")) {
        var parts = jQuery(el).attr("id").substring(6).split("-");
        if (parts[1] == 'index' || parts[1] == "follow") parts[1] = 'no' + parts[1];
        else parts[1] = parts[1].substr(2);
        var id = "#gdptm-" + parts[0] + "-" + parts[1];
        jQuery(id).attr('checked', false);
    }
}
