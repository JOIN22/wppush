<?php

class GDPTDB {
    function duplicate_post($old_id) {
        global $wpdb, $table_prefix;

        $sql = sprintf("select * from %sposts where ID = %s", $table_prefix, $old_id);
        $old_post = $wpdb->get_row($sql);
        $sql = sprintf("select * from %spostmeta where post_id = %s", $table_prefix, $old_id);
        $old_meta = $wpdb->get_results($sql);
        $sql = sprintf("select * from %sterm_relationships where object_id = %s", $table_prefix, $old_id);
        $old_term = $wpdb->get_results($sql);
        if (defined('STARRATING_INSTALLED')) {
            $sql = sprintf("select * from %sgdsr_data_article where post_id = %s", $table_prefix, $old_id);
            $old_gdsr = $wpdb->get_row($sql);
        }

        $post_date = current_time('mysql');
        $post_date_gmt = get_gmt_from_date($post_date);

        unset($old_post->ID);
        unset($old_post->filter);
        unset($old_post->guid);
        unset($old_post->post_date_gmt);

        $old_post->post_date = $post_date;
        $old_post->post_status = "draft";
        $old_post->post_title.= " (1)";
        $old_post->post_name.= "-1";
        $old_post->post_modified = $post_date;
        $old_post->post_modified_gmt = $post_date_gmt;
        $old_post->comment_count = "0";

        if (false === $wpdb->insert($wpdb->posts, get_object_vars($old_post))) return 0;
        $post_ID = (int)$wpdb->insert_id;

        $wpdb->update($wpdb->posts, array('guid' => get_permalink($post_ID)), array('ID' => $post_ID));

        foreach ($old_meta as $m) {
            unset($m->meta_id);
            $m->post_id = $post_ID;
            $wpdb->insert($wpdb->postmeta, get_object_vars($m));
        }
        foreach ($old_term as $m) {
            unset($m->meta_id);
            $m->object_id = $post_ID;
            $wpdb->insert($wpdb->term_relationships, get_object_vars($m));
        }

        if (is_object($old_gdsr)) {
            $old_gdsr->post_id = $post_ID;
            unset($old_gdsr->user_voters);
            unset($old_gdsr->user_votes);
            unset($old_gdsr->visitor_voters);
            unset($old_gdsr->visitor_votes);
            unset($old_gdsr->review);
            unset($old_gdsr->views);
            unset($old_gdsr->user_recc_plus);
            unset($old_gdsr->user_recc_minus);
            unset($old_gdsr->visitor_recc_plus);
            unset($old_gdsr->visitor_recc_minus);
            unset($old_gdsr->last_voted);
            unset($old_gdsr->last_voted_recc);
            $wpdb->insert($table_prefix."gdsr_data_article", get_object_vars($old_gdsr));
        }

        return $post_ID;
    }

    function count_views($post_id) {
        global $wpdb, $table_prefix;

        $sql = sprintf("select v.post_id, sum(v.usr_views) as usr_views, sum(v.vst_views) as vst_views, sum(v.usr_views + v.vst_views) as tot_views from %sgdpt_posts_views v where v.post_id = %s", $table_prefix, $post_id);
        return $wpdb->get_row($sql);
    }

    function count_user_views_for_post($id, $post_id) {
        global $wpdb, $table_prefix;

        $sql = sprintf("select v.user_id, v.post_id, sum(v.views) as views from %sgdpt_users_tracking v where v.post_id = %s and v.user_id = %s", $table_prefix, $post_id, $id);
        return $wpdb->get_row($sql);
    }

    function count_user_views($id) {
        global $wpdb, $table_prefix;

        $sql = sprintf("select v.user_id, v.post_id, sum(v.views) as views from %sgdpt_users_tracking v where v.user_id = %s group by post_id order by post_id asc", $table_prefix, $id);
        return $wpdb->get_results($sql);
    }

    function get_cron_elements($info) {
        global $wpdb;

        $filter = $where = array();
        if ($info["posts"] == 1) $filter[] = "'post'";
        if ($info["pages"] == 1) $filter[] = "'page'";
        if ($info["start"] > 0) $where[] = "ID >= ".$info["start"];
        if ($info["end"] > 0) $where[] = "ID < ".$info["end"];
        $where = count($where) == 0 ? "" : " and ".join(" and ", $where);

        $sql = sprintf("select ID, post_title, post_content from $wpdb->posts where post_status = 'publish' and post_type in (%s)%s order by ID asc", join(", ", $filter), $where);
        wp_gdpt_log_sql("TAGGER_CRON_POSTS", $sql);
        return $wpdb->get_results($sql);
    }

