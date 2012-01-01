<?php

/**
 * Add capabilites to a role.
 *
 * @param string $role role to add to
 * @param array $caps capabilities to add
 */
function presstools_add_caps_to_role($role, $caps) {
    $role = get_role($role);
    $caps = (array)$caps;
    foreach ($caps as $cap) {
        if (!$role->has_cap($cap)) {
            $role->add_cap($cap);
        }
    }
}

/**
 * Function to find and replace versions of a string targeting the WordPress word.
 *
 * @param string $input text
 * @param string $search_for what to replace
 * @param string $replace_with version fo the word to replace with
 * @return string text with replacement words
 */
function presstools_capital_p($text, $search_for = "wordpress", $replace_with = "WordPress") {
    $regex = '/((?<skip>(src|href)\s*=\s*"[^"]*'.$search_for.')|(?<replace>'.$search_for.'))/i';
    preg_match_all($regex, $text, $matches, PREG_PATTERN_ORDER | PREG_OFFSET_CAPTURE);
    $to_replace = $matches["replace"];
    $rep_length = strlen($replace_with);

    if (is_array($to_replace)) {
        foreach ($to_replace as $r) {
            if (is_array($r)) {
                $text = substr($text, 0, $r[1]).$replace_with.substr($text, $r[1] + $rep_length);
            }
        }
    }

    return $text;
}

/**
 * Get post or posts views for user.
 *
 * @param int $id user ID to get data for, or leave 0 for current user
 * @param bool $all_posts true to get for all posts, false for current or specified
 * @param int $post_id specify the post ID, or leave 0 to get current
 * @return int|array|WP_Error number of views for post, all posts or error if input is invalid. array has post_id as key and views as values.
 */
function presstools_get_user_views($id = 0, $all_posts = false, $post_id = 0) {
    if ($id == 0) {
        global $user_ID;
        if (intval($user_ID) == 0) {
            return new WP_Error("presstools_get_user_views", __("Given user ID is invalid.", "gd-press-tools"));
        }
        $id = $user_ID;
    }
    if (!$all_posts && $post_id == 0) {
        global $post;
        $post_id = $post->ID;
    }
    if (!$all_posts) {
        if ($post_id > 0) {
            $results = GDPTDB::count_user_views_for_post($id, $post_id);
            if (is_object($results) && !is_null($results)) {
                return intval($results->views);
            } else return 0;
        } else {
            return new WP_Error("presstools_get_user_views", __("Given post ID is invalid.", "gd-press-tools"));
        }
    } else {
        $results = GDPTDB::count_user_views($id);
        $output = array();
        if (is_array($results) && !empty($results)) {
            foreach ($results as $row) {
                $output[$row->post_id] = $row->views;
            }
        }
        return $output;
    }
}

/**
 * Get views summary for any given post.
 *
 * @param int $post_id post to get views for
 * @return gdptPostViews|WP_Error views summary or error if post ID is invalid.
 */
function presstools_get_post_views($post_id = 0) {
    if ($post_id == 0) {
        global $post;
        $post_id = $post->ID;
    }
    if ($post_id > 0) {
        $results = gd_count_views($post_id);
        $views = new gdptPostViews($post_id);
        if (is_object($results) && !is_null($results)) {
            $views->users_views = $results->usr_views;
            $views->visitors_views = $results->vst_views;
            $views->total_views = $results->tot_views;
            return $views;
        }
    } else {
        return new WP_Error("presstools_get_post_views", __("Given post ID is invalid.", "gd-press-tools"));
    }
}

/**
 * Unschedule a previously scheduled cron job using job key.
 *
 * @param int $timestamp timestamp for when to run the event.
 * @param string $hook action hook, the execution of which will be unscheduled.
 * @param string $key key for arguments to identify the event.
 */
function gd_unschedule_event($timestamp, $hook, $key) {
    $crons = _get_cron_array();
    unset($crons[$timestamp][$hook][$key]);
    if (empty($crons[$timestamp][$hook])) unset($crons[$timestamp][$hook]);
    if (empty($crons[$timestamp])) unset($crons[$timestamp]);
    _set_cron_array($crons);
}

