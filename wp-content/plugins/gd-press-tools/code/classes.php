<?php

class gdptPostViews {
    var $post_id;
    var $users_views;
    var $visitors_views;
    var $total_views;

    function gdptPostViews($post_id) {
        $this->post_id = $post_id;
    }
}

class gdptStats {
    var $memory = "0";
    var $queries = "0";
    var $timer = "0";

    function gdptStats() {
        if (function_exists("memory_get_usage"))
            $this->memory = gdFunctionsGDPT::size_format(memory_get_usage());

        $this->queries = get_num_queries();
        $this->timer = timer_stop(1);
    }
}

class gdptAvatar {
    var $include = true;
    var $name = "";
    var $file = "";

    function gdptAvatar() { }

    function get_url() {
        return WP_CONTENT_URL."/avatars/".$this->file;
    }
}

?>