    function get_all_pined($pins = array()) {
        if (count($pins) == 0) return array();
        global $wpdb, $table_prefix;
        if (!is_array($pins)) $pins = array();

        $where = " where ID in (".join(", ", $pins).")";
        $sql = sprintf("select * from %sposts%s", $table_prefix, $where);
        return $wpdb->get_results($sql);
    }

    function get_all_users_comments_count() {
        global $wpdb, $table_prefix;
        $users = array();

        $sql = sprintf("select user_id, count(*) as cmms from %scomments where user_id > 0 group by user_id order by user_id asc", $table_prefix);
        $res = $wpdb->get_results($sql);

        foreach ($res as $r) $users[$r->user_id] = $r->cmms;
        return $users;
    }

    function render_meta_robots($name, $value, $extra = "") {
?>
        <select name="<?php echo $name; ?>"<?php echo $extra; ?>>
            <option value=""><?php _e("None", "gd-press-tools"); ?></option>
            <option value="index,follow"<?php echo $value == "index,follow" ? ' selected="selected"' : ''; ?>><?php _e("Index, Follow", "gd-press-tools"); ?></option>
            <option value="index,nofollow"<?php echo $value == "index,nofollow" ? ' selected="selected"' : ''; ?>><?php _e("Index, No follow", "gd-press-tools"); ?></option>
            <option value="noindex,follow"<?php echo $value == "noindex,follow" ? ' selected="selected"' : ''; ?>><?php _e("No index, Follow", "gd-press-tools"); ?></option>
            <option value="noindex,nofollow"<?php echo $value == "noindex,nofollow" ? ' selected="selected"' : ''; ?>><?php _e("No index, No follow", "gd-press-tools"); ?></option>
        </select>
<?php
    }

    function get_php_gd_version() {
        $gdi = gd_info();
        return $gdi["GD Version"];
    }

    function insert_posts_views($post_id, $user = true) {
        global $wpdb, $table_prefix;

        $day = date("Y-m-d");
        $sql = sprintf("update %sgdpt_posts_views set %s_views = %s_views + 1 where post_id = %s and day = '%s'",
            $table_prefix, $user ? "usr" : "vst", $user ? "usr" : "vst", $post_id, $day);
        $wpdb->query($sql);
        if ($wpdb->rows_affected == 0) {
            $sql = sprintf("insert into %sgdpt_posts_views (post_id, day, %s_views) values (%s, '%s', 1)",
                $table_prefix, $user ? "usr" : "vst", $post_id, $day);
            $wpdb->query($sql);
        }
    }

    function insert_users_tracking($post_id, $user_id) {
        global $wpdb, $table_prefix;

        $day = date("Y-m-d");
        $sql = sprintf("update %sgdpt_users_tracking set views = views + 1 where user_id = %s and post_id = %s and day = '%s'",
            $table_prefix, $user_id, $post_id, $day);
        $wpdb->query($sql);
        if ($wpdb->rows_affected == 0) {
            $sql = sprintf("insert into %sgdpt_users_tracking (user_id, post_id, day, views) values (%s, %s, '%s', 1)",
                $table_prefix, $user_id, $post_id, $day);
            $wpdb->query($sql);
        }
    }

    function set_posts_comments_status($date, $comments = 0, $pings = 0) {
        global $wpdb, $table_prefix;

        $set = array();
        if ($comments == 1) $set[] = "comment_status = 'closed'";
        if ($pings == 1) $set[] = "ping_status = 'closed'";
        if ($date != "" && count($set) > 0) {
            $sql = sprintf("update %sposts %s where post_date < '%s'", $table_prefix, join(", ", $set), $date);
            $wpdb->query($sql);
        }
    }

    function get_posts_for_delete($post_date) {
        global $wpdb, $table_prefix;

        $sql = sprintf("select id from %sposts where post_type = 'post' and post_status = 'publish' and post_date < '%s'", $table_prefix, $post_date);
        wp_gdpt_log_sql("DELETE_POSTS_GET_IDS", $sql);
        $ids_raw = $wpdb->get_results($sql);
        $ids = array();
        foreach ($ids_raw as $id) $ids[] = $id->id;
        return $ids;
    }

    function delete_all_spam() {
        global $wpdb;

        $deleted_spam = $wpdb->query("DELETE FROM $wpdb->comments WHERE comment_approved = 'spam'");
        $wpdb->query($deleted_spam);
    }