/**
 * Get shortlink for any post or page.
 *
 * @global GDPressTools $gdpt main plugin object instance
 * @global object $post wordpress post object
 * @param int $post_id id of post or page to get url
 * @return string shortened url
 */
function gd_get_shortlink($post_id = 0) {
    global $gdpt;
    if ($post_id == 0) {
        global $post;
        $post_id = $post->ID;
    }
    return $gdpt->get_shortlink($post_id);
}

/**
 * Return WordPress option without using cache.
 */
function gd_get_option_force($setting) {
    global $wpdb;
    $row = $wpdb->get_row("SELECT option_value FROM $wpdb->options WHERE option_name = '$setting' LIMIT 1");
    $value = is_object($row) ? $row->option_value : "";
    return apply_filters('option_'.$setting, maybe_unserialize($value));
}

/**
 * Return object with page loading stats to a point where the function is called.
 */
function gd_page_stats() {
    return new gdptStats();
}

/**
 * Message to dispay to disabled RSS feeds.
 */
function gd_disable_feed() {
    $feed = __("RSS feed is not available. Instead, please visit our website: ", "gd-press-tools");
    $feed.= '<a href="'.get_bloginfo('url').'">'.get_option('blogname').'</a>';
    wp_die($feed);
}

/**
 * Change the status of post revisions saving
 *
 * @param int $status Status to set (-1, 0, >0)
 */
function gd_set_revisions($status) {
    define('WP_POST_REVISIONS', $status);
}

/**
 * Resets sidebars widgets
 */
function gd_reset_widgets() {
    update_option('sidebars_widgets', $null);
}

/**
 * Optimizes WordPress database tables.
 *
 * @global WPDB $wpdb wordpress database object
 */
function gd_optimize_db() {
    global $wpdb;
    $tables = gd_get_wordpress_tables();
    foreach ($tables as $t) {
        $wpdb->query("OPTIMIZE TABLE `".$t."`");
        $wpdb->query("REPAIR TABLE `".$t."`");
    }
}

/**
 * Clear RSS Feeds cache from the database.
 *
 * @global WPDB $wpdb wordpress database object
 * @global string $table_prefix prefix used for database tables
 */
function gd_clear_rss_cache() {
    global $wpdb, $table_prefix;
    $sql = sprintf("DELETE FROM %soptions WHERE option_name LIKE '%s' and LENGTH(option_name) IN (36, 39)",
        $table_prefix, "rss\_%");
    wp_gdpt_log_sql("CLEAR_RSS_CACHE", $sql);
    $wpdb->query($sql);
}

/**
 * Clear RSS Feeds cache from the database with transient options.
 *
 * @global WPDB $wpdb wordpress database object
 * @global string $table_prefix prefix used for database tables
 */
function gd_clear_rss_cache_transient() {
    global $wpdb, $table_prefix;
    $sql = sprintf("DELETE FROM %soptions WHERE option_name LIKE '%s' or option_name LIKE '%s' or option_name LIKE '%s'",
            $table_prefix, "\_transient\_feed\_%", "\_transient\_rss\_%", "\_transient\_timeout\_%");
    wp_gdpt_log_sql("CLEAR_RSS_CACHE", $sql);
    $wpdb->query($sql);
}

/**
 * Gets the list of all WordPress tables.
 *
 * @global WPDB $wpdb wordpress database object
 * @global string $table_prefix prefix used for database tables
 * @return array list of wordpress tables
 */
function gd_get_wordpress_tables() {
    global $wpdb, $table_prefix;
    $sql = "SHOW TABLES LIKE '".str_replace("_", "\_", $table_prefix)."%'";
    $tables = $wpdb->get_results($sql, ARRAY_N);
    $result = array();
    foreach ($tables as $t) $result[] = $t[0];
    if ($wpdb->usermeta != $table_prefix."usermeta") $result[] = $wpdb->usermeta;
    if ($wpdb->users != $table_prefix."users") $result[] = $wpdb->users;
    return $result;
}

