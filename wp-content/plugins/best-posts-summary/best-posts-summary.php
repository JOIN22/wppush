<?php
/*
Plugin Name: Best Posts Summary
Plugin URI: http://helpdeskgeek.com/wp-best-posts-summary/
Description: Create summary posts with the Best of Month, Week, or Day content. Based on number of comments or popularity (visits)
Version: 1.0
Author: Aseem Kishore
Author URI: http://helpdeskgeek.com
*/

if (!class_exists('tech_bestofwp')) {

define("BP_FREQUENCY_NEVER", 0);
define("BP_FREQUENCY_DAILY", 1);
define("BP_FREQUENCY_WEEKLY", 2);
define("BP_FREQUENCY_MONTHLY", 3);

define("BP_CRITERIA_COMMENTS", 1);
define("BP_CRITERIA_VIEWS", 2);

define("BP_ORDERING_TITLE", 1);
define("BP_ORDERING_DATE", 2);
define("BP_ORDERING_RANDOM", 3);
define("BP_ORDERING_CRITERIA", 4);

class tech_bestofwp{
    function tech_bestofwp() {
        /* Initializes the plugin. */
        register_activation_hook( __FILE__ , array(&$this , 'upon_activation') );
        register_deactivation_hook( __FILE__ , array(&$this , 'upon_deactivation') );
        add_action('admin_menu', array( &$this , 'admin_menu') );
        add_action('wpbo_on_cron_event', array(&$this, 'on_cron_event'));
    }

    function is_popularity_contest_installed() {
        /* Return true if the "Popularity Contest" plugin is installed */

        global $table_prefix;
        
        $active_plugins = get_option('active_plugins');
        $popularity = false;

        foreach ( $active_plugins as $p) {
            if(substr_count($p , "popularity-contest.php")) {
                $popularity = true; 
                break;
            }    
        }
        
        if($popularity){
            list($mpost) = mysql_fetch_row(
                mysql_query("SELECT MAX(post_id) as mx from ".
                            $table_prefix."ak_popularity"));
            if($mpost == "") $popularity = false;
        }
        
        return $popularity;
    }
    
    function should_post($timestamp) {
        /* Returns true if a new summary post should be added at the given time */
        $wpbo_frequency = get_option('wpbo_frequency');
        if ($wpbo_frequency == BP_FREQUENCY_NEVER)
            return;

        $prev_done = get_option('wpbo_prev_done');
        if ($prev_done == date("Y-m-d", $timestamp))
            return false;
         
        $day_of_week = date('N', $timestamp);
        $day = date('d', $timestamp);
        $day = 1;

        $result = (
            (($wpbo_frequency == BP_FREQUENCY_MONTHLY) && ($day == 1)) ||
            (($wpbo_frequency == BP_FREQUENCY_WEEKLY) && ($day_of_week == 1)) ||
            (($wpbo_frequency == BP_FREQUENCY_DAILY))
        );
        return $result;
    }

    function on_cron_event() {
        /* Runs daily */
        if (!$this->should_post(time())) {
            return;
        }
        $this->load_options();
        $end_time = date('Y-m-d 00:00:00');
        $yesterday = strtotime('yesterday 00:00:00');
        if ( $this->period == BP_FREQUENCY_DAILY ) {
            $start_time = date('Y-m-d 00:00:00', $yesterday);
        } elseif ( $this->period == BP_FREQUENCY_WEEKLY ) {
            $start_time = date('Y-m-d 00:00:00', strtotime('-7days'));
        } elseif ( $this->period == BP_FREQUENCY_MONTHLY ) {
            // yesterday was the last day of the previous month, modify the day to 01
            $start_time = date('Y-m-01 00:00:00', $yesterday);
        } else {
            return;
        }


        $posts = $this->get_posts($start_time, $end_time);
        $this->create_post(strtotime($start_time), strtotime($end_time), $posts);
        update_option('wpbo_prev_done'  , date("Y-m-d"));
    }

    function load_options() {
        /* Loads options as attributes */

        $this->frequency = get_option('wpbo_frequency');
        $this->period = get_option('wpbo_period');
        $this->num_posts = intval(get_option('wpbo_posts'));
        $this->post_title = stripslashes(get_option('wpbo_post_title'));
        $this->ordering = get_option('wpbo_ordering');
        $this->criteria = get_option('wpbo_criteria');
        $this->template = stripslashes(get_option('wpbo_text'));
        $this->max_words = intval(get_option('wpbo_wordnumber'));
        $this->last_posts = get_option('wpbo_last_posts');
        $this->strict = get_option('wpbo_strict');
            
        $wpbo_categories = get_option('wpbo_categories');
        if ($wpbo_categories) {
            $expcats = explode("," , $wpbo_categories);
            $wpbo_categories = array();
            foreach ( $expcats as $p ) {
                if ($p) $wpbo_categories[] = str_replace("~" , "" , $p);
            }
        }
        $this->post_cats = $wpbo_categories;
    }

    function create_post($start_time , $run_time , $posts = array()){
        /* creates a new summary post */
        global $table_prefix;
        
        $t1 = explode("[loop_start]" , $this->template);
        $t2 = explode("[loop_end]" , "[loop_start]".$t1[1]);
        $template = $t2[0]."[loop_end]";
        $otemplate = str_replace(array("[loop_start]" , "[loop_end]") , "" , $template);
        
        $allposts = "";
        foreach ($posts as $post) {
            $thispost = $otemplate;
            
            $expl = explode(" " , $post['date']);
            $dt = $expl[0];
            $tm = $expl[1];
            $expl = explode("-" , $dt);
            $expl2 = explode(":" , $tm);
            $mkt = mktime($expl2[0] , $expl2[1] , $expl2[2] , $expl[1] , $expl[2] , $expl[0]);
            
            $thispost = str_replace("[link]" , $post['permalink'] , $thispost);
            $thispost = str_replace("[title]" , $post['title'] , $thispost);
            $thispost = str_replace("[text]" , $post['content'] , $thispost);
            $thispost = str_replace("[category]" , $post['cat'] , $thispost);
            $thispost = str_replace("[author]" , $post['author'] , $thispost);
            $thispost = str_replace("[date]" , date("l, F jS, Y" , $mkt) , $thispost);
            $thispost = str_replace("[VisitsOrComments]" , $post['clabel'] , $thispost);
            $thispost = str_replace("[count]" , $post['count'] , $thispost);
            
            $allposts .= $thispost;
        }
        $post = str_replace($template , $allposts , $this->template);
        
        $post = mysql_real_escape_string(str_replace(array("\n" , "\r") , "" , $post));
        
        $period = date("D" , $start_time); 
        $post_title = str_replace("[day]" , $period , $this->post_title);
        
        $period = date("jS" , $start_time);
        $post_title = str_replace("[date]" , $period , $post_title);
        
        $period = date("jS F" , $start_time)." - ".date("jS F" , ($start_time + (6*86400)));
        $post_title = str_replace("[week]" , $period , $post_title);
        
        $period = date("M" , $start_time); 
        $post_title = str_replace("[month]" , $period , $post_title);
        
        $period = date("Y" , $start_time); 
        $post_title = str_replace("[year]" , $period , $post_title);
        
        $post_title = str_replace("[blog-name]" , get_option('blogname') , $post_title);
        
        list($aid) = mysql_fetch_row(mysql_query("SELECT user_id from ".$table_prefix."usermeta WHERE meta_key = 'wp_capabilities' and meta_value LIKE '%administrator%' order by user_id asc limit 1"));
        
        $gmt_date = get_gmt_from_date(date("Y-m-d G:i:s" , $run_time));
        $post_name = sanitize_title($post_title);
        
        $tpost[post_title] = mysql_real_escape_string(str_replace(array("\n" , "\r") , "" , $post_title));
        $tpost[post_content] = $post;
        $tpost[post_date] = date("Y-m-d G:i:s" , $run_time);
        $tpost[post_date_gmt] = $gmt_date;
        $tpost[comment_status] = 'open';
        $tpost[ping_status] = 'open';
        $tpost[post_status] = 'publish';
        $tpost[post_author] = $aid;
        $tpost[post_name] = $post_name;
        $tpost[post_type] = 'post';
        
        error_reporting(0);
        $newid = wp_insert_post($tpost);
        
        $this->last_posts = $this->last_posts.$newid.",";
        update_option('wpbo_last_posts' , $this->last_posts);
        update_option('wpbo_prev_done'  , date("Y-m-d" , $run_time));
    }
    
    function is_post_in_selected_categories( $post_id ) {
        /* Checks whether the post with the given $post_id has at least one 
           category from the selected categories. If no categories were selected
           then the result is always true;
        */
        $result = false;

        if (empty( $this->post_cats ))
            return true;

        $post_categories = wp_get_post_categories($post_id);
        $result = array_intersect($this->post_cats, $post_categories);
        return !empty($result);
    }
            
    function get_posts( $start_time, $end_time ) {
        /* Returns an array of all posts in the given date range that should be
           included the summary */

        global $table_prefix;
        
        $posts = array();
        
        if ($this->last_posts) $qp = " AND ID NOT IN (".substr($this->last_posts , 0 , -1).")";
        
        if ($this->criteria == BP_CRITERIA_VIEWS) {
            $q = "SELECT a.* , single_views from ".$table_prefix."posts as a 
                    LEFT JOIN ".$table_prefix."ak_popularity as b 
                    ON a.ID = b.post_id
                    WHERE (post_date >= '$start_time' and post_date < '$end_time') 
                    AND post_status = 'publish'
                    AND post_type = 'post' $qp
                    ORDER BY single_views DESC LIMIT ".$this->num_posts;
            $criteria = "single_views";
            $criteria_label = "Views";        
        } elseif ($this->criteria == BP_CRITERIA_COMMENTS) {
            $q = "SELECT a.* , count(b.comment_post_ID) as comments_ from ".$table_prefix."posts as a 
                    LEFT JOIN ".$table_prefix."comments as b 
                    ON (a.ID = b.comment_post_ID AND comment_approved = '1')
                    WHERE (post_date >= '$start_time' and post_date < '$end_time') 
                    AND post_status = 'publish'
                    AND post_type = 'post' $qp
                    GROUP BY ID 
                    ORDER BY comments_ DESC LIMIT ".$this->num_posts;
            $criteria = "comments_";
            $criteria_label = "Comments";                
        }
            
        $query = mysql_query($q) or die(mysql_error());
        while ($arr = mysql_fetch_array($query)) {
            if ($this->strict == 1 && $this->criteria == 1 && $arr["comments_"] == 0) continue;
            
            $ID = $arr[ID];

            if (!$this->is_post_in_selected_categories($ID))
                continue;

            $title = stripslashes($arr[post_title]);
            $permalink = get_permalink($ID);
            $post_categories = wp_get_post_categories($ID);
            $sentences = $this->findSentences($content);
            $content = strip_tags(stripslashes($arr[post_content]));

            foreach($sentences as $s){
                $content .= $s;
                $fwords = str_word_count($content);
                
                if($fwords > $this->max_words) break;
            }
            
            if ( $this->ordering == BP_ORDERING_TITLE )
                $key = strtolower($title);
            else if ( $this->ordering == BP_ORDERING_DATE )
                $key = $arr[post_date];
            else if ( $this->ordering == BP_ORDERING_RANDOM )
                $key = rand();
            else
                $key = $arr[$criteria];

            $username = get_usermeta($arr[post_author], 'user_login');

            $posts[$key] = array('ID'        => $ID ,
                                 'title'     => $title ,
                                 'permalink' => $permalink ,
                                 'content'   => $content ,
                                 'date'      => $arr[post_date] ,
                                 'author'    => $username ,
                                 'count'     => $arr[$criteria],
                                 'clabel'    => $criteria_label,
                                 'cat'       => (
             '<a href="'.get_category_link($post_categories[0]).'">'.get_cat_name($post_categories[0]).'</a>'));
        }
        
        ksort($posts);
        return $posts;
    }
    
    function findSentences($bigstring){
        /* Returns an array of all the sentences in the string */
        $mod_c = 0;
        $mods = array();
        $aftermods = $bigstring;
        $findurls = "(?#WebOrIP)((?#protocol)((http|https):\/\/)?(?#subDomain)(([a-zA-Z0-9]+\.(?#domain)[a-zA-Z0-9\-]+(?#TLD)(\.[a-zA-Z]+){1,2})|(?#IPAddress)((25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9])\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[1-9]|0)\.(25[0-5]|2[0-4][0-9]|[0-1]{1}[0-9]{2}|[1-9]{1}[0-9]{1}|[0-9])))+(?#Port)(:[1-9][0-9]*)?)+(?#Path)((\/((?#dirOrFileName)[a-zA-Z0-9_\-\%\~\+]+)?)*)?(?#extension)(\.([a-zA-Z0-9_]+))?(?#parameters)(\?([a-zA-Z0-9_\-]+\=[a-z-A-Z0-9_\-\%\~\+]+)?(?#additionalParameters)(\&([a-zA-Z0-9_\-]+\=[a-z-A-Z0-9_\-\%\~\+]+)?)*)?";
        preg_match_all("/".$findurls."/" , $bigstring , $matches);
        foreach($matches[0] as $index=>$url){
            $mods[$mod_c] = $url;
            $url = str_replace("/" , "\/" , preg_quote($url));
            $aftermods = preg_replace("/".$url."/" , "MYREP-$mod_c" , $aftermods , 1);
            $mod_c++;
        }

        $findnumbs = "(^[0-9]*\.?[0-9]+|[0-9]+\.?[0-9]*)";
        preg_match_all("/".$findnumbs."/" , $aftermods , $numbers);
        foreach($numbers[0] as $index=>$number){
            $number = trim($number);
            if(substr_count($number , ".")){
                $dotpos = strpos($number,".");
                if(($dotpos+1) < strlen($number)){
                    $mods[$mod_c] = $number;
                    $aftermods = preg_replace("/".$number."/" , "MYREP-$mod_c" , $aftermods , 1);
                    $mod_c++;
                }    
            }
        }

        $findsent = "([^\.\?\!]*)[\.\?\!]";
        preg_match_all("/".$findsent."/" , $aftermods , $sentences);
        $sentences = $sentences[0];
        foreach($sentences as $index=>$sentence){
            foreach($mods as $mc => $onemod){
                $sentences[$index] = str_replace("MYREP-$mc" , $onemod , $sentences[$index]);
            }
        }

        return $sentences;
    }
    
    function admin_menu() {
        add_options_page('Best Posts Summary', 'Best Posts Summary', 8, basename(__FILE__), array(&$this, 'settings_page'));
    }
    
    function settings_page() {
        if(count($_POST) && $_POST[wpbo_ordering]) {
            /* update settings if necessary */
            if(!$_POST[wpbo_cat_head]) $_POST[wpbo_cat_head] = 0;
            if(!$_POST[wpbo_strict]) $_POST[wpbo_strict] = 0;
            if($_POST[wpbo_allcats]) $_POST[wpbo_categories] = "";
            
            foreach($_POST as $k=>$v){
                if(!is_array($v)){
                    update_option($k,$v);
                }else{
                    $val = "";
                    foreach($v as $vv){
                        $val .= "~$vv~,";
                    }
                    update_option($k,$val);
                }
            }
            
            $msg = "Settings saved.";
        }
        $this->load_options();
        include("inc.form.php");            
    }

    function schedule_event() {
        wp_schedule_event(time(), 'daily', 'wpbo_on_cron_event');
    }

    function upon_activation() {
        $wpbo_text = "<div>Too many posts to handle? If you missed out on a great post from last month, here's a quick digest of the top posts that you may want to check out:</div>
<ul style='padding-left:10px;padding-right:10px;margin-top:15px;margin-left:0px;margin-right:0px;margin-bottom:0px'>
[loop_start]
<li style='padding-bottom:15px'>
<b><a href='[link]' style='color:#0000FF'>[title]</a></b>
<div><small>Posted on [date] in [category] - [VisitsOrComments]: ([count])</small></div>
<div>[text]</div>
</li>
[loop_end]
</ul>
<div>If you enjoy the content on this site, please make sure to subscribe to the RSS feed.</div>";
        
        add_option('wpbo_activation_date' , date("Y-m-d"));
        add_option('wpbo_frequency','0');
        add_option('wpbo_period','3');
        add_option('wpbo_posts','10');
        add_option('wpbo_categories','');
        add_option('wpbo_post_title','Best posts on [blog-name] from [month] [year]');
        add_option('wpbo_ordering','1');
        add_option('wpbo_wordnumber','60');
        add_option('wpbo_criteria','1');
        add_option('wpbo_text', $wpbo_text);
        $this->schedule_event();
    }

    function upon_deactivation() {
        wp_clear_scheduled_hook('wpbo_on_cron_event');
    }
}

$tech_bestofwp = new tech_bestofwp();
$tech_bestofwp->upon_deactivation();
}
?>
