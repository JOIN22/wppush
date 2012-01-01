<?php

/*
Plugin Name: GD Press Tools
Plugin URI: http://www.dev4press.com/gd-press-tools/
Description: GD Press Tools is a collection of various administration, seo, maintenance and security related tools that can help with everyday blog tasks and blog optimizations.
Version: 2.6.0
Author: Milan Petrovic
Author URI: http://www.dev4press.com/

== Copyright ==
Copyright 2008 - 2011 Milan Petrovic (email: milan@gdragon.info)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License as published by
the Free Software Foundation; either version 2 of the License, or
(at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

$gdpt_dirname_basic = dirname(__FILE__);

require_once($gdpt_dirname_basic."/config.php");

require_once($gdpt_dirname_basic."/code/defaults.php");
require_once($gdpt_dirname_basic."/gdragon/gd_debug.php");
require_once($gdpt_dirname_basic."/gdragon/gd_db_install.php");
require_once($gdpt_dirname_basic."/gdragon/gd_functions.php");
require_once($gdpt_dirname_basic."/gdragon/gd_wordpress.php");
require_once($gdpt_dirname_basic."/code/classes.php");
require_once($gdpt_dirname_basic."/code/functions.php");
require_once($gdpt_dirname_basic."/code/db.php");
require_once($gdpt_dirname_basic."/code/meta.php");

if (!class_exists('GDPressTools')) {
    /**
     * Main plugin class.
     */
    class GDPressTools {
        var $load_time = "0";
        var $load_timer = "0";
        var $load_memory = "0";
        var $load_query = "0";

        var $avatar_extensions = array("gif", "png", "jpg", "jpeg");
        var $used_memory = array();
        var $time_marker = array();
        var $wp_version;
        var $plugin_url;
        var $plugin_path;
        var $admin_plugin;
        var $admin_plugin_page;
        var $u;
        var $a;
        var $o;
        var $r;
        var $g;
        var $s;
        var $l;

        var $status = "";
        var $script = "";

        var $default_global;
        var $default_options;
        var $default_robots;
        var $default_caps;

        /**
         * Constructor
         */
        function GDPressTools() {
            $this->used_memory["load"] = function_exists("memory_get_usage") ? gdFunctionsGDPT::size_format(memory_get_usage()) : 0;
            $this->time_marker["load"] = microtime();

            $gdd = new GDPTDefaults();
            $this->default_global = $gdd->default_global;
            $this->default_options = $gdd->default_options;
            $this->default_robots = $gdd->default_robots;
            $this->default_caps = $gdd->default_caps;
            define('PRESSTOOLS_INSTALLED', $this->default_options["version"]." ".($this->default_options["edition"] == "lite" ? "Lite" : "Pro"));

            $this->plugin_path_url();
            $this->install_plugin();
            $this->actions_filters();
            $this->updates_checks();
            $this->remove_actions();
            $this->initialize_caps();

            if (PRESSTOOLS_PHP_SETTINGS) $this->php_ini();

            if ($this->o["revisions_to_save"] != -1 && !defined('WP_POST_REVISIONS')) define('WP_POST_REVISIONS', $this->o["revisions_to_save"]);
            if ($this->o["debug_queries_global"] == 1 && !defined('SAVEQUERIES')) define('SAVEQUERIES', true);
            if ($this->o["enable_db_autorepair"] == 1 && !defined('WP_ALLOW_REPAIR')) define('WP_ALLOW_REPAIR', true);

            define("PRESSTOOLS_DEBUG_SQL", $this->o["debug_sql"] == 1);
        }

        /**
         * Initialize capabilities.
         */
        function initialize_caps() {
            presstools_add_caps_to_role("administrator", $this->default_caps);
        }

        /**
         * Get the plugin setting from main settings array.
         *
         * @param $string $setting setting to retreive
         * @return mixed setting value
         */
        function get($setting) {
            return $this->o[$setting];
        }

        /**
         * Manipulation of PHP.INI file
         */
        function php_ini() {
            if ($this->o["php_memory_limit_enabled"] == 1)
                @ini_set('memory_limit', $this->o["php_memory_limit"]);
        }

        /**
         * Main installation entry point. This function create db tables, set default settings, check for changes.
         */
        function install_plugin() {
            global $wp_version;
            $this->wp_version = substr(str_replace('.', '', $wp_version), 0, 2);

            $this->o = get_option('gd-press-tools');
            $this->r = get_option('gd-press-tools-robots');
            $this->g = get_option('gd-press-tools-avatars');
            $this->s = get_option('gd-press-tools-status');

            $this->a = get_site_option('gd-press-tools-global');

            if (!is_array($this->a)) {
                update_site_option('gd-press-tools-global', $this->default_global);
                $this->a = get_site_option('gd-press-tools-global');
            }

            if (!is_array($this->s)) {
                update_option('gd-press-tools-status', array());
                $this->s = get_option('gd-press-tools-status');
            }

            if (!is_array($this->r)) {
                update_option('gd-press-tools-robots', $this->default_robots);
                $this->r = get_option('gd-press-tools-robots');
            }

            if (!is_array($this->g)) {
                update_option('gd-press-tools-avatars', array());
                $this->g = get_option('gd-press-tools-avatars');
            }

            $installed = false;
            if (!is_array($this->o)) {
                $this->default_options["memory_limit"] = ini_get('memory_limit');
                update_option('gd-press-tools', $this->default_options);
                $this->o = get_option('gd-press-tools');
                $installed = true;
            }

            if ($this->o["build"] != $this->default_options["build"] ||
                $this->o["edition"] != $this->default_options["edition"] ||
                $installed) {

                $this->o = gdFunctionsGDPT::upgrade_settings($this->o, $this->default_options);
                $this->a = gdFunctionsGDPT::upgrade_settings($this->a, $this->default_global);

                gdDBInstallGDPT::delete_tables(PRESSTOOLS_PATH);
                gdDBInstallGDPT::create_tables(PRESSTOOLS_PATH);
                gdDBInstallGDPT::upgrade_tables(PRESSTOOLS_PATH);
                gdDBInstallGDPT::alter_tables(PRESSTOOLS_PATH);
                $this->o["database_upgrade"] = date("r");

                $this->o["version"] = $this->default_options["version"];
                $this->o["date"] = $this->default_options["date"];
                $this->o["status"] = $this->default_options["status"];
                $this->o["build"] = $this->default_options["build"];
                $this->o["revision"] = $this->default_options["revision"];
                $this->o["edition"] = $this->default_options["edition"];

                update_option('gd-press-tools', $this->o);
                update_site_option('gd-press-tools-global', $this->a);

                $this->fix_folders();
                $this->backup_folder();
                update_option('gd-press-tools-avatars', $this->gravatar_folder($this->g));
                $this->g = get_option('gd-press-tools-avatars');

                gd_create_protection_file(WP_CONTENT_DIR."/avatars/");
                gd_create_protection_file(WP_CONTENT_DIR."/gdbackup/");
            }

            $this->script = $_SERVER["PHP_SELF"];
            $this->script = end(explode("/", $this->script));
        }

        /**
         * Creates basic plugin urls for local and global use.
         */
        function plugin_path_url() {
            $this->plugin_url = WP_PLUGIN_URL.'/gd-press-tools/';
            $this->plugin_path = dirname(__FILE__)."/";

            define('PRESSTOOLS_URL', $this->plugin_url);
            define('PRESSTOOLS_PATH', $this->plugin_path);
        }

        /**
         * Creates backup folder.
         */
        function backup_folder() {
            if (!is_dir(WP_CONTENT_DIR."/gdbackup")) {
                mkdir(WP_CONTENT_DIR."/gdbackup", 0755);
            }
        }

        /**
         * Try to set proper mode for folders.
         */
        function fix_folders() {
            if (is_dir(WP_CONTENT_DIR."/gdbackup")) {
                if (gdFunctionsGDPT::file_permission(WP_CONTENT_DIR."/gdbackup") != "0755")
                    chmod(WP_CONTENT_DIR."/gdbackup", 0755);
            }
            if (is_dir(WP_CONTENT_DIR."/avatars")) {
                if (gdFunctionsGDPT::file_permission(WP_CONTENT_DIR."/avatars") != "0755")
                    chmod(WP_CONTENT_DIR."/avatars", 0755);
            }
        }

        /**
         * Scans avatars folder for gravatars.
         *
         * @param array $gravatars list of gravatars
         * @return array list of gravatars
         */
        function gravatar_folder($gravatars) {
            if (!is_dir(WP_CONTENT_DIR."/avatars")) {
                mkdir(WP_CONTENT_DIR."/avatars", 0755);
            }

            if (is_dir(WP_CONTENT_DIR."/avatars/")) {
                $files = gdFunctionsGDPT::scan_dir(WP_CONTENT_DIR."/avatars");
                foreach ($files as $file) {
                    $ext = end(explode(".", $file));
                    if (in_array($ext, $this->avatar_extensions)) {
                        $nme = substr($file, 0, strlen($file) - 1 - strlen($ext));
                        $found = false;
                        foreach ($gravatars as $gr) {
                            if ($gr->file == $file) {
                                $found = true;
                                break;
                            }
                        }
                        if (!$found) {
                            $gr = new gdptAvatar();
                            $gr->name = $nme;
                            $gr->file = $file;
                            $gravatars[] = $gr;
                        }
                    }
                }
            }

            return $gravatars;
        }

        /**
         * Adds gravatars to wordpress.
         *
         * @param array $avatars list of gravatars
         * @return <type>
         */
        function add_avatars($avatars) {
            foreach ($this->g as $gravatar) {
                if ($gravatar->include) {
                    $avatars[$gravatar->get_url()] = $gravatar->name;
                }
            }
            return $avatars;
        }

        /**
         * Returns the false, to disable flash uploader with action attached.
         *
         * @return bool false
         */
        function disable_flash_uploader(){
            return false;
        }

        /**
         * Modify the post actions for post editor.
         *
         * @param array $actions list of current actions
         * @param object $post post to render
         * @return array expanded list of actions
         */
        function post_row_actions($actions, $post) {
            $url = add_query_arg("pid", $post->ID, $_SERVER['REQUEST_URI']);
            $actions["duplicate"] = sprintf('<a style="color: #00008b" href="%s" title="%s">%s</a>', add_query_arg("gda", "duplicate", $url), __("Duplicate", "gd-press-tools"), __("Duplicate", "gd-press-tools"));
            $counter = gd_count_revisions($post->ID);
            if ($counter > 0) $actions["revisions"] = sprintf('<a style="color: #cc0000" onclick="if (confirm(\'%s\')) { return true; } return false;" href="%s" title="%s">%s (%s)</a>', __("Are you sure that you want to delete revisions for this post?", "gd-press-tools"), add_query_arg("gda", "delrev", $url), __("Delete Revisions", "gd-press-tools"), __("Delete Revisions", "gd-press-tools"), $counter);
            return $actions;
        }

        /**
         * Get the tags from Yahoo Content service.
         *
         * @param string $title title to parse
         * @param string $content content to parse
         * @return array|string results or error
         */
        function get_tags_yahoo($title, $content) {
            if(!function_exists('curl_init')) return array();
            $content = $title."\r\n".strip_tags($content);

            $crl = curl_init();
            curl_setopt($crl, CURLOPT_URL, 'http://search.yahooapis.com/ContentAnalysisService/V1/termExtraction');
            curl_setopt($crl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($crl, CURLOPT_POST, 1);
            curl_setopt($crl, CURLOPT_POSTFIELDS, array('appid' => 'GDPressTools', 'context' => $content, 'query' => $title, 'output' => 'php'));
            $response = curl_exec($crl);
            if(curl_errno($crl)) return curl_error($crl);
            curl_close($crl);
            $results = unserialize($response);
            $tags = is_array($results['ResultSet']['Result']) ? $results['ResultSet']['Result'] : array();
            return $tags;
        }

        /**
         * Main auto tagger cron function
         */
        function auto_tagger_cron() {
            $this->s["tagger"]["memory_init"] = memory_get_usage();
            $posts = GDPTDB::get_cron_elements($this->s["tagger"]);
            $this->s["tagger"]["status"] = "running";
            $this->s["tagger"]["total"] = count($posts);
            $this->s["tagger"]["started"] = time();
            $this->s["tagger"]["processed"] = 0;
            $this->s["tagger"]["tags_found"] = 0;
            $this->s["tagger"]["last_id"] = 0;
            $this->s["tagger"]["last_error"] = "";
            update_option("gd-press-tools-status", $this->s);
            set_time_limit(10800);
            $i = $abort = 0;

            foreach ($posts as $p) {
                if ($i%5) {
                    $gl_o = gd_get_option_force('gd-press-tools');
                    $abort = $gl_o["tagger_abort"];
                }
                if ($abort == 0) {
                    $tags = $this->get_tags_yahoo($p->post_title, $p->post_content);
                    if (is_array($tags)) {
                        if (count($tags) > 0) {
                            $this->s["tagger"]["tags_found"]+= count($tags);
                            $tags = array_slice($tags, 0, $this->s["tagger"]["limit"]);
                            wp_add_post_tags($p->ID, strtolower(join(", ", $tags)));
                        }
                    } else $this->s["tagger"]["last_error"] = $tags;
                    $this->s["tagger"]["processed"]++;
                    $this->s["tagger"]["last_id"] = $p->ID;
                    $this->s["tagger"]["latest"] = time();
                    $this->s["tagger"]["memory"] = memory_get_usage();
                    update_option("gd-press-tools-status", $this->s);
                } else break;
                $i++;
            }

            $this->s["tagger"]["status"] = "idle";
            $this->s["tagger"]["ended"] = time();
            update_option("gd-press-tools-status", $this->s);
        }

        /**
         * All WP actions are added through this function
         */
        function actions_filters() {
            if (is_admin()) {
                add_action('admin_init', array(&$this, 'admin_init'));
                add_action('admin_menu', array(&$this, 'admin_menu'));
                add_action('admin_head', array(&$this, 'admin_head'));
                add_action('in_admin_footer', array(&$this, 'in_admin_footer'));
                add_action('admin_footer', array(&$this, 'admin_footer'));
                add_filter('plugin_row_meta', array(&$this, 'plugin_links'), 10, 2);
                add_filter('plugin_action_links', array(&$this, 'plugin_actions'), 10, 2);
                if ($this->o["integrate_post_options"] == 1) {
                    add_filter('post_row_actions', array(&$this, 'post_row_actions'), 10, 2);
                    add_filter('page_row_actions', array(&$this, 'post_row_actions'), 10, 2);
                }
                if ($this->get("integrate_postedit_widget") == 1) {
                    add_action('save_post', array(&$this, 'saveedit_post'));
                }

                add_action('wp_dashboard_setup', array(&$this, 'add_dashboard_widget'));
                if (!function_exists('wp_add_dashboard_widget')) {
                    add_filter('wp_dashboard_widgets', array(&$this, 'add_dashboard_widget_filter'));
                }

                if ($this->wp_version < 30) {
                    add_action('manage_categories_columns', array(&$this, 'admin_cats_columns'));
                    add_filter('manage_categories_custom_column', array(&$this, 'admin_columns_data_filter'), 10, 3);
                    add_action('manage_edit-tags_columns', array(&$this, 'admin_tags_columns'));
                } else {
                    add_filter('manage_edit-category_columns', array(&$this, 'admin_cats_columns'));
                    add_filter('manage_edit-post_tag_columns', array(&$this, 'admin_tags_columns'));
                    add_filter('manage_category_custom_column', array(&$this, 'admin_columns_data_filter'), 10, 3);
                }

                add_filter('manage_edit-comments_columns', array(&$this, 'admin_comments_columns'));
                add_action('manage_comments_custom_column', array(&$this, 'admin_columns_data_filter_new'), 10, 2);
                add_action('manage_users_columns', array(&$this, 'admin_user_columns'));
                add_filter('manage_users_custom_column', array(&$this, 'admin_columns_data_filter'), 10, 3);
                add_filter('manage_post_tag_custom_column', array(&$this, 'admin_columns_data_filter'), 10, 3);

                add_action('manage_posts_columns', array(&$this, 'admin_post_columns'));
                add_action('manage_pages_columns', array(&$this, 'admin_post_columns'));
                add_action('manage_media_columns', array(&$this, 'admin_media_columns'));
                add_action('manage_link-manager_columns', array(&$this, 'admin_links_columns'));
                add_action('manage_posts_custom_column', array(&$this, 'admin_columns_data'), 10, 2);
                add_action('manage_pages_custom_column', array(&$this, 'admin_columns_data'), 10, 2);
                add_action('manage_media_custom_column', array(&$this, 'admin_columns_data'), 10, 2);
                add_action('manage_link_custom_column', array(&$this, 'admin_columns_data'), 10, 2);
            } else {
                add_action('wp_head', array(&$this, 'wp_head'));
                add_action('wp_footer', array(&$this, 'blog_footer'));
                add_filter('the_content', array(&$this, 'count_views'));
                add_filter('the_excerpt_rss', array(&$this, 'expand_rss'));
                add_filter('the_content_rss', array(&$this, 'expand_rss'));
            }

            add_action('login_head', array(&$this, 'login_head'));
            add_action('init', array(&$this, 'init'));
            add_filter('avatar_defaults', array(&$this, 'add_avatars'));
            add_filter('login_redirect', array(&$this, 'login_redirect'), 10, 3);
            add_action('gdpt_auto_tagger', array(&$this, 'auto_tagger_cron'));

            if ($this->get("remove_capital_p_filter") == 1) {
                remove_filter('the_content', 'capital_P_dangit');
                remove_filter('the_title', 'capital_P_dangit');
                remove_filter('comment_text', 'capital_P_dangit');
            }

            if ($this->get("real_capital_p_filter") == 1) {
                add_filter('the_content', 'presstools_capital_p');
                add_filter('the_title', 'presstools_capital_p');
                add_filter('comment_text', 'presstools_capital_p');
            }

            if ($this->get("shorturl_active") == 1) {
                add_filter('query_vars', array(&$this, 'rewrite_variables'));
                add_action('generate_rewrite_rules', array(&$this, 'rewrite_rules'));
                add_action('parse_request', array(&$this, 'rewrite_parse'));
            }

            if ($this->o['auth_require_login'] == 1)
                add_action('get_header', array(&$this, 'require_login'));
            if ($this->o["disable_auto_save"] == 1 && is_admin())
                add_action('wp_print_scripts', array(&$this, 'disable_auto_save'));
            if ($this->get("rss_delay_active") == 1 && $this->get("rss_delay_time") > 0)
                add_filter('posts_where', array(&$this, 'delayed_rss_publish'));
            if ($this->o["remove_login_error"] == 1)
                add_filter('login_errors', create_function('$gdptloginerror', "return null;"));
            if ($this->o["disable_flash_uploader"] == 1)
                add_filter('flash_uploader', array(&$this, 'disable_flash_uploader'), 5);
        }

        /**
         * Remove some of the actions and filters according to settings.
         */
        function remove_actions() {
            if ($this->o["admin_bar_disable"] == 1) add_filter("show_admin_bar", "__return_false");
            if ($this->o["html_desc_terms"] == 1) remove_filter("pre_term_description", "wp_filter_kses");
            if ($this->o["html_desc_links"] == 1) remove_filter("pre_link_description", "wp_filter_kses");
            if ($this->o["html_note_links"] == 1) remove_filter("pre_link_notes", "wp_filter_kses");
            if ($this->o["html_desc_users"] == 1) remove_filter("pre_user_description", "wp_filter_kses");
            if ($this->o["meta_wp_noindex"] == 1) {
                remove_action("login_head", "noindex");
                remove_action("wp_head", "noindex");
            }
        }

        function updates_checks() {
            if ($this->o["updates_disable_core"] == 1) {
                remove_action('wp_version_check', 'wp_version_check');
                remove_action('admin_init', '_maybe_update_core');
                add_filter('pre_transient_update_core', create_function('$update_core', "return null;"));
            }

            if ($this->o["updates_disable_plugins"] == 1) {
                remove_action('load-plugins.php', 'wp_update_plugins');
                remove_action('load-update.php', 'wp_update_plugins');
                remove_action('admin_init', '_maybe_update_plugins');
                remove_action('wp_update_plugins', 'wp_update_plugins');
                add_filter('pre_transient_update_plugins', create_function('$update_plugin', "return null;"));
            }

            if ($this->o["updates_disable_themes"] == 1) {
                remove_action('load-themes.php', 'wp_update_themes');
                remove_action('load-update.php', 'wp_update_themes');
                remove_action('admin_init', '_maybe_update_themes');
                remove_action('wp_update_themes', 'wp_update_themes');
                add_filter('pre_transient_update_themes', create_function('$update_theme', "return null;"));
            }
        }

        function upgrade_notice() {
            if ($this->o["upgrade_to_pro_40"] == 1) {
                $no_thanks = add_query_arg("proupgrade", "hide");
                echo '<div class="updated">';
                _e("Thank you for using this plugin. Please, take a few minutes and check out the Pro version of this plugin with massive list of new features.", "gd-press-tools");
                echo ' ';
                _e("Pro version includes powerful files and database backup with offsite storage support, xml sitemaps generator, file manager, advanced debugger, security center with htaccess control, users registration protection, users pruning, maintenance mode and much more.", "gd-press-tools");
                echo '<br/><strong><a href="http://d4p.me/gdpt" target="_blank">'.__("GD Press Tools 4.0 Pro", "gd-press-tools")."</a></strong>";
                echo ' | <a href="'.$no_thanks.'">'.__("Don't display this message anymore", "gd-press-tools")."</a>";
                echo '</div>';
            }
        }

        function login_redirect($redirect, $request, $user) {
            if (strtolower(get_class($user)) == "wp_user") {
                update_user_meta($user->ID, "gdpt_last_login", time());
            }
            return $redirect;
        }

        function get_shortlink($post_id) {
            return trailingslashit(get_option("home")).$this->o["shorturl_prefix"].$post_id;
        }

        function rewrite_variables($qv) {
            $qv[] = "gdshlink";
            return $qv;
        }

        function rewrite_rules($wp_rewrite) {
            $rules = array(
                $this->o["shorturl_prefix"].'([0-9]{1,})$' => 'index.php?gdshlink='.$wp_rewrite->preg_index(1)
            );

            $wp_rewrite->rules = $rules + $wp_rewrite->rules;
            return $wp_rewrite;
        }

        function rewrite_parse($wpq) {
            if (isset($wpq->query_vars["gdshlink"])) {
                $post_id = $wpq->query_vars["gdshlink"];

                if ($post_id > 0) {
                    $location = get_permalink($post_id);
                    wp_redirect($location);
                    exit;
                }
            }
        }

        function global_allow($panel) {
            if (is_multisite() && !is_super_admin()) {
                $pcode = "access_".$panel;
                $panels = array_keys($this->a);
                if (in_array($pcode, $panels)) {
                    $allow = $this->a[$pcode];
                    return $allow == 1;
                } else return true;
            } else return true;
        }

        /**
         * Disables post auto save.
         */
        function disable_auto_save() {
            wp_deregister_script('autosave');
        }

        /**
         * Modify where part of the query for rss feed to delay posts.
         *
         * @global object $wpdb wp database object
         * @param string $content wp query where
         * @return string wp query where
         */
        function delayed_rss_publish($content) {
            if (is_feed()) {
                global $wpdb;
                $content.= sprintf(" AND timestampdiff(minute, %s.post_date_gmt, '%s') > %s",
                    $wpdb->posts, gmdate('Y-m-d H:i:s'), intval($this->get("rss_delay_time")));
            }
            return $content;
        }

        /**
         * WP Action wp_head.
         */
        function wp_head() {
            if ($this->o["debug_queries_global"] == 1 && $this->o["debug_queries_blog"] == 1 && current_user_can("presstools_debug"))
                echo('<link rel="stylesheet" href="'.$this->plugin_url.'css/blog_debug.css" type="text/css" media="screen" />');
            if ($this->o["meta_language_active"] == 1)
                echo sprintf('<meta http-equiv="content-language" content="%s">', $this->o["meta_language_values"]);

            add_meta_tag_robots($this->r);

            $this->used_memory["blog_head"] = function_exists("memory_get_usage") ? gdFunctionsGDPT::size_format(memory_get_usage()) : 0;
            $this->time_marker["blog_head"] = microtime();
        }

        /**
         * WP Action login_head.
         */
        function login_head() {
            print_robots_tag(get_robots_value($this->r, "login"));
        }

        /**
         * Perform URL scan.
         */
        function url_scanner() {
            if (!current_user_can('manage_options')) {
                $filter = true;
                if (is_admin()) {
                    $filter = $this->o["urlfilter_wpadmin_active"] == 1;
                }
                if ($filter) {
                    $fail = false;
                    $url = $_SERVER['REQUEST_URI'];
                    if ($this->o["urlfilter_requestlength_active"] == 1) {
                        $fail = strlen($url) > intval($this->o["urlfilter_requestlength_value"]);
                    }
                    if (!$fail && $this->o["urlfilter_sqlqueries_active"] == 1) {
                        $fail = stripos($_SERVER['REQUEST_URI'], "eval(") ||
                                stripos($_SERVER['REQUEST_URI'], "CONCAT") ||
                                stripos($_SERVER['REQUEST_URI'], "UNION+SELECT") ||
                                stripos($_SERVER['REQUEST_URI'], "base64");
                    }
                    if ($fail) {
                        wp_gdpt_dump("URL_RESTRICTED", $url);

                        @header("HTTP/1.1 414 Request-URI Too Long");
                        @header("Status: 414 Request-URI Too Long");
                        @header("Connection: Close");
                        @exit;
                    }
                }
            }
        }

        /**
         * WP Action init.
         */
        function init() {
            $this->url_scanner();

            $this->used_memory["init"] = function_exists("memory_get_usage") ? gdFunctionsGDPT::size_format(memory_get_usage()) : 0;
            $this->time_marker["init"] = microtime();

            $this->l = get_locale();
            if(!empty($this->l)) {
                $moFile = dirname(__FILE__)."/languages/gd-press-tools-".$this->l.".mo";
                if (@file_exists($moFile) && is_readable($moFile)) load_textdomain('gd-press-tools', $moFile);
            }

            if ($this->o["rss_disable"] == 1) {
                add_action('do_feed', 'gd_disable_feed', 1);
                add_action('do_feed_rdf', 'gd_disable_feed', 1);
                add_action('do_feed_rss', 'gd_disable_feed', 1);
                add_action('do_feed_rss2', 'gd_disable_feed', 1);
                add_action('do_feed_atom', 'gd_disable_feed', 1);
            }

            if ($this->o["remove_wp_version"] == 1)
                add_filter('the_generator', create_function('$wpv', "return null;"));

            if ($this->o["remove_rds"] == 1 && function_exists('rsd_link') && !is_admin())
                remove_action('wp_head', 'rsd_link');
            if ($this->o["remove_wlw"] == 1 && function_exists('wlwmanifest_link') && !is_admin())
                remove_action('wp_head', 'wlwmanifest_link');
        }

        /**
         * WP Action admin_init.
         */
        function admin_init() {
            if (isset($_GET["page"])) {
                if (substr($_GET["page"], 0, 14) == "gd-press-tools") {
                    $this->admin_plugin = true;
                    $this->admin_plugin_page = substr($_GET["page"], 15);
                }
            }

            if ($this->admin_plugin) {
                if (!$this->global_allow($this->admin_plugin_page)) {
                    wp_die(__("You do not have permission to access this page.", "gd-press-tools"));
                }
            }

            global $wp_taxonomies;
            foreach ($wp_taxonomies as $tax => $vals) {
                add_filter('manage_'.$tax.'_custom_column', array(&$this, 'admin_columns_data_filter'), 10, 3);
            }

            wp_enqueue_script('jquery');
            if ($this->admin_plugin) {
                wp_enqueue_script('jquery-ui-core');
                wp_enqueue_script('jquery-ui-tabs');

                wp_enqueue_script('thickbox');
                wp_enqueue_style('thickbox');
            }

            $this->dashboard_operations();
            $this->init_operations();
            $this->settings_operations();

            if ($this->o["updates_disable_plugins"] == 1)
                remove_action('admin_init', 'wp_update_plugins');
        }

        /**
         * WP Action admin_menu.
         */
        function admin_menu() {
            global $userdata, $menu;
            if ($this->wp_version < 30) $menu[0][2] = "index.php";

            add_menu_page('GD Press Tools', 'GD Press Tools', "presstools_front", __FILE__, array(&$this,"admin_tool_front"), plugins_url('gd-press-tools/gfx/menu.png'));

            if ($this->get("integrate_postedit_widget") == 1) {
                add_meta_box("gdpt-meta-box", "GD Press Tools", array(&$this, 'editbox_post'), "post", "side", "high");
                add_meta_box("gdpt-meta-box", "GD Press Tools", array(&$this, 'editbox_post'), "page", "side", "high");
            }

            add_submenu_page(__FILE__, 'GD Press Tools: '.__("Front Page", "gd-press-tools"), __("Front Page", "gd-press-tools"), "presstools_front", __FILE__, array(&$this,"admin_tool_front"));

            if (is_multisite() && is_super_admin()) {
                add_submenu_page(__FILE__, 'GD Press Tools: '.__("Global Settings", "gd-press-tools"), "Global Settings", "presstools_global", "gd-press-tools-global", array(&$this,"admin_tool_global"));
            }
            if ($this->global_allow("server")) {
                add_submenu_page(__FILE__, 'GD Press Tools: '.__("Environment Info", "gd-press-tools"), __("Environment Info", "gd-press-tools"), "presstools_info", "gd-press-tools-server", array(&$this,"admin_tool_server"));
            }
            if ($this->global_allow("hooks")) {
                add_submenu_page(__FILE__, 'GD Press Tools: '.__("WP Hooks", "gd-press-tools"), __("WP Hooks", "gd-press-tools"), "presstools_info", "gd-press-tools-hooks", array(&$this,"admin_tool_hooks"));
            }
            add_submenu_page(__FILE__, 'GD Press Tools: '.__("Administration", "gd-press-tools"), __("Administration", "gd-press-tools"), "presstools_global", "gd-press-tools-admin", array(&$this,"admin_tool_admin"));
            add_submenu_page(__FILE__, 'GD Press Tools: '.__("Posts", "gd-press-tools"), __("Posts", "gd-press-tools"), "presstools_global", "gd-press-tools-posts", array(&$this,"admin_tool_posts"));
            add_submenu_page(__FILE__, 'GD Press Tools: '.__("Auto Tagger", "gd-press-tools"), __("Auto Tagger", "gd-press-tools"), "presstools_global", "gd-press-tools-tagger", array(&$this,"admin_tool_tagger"));
            add_submenu_page(__FILE__, 'GD Press Tools: '.__("Meta Tags", "gd-press-tools"), __("Meta Tags", "gd-press-tools"), "presstools_global", "gd-press-tools-meta", array(&$this,"admin_tool_meta"));
            if ($this->global_allow("database")) {
                add_submenu_page(__FILE__, 'GD Press Tools: '.__("Database", "gd-press-tools"), __("Database", "gd-press-tools"), "presstools_global", "gd-press-tools-database", array(&$this,"admin_tool_database"));
            }
            if ($this->global_allow("cron")) {
                add_submenu_page(__FILE__, 'GD Press Tools: '.__("Cron Scheduler", "gd-press-tools"), __("Cron Scheduler", "gd-press-tools"), "presstools_global", "gd-press-tools-cron", array(&$this,"admin_tool_cron"));
            }
            add_submenu_page(__FILE__, 'GD Press Tools: '.__("Settings", "gd-press-tools"), __("Settings", "gd-press-tools"), "presstools_global", "gd-press-tools-settings", array(&$this,"admin_tool_settings"));
            add_submenu_page(__FILE__, 'GD Press Tools: '.__("Upgrade to Pro", "gd-press-tools"), __("Upgrade to Pro", "gd-press-tools"), "presstools_global", "gd-press-tools-gopro", array(&$this,"admin_tool_gopro"));

            if ($this->o["updates_disable_plugins"] == 1)
                remove_action('load-plugins.php', 'wp_update_plugins');
        }

        /**
         * WP Action admin_head.
         */
        function admin_head() {
            if ($this->o["updates_disable_core"] == 1) {
                remove_action("admin_notices", "update_nag", 3 );
                remove_action("admin_notices", "maintenance_nag");
            }

            if ($this->script == "users.php")
                $this->u = GDPTDB::get_all_users_comments_count();

            $this->used_memory["admin_head"] = function_exists("memory_get_usage") ? gdFunctionsGDPT::size_format(memory_get_usage()) : 0;
            $this->time_marker["admin_head"] = microtime();
            print_robots_tag(get_robots_value($this->r, "admin"));
            if ($this->admin_plugin) {
                wp_admin_css('css/dashboard');
                $datepicker_date = date("Y, n, j");

                echo('<script type="text/javascript" src="'.$this->plugin_url.'js/jquery-ui-datepicker-17.js"></script>');
                echo('<link rel="stylesheet" href="'.$this->plugin_url.'css/jquery_ui17.css" type="text/css" media="screen" />');
                echo('<link rel="stylesheet" href="'.$this->plugin_url.'css/admin_main.css" type="text/css" media="screen" />');
                echo('<link rel="stylesheet" href="'.$this->plugin_url.'css/admin_wp28.css" type="text/css" media="screen" />');

                if(!empty($this->l)) {
                    $jsFile = $this->plugin_path.'js/i18n-17/jquery-ui-datepicker-'.$this->l.'.js';
                    if (@file_exists($jsFile) && is_readable($jsFile)) echo '<script type="text/javascript" src="'.$this->plugin_url.'js/i18n-17/jquery-ui-datepicker-'.$this->l.'.js"></script>';
                }

                include($this->plugin_path."/code/js.php");
                echo('<script type="text/javascript" src="'.$this->plugin_url.'js/press-tools.js"></script>');
            }

            include($this->plugin_path."code/corrections.php");
            echo('<link rel="stylesheet" href="'.$this->plugin_url.'css/admin.css" type="text/css" media="screen" />');
            echo('<script type="text/javascript" src="'.$this->plugin_url.'js/dashboard.js"></script>');
            echo('<!--[if IE]><link rel="stylesheet" href="'.$this->plugin_url.'css/admin_ie.css" type="text/css" media="screen" /><![endif]-->');
        }

        /**
         * Time recalculation with timespans.
         */
        function recalculate_load_time($endtime, $starttime) {
            $startarray = explode(" ", $starttime);
            $starttime = $startarray[1] + $startarray[0];
            $endarray = explode(" ", $endtime);
            $endtime = $endarray[1] + $endarray[0];
            $totaltime = $endtime - $starttime; 
            return round($totaltime, 5);
        }

        /**
         * Setting some properties for time statistics.
         */
        function footer_stats() {
            $this->load_time = $this->recalculate_load_time($this->time_marker["footer"], $this->time_marker["load"]);
            $this->load_memory = function_exists("memory_get_usage") ? gdFunctionsGDPT::size_format(memory_get_usage()) : 0;
            $this->load_query = get_num_queries();
            $this->load_timer = timer_stop();
        }

        /**
         * WP action admin_footer
         */
        function in_admin_footer() {
            $this->time_marker["footer"] = microtime();
            $this->footer_stats();
            $this->used_memory["footer"] = $this->load_memory;
            if ($this->o["footer_stats"] == 1) {
                echo __("Executed Queries", "gd-press-tools").': ';
                echo '<strong>'.$this->load_query.'</strong> | ';
                echo __("Used memory", "gd-press-tools").': ';
                echo __("Init", "gd-press-tools").': <strong>'.$this->used_memory["init"].'</strong> | ';
                echo __("Header", "gd-press-tools").': <strong>'.$this->used_memory["admin_head"].'</strong> | ';
                echo __("Footer", "gd-press-tools").': <strong>'.$this->used_memory["footer"].'</strong> | ';
                echo __("Page generated in", "gd-press-tools").': <strong>';
                echo $this->load_timer.' '.__("seconds.", "gd-press-tools");
                echo '</strong><br/>';
            }
            _e("Thank you for using", "gd-press-tools");
            echo ' <a target="_blank" href="http://www.dev4press.com/plugins/gd-press-tools/">GD Press Tools '.$this->o["version"].' '.($this->o["edition"] == "lite" ? "Lite" : "Pro").'</a> ';
            _e("administration addon plugin", "gd-press-tools");
            if (defined("GDSIMPLEWIDGETS_INSTALLED")) {
                $gdsw_ver = substr(GDSIMPLEWIDGETS_INSTALLED, strpos(GDSIMPLEWIDGETS_INSTALLED, "_") + 1, 5);
                echo ' and <a target="_blank" href="http://www.dev4press.com/plugins/gd-simple-widgets/">GD Simple Widgets '.$gdsw_ver.'</a> ';
                _e("collection of widgets", "gd-press-tools");
            }
            echo '.<br/>';
        }

        /**
         * WP Action admin_footer.
         */
        function admin_footer() {
            if ($this->o["debug_queries_global"] == 1 && $this->o["debug_queries_admin"] == 1 && current_user_can("presstools_debug")) {
                echo $this->generate_queries_log();
            }
        }

        /**
         * WP Action wp_footer.
         */
        function blog_footer() {
            if ($this->o["debug_queries_global"] == 1 && $this->o["debug_queries_blog"] == 1 && current_user_can("presstools_debug")) {
                echo $this->generate_queries_log(true);
            }
        }

        function generate_queries_log($info = false) {
            global $wpdb;
            $result = $queries = "";

            if ($wpdb->queries) {
                $queries.= '<div class="gdptdebugq"><ol>';
                $total_time = 0;
                foreach ($wpdb->queries as $q) {
                    $queries.= "<li>";

                    $queries.= "<strong>".__("Call originated from", "gd-press-tools").":</strong> ".$q[2]."<br />";
                    $queries.= "<strong>".__("Execution time", "gd-press-tools").":</strong> ".$q[1]."<br />";
                    $queries.= "<em>".$q[0]."</em>";
                    $total_time+= $q[1];

                    $queries.= "</li>";
                }
                $queries.= '</ol></div>';

                $result.= '<div class="gdptdebugq">';
                if ($info) {
                    $result.= '<p class="gdptinfoq">';
                    $result.= __("Thank you for using", "gd-press-tools");
                    $result.= ' <a target="_blank" href="http://www.dev4press.com/plugins/gd-press-tools/">GD Press Tools '.$this->o["version"].'</a> ';
                    $result.= __("administration addon plugin", "gd-press-tools");
                    $result.= '.</p>';
                }
                $result.= "<strong>".__("Page generated in", "gd-press-tools").":</strong> ".(timer_stop(0)).' '.__("seconds.", "gd-press-tools")."<br />";
                $result.= "<strong>".__("Total memory used", "gd-press-tools").":</strong> ".(function_exists("memory_get_usage") ? gdFunctionsGDPT::size_format(memory_get_usage()) : 0)."<br />";
                $result.= "<strong>".__("Total number of queries", "gd-press-tools").":</strong> ".count($wpdb->queries)."<br />";
                $result.= "<strong>".__("Total execution time", "gd-press-tools").":</strong> ".round($total_time, 5)." ".__("seconds", "gd-press-tools");
                $result.= '</div>';
                $result.= $queries;
            }

            return $result;
        }

        function add_dashboard_widget() {
            global $userdata;

            if (!function_exists('wp_add_dashboard_widget')) {
                if ($this->o["integrate_dashboard"] == 1 && current_user_can("presstools_dashboard")) {
                    wp_register_sidebar_widget("dashboard_gdpresstools", "GD Press Tools: ".__("Additional Options", "gd-press-tools"), array(&$this, 'display_dashboard_widget'), array('all_link' => get_bloginfo('wpurl').'/wp-admin/admin.php?page=gd-press-tools/gd-press-tools.php', 'width' => 'half', 'height' => 'single'));
                }
            } else {
                if ($this->o["integrate_dashboard"] == 1 && current_user_can("presstools_dashboard")) {
                    wp_add_dashboard_widget("dashboard_gdpresstools", "GD Press Tools: ".__("Additional Options", "gd-press-tools"), array(&$this, 'display_dashboard_widget'));
                }
            }
        }

        function add_dashboard_widget_filter($widgets) {
            global $wp_registered_widgets, $userdata;

            if (!isset($wp_registered_widgets["dashboard_gdpresstools"])) return $widgets;

            if ($this->o["integrate_dashboard"] == 1 && current_user_can("presstools_dashboard")) {
                array_splice($widgets, 2, 0, "dashboard_gdpresstools");
            }
            return $widgets;
        }

        function display_dashboard_widget($sidebar_args) {
            if (!function_exists('wp_add_dashboard_widget')) {
                extract($sidebar_args, EXTR_SKIP);
                echo $before_widget.$before_title.$widget_name.$after_title;
            }
            $options = $this->o;
            include($this->plugin_path.'modules/widgets/dashboard.php');
            if (!function_exists('wp_add_dashboard_widget')) echo $after_widget;
        }

        function editbox_post() {
            global $post;

            $robots = get_post_meta($post->ID, "_gdpt_meta_robots", true);
            $meta_robots = array();
            if (!empty($robots)) {
                $robots = explode(",", $robots);
                if ($robots[0] == "index" || $robots[0] == "noindex") {
                    $meta_robots["standard"] = $robots[0].",".$robots[1];
                    unset($robots[0]);
                    unset($robots[0]);
                }
                foreach ($robots as $robot) $meta_robots[$robot] = 1;
            } else $meta_robots["active"] = 1;

            include($this->plugin_path.'modules/integrate/postedit.php');
        }

        function saveedit_post($post_id) {
            if (isset($_POST["post_ID"]) && $_POST["post_ID"] > 0)
                $post_id = $_POST["post_ID"];

            if (isset($_POST['gdpt_post_edit']) && $_POST['gdpt_post_edit'] == "edit") {
                if (isset($_POST['gdpt_meta_robots'])) {
                    delete_post_meta($post_id, "_gdpt_meta_robots");
                } else {
                    $robots = array();
                    $raw = $_POST["gdpt_meta_robots_extra"];
                    if ($_POST["gdpt_meta_robots_standard"] != "")
                        $robots = explode(",", $_POST["gdpt_meta_robots_standard"]);
                    if (is_array($raw))
                        foreach ($raw as $value => $status) $robots[] = $value;
                    update_post_meta($post_id, "_gdpt_meta_robots", join(",", $robots));
                }
            }
        }

        function expand_rss($content) {
            if (is_feed()) {
                global $post;
                if ($this->get("rss_header_enable") == 1) {
                    $header = '<p>'.html_entity_decode($this->get("rss_header_contents")).'</p>';
                    $header = apply_filters("gdpt_expandrss_header", $header, $post);
                    $content = $header.$content;
                }
                if ($this->get("rss_footer_enable") == 1) {
                    $footer = '<p>'.html_entity_decode($this->get("rss_footer_contents")).'</p>';
                    $footer = apply_filters("gdpt_expandrss_footer", $footer, $post);
                    $content.= $footer;
                }
            }
            return $content;
        }

        function count_views($content) {
            global $post, $userdata;
            $user_id = isset($userdata) ? $userdata->ID : 0;

            if ($post->post_status == 'publish' && !is_feed() && !is_admin()) {
                if ($this->o["posts_views_tracking"] == 1) {
                    if ((is_single() && $this->o["posts_views_tracking_posts"] == 1) ||
                        (is_page() && $this->o["posts_views_tracking_pages"] == 1)) {
                        $users = explode(",", $this->o["posts_views_tracking_ignore"]);
                        if (($user_id == 0 && $this->o["posts_views_tracking_visitors"] == 1) ||
                            ($user_id > 0 && $this->o["posts_views_tracking_users"] == 1 && !in_array($user_id, $users))) {
                            GDPTDB::insert_posts_views($post->ID, $user_id > 0);
                        }
                    }
                }

                if ($this->o["users_tracking"] == 1 && $user_id > 0) {
                    if ((is_single() && $this->o["users_tracking_posts"] == 1) ||
                        (is_page() && $this->o["users_tracking_pages"] == 1)) {
                        $users = explode(",", $this->o["users_tracking_ignore"]);
                        if (!in_array($user_id, $users)) {
                            GDPTDB::insert_users_tracking($post->ID, $user_id);
                        }
                    }
                }
            }

            return $content;
        }

        function plugin_actions($links, $file) {
            static $this_plugin;
            if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);

            if ($file == $this_plugin ){
                $settings_link = '<a href="admin.php?page=gd-press-tools-settings">'.__("Settings", "gd-press-tools").'</a>';
                array_unshift($links, $settings_link);
            }
            return $links;
        }

	function plugin_links($links, $file) {
            static $this_plugin;
            if (!$this_plugin) $this_plugin = plugin_basename(__FILE__);
            if ($file == $this_plugin){
                $links[] = '<a href="admin.php?page=gd-press-tools-database">'.__("Database Tools", "gd-press-tools").'</a>';
                $links[] = '<a href="http://www.dev4press.com/plugins/gd-press-tools/faq/">'.__("FAQ", "gd-press-tools").'</a>';
                $links[] = '<a target="_blank" style="color: #cc0000; font-weight: bold;" href="http://d4p.me/gdpt">'.__("Upgrade to PRO", "gd-press-tools").'</a>';
            }
            return $links;
	}

        function admin_media_columns($columns) {
            $new_columns = array();
            if ($this->get("integrate_media_id") == 1) {
                $i = 0;
                foreach ($columns as $key => $value) {
                    if ($i == 1) $new_columns["gdpt_mediaid"] = "ID";
                    $new_columns[$key] = $value;
                    $i++;
                }
            }
            else $new_columns = $columns;
            return $new_columns;
        }

        function admin_links_columns($columns) {
            $new_columns = array();
            if ($this->get("integrate_links_id") == 1) {
                $i = 0;
                foreach ($columns as $key => $value) {
                    if ($i == 1) $new_columns["gdpt_linksid"] = "ID";
                    $new_columns[$key] = $value;
                    $i++;
                }
            }
            else $new_columns = $columns;
            return $new_columns;
        }

        function admin_comments_columns($columns) {
            if ($this->script != "edit-comments.php" && $this->script != "admin-ajax.php") return $columns;

            $new_columns = array();
            if ($this->get("integrate_comment_id") == 1) {
                $i = 0;
                foreach ($columns as $key => $value) {
                    if ($i == 1) $new_columns["gdpt_commentid"] = "ID";
                    $new_columns[$key] = $value;
                    $i++;
                }
            } else $new_columns = $columns;
            return $new_columns;
        }

        function admin_columns_data_filter_new($column, $id) {
            switch ($column) {
                case "gdpt_commentid":
                    echo $id;
                    break;
            }
        }

        function admin_cats_columns($columns) {
            $new_columns = array();
            if ($this->get("integrate_cat_id") == 1) {
                $i = 0;
                foreach ($columns as $key => $value) {
                    if ($i == 1) $new_columns["gdpt_catid"] = "ID";
                    $new_columns[$key] = $value;
                    $i++;
                }
            }
            else $new_columns = $columns;
            return $new_columns;
        }

        function admin_tags_columns($columns) {
            $new_columns = array();
            if ($this->get("integrate_tag_id") == 1) {
                $i = 0;
                foreach ($columns as $key => $value) {
                    if ($i == 1) $new_columns["gdpt_tagid"] = "ID";
                    $new_columns[$key] = $value;
                    $i++;
                }
            }
            else $new_columns = $columns;
            return $new_columns;
        }

        function admin_user_columns($columns) {
            $new_columns = array();
            if ($this->get("integrate_user_id") == 1) {
                $i = 0;
                foreach ($columns as $key => $value) {
                    if ($i == 1) $new_columns["gdpt_userid"] = "ID";
                    $new_columns[$key] = $value;
                    $i++;
                }
            }
            else $new_columns = $columns;
            if ($this->get("integrate_user_comments") == 1) $new_columns["gdpt_usercomments"] = __("Comments", "gd-press-tools");
            if ($this->get("integrate_user_display") == 1) $new_columns["gdpt_displayname"] = __("Display Name", "gd-press-tools");
            return $new_columns;
        }

        function admin_post_columns($columns) {
            $new_columns = array();
            if ($this->get("integrate_post_id") == 1) {
                $i = 0;
                foreach ($columns as $key => $value) {
                    if ($i == 1) $new_columns["gdpt_postid"] = "ID";
                    $new_columns[$key] = $value;
                    $i++;
                }
            } else $new_columns = $columns;

            if ($this->get("integrate_post_sticky") == 1) $new_columns["gdpt_sticky"] = __("Sticky", "gd-press-tools");
            if ($this->get("integrate_post_views") == 1) $new_columns["gdpt_views"] = __("Views", "gd-press-tools");

            return $new_columns;
        }

        function admin_columns_data_filter($data, $column, $id) {
            switch ($column) {
                case "gdpt_catid":
                case "gdpt_userid":
                case "gdpt_tagid":
                    return $id;
                    break;
                case "gdpt_usercomments":
                    $cmms = isset($this->u[$id]) ? $this->u[$id] : "0";
                    return $cmms;
                    break;
                case "gdpt_displayname":
                    $user = get_userdata($id);
                    return $user->display_name;
                    break;
            }
        }

        function admin_columns_data($column, $id) {
            switch ($column) {
                case "gdpt_sticky":
                    if (is_sticky($id)) echo __("Yes", "gd-press-tools");
                    break;
                case "gdpt_mediaid":
                case "gdpt_postid":
                case "gdpt_linksid":
                    echo $id;
                    break;
                case "gdpt_views":
                    $data = gd_count_views($id);
                    echo sprintf('<div class="gdpt_view_line">%s: <strong class="gdpt_view_value">%s</strong></div>%s: <strong class="gdpt_view_value">%s</strong><br />%s: <strong class="gdpt_view_value">%s</strong><br />',
                        __("total", "gd-press-tools"), intval($data->tot_views),
                        __("users", "gd-press-tools"), intval($data->usr_views),
                        __("visitors", "gd-press-tools"), intval($data->vst_views));
                    break;
                case "gdpt_options":
                    $url = add_query_arg("pid", $id, $_SERVER['REQUEST_URI']);
                    $counter = gd_count_revisions($id);
                    echo sprintf('<a style="color: #00008b" href="%s" title="%s">%s</a><br />', add_query_arg("gda", "duplicate", $url), __("Duplicate", "gd-press-tools"), __("Duplicate", "gd-press-tools"));
                    if ($counter > 0) echo sprintf('<a style="color: #cc0000" onclick="if (confirm(\'%s\')) { return true; } return false;" href="%s" title="%s">%s (%s)</a>', __("Are you sure that you want to delete revisions for this post?", "gd-press-tools"), add_query_arg("gda", "delrev", $url), __("Delete Revisions", "gd-press-tools"), __("Delete Revisions", "gd-press-tools"), $counter);
                    break;
            }
        }

        function require_login() {
            if (!is_user_logged_in() && strpos($_SERVER['PHP_SELF'], 'wp-login.php') === false && strpos($_SERVER['PHP_SELF'], 'wp-register.php') === false)
                auth_redirect();
        }

        function settings_operations() {
            if (isset($_POST['gdpt_default_meta'])) {
                update_option('gd-press-tools-robots', $this->default_robots);
                wp_redirect(add_query_arg("settings", "saved"));
                exit();
            }

            if (isset($_POST['gdpt_saving_meta_general'])) {
                $this->o["meta_wp_noindex"] = isset($_POST['meta_wp_noindex']) ? 1 : 0;
                $this->o["meta_language_active"] = isset($_POST['meta_language_active']) ? 1 : 0;
                $this->o["meta_language_values"] = $_POST['meta_language_values'];
                update_option("gd-press-tools", $this->o);

                wp_redirect(add_query_arg("settings", "saved"));
                exit();
            }

            if (isset($_POST['gdpt_saving_meta'])) {
                $meta_active = $_POST["gdpt_meta_active"];
                $meta_robots = $_POST["gdpt_meta"];
                $this->r = get_default_meta_robots();
                if (is_array($meta_active)) {
                    foreach ($meta_active as $active) $this->r[$active]["active"] = 1;
                }
                if (is_array($meta_robots)) {
                    foreach ($meta_robots as $meta) {
                        $parts = explode("|", $meta);
                        $this->r[$parts[0]][$parts[1]] = 1;
                    }
                }

                update_option('gd-press-tools-robots', $this->r);
                wp_redirect(add_query_arg("settings", "saved"));
                exit();
            }

            if (isset($_POST['gdpt_saving_global'])) {
                $this->a["access_server"] = isset($_POST['access_server']) ? 1 : 0;
                $this->a["access_hooks"] = isset($_POST['access_hooks']) ? 1 : 0;
                $this->a["access_database"] = isset($_POST['access_database']) ? 1 : 0;
                $this->a["access_cron"] = isset($_POST['access_cron']) ? 1 : 0;

                update_site_option('gd-press-tools-global', $this->a);

                wp_redirect(add_query_arg("settings", "saved"));
                exit();
            }

            if (isset($_POST['gdpt_saving'])) {
                global $wp_rewrite;
                $wp_rewrite->flush_rules();

                $this->o["integrate_dashboard"] = isset($_POST['integrate_dashboard']) ? 1 : 0;

                $this->o["html_desc_terms"] = isset($_POST['html_desc_terms']) ? 1 : 0;
                $this->o["html_desc_users"] = isset($_POST['html_desc_users']) ? 1 : 0;
                $this->o["html_desc_links"] = isset($_POST['html_desc_links']) ? 1 : 0;
                $this->o["html_note_links"] = isset($_POST['html_note_links']) ? 1 : 0;

                $this->o["real_capital_p_filter"] = isset($_POST['real_capital_p_filter']) ? 1 : 0;
                $this->o["remove_capital_p_filter"] = isset($_POST['remove_capital_p_filter']) ? 1 : 0;
                $this->o["footer_stats"] = isset($_POST['footer_stats']) ? 1 : 0;
                $this->o["update_report_usage"] = isset($_POST['update_report_usage']) ? 1 : 0;
                $this->o["integrate_post_options"] = isset($_POST['integrate_post_options']) ? 1 : 0;
                $this->o["integrate_comment_id"] = isset($_POST['integrate_comment_id']) ? 1 : 0;
                $this->o["integrate_cat_id"] = isset($_POST['integrate_cat_id']) ? 1 : 0;
                $this->o["integrate_tag_id"] = isset($_POST['integrate_tag_id']) ? 1 : 0;
                $this->o["integrate_user_id"] = isset($_POST['integrate_user_id']) ? 1 : 0;
                $this->o["integrate_user_comments"] = isset($_POST['integrate_user_comments']) ? 1 : 0;
                $this->o["integrate_post_id"] = isset($_POST['integrate_post_id']) ? 1 : 0;
                $this->o["integrate_post_views"] = isset($_POST['integrate_post_views']) ? 1 : 0;
                $this->o["integrate_post_sticky"] = isset($_POST['integrate_post_sticky']) ? 1 : 0;
                $this->o["rss_disable"] = isset($_POST['rss_disable']) ? 1 : 0;
                $this->o["updates_disable_core"] = isset($_POST['updates_disable_core']) ? 1 : 0;
                $this->o["updates_disable_themes"] = isset($_POST['updates_disable_themes']) ? 1 : 0;
                $this->o["updates_disable_plugins"] = isset($_POST['updates_disable_plugins']) ? 1 : 0;
                $this->o["auth_require_login"] = isset($_POST['auth_require_login']) ? 1 : 0;
                $this->o["remove_wp_version"] = isset($_POST['remove_wp_version']) ? 1 : 0;
                $this->o["remove_rds"] = isset($_POST['remove_rds']) ? 1 : 0;
                $this->o["remove_wlw"] = isset($_POST['remove_wlw']) ? 1 : 0;
                $this->o["integrate_media_id"] = isset($_POST['integrate_media_id']) ? 1 : 0;
                $this->o["integrate_links_id"] = isset($_POST['integrate_links_id']) ? 1 : 0;
                $this->o["disable_flash_uploader"] = isset($_POST['disable_flash_uploader']) ? 1 : 0;
                $this->o["remove_login_error"] = isset($_POST['remove_login_error']) ? 1 : 0;
                $this->o["disable_auto_save"] = isset($_POST['disable_auto_save']) ? 1 : 0;
                $this->o["urlfilter_wpadmin_active"] = isset($_POST['urlfilter_wpadmin_active']) ? 1 : 0;
                $this->o["urlfilter_sqlqueries_active"] = isset($_POST['urlfilter_sqlqueries_active']) ? 1 : 0;
                $this->o["urlfilter_requestlength_active"] = isset($_POST['urlfilter_requestlength_active']) ? 1 : 0;
                $this->o["urlfilter_requestlength_value"] = intval($_POST['urlfilter_requestlength_value']);

                $this->o["admin_interface_remove_help"] = isset($_POST['admin_interface_remove_help']) ? 1 : 0;
                $this->o["admin_interface_remove_favorites"] = isset($_POST['admin_interface_remove_favorites']) ? 1 : 0;
                $this->o["admin_interface_remove_logo"] = isset($_POST['admin_interface_remove_logo']) ? 1 : 0;
                $this->o["admin_interface_remove_turbo"] = isset($_POST['admin_interface_remove_turbo']) ? 1 : 0;

                $this->o["debug_sql"] = isset($_POST['debug_sql']) ? 1 : 0;
                $this->o["debug_queries_admin"] = isset($_POST['debug_queries_admin']) ? 1 : 0;
                $this->o["debug_queries_blog"] = isset($_POST['debug_queries_blog']) ? 1 : 0;
                $this->o["debug_queries_global"] = isset($_POST['debug_queries_global']) ? 1 : 0;
                $this->o["admin_bar_disable"] = isset($_POST['admin_bar_disable']) ? 1 : 0;

                $this->o["shorturl_active"] = isset($_POST['shorturl_active']) ? 1 : 0;
                $this->o["shorturl_prefix"] = $_POST['shorturl_prefix'];

                $this->o["posts_views_tracking"] = isset($_POST['posts_views_tracking']) ? 1 : 0;
                $this->o["posts_views_tracking_posts"] = isset($_POST['posts_views_tracking_posts']) ? 1 : 0;
                $this->o["posts_views_tracking_pages"] = isset($_POST['posts_views_tracking_pages']) ? 1 : 0;
                $this->o["posts_views_tracking_visitors"] = isset($_POST['posts_views_tracking_visitors']) ? 1 : 0;
                $this->o["posts_views_tracking_users"] = isset($_POST['posts_views_tracking_users']) ? 1 : 0;
                $this->o["users_tracking"] = isset($_POST['users_tracking']) ? 1 : 0;
                $this->o["users_tracking_posts"] = isset($_POST['users_tracking_posts']) ? 1 : 0;
                $this->o["users_tracking_pages"] = isset($_POST['users_tracking_pages']) ? 1 : 0;
                $this->o["posts_views_tracking_ignore"] = $_POST['posts_views_tracking_ignore'];
                $this->o["users_tracking_ignore"] = $_POST['users_tracking_ignore'];

                $this->o["enable_db_autorepair"] = isset($_POST['enable_db_autorepair']) ? 1 : 0;
                $this->o["revisions_to_save"] = $_POST['revisions_to_save'];

                $this->o["php_memory_limit"] = $_POST['php_memory_limit'];
                $this->o["php_memory_limit_enabled"] = isset($_POST['php_memory_limit_enabled']) ? 1 : 0;

                $this->o["integrate_postedit_widget"] = isset($_POST['integrate_postedit_widget']) ? 1 : 0;

                $this->o["rss_header_enable"] = isset($_POST['rss_header_enable']) ? 1 : 0;
                $this->o["rss_footer_enable"] = isset($_POST['rss_footer_enable']) ? 1 : 0;
                $this->o["rss_header_contents"] = stripslashes(htmlentities($_POST['rss_header_contents'], ENT_QUOTES, get_option("blog_charset")));
                $this->o["rss_footer_contents"] = stripslashes(htmlentities($_POST['rss_footer_contents'], ENT_QUOTES, get_option("blog_charset")));
                $this->o["rss_delay_active"] = isset($_POST['rss_delay_active']) ? 1 : 0;
                $this->o["rss_delay_time"] = $_POST['rss_delay_time'];

                update_option("gd-press-tools", $this->o);

                wp_redirect(add_query_arg("settings", "saved"));
                exit();
            }
        }

        function dashboard_operations() {
            if (isset($_GET["gdpt"])) {
                $opr = $_GET["gdpt"];
                switch ($opr) {
                    case "delspam":
                        GDPTDB::delete_all_spam();
                        break;
                    case "delrev":
                        $counter = gd_delete_all_revisions();
                        $this->o["counter_total_revisions"]+= $counter;
                        $this->o["tool_revisions_removed"] = date("r");
                        update_option("gd-press-tools", $this->o);
                        break;
                    case "cledtb":
                        $size = GDPTDB::get_tables_overhead_simple();
                        $this->o["counter_total_overhead"]+= $size;
                        update_option("gd-press-tools", $this->o);
                        gd_optimize_db();
                        break;
                }
                wp_redirect("index.php");
                exit();
            }
        }

        function init_operations() {
            if (isset($_GET["proupgrade"]) && $_GET["proupgrade"] == "hide") {
                $this->o["upgrade_to_pro_40"] = 0;
                update_option("gd-press-tools", $this->o);
                wp_redirect(remove_query_arg("proupgrade"));
                exit;
            }

            if (isset($_GET["gda"])) {
                $gd_action = $_GET["gda"];
                if ($gd_action != '') {
                    switch ($gd_action) {
                        case "unsevt":
                            gd_unschedule_event($_GET['time'], $_GET['job'], $_GET['key']);
                            wp_redirect(remove_query_arg(array('time', 'job', 'gda', 'key'), stripslashes($_SERVER['REQUEST_URI'])));
                            exit();
                            break;
                        case "runevt":
                            $job = $_GET['job'];
                            if ($job == "wp_update_plugins") delete_transient("update_plugins");
                            if ($job == "wp_update_themes") delete_transient("update_themes");
                            if ($job == "wp_version_check") delete_transient("update_core");
                            do_action($job);
                            wp_redirect(remove_query_arg(array('job', 'gda'), stripslashes($_SERVER['REQUEST_URI'])));
                            exit();
                            break;
                        case "delrev":
                            $post_id = $_GET["pid"];
                            $counter = gd_delete_revisions($post_id);
                            $this->o["counter_total_revisions"]+= $counter;
                            wp_redirect(remove_query_arg(array('pid', 'gda'), stripslashes($_SERVER['REQUEST_URI'])));
                            exit();
                            break;
                        case "duplicate":
                            $post_id = $_GET["pid"];
                            $new_id = GDPTDB::duplicate_post($post_id);
                            if ($new_id > 0) wp_redirect(sprintf("post.php?action=edit&post=%s", $new_id));
                            else wp_redirect(remove_query_arg(array('pid', 'gda'), stripslashes($_SERVER['REQUEST_URI'])));
                            exit();
                            break;
                        case "tpldrp":
                            $table = $_GET["name"];
                            gd_db_table_drop($table);
                            wp_redirect(remove_query_arg(array('name', 'gda'), stripslashes($_SERVER['REQUEST_URI'])));
                            exit();
                            break;
                        case "tblemp":
                            $table = $_GET["name"];
                            gd_db_table_empty($table);
                            wp_redirect(remove_query_arg(array('name', 'gda'), stripslashes($_SERVER['REQUEST_URI'])));
                            exit();
                            break;
                    }
                }
            }

            if (isset($_POST['gdpt_tagger_forced'])) {
                $this->o["tagger_abort"] = 1;
                update_option("gd-press-tools", $this->o);
                $this->s["tagger"]["status"] = "idle";
                $this->s["tagger"]["ended"] = time();
                update_option("gd-press-tools-status", $this->s);
                wp_redirect_self();
                exit;
            }

            if (isset($_POST['gdpt_tagger_stop'])) {
                $this->o["tagger_abort"] = 1;
                update_option("gd-press-tools", $this->o);
                wp_redirect_self();
                exit;
            }

            if (isset($_POST['gdpt_dbbackup_delete'])) {
                $files = gdFunctionsGDPT::scan_dir(WP_CONTENT_DIR."/gdbackup/");
                foreach ($files as $fl) {
                    if (substr($fl, 0, 10) == "db_backup_") {
                        unlink(WP_CONTENT_DIR."/gdbackup/".$fl);
                    }
                }
                wp_redirect_self();
                exit;
            }

            if (isset($_POST['gdpt_backup_run'])) {
                $gziped = isset($_POST["backup_compressed"]);
                $backup = new gdMySQLBackup(GDPTDB::get_tables_names(), WP_CONTENT_DIR."/gdbackup/", $gziped);
                $backup->drop_tables = isset($_POST["backup_drop_exists"]);
                $backup->structure_only = isset($_POST["backup_structure_only"]);
                $backup->backup();
                wp_redirect_self();
                exit;
            }

            if (isset($_POST['gdpt_tagger_run'])) {
                if (!isset($this->s["tagger"]) || $this->s["tagger"]["status"]  == "idle") {
                    $this->s["tagger"]["status"] = "scheduled";
                    $this->s["tagger"]["limit"] = $_POST['gdpt_tagger_limit'];
                    $this->s["tagger"]["posts"] = isset($_POST['gdpt_tagger_post']) ? 1 : 0;
                    $this->s["tagger"]["pages"] = isset($_POST['gdpt_tagger_page']) ? 1 : 0;
                    $this->s["tagger"]["start"] = $_POST['gdpt_tagger_start'];
                    $this->s["tagger"]["end"] = $_POST['gdpt_tagger_end'];
                    $this->o["tagger_abort"] = 0;
                    update_option("gd-press-tools-status", $this->s);
                    update_option("gd-press-tools", $this->o);
                    wp_schedule_single_event(time() + 20, 'gdpt_auto_tagger');
                    wp_redirect_self();
                    exit;
                }
            }

            if (isset($_POST['gdpt_revisions_delete'])) {
                $counter = gd_delete_all_revisions();
                $this->o["counter_total_revisions"]+= $counter;
                $this->o["tool_revisions_removed"] = date("r");
                update_option("gd-press-tools", $this->o);
                wp_redirect_self();
                exit;
            }

            if (isset($_POST['gdpt_cmm_set'])) {
                $cmm_date = $_POST["gdpt_cmm_date"];
                $cmm_comments = isset($_POST["gdpt_cmm_comments"]) ? 1 : 0;
                $cmm_pings = isset($_POST["gdpt_cmm_pings"]) ? 1 : 0;
                GDPTDB::set_posts_comments_status($cmm_date, $cmm_comments, $cmm_pings);
                wp_redirect_self();
                exit;
            }

            if (isset($_POST['gdpt_db_clean'])) {
                $size = GDPTDB::get_tables_overhead_simple();
                $this->o["counter_total_overhead"]+= $size;
                update_option("gd-press-tools", $this->o);
                gd_optimize_db();
                wp_redirect_self();
                exit;
            }

            if (isset($_POST['gdpt_admin_rss_cache_reset'])) {
                gd_clear_rss_cache_transient();
                wp_redirect_self();
                exit;
            }

            if (isset($_POST['gdpt_admin_widget_reset'])) {
                gd_reset_widgets();
                wp_redirect_self();
                exit;
            }

            if (isset($_POST['gdpt_admin_avatar_scan'])) {
                update_option('gd-press-tools-avatars', $this->gravatar_folder($this->g));
                wp_redirect_self();
                exit;
            }

            if (isset($_POST['gdpt_admin_rename'])) {
                $this->status = GDPTDB::rename_account($_POST['gdpt_admin_username']);
                if ($this->status == "OK");
                wp_redirect_self();
                exit;
            }

            if (isset($_POST['gdpt_admin_folder_protect'])) {
                gd_create_protection_files();
                wp_redirect_self();
                exit;
            }

            if (isset($_POST['gdpt_posts_delete'])) {
                $results = GDPTDB::delete_posts($_POST['gdpt_delposts_date']);
                $this->status = sprintf(__("Deleted %s posts and %s comments.", "gd-press-tools"), $results["posts"], $results["comments"]);
            }
        }

        function admin_tool_front() {
            $options = $this->o;
            $status = $this->status;
            include($this->plugin_path.'modules/front.php');
        }

        function admin_tool_server() {
            $options = $this->o;
            $status = $this->status;
            include($this->plugin_path.'modules/server.php');
        }

        function admin_tool_hooks() {
            $options = $this->o;
            $status = $this->status;
            include($this->plugin_path.'modules/wp_hooks.php');
        }

        function admin_tool_cron() {
            $options = $this->o;
            $status = $this->status;
            include($this->plugin_path.'modules/cron.php');
        }

        function admin_tool_admin() {
            $options = $this->o;
            $status = $this->status;
            include($this->plugin_path.'modules/admin.php');
        }

        function admin_tool_posts() {
            $options = $this->o;
            $status = $this->status;
            include($this->plugin_path.'modules/posts.php');
        }

        function admin_tool_tagger() {
            $options = $this->o;
            $status = $this->status;
            $s = $this->s;
            include($this->plugin_path.'modules/tagger.php');
        }

        function admin_tool_gopro() {
            $load = "http://www.dev4press.com/wp-content/plugins/gd-product-central/get_lite.php?name=gdpt";
            $response = wp_remote_retrieve_body(wp_remote_get($load));
            echo($response);
        }
        
        function admin_tool_meta() {
            $options = $this->o;
            $status = $this->status;
            $meta = $this->r;
            include($this->plugin_path.'modules/meta.php');
        }

        function admin_tool_post_custom() {
            $options = $this->o;
            $status = $this->status;
            include($this->plugin_path.'modules/custom_post.php');
        }

        function admin_tool_user_custom() {
            $options = $this->o;
            $status = $this->status;
            include($this->plugin_path.'modules/custom_user.php');
        }

        function admin_tool_database() {
            $options = $this->o;
            $status = $this->status;
            include($this->plugin_path.'modules/db.php');
        }

        function admin_tool_rss() {
            $options = $this->o;
            $status = $this->status;
            include($this->plugin_path.'modules/rss.php');
        }

        function admin_tool_global() {
            $options = $this->o;
            $global = $this->a;
            $status = $this->status;
            include($this->plugin_path.'modules/global.php');
        }

        function admin_tool_settings() {
            $options = $this->o;
            $status = $this->status;
            include($this->plugin_path.'modules/settings.php');
        }
    }

    $gdpt_debug = new gdDebugGDPT(PRESSTOOLS_LOG_PATH);
    $gdpt = new GDPressTools();

    function gdpt_upgrade_notice() {
        global $gdpt;
        $gdpt->upgrade_notice();
    }

    /**
    * Writes a object dump into the log file
    *
    * @param string $msg log entry message
    * @param mixed $object object to dump
    * @param string $block adds start or end dump limiters { none | start | end }
    * @param string $mode file open mode
    */
    function wp_gdpt_dump($msg, $obj, $block = "none", $mode = "a+") {
        if (PRESSTOOLS_DEBUG_ACTIVE) {
            global $gdpt_debug;
            $gdpt_debug->dump($msg, $obj, $block, $mode);
        }
    }

    /**
    * Writes a object dump into the log file if the sql logging is active
    *
    * @param string $msg log entry message
    * @param mixed $object object to dump
    * @param string $block adds start or end dump limiters { none | start | end }
    * @param string $mode file open mode
    */
    function wp_gdpt_log_sql($msg, $obj, $block = "none", $mode = "a+") {
        if (PRESSTOOLS_DEBUG_SQL) wp_gdpt_dump($msg, $obj, $block, $mode);
    }
}

?>