/**
 * Gets the list of all tables in active database.
 *
 * @global WPDB $wpdb wordpress database object
 * @global string $table_prefix prefix used for database tables
 * @return array list of wordpress tables
 */
function gd_get_database_tables() {
    global $wpdb, $table_prefix;
    $sql = "SHOW TABLES";
    $tables = $wpdb->get_results($sql, ARRAY_N);
    $result = array();
    foreach ($tables as $t) $result[] = $t[0];
    return $result;
}

/**
 * Gets the array with columns of the database table.
 *
 * @global WPDB $wpdb wordpress database object
 * @global string $table_prefix prefix used for database tables
 * @return array table columns
 */
function gd_get_database_table_columns($table) {
    global $wpdb, $table_prefix;
    $sql = "SHOW COLUMNS FROM ".$table;
    return $wpdb->get_results($sql);
}

/**
 * Drop database table.
 *
 * @global WPDB $wpdb wordpress database object
 * @global string $table_prefix prefix used for database tables
 * @return array table columns
 */
function gd_db_table_drop($table) {
    global $wpdb, $table_prefix;
    $sql = "DROP TABLE ".$table;
    wp_gdpt_log_sql("DROP_TABLE", $sql);
    $wpdb->query($sql);
}

/**
 * Empty database table.
 *
 * @global WPDB $wpdb wordpress database object
 * @global string $table_prefix prefix used for database tables
 * @return array table columns
 */
function gd_db_table_empty($table) {
    global $wpdb, $table_prefix;
    $sql = "TRUNCATE ".$table;
    wp_gdpt_log_sql("TRUNCATE_TABLE", $sql);
    $wpdb->query($sql);
}

/**
 * Get total number of revisions for all posts.
 *
 * @global WPDB $wpdb wordpress database object
 * @global string $table_prefix prefix used for database tables
 * @return int number of revisions
 */
function gd_count_revisions_total() {
    global $wpdb, $table_prefix;

    $sql = sprintf("select count(*) as revisions from %sposts p inner join %sposts r on p.post_parent = r.ID where p.post_type = 'revision' and r.post_status = 'publish'", $table_prefix, $table_prefix);
    return $wpdb->get_var($sql);
}

/**
 * Get the number of spam comments for all posts.
 *
 * @return int number of spam comments
 */
function gd_count_spam_total() {
    $cmm = wp_count_comments();
    return $cmm->spam;
}

/**
 * Get revisions count for the post.
 *
 * @global WPDB $wpdb wordpress database object
 * @global string $table_prefix prefix used for database tables
 * @param int $post_id post id to get revisions from
 * @return int number of revisions
 */
function gd_count_revisions($post_id) {
    global $wpdb, $table_prefix;

    $sql = sprintf("select count(*) as revisions from %sposts where post_type = 'revision' and post_parent = %s", $table_prefix, $post_id);
    return $wpdb->get_var($sql);
}

/**
 * Get views count for the post.
 *
 * @global WPDB $wpdb wordpress database object
 * @global string $table_prefix prefix used for database tables
 * @param int $post_id post id to get revisions from
 * @return object views results
 */
function gd_count_views($post_id) {
    global $wpdb, $table_prefix;

    $sql = sprintf("select p.ID as post_id, sum(v.usr_views) as usr_views, sum(v.vst_views) as vst_views, sum(v.usr_views + v.vst_views) as tot_views from %sposts p left join %sgdpt_posts_views v on v.post_id = p.ID where p.ID = %s group by p.ID",
        $table_prefix, $table_prefix, $post_id);
    return $wpdb->get_row($sql);
}

/**
 * Deletes all revisions for all published posts.
 *
 * @global WPDB $wpdb wordpress database object
 * @global string $table_prefix prefix used for database tables
 * @return int number of deleted revisions
 */
