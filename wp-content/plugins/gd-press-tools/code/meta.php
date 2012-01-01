<?php

$gdpt_robots_values = array(
    "index",
    "noindex",
    "follow",
    "nofollow",
    "noydir",
    "noodp",
    "noarchive",
    "noimageindex"
);

$gdpt_robots_insert = array(
    "front" => __("Front Page", "gd-press-tools"),
    "post" => __("Single Post", "gd-press-tools"),
    "page" => __("Single Page", "gd-press-tools"),
    "search" => __("Search Results Page", "gd-press-tools"),
    "archive_tag" => __("Tag Archives", "gd-press-tools"),
    "archive_cat" => __("Category Archives", "gd-press-tools"),
    "archive_date" => __("Date based Archives", "gd-press-tools"),
    "archive_author" => __("Author Archives", "gd-press-tools"),
    "login" => __("Login Page", "gd-press-tools"),
    "admin" => __("Administration Pages", "gd-press-tools")
);

function add_meta_tag_robots($robots) {
    if (is_single() || is_page()) {
        global $post;
        $post_page_added = false;
        $post_robots = get_post_meta($post->ID, "_gdpt_meta_robots", true);
        if (!empty($post_robots)) {
            $post_page_added = true;
            print_robots_tag($post_robots);
        }
        if (!$post_page_added) {
            if (is_single()) print_robots_tag(get_robots_value($robots, "post"));
            if (is_page()) print_robots_tag(get_robots_value($robots, "page"));
        }
    }
    else if (is_front_page()) print_robots_tag(get_robots_value($robots, "front"));
    else if (is_search()) print_robots_tag(get_robots_value($robots, "search"));
    else if (is_tag()) print_robots_tag(get_robots_value($robots, "archive_tag"));
    else if (is_category()) print_robots_tag(get_robots_value($robots, "archive_cat"));
    else if (is_date()) print_robots_tag(get_robots_value($robots, "archive_date"));
    else if (is_author()) print_robots_tag(get_robots_value($robots, "archive_author"));
}

function print_robots_tag($value) {
    if (trim($value) != "") {
        echo "\r\n";
        echo '<meta name="robots" content="'.$value.'" />';
        echo "\r\n";
    }
}

function get_robots_value($robots, $name) {
    if (isset($robots[$name]) && isset($robots[$name]["active"]) && $robots[$name]["active"] == 1) {
        $values = array();
        foreach ($robots[$name] as $value => $status) {
            if ($status == 1 && $value != "active") $values[] = $value;
        }
        return join(",", $values);
    } else return "";
}

function get_default_meta_robots() {
    global $gdpt_robots_values, $gdpt_robots_insert;
    $robots = array();
    foreach ($gdpt_robots_insert as $name => $var) {
        $row = array();
        $row["active"] = 0;
        foreach ($gdpt_robots_values as $value) $row[$value] = 0;
        $robots[$name] = $row;
    }
    return $robots;
}

?>