function gdpt_dashboard_element(el, tx_show, tx_hide, ajax_url) {
    var value_name = jQuery(el).attr("id").substring(10);
    var table_id = 'gdpt-table-' + value_name;
    var shown = jQuery(el).hasClass('shown');

    if (shown) {
        jQuery("#" + table_id).hide('slow');
        jQuery(el).removeClass('shown').addClass('hiden').html("[ " + tx_show + " ]");
        jQuery.get(ajax_url, {action: "hide", value: value_name}, function(data) {});
    } else {
        jQuery("#" + table_id).show('slow');
        jQuery(el).removeClass('hiden').addClass('shown').html("[ " + tx_hide + " ]");
        jQuery.get(ajax_url, {action: "show", value: value_name}, function(data) {});
    }
}

function gdpt_postedit_disabling(el, cls) {
    var checked = jQuery(el).is(":checked");
    if (checked) {
        jQuery("." + cls + " input").attr("disabled", "disabled");
        jQuery("." + cls + " select").attr("disabled", "disabled");
        jQuery("." + cls).addClass("gdpt-disabled");
    } else {
        jQuery("." + cls + " input").removeAttr("disabled");
        jQuery("." + cls + " select").removeAttr("disabled");
        jQuery("." + cls).removeClass("gdpt-disabled");
    }
}

function gdpt_clear_robots(form) {
     for (i = 0, n = form.elements.length; i < n; i++) {
         if (form.elements[i].type == "checkbox") form.elements[i].checked = false;
     }
}