function gd_delete_all_revisions() {
    global $wpdb, $table_prefix;

    $sql = sprintf("delete %s, %s, %s from %sposts p inner join %sposts r on p.post_parent = r.ID left join %sterm_relationships t on t.object_id = p.ID left join %spostmeta m on m.post_id = p.ID where p.post_type = 'revision' and r.post_status = 'publish'",
        gdFunctionsGDPT::mysql_pre_4_1() ? sprintf("%sposts", $table_prefix) : "p",
        gdFunctionsGDPT::mysql_pre_4_1() ? sprintf("%sterm_relationships", $table_prefix) : "t",
        gdFunctionsGDPT::mysql_pre_4_1() ? sprintf("%spostmeta", $table_prefix) : "m",
        $table_prefix, $table_prefix, $table_prefix, $table_prefix);
    $wpdb->query($sql);
    wp_gdpt_log_sql("DELETE_REVISIONS_ALL", $sql);
    return $wpdb->rows_affected;
}

/**
 * Deletes revisions for selected post.
 *
 * @global WPDB $wpdb wordpress database object
 * @global string $table_prefix prefix used for database tables
 * @param int $post_id post id to delete revisions from
 * @return int number of deleted revisions
 */
function gd_delete_revisions($post_id) {
    global $wpdb, $table_prefix;

    $sql = sprintf("delete %s, %s, %s from %sposts p left join %sterm_relationships t on t.object_id = p.ID left join %spostmeta m on m.post_id = p.ID where p.post_type = 'revision' and p.post_parent = %s",
        gdFunctionsGDPT::mysql_pre_4_1() ? sprintf("%sposts", $table_prefix) : "p",
        gdFunctionsGDPT::mysql_pre_4_1() ? sprintf("%sterm_relationships", $table_prefix) : "t",
        gdFunctionsGDPT::mysql_pre_4_1() ? sprintf("%spostmeta", $table_prefix) : "m",
        $table_prefix, $table_prefix, $table_prefix, $post_id);
    $wpdb->query($sql);
    wp_gdpt_log_sql("DELETE_REVISIONS_FOR_POST", $sql);
    return $wpdb->rows_affected;
}

/**
 * Creates index.php for protecting wp-content folders.
 */
function gd_create_protection_files() {
    $wp["plugins"] = trailingslashit(dirname(PRESSTOOLS_PATH));
    $wp["content"] = trailingslashit(dirname($wp["plugins"]));
    $wp["themes"] = $wp["content"]."themes/";

    $index = file_get_contents(PRESSTOOLS_PATH."files/index.php");
    $index = str_replace('%URL%', get_bloginfo("url"), $index);

    foreach ($wp as $folder) {
        $path = $folder."index.php";
        if (!file_exists($path) && is_writable($folder)) {
            $f = fopen($path, "a+");
            fwrite ($f, "$index");
            fclose($f);
        }
    }
}

/**
 * Creates index.php for protecting single folder.
 */
function gd_create_protection_file($folder) {
    $index = file_get_contents(PRESSTOOLS_PATH."files/index.php");
    $index = str_replace('%URL%', get_bloginfo("url"), $index);
    $path = $folder."index.php";
    if (!file_exists($path) && is_writable($folder)) {
        $f = fopen($path, "a+");
        fwrite ($f, "$index");
        fclose($f);
    }
}

if (!function_exists("update_user_meta")) {
    function update_user_meta($user_id, $meta_key, $meta_value, $prev_value = '') {
        return update_usermeta($user_id, $meta_key, $meta_value);
    }
}

if (!function_exists("get_user_meta")) {
    function get_user_meta($user_id, $key, $single = false) {
        return get_usermeta($user_id, $meta_key = '');
    }
}

if (!function_exists("is_current_user_admin")) {
    /**
     * Checks to see if the currently logged user is admin.
     *
     * @return bool is user admin or not
     */
    function is_current_user_admin() {
        return is_current_user_role();
    }
}

if (!function_exists("is_current_user_role")) {
    /**
     * Checks to see if the currently logged user has a given role.
     *
     * @return bool is user has a given role or not
     */
    function is_current_user_role($role = "administrator") {
        global $current_user;
        if (is_array($current_user->roles)) {
            return in_array($role, $current_user->roles);
        } else return false;
    }
}

?>