    function delete_posts($post_date) {
        global $wpdb, $table_prefix;

        $ids = GDPTDB::get_posts_for_delete($post_date);
        $results["posts"] = count($ids);
        $ids = join(",", $ids);
        $sql = sprintf("delete from %scomments where comment_post_id in (%s)", $table_prefix, $ids);
        wp_gdpt_log_sql("DELETE_POSTS_COMMENTS", $sql);
        $wpdb->query($sql);
        $results["comments"] = $wpdb->rows_affected;
        $sql = sprintf("delete %s, %s, %s from %sposts p left join %sterm_relationships t on t.object_id = p.ID left join %spostmeta m on m.post_id = p.ID where p.ID in (%s)",
            gdFunctionsGDPT::mysql_pre_4_1() ? sprintf("%sposts", $table_prefix) : "p",
            gdFunctionsGDPT::mysql_pre_4_1() ? sprintf("%sterm_relationships", $table_prefix) : "t",
            gdFunctionsGDPT::mysql_pre_4_1() ? sprintf("%spostmeta", $table_prefix) : "m",
            $table_prefix, $table_prefix, $table_prefix, $ids);
        wp_gdpt_log_sql("DELETE_POSTS_POSTS", $sql);
        $wpdb->query($sql);
        $sql = sprintf("delete %s, %s, %s from %sposts p left join %sterm_relationships t on t.object_id = p.ID left join %spostmeta m on m.post_id = p.ID where p.post_type = 'revision' and p.post_parent in (%s)",
            gdFunctionsGDPT::mysql_pre_4_1() ? sprintf("%sposts", $table_prefix) : "p",
            gdFunctionsGDPT::mysql_pre_4_1() ? sprintf("%sterm_relationships", $table_prefix) : "t",
            gdFunctionsGDPT::mysql_pre_4_1() ? sprintf("%spostmeta", $table_prefix) : "m",
            $table_prefix, $table_prefix, $table_prefix, $ids);
        wp_gdpt_log_sql("DELETE_POSTS_REST", $sql);
        $wpdb->query($sql);
        return $results;
    }

    function get_mysql_server_info() {
        global $wpdb, $table_prefix;
        return $wpdb->get_results("SHOW VARIABLES");
    }

    function rename_account($new_username, $old_username = "") {
        global $wpdb, $table_prefix;

        if ($old_username == "") {
            global $userdata;
            $old_username = $userdata->user_login;
            $user_id = $userdata->ID;
        } else {
            $old_user = get_user_by("login", $old_username);
            $user_id = $old_user->ID;
        }

        $new_username = apply_filters('pre_user_login', $new_username);
        if (!validate_username($new_username)) return __("Username is invalid.", "gd-press-tools");
        if (strcasecmp($old_username, $new_username) == 0) return __("Username is the same. Nothing is changed.", "gd-press-tools");
        if (username_exists($new_username) != null) return __("Choosen username already exists.", "gd-press-tools");

        $user_nicename = sanitize_title($new_username);
	$user_nicename = apply_filters('pre_user_nicename', $user_nicename);

        $sql = sprintf("update %s set user_login = '%s', user_nicename = '%s' where ID = %s", $wpdb->users, $new_username, $user_nicename, $user_id);
        $wpdb->query($sql);
        $sql = sprintf("update %scomments set comment_author = '%s' where comment_author = '%s' and user_id = %s", $table_prefix, $new_username, $old_username, $user_id);
        $wpdb->query($sql);
        return "OK";
    }

    function get_tables_status() {
        global $wpdb;
        $prefix = $wpdb->prefix;

        $sql = "SHOW TABLE STATUS LIKE '".str_replace("_", "\_", $prefix)."%'";
        $tables = $wpdb->get_results($sql);
        if ($wpdb->usermeta != $prefix."usermeta") $tables[] = GDPTDB::get_table_status($wpdb->usermeta);
        if ($wpdb->users != $prefix."users") $tables[] = GDPTDB::get_table_status($wpdb->users);

        return $tables;
    }

    function get_tables_names() {
        global $wpdb, $table_prefix;
        $sql = "SHOW TABLES LIKE '".str_replace("_", "\_", $table_prefix)."%'";
        $tables = $wpdb->get_results($sql, ARRAY_N);
        $results = array();
        foreach ($tables as $tbl) $results[] = $tbl[0];
        if (!in_array($wpdb->usermeta, $results)) $results[] = $wpdb->usermeta;
        if (!in_array($wpdb->users, $results)) $results[] = $wpdb->users;
        return $results;
    }

    function get_table_status($tbl) {
        global $wpdb, $table_prefix;
        $sql = "SHOW TABLE STATUS LIKE '".$tbl."'";
        return $wpdb->get_row($sql);
    }

    function get_tables_size() {
        $tables = GDPTDB::get_tables_status();
        $size = 0;
        foreach ($tables as $t) {
            $size += $t->Data_length;
        }
        return gdFunctionsGDPT::size_format($size);
    }

    function get_database_size() {
        global $wpdb;
        $tables = $wpdb->get_results("SHOW TABLE STATUS");
        $size = 0;
        foreach ($tables as $t) {
            $size += $t->Data_length;
        }
        return gdFunctionsGDPT::size_format($size);
    }

    function get_tables_overhead_simple() {
        $tables = GDPTDB::get_tables_status();
        $size = 0;
        foreach ($tables as $t) {
            $size += $t->Data_free;
        }
        return $size;
    }

    function get_tables_overhead() {
        $size = GDPTDB::get_tables_overhead_simple();
        return gdFunctionsGDPT::size_format($size);
    }

    function check_table($table) {
        global $wpdb, $table_prefix;
        $sql = "CHECK TABLE ".$table;
        return $wpdb->get_row($sql);
    }
}

class gdMySQLBackup {
    var $f;

    var $tables = array();
    var $gziped = true;
    var $insert_limit = 65536;
    var $drop_tables = true;
    var $structure_only = false;
    var $comments = true;
    var $folder = '';
    var $error = '';

    function gdMySQLBackup($tables = array(), $folder = "", $gziped = true) {
        $this->tables = $tables;
        $this->folder = $folder;
        $this->gziped = $gziped;
        if (!function_exists('gzopen')) $this->gziped = false;
    }

    function open_file($file_name) {
        if ($this->gziped) {
            $this->f = @gzopen($file_name, "w");
        } else {
            $this->f = @fopen($file_name, "w");
        }
    }

    function close_file() {
        if ($this->gziped) {
            gzclose($this->f);
        } else {
            fclose($this->f);
        }
    }

    function w($el) {
        if ($this->gziped) {
            @gzwrite($this->f, $el);
        } else {
            @fwrite($this->f, $el);
        }
    }

    function backup() {
        $file_name = $this->folder."db_backup_".date('ymd_His').'.sql';
        if ($this->gziped) $file_name.= ".gz";

        $this->open_file($file_name);
        $this->generate();
        $this->close_file();
    }

    function table_dump($table) {
        global $wpdb;
        $wpdb->query('LOCK TABLES '.$table.' WRITE');

        $value = '# Table structure for table `'.$table.'`'.PRESSTOOLS_LINE_ENDING.PRESSTOOLS_LINE_ENDING;
        if ($this->drop_tables) $value.= 'DROP TABLE IF EXISTS `'.$table.'`;'.PRESSTOOLS_LINE_ENDING;
        $row = $wpdb->get_row('SHOW CREATE TABLE '.$table, ARRAY_A);
        $value.= str_replace("\n", PRESSTOOLS_LINE_ENDING, $row['Create Table']).';'.PRESSTOOLS_LINE_ENDING.PRESSTOOLS_LINE_ENDING;
        $this->w($value);

        if (!$this->structure_only) {
            $this->w('# Table data for table `'.$table.'`'.PRESSTOOLS_LINE_ENDING.PRESSTOOLS_LINE_ENDING);
            $this->table_data($table);
        }

        $this->w(PRESSTOOLS_LINE_ENDING.PRESSTOOLS_LINE_ENDING);
        $wpdb->query('UNLOCK TABLES');
        return $value;
    }

    function table_data($table) {
        global $wpdb;
        $result = @mysql_query("select * from ".$table, $wpdb->dbh);
        $line = '';
        $first = true;

        while ($row = mysql_fetch_row($result)) {
            $values = '(';
            foreach ($row as $data) $values.= '\''.addslashes($data).'\', ';
            $values = substr($values, 0, -2).')';
            if ($first) {
                $line.= 'INSERT INTO '.$table.' VALUES '.$values;
                $first = false;
            } else $line.= ', '.$values;
            if (strlen($line) > $this->insert_limit) {
                $line.= ';'.PRESSTOOLS_LINE_ENDING;
                $first = true;
                $this->w($line);
                $line = '';
            }
        }

        if ($line != '') $this->w($line.';'.PRESSTOOLS_LINE_ENDING);
    }

    function generate() {
        $value = "# WordPress MySQL database dump".PRESSTOOLS_LINE_ENDING;
        $value.= "# Generated by GD Press Tools ".PRESSTOOLS_INSTALLED.PRESSTOOLS_LINE_ENDING;
        $value.= "# http://www.dev4press.com/".PRESSTOOLS_LINE_ENDING;
        $value.= "#".PRESSTOOLS_LINE_ENDING;
        $value.= "# MySQL version: ".mysql_get_server_info().PRESSTOOLS_LINE_ENDING;
        $value.= "# PHP version: ".phpversion().PRESSTOOLS_LINE_ENDING;
        $value.= PRESSTOOLS_LINE_ENDING.PRESSTOOLS_LINE_ENDING;
        $this->w($value);

        foreach ($this->tables as $table) {
            $this->table_dump($table);
        }
    }
}

?>