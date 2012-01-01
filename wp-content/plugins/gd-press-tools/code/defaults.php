<?php

class GDPTDefaults {
    var $default_global = array(
        "access_global" => 0,
        "access_server" => 1,
        "access_hooks" => 1,
        "access_database" => 1,
        "access_cron" => 0
    );

    var $default_caps = array(
        "presstools_global",
        "presstools_info",
        "presstools_front",
        "presstools_debug",
        "presstools_dashboard"
    );

    var $default_options = array(
        "version" => "2.6.0",
        "date" => "2011.07.04.",
        "status" => "Stable",
        "product_id" => "gd-press-tools",
        "edition" => "lite",
        "revision" => 0,
        "build" => 2600,
        "upgrade_to_pro_40" => 1,
        "admin_bar_disable" => 0,
        "real_capital_p_filter" => 0,
        "remove_capital_p_filter" => 0,
        "footer_stats" => 1,
        "update_report_usage" => 1,
        "enable_db_autorepair" => 1,
        "meta_wp_noindex" => 0,
        "meta_language_active" => 0,
        "meta_language_values" => "en",
        "debug_sql" => 0,
        "admin_interface_remove_help" => 0,
        "admin_interface_remove_favorites" => 0,
        "admin_interface_remove_logo" => 0,
        "admin_interface_remove_turbo" => 0,
        "tagger_abort" => 0,
        "html_desc_terms" => 1,
        "html_desc_users" => 1,
        "html_desc_links" => 0,
        "html_note_links" => 0,
        "debug_queries_global" => 0,
        "debug_queries_admin" => 0,
        "debug_queries_blog" => 0,
        "shorturl_active" => 0,
        "shorturl_prefix" => "gd",
        "database_upgrade" => "",
        "disable_flash_uploader" => 0,
        "counter_total_revisions" => 0,
        "counter_total_overhead" => 0,
        "auth_require_login" => 0,
        "remove_login_error" => 0,
        "remove_wp_version" => 0,
        "remove_rds" => 0,
        "remove_wlw" => 0,
        "posts_views_tracking" => 1,
        "posts_views_tracking_posts" => 1,
        "posts_views_tracking_pages" => 1,
        "posts_views_tracking_users" => 1,
        "posts_views_tracking_visitors" => 1,
        "posts_views_tracking_ignore" => "",
        "users_tracking" => 1,
        "users_tracking_posts" => 1,
        "users_tracking_pages" => 1,
        "users_tracking_ignore" => "",
        "dashboard_handler_info" => 1,
        "dashboard_handler_tracking" => 1,
        "dashboard_handler_tools" => 0,
        "integrate_dashboard" => 1,
        "integrate_comment_id" => 1,
        "integrate_cat_id" => 1,
        "integrate_tag_id" => 1,
        "integrate_user_id" => 1,
        "integrate_user_comments" => 1,
        "integrate_user_display" => 1,
        "integrate_post_options" => 1,
        "integrate_post_id" => 1,
        "integrate_post_sticky" => 0,
        "integrate_media_id" => 1,
        "integrate_links_id" => 1,
        "integrate_post_views" => 1,
        "integrate_postedit_widget" => 1,
        "disable_auto_save" => 0,
        "revisions_to_save" => -1,
        "urlfilter_wpadmin_active" => 1,
        "urlfilter_sqlqueries_active" => 1,
        "urlfilter_requestlength_value" => 1024,
        "urlfilter_requestlength_active" => 0,
        "php_memory_limit" => "64M",
        "php_memory_limit_enabled" => 0,
        "rss_disable" => 0,
        "rss_header_enable" => 0,
        "rss_header_contents" => "",
        "rss_footer_enable" => 0,
        "rss_footer_contents" => "",
        "rss_delay_active" => 0,
        "rss_delay_time" => 5,
        "tool_revisions_removed" => '/',
        "updates_disable_core" => 0,
        "updates_disable_themes" => 0,
        "updates_disable_plugins" => 0
    );

    var $default_robots = array(
        "login" => array("active" => 1, "noindex" => 1, "nofollow" => 1),
        "admin" => array("active" => 1, "noindex" => 1, "nofollow" => 1)
    );

    function GDPTDefaults() { }
}

?>