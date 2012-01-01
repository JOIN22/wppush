<?php
/*
Plugin Name: Cross-linker
Plugin URI: http://www.janhvizdak.com/make-donation-cross-linker-plugin-wordpress.php
Description: A plugin which allows to set-up words which are automatically hyperlinked to desired URLs
Version: 1.4.4
Author: Jan Hvizdak
Author URI: http://www.janhvizdak.com/
*/


/*  Copyright 2008  Jan Hvizdak  (email : postmaster@aqua-fish.net)

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
 global $wpdb;
 $crosslinker_version = "1.4.4";

 if(function_exists('add_action'))
	add_action('admin_menu', 'crosslinker_add_pages');

 function crosslinker_add_pages()
	{
		add_management_page('Cross-Linker Plug-In Management', 'Cross-Linker', 8, 'crosslinker', 'cross_linker');
	}

 $jal_db_version_x     = "1.0";
 $table_crosslink_main = "interlinker";
 $table_crosslink_tags = "interlinker_special_chars";
 $table_crosslink_chars= "interlinker_divide_chars";
 $table_crosslink_setts= "interlinker_settings";
 $table_crosslink_attrb= "interlinker_attributes";

 $table_backups        = "interlinker_backups";
 $table_backup_main    = "interlinker_backup_main";
 $table_backup_chars   = "interlinker_special_chars";
 $table_backup_tags    = "interlinker_divide_chars";
 $table_backup_setting = "interlinker_settings";
 $table_backup_attrb   = "interlinker_attributes";

 $cross_folder     = @str_replace('\\', '/', dirname(__FILE__));
 $cross_thisfolder = @explode('/', $cross_folder);

 if(!function_exists('report_echo_vypis'))
	{
		function report_echo_vypis()
			{
			echo "<h1>OK</h1>";
			}
	}

 function add_mysql_tables()
	{
		global $wpdb, $table_backups, $table_crosslink_attrb;

		$table_name = $wpdb->prefix . $table_backups;

		if($wpdb->get_var("show tables like '$table_name'") != $table_name)
			{
				$sql = "CREATE TABLE `$table_name` (
`id` int(11) NOT NULL auto_increment,
`timestamp` int(11) NOT NULL,
PRIMARY KEY  (`id`),
KEY `timestamp` (`timestamp`)
)";
				$wpdb->query($sql);
			}

		$table_name = $wpdb->prefix . $table_crosslink_attrb;
		if($wpdb->get_var("show tables like '$table_name'") != $table_name)
			{
				$sql = "CREATE TABLE `$table_name` (
`id` int(11) NOT NULL,
`attrib` varchar(250) NOT NULL,
PRIMARY KEY  (`id`),
KEY `attrib` (`attrib`)
);";
				$wpdb->query($sql);
			}
	}

 function table_interlinker_install()
	{
		global $wpdb;
		global $jal_db_version_x;
		global $table_crosslink_main;
		global $table_crosslink_tags;
		global $table_crosslink_chars;
		global $table_crosslink_setts;

		$table_name = $wpdb->prefix . $table_crosslink_main;
		if($wpdb->get_var("show tables like '$table_name'") != $table_name)
			{
				$sql = "CREATE TABLE ".$table_name." (
id smallint(11) NOT NULL auto_increment,
link_word varchar(250) NOT NULL,
link_url text NOT NULL,
visible int(2) NOT NULL,
PRIMARY KEY  (id),
KEY link_word (link_word),
KEY visible (visible)
);";
				$wpdb->query($sql);

				add_option("jal_db_version", $jal_db_version_x);
			}

		$table_name = $wpdb->prefix . $table_crosslink_tags;
		if($wpdb->get_var("show tables like '$table_name'") != $table_name)
			{
				$sql = "CREATE TABLE ".$table_name." (
id smallint(11) NOT NULL auto_increment,
tag_1  varchar(250) NOT NULL,
tag_2  varchar(250) NOT NULL,
PRIMARY KEY  (id),
KEY tag_1 (tag_1),
KEY tag_2 (tag_2)
);";
				$wpdb->query($sql);

				$sql = "INSERT INTO ".$table_name." values ( 'NULL' , '>' , '<' );";
				$wpdb->query($sql);
				$sql = "INSERT INTO ".$table_name." values ( 'NULL' , '</h' , '<h' );";
				$wpdb->query($sql);
				$sql = "INSERT INTO ".$table_name." values ( 'NULL' , '</strong' , '<strong' );";
				$wpdb->query($sql);
				$sql = "INSERT INTO ".$table_name." values ( 'NULL' , '</b' , '<b' );";
				$wpdb->query($sql);
				$sql = "INSERT INTO ".$table_name." values ( 'NULL' , '</a' , '<a' );";
				$wpdb->query($sql);
				$sql = "INSERT INTO ".$table_name." values ( 'NULL' , '</textarea' , '<textarea' );";
				$wpdb->query($sql);
			}

		$table_name = $wpdb->prefix . $table_crosslink_chars;
		if($wpdb->get_var("show tables like '$table_name'") != $table_name)
			{
				$sql = "CREATE TABLE ".$table_name." (
id smallint(11) NOT NULL auto_increment,
characters text NOT NULL,
PRIMARY KEY  (id)
);";
				$wpdb->query($sql);

				$sql = "INSERT INTO ".$table_name." values ( 'NULL' , ' ; . , ) ( - : & > < ? ! * / +' );";
				$wpdb->query($sql);
			}

		$table_name = $wpdb->prefix . $table_crosslink_setts;
		if($wpdb->get_var("show tables like '$table_name'") != $table_name)
			{
				$sql = "CREATE TABLE ".$table_name." (
id smallint(11) NOT NULL auto_increment,
setting text NOT NULL,
value text NOT NULL,
PRIMARY KEY  (id)
);";
				$wpdb->query($sql);

				$sql = "INSERT INTO ".$table_name." values ( 'NULL' , 'link_to_thrusites' , '0' );";
				$wpdb->query($sql);
				$sql = "INSERT INTO ".$table_name." values ( 'NULL' , 'link_first_word' , '0' );";
				$wpdb->query($sql);
			}
	}

 function assign_correct_uri($uri)
	{
		global $wpdb;

		$table_name = $wpdb->prefix . "posts";
		$var        = "post:";
		if(@substr($uri,0,strlen($var))==$var)
			{
				$uri_id= @substr($uri,strlen($var));
				$uri   = $wpdb->get_var("SELECT guid FROM $table_name WHERE ID = '".$uri_id."' limit 1;");
			}

		return $uri;
	}

 function check_chars($in)
	{
		if((@strpos($in,"'")!==false)&&(@strpos($in,"\'")===false))
		$in = addslashes($in);
		return $in;
	}

 function uncheck_word($in)
	{
		while(@strpos($in,"\'")!==false)
			$in = stripslashes($in);
		return $in;
	}

 function cross_linker()
	{
		if (!is_user_logged_in())
			{
				die("sorry, unauthorised access to cross-linker!");
			}
		global $wpdb;
		global $table_crosslink_main;
		global $table_crosslink_tags;
		global $table_crosslink_chars;
		global $table_crosslink_setts;
		global $table_crosslink_attrb;
		global $table_backups;
		global $table_backup_main, $table_backup_chars, $table_backup_tags, $table_backup_setting, $table_backup_attrb;
		global $cut_empty_spaces, $crosslinker_version;

		$fix_uri = str_replace("&del_word=".$_REQUEST['del_word'],"",$_SERVER['REQUEST_URI']);

		echo "<script type=\"text/javascript\">
         <!--
         function setCookie(c_name,value,expiredays)
          {
           var exdate=new Date();
           exdate.setDate(exdate.getDate()+expiredays);
           document.cookie=c_name+ \"=\" +escape(value)+
           ((expiredays==null) ? \"\" : \";expires=\"+exdate.toGMTString());
          }
         function getCookie(c_name)
          {
           if (document.cookie.length>0)
            {
             c_start=document.cookie.indexOf(c_name + \"=\");
             if (c_start!=-1)
              { 
               c_start=c_start + c_name.length+1; 
               c_end=document.cookie.indexOf(\";\",c_start);
               if (c_end==-1) c_end=document.cookie.length;
                return unescape(document.cookie.substring(c_start,c_end));
              } 
            }
           return \"\";
          }
         function make_cookie(c_n)
          {
           this_cookie = getCookie(c_n) + '';
           value       = 1;
           z_value     = 0;
           expiredays  = 365;
           if((this_cookie==null)||(this_cookie==''))
            setCookie(c_n,value,expiredays);
           if(this_cookie==z_value)
            {
             setCookie(c_n,z_value,(-expiredays));
             setCookie(c_n,value,expiredays);
            }
             else
              {
               setCookie(c_n,value,(-expiredays));
               setCookie(c_n,z_value,expiredays);
              }
          }
         -->
         </script>";

		if(($_POST['restore_backup']!='')&&($_POST['agree']=='on'))
			{
				$table_name =  $wpdb->prefix . $table_backups;

				$time = intval($_POST['restore_backup']);
				$id   = $wpdb->get_var("SELECT id FROM $table_name WHERE timestamp = '".$time."' limit 1;");

				$source_table = $wpdb->prefix . $table_backup_main . "_" . $id;
				$target_table = $wpdb->prefix . $table_crosslink_main;
				$wpdb->query("DROP TABLE $target_table");
				$wpdb->query("CREATE TABLE $target_table LIKE ".$source_table);
				$wpdb->query("INSERT $target_table SELECT * FROM $source_table;");

				$source_table = $wpdb->prefix . $table_backup_chars . "_" . $id;
				$target_table = $wpdb->prefix . $table_crosslink_chars;
				$wpdb->query("DROP TABLE $target_table");
				$wpdb->query("CREATE TABLE $target_table LIKE ".$source_table);
				$wpdb->query("INSERT $target_table SELECT * FROM $source_table;");

				$source_table = $wpdb->prefix . $table_backup_tags . "_" . $id;
				$target_table = $wpdb->prefix . $table_crosslink_tags;
				$wpdb->query("DROP TABLE $target_table");
				$wpdb->query("CREATE TABLE $target_table LIKE ".$source_table);
				$wpdb->query("INSERT $target_table SELECT * FROM $source_table;");

				$source_table = $wpdb->prefix . $table_backup_setting . "_" . $id;
				$target_table = $wpdb->prefix . $table_crosslink_setts;
				$wpdb->query("DROP TABLE $target_table");
				$wpdb->query("CREATE TABLE $target_table LIKE ".$source_table);
				$wpdb->query("INSERT $target_table SELECT * FROM $source_table;");

				$source_table = $wpdb->prefix . $table_backup_attrb . "_" . $id;
				$target_table = $wpdb->prefix . $table_crosslink_attrb;
				$wpdb->query("DROP TABLE $target_table");
				if($wpdb->get_var("show tables like '$table_name'") == $source_table)
					{
						$wpdb->query("CREATE TABLE $target_table LIKE ".$source_table);
						$wpdb->query("INSERT $target_table SELECT * FROM $source_table;");
					}

				echo "<script type=\"text/javascript\" language=\"javascript\">
           <!--
            alert (\"The backup was restored successfully!\");
           -->
           </script>";
			}

		if(($_POST['delete_backup']!='')&&($_POST['agree']=='on'))
			{
				$table_name =  $wpdb->prefix . $table_backups;

				$time    = intval($_POST['delete_backup']);
				$drop_id = $wpdb->get_var("SELECT id FROM $table_name WHERE timestamp = '$time' limit 1;");

				$wpdb->query("DELETE from $table_name WHERE id = '$drop_id' limit 1;");

				$source_table = $wpdb->prefix . $table_backup_chars . "_" . $drop_id;
				$wpdb->query("DROP TABLE $source_table;");

				$source_table = $wpdb->prefix . $table_backup_tags . "_" . $drop_id;
				$wpdb->query("DROP TABLE $source_table;");

				$source_table = $wpdb->prefix . $table_backup_setting . "_" . $drop_id;
				$wpdb->query("DROP TABLE $source_table;");

				$source_table = $wpdb->prefix . $table_backup_main . "_" . $drop_id;
				$wpdb->query("DROP TABLE $source_table;");

				$source_table = $wpdb->prefix . $table_backup_attrb . "_" . $drop_id;
				$wpdb->query("DROP TABLE $source_table;");
			}

		add_mysql_tables();

		$t4    = $wpdb->prefix . $table_crosslink_setts;

		//update options
		if($_POST['up_set']==1)
			{
				$s1 = $_POST['link_to_thrusites'];
				if($wpdb->get_var("SELECT id FROM $t4 WHERE setting = 'link_to_thrusites' LIMIT 1;",0)=='')
					{
						$sql = "INSERT INTO ".$t4." values ( 'NULL' , 'link_to_thrusites' , '0' );";
						$wpdb->query($sql);
					}
				if($s1=='on')
					$wpdb->query("UPDATE $t4 SET value = '1' WHERE setting = 'link_to_thrusites';");
						else
							$wpdb->query("UPDATE $t4 SET value = '0' WHERE setting = 'link_to_thrusites';");

				//t_b
				if($wpdb->get_var("SELECT id FROM $t4 WHERE setting = 'link_first_word' LIMIT 1;",0)=='')
					{
						$sql = "INSERT INTO ".$t4." values ( 'NULL' , 'link_first_word' , '0' );";
						$wpdb->query($sql);
					}
				$s1 = $_POST['link_first_word'];
				if($s1=='on')
					$wpdb->query("UPDATE $t4 SET value = '1' WHERE setting = 'link_first_word';");
						else
							$wpdb->query("UPDATE $t4 SET value = '0' WHERE setting = 'link_first_word'");
				//t_e

				if($wpdb->get_var("SELECT id FROM $t4 WHERE setting = 'link_comments' LIMIT 1;",0)=='')
					{
						$sql = "INSERT INTO ".$t4." values ( 'NULL' , 'link_comments' , '0' );";
						$wpdb->query($sql);
					}
				$s1 = $_POST['link_comments'];
				if($s1=='on')
					$wpdb->query("UPDATE $t4 SET value = '1' WHERE setting = 'link_comments';");
						else
							$wpdb->query("UPDATE $t4 SET value = '0' WHERE setting = 'link_comments';");

				if($wpdb->get_var("SELECT id FROM $t4 WHERE setting = 'delete_option' LIMIT 1;",0)=='')
					{
						$sql = "INSERT INTO ".$t4." values ( 'NULL' , 'delete_option' , '0' );";
						$wpdb->query($sql);
					}
				$s1 = $_POST['delete_option'];
				if($s1=='on')
					$wpdb->query("UPDATE $t4 SET value = '1' WHERE setting = 'delete_option';");
						else
							$wpdb->query("UPDATE $t4 SET value = '0' WHERE setting = 'delete_option';");

				//linkings option
				if($wpdb->get_var("SELECT id FROM $t4 WHERE setting = 'limit_links' LIMIT 1;",0)=='')
					{
						$sql = "INSERT INTO ".$t4." values ( 'NULL' , 'limit_links' , '0' );";
						$wpdb->query($sql);
					}
				$s1 = $_POST['limitlinking'];
				if($s1!='')
					$wpdb->query("UPDATE $t4 SET value = '$s1' WHERE setting = 'limit_links';");
				//unusual case

				//permalinks allowed?
				$s1 = $_POST['link_to_permalinks'];
				if($s1=='on')
					$wpdb->query("UPDATE $t4 SET value = '1' WHERE setting = 'link_to_permalinks';");
						else
							$wpdb->query("UPDATE $t4 SET value = '0' WHERE setting = 'link_to_permalinks';");

				if($wpdb->get_var("SELECT id FROM $t4 WHERE setting = 'link_to_permalinks' LIMIT 1;",0)=='')
					{
						$sql = "INSERT INTO ".$t4." values ( 'NULL' , 'link_to_permalinks' , '1' );";
						$wpdb->query($sql);
					}
				//end permalinks allowed?

				//link to itself?
				$s1 = $_POST['link_to_itself'];
				if($s1=='on')
					$wpdb->query("UPDATE $t4 SET value = '1' WHERE setting = 'link_to_itself';");
						else
							$wpdb->query("UPDATE $t4 SET value = '0' WHERE setting = 'link_to_itself';");

				if($wpdb->get_var("SELECT id FROM $t4 WHERE setting = 'link_to_itself' LIMIT 1;",0)=='')
					{
						$sql = "INSERT INTO ".$t4." values ( 'NULL' , 'link_to_itself' , '1' );";
						$wpdb->query($sql);
					}
				//end link to itself?
			}

		//assign one attribute to all without attribute
		if($_POST['attrib_assign_to_all']!='')
			{
				$assign_to_all = $_POST['attrib_assign_to_all'];

				$table_name       = $wpdb->prefix . $table_crosslink_main;
				$table_name_attrs = $wpdb->prefix . $table_crosslink_attrb;

				$this_minimum     = $wpdb->get_var("SELECT id FROM $table_name ORDER BY id ASC LIMIT 1;");
				$sql              = "SELECT id FROM $table_name WHERE id = '$this_minimum';";

				while($wpdb->get_var($sql)!='')
					{
						$sql_1 = "SELECT id FROM $table_name_attrs WHERE id = '$this_minimum';";

						if( ($wpdb->get_var($sql_1)=='') || ( ($wpdb->get_var($sql_1)!='') && (strlen($wpdb->get_var("SELECT attrib FROM $table_name_attrs WHERE id = '$this_minimum';"))<1) ) )
							{
								if($wpdb->get_var($sql_1)=='')
									$sql_2 = "INSERT INTO ".$table_name_attrs." values ( '$this_minimum' , '$assign_to_all' );";
										else
											$sql_2 = "UPDATE ".$table_name_attrs." SET attrib = '$assign_to_all' WHERE id = '$this_minimum' LIMIT 1;";
								$wpdb->query($sql_2);
							}
						$sql          = "SELECT id FROM $table_name WHERE id > '$this_minimum' ORDER BY ID ASC;";
						$this_minimum = $wpdb->get_var($sql);
					}
			}
		//end

		$linkto_word = $_POST['linker_word'];
		$linkto_uri  = $_POST['linker_uri'];
		$linkto_attr = stripslashes($_POST['linker_attr']);

		$table_name =  $wpdb->prefix . $table_crosslink_setts;

		if($_POST['show_news_12']!='')
			{
				$cid = intval($_REQUEST['show_news_12']);
				if($wpdb->get_var("SELECT id FROM $table_name WHERE setting = 'news_1_2' limit 1;")!='')
					$wpdb->query("UPDATE $table_name SET value = '0' WHERE setting = 'news_1_2' limit 1;");
						else
							$wpdb->query("INSERT INTO $table_name values ( 'NULL' , 'news_1_2' , '0' )");
			}

		if($_POST['recommend_link_12']!='')
			{
				$cid = intval($_REQUEST['recommend_link_12']);
				if($wpdb->get_var("SELECT id FROM $table_name WHERE setting = 'recommend_link_12' limit 1;")!='')
					$wpdb->query("UPDATE $table_name SET value = '0' WHERE setting = 'recommend_link_12' limit 1;");
						else
							$wpdb->query("INSERT INTO $table_name values ( 'NULL' , 'recommend_link_12' , '0' )");
			}

		if($_POST['bigchanges']!='')
			{
				$cid = intval($_REQUEST['bigchanges']);
				if($wpdb->get_var("SELECT id FROM $table_name WHERE setting = 'bigchanges' limit 1;")!='')
					$wpdb->query("UPDATE $table_name SET value = '0' WHERE setting = 'bigchanges' limit 1;");
						else
							$wpdb->query("INSERT INTO $table_name values ( 'NULL' , 'bigchanges' , '0' )");
			}

		if($_POST['forgot_something_130']!='')
			{
				$cid = intval($_REQUEST['forgot_something_130']);
				if($wpdb->get_var("SELECT id FROM $table_name WHERE setting = 'forgot_something_130' limit 1;")!='')
					$wpdb->query("UPDATE $table_name SET value = '0' WHERE setting = 'forgot_something_130' limit 1;");
						else
							$wpdb->query("INSERT INTO $table_name values ( 'NULL' , 'forgot_something_130' , '0' )");
			}

		if($_POST['bug_reports_12']!='')
			{
				$cid = intval($_REQUEST['bug_reports_12']);
				if($wpdb->get_var("SELECT id FROM $table_name WHERE setting = 'bug_reports_12' limit 1;")!='')
					$wpdb->query("UPDATE $table_name SET value = '0' WHERE setting = 'bug_reports_12' limit 1;");
						else
							$wpdb->query("INSERT INTO $table_name values ( 'NULL' , 'bug_reports_12' , '0' )");
			}

		if($_POST['valid_code_131']!='')
			{
				$cid = intval($_REQUEST['valid_code_131']);
				if($wpdb->get_var("SELECT id FROM $table_name WHERE setting = 'valid_code_131' limit 1;")!='')
				$wpdb->query("UPDATE $table_name SET value = '0' WHERE setting = 'valid_code_131' limit 1;");
					else
						$wpdb->query("INSERT INTO $table_name values ( 'NULL' , 'valid_code_131' , '0' )");
			}
		if($_POST['core']!='')
			{
				$cid = intval($_REQUEST['core']);
				if($wpdb->get_var("SELECT id FROM $table_name WHERE setting = 'cut_empty_spaces' limit 1;")!='')
					$wpdb->query("UPDATE $table_name SET value = '$cid' WHERE setting = 'cut_empty_spaces' limit 1;");
						else
							$wpdb->query("INSERT INTO $table_name values ( 'NULL' , 'cut_empty_spaces' , '$cid' )");
			}
		if($_POST['core_s']!='')
			{
				$cid = intval($_REQUEST['core_s']);
				if($wpdb->get_var("SELECT id FROM $table_name WHERE setting = 'cut_empty_spaces' limit 1;")!='')
					$wpdb->query("UPDATE $table_name SET value = '$cid' WHERE setting = 'cut_empty_spaces' limit 1;");
						else
							$wpdb->query("INSERT INTO $table_name values ( 'NULL' , 'cut_empty_spaces' , '$cid' )");
			}

		echo "<div class=\"wrap\">
";
		echo "<h2>Cross-Linker</h2>
";
		echo "<font style='font-size: 11px;'>&raquo;brought to you by <a href=\"http://www.janhvizdak.com/\">Jan Hvizdak</a><br />
&raquo;problems? email me: <a href=\"mailto:postmaster@aqua-fish.net\">postmaster@aqua-fish.net</a><br />
&raquo;want new features? email me too!<br />
</font>
";

		$t4    = $wpdb->prefix . $table_crosslink_setts;

		if($wpdb->get_var("SELECT id FROM $t4 WHERE ( (setting = 'link_to_permalinks') AND (value = '1') ) LIMIT 1;")!='')
			{
				$add_permalinks  = "<font style=\"color: #007e46;\"><br />Or select a post from the following list<br /><small>
1) URLs will be loaded from the original WP's MySQL tables, so if your WP uses some plugin which rewrites URLs, make sure that all original URLs are redirected via the 301 redirect.<br />
2) Published posts are these which contain \"publish\" in the <b>post_status</b> column.<br />
3) Links are values in the <b>guid</b> column.</small></font><br />
<script type=\"text/javascript\">
     <!--
      function movetourl()
       {
        if(document.linkerform.permalinkselect.value!='0')
         document.linkerform.linkeruri.value = 'post:' + document.linkerform.permalinkselect.value;
          else
           document.linkerform.linkeruri.value = '';
       }
     -->
</script>
";
				$table_posts     = $wpdb->prefix . "posts";
				$sql             = "select ID, post_title, guid from $table_posts where post_status = 'publish' order by ID desc limit 1;";
				$add_permalinks .= "<select name=\"permalinkselect\" onchange=\"movetourl();\" id=\"permalinkselect\">";
				$add_permalinks .= "<option value=\"0\" selected=\"selected\">Ignore this option</option>";
				while($wpdb->get_var($sql,0)!='')
					{
						$new_id          = $wpdb->get_var($sql,0);
						$add_permalinks .= "<option value=\"".$new_id."\">".$wpdb->get_var($sql,1)." (".$wpdb->get_var($sql,2).")</option> ";
						$sql             = "select ID, post_title, guid from $table_posts where ( (post_status = 'publish') AND (ID < '$new_id') ) order by ID desc limit 1;";
					}
				$add_permalinks  .= "</select>";
			}
				else
					$add_permalinks = "";

		$source_table = $wpdb->prefix . $table_crosslink_setts;
		if( (($crosslinker_version=='1.4.3') || ($crosslinker_version=='1.4.4')) && ($wpdb->get_var("SELECT value FROM $source_table WHERE setting = 'quotes_news_143' LIMIT 1;",0)=='') )
			{
				echo "<strong><em>Version 1.4.3 supports hyperlinking of such words too: <font style=\"color: Green;\">Eric&#8217;s</font> or <font style=\"color: Green;\">Eric's</font> or <font style=\"color: Green;\">Employees&#8217;</font>. Other types of quotes aren't supported yet, however if you want me to add them to the core, email me. This message will not be shown any more.</em></strong>
";
				$wpdb->query("insert into $source_table values ('NULL' , 'quotes_news_143' , '1');");
			}

		$current_cookie    = $_COOKIE['hyperlink_console'];
		if($current_cookie=='1')
			$current_display = "style=\"display:block; position: relative; left: 0px; top: 0px; border: 0px; padding: 0px; margin: 0px;\"";
				else
					$current_display = "style=\"display:none; position: relative; left: 0px; top: 0px; border: 0px; padding: 0px; margin: 0px;\"";
		if($wpdb->get_var("SELECT id FROM $t4 WHERE setting = 'cut_empty_spaces' LIMIT 1;",0)=='')
			echo "<div style=\"padding: 2em; border-top: solid 1px #A9A9F2; border-left: solid 1px #B5B5ED; border-bottom: solid 1px #B5B5ED; border-right: solid 1px #A9A9F2;\"><h3>Please, select the core before you do anything else:</h3>
          <form action=\"\" method=\"post\">
           <input type=\"radio\" value=\"1\" name=\"core\" checked=\"checked\" />1.4.2 and above<br />
           <input type=\"radio\" value=\"0\" name=\"core\" />1.4.1 and before<br />
           <input type=\"submit\" value=\"Select!\" /><br />
           <div>
           The difference between these two options is this:<br />
           &raquo;In the version 1.4.1 and earlier versions words and URLs haven't been checked for existence of empty spaces at the end and at the beginning. Thus, some people, who linked (say) the word <em>something</em> as <em>something<strong>EMPTY_SPACE</strong></em> (or <em><strong>EMPTY_SPACE</strong>something</em>) instead of normal <em>something</em>, got that word never linked, or the word hasn't been hyperlinked in all cases.<br />
           &raquo;In the version 1.4.2+ and above all empty spaces are cut automatically, they can exist between words only. All in all, empty spaces will no longer cause missing automatic links.<br />
           <em><strong>Bear in mind that the core 1.4.2+ is released as testing. So if you are experiencing invalid hyperlinking when using 1.4.2+, open the settings console and use the 1.4.1 core.</strong></em>
           </div>
          </form>
          </div>
";
		echo "<div><h3><a href=\"#h_1\" onclick=\"javascript:make_cookie('hyperlink_console');ReverseContentDisplay('hyperlink_console');\" name=\"h_1\">Open/Close The Console For Hyperlinking</a></h3>
<div id=\"hyperlink_console\" ".$current_display."><div style=\"border-top: solid 1px #A9A9F2; border-left: solid 1px #B5B5ED; border-bottom: solid 1px #B5B5ED; border-right: solid 1px #A9A9F2; padding: 1em;\">
<strong>Here you can set-up automatic hyperlinking between words and URLs.</strong><br />
<form action=\"".$fix_uri."\" method=\"post\" name=\"linkerform\">
Specify the word/phrase below, please (more words/phrases <b>MUST</b> be divided by the following symbol: | . For example: <i>seo|web design|seo services</i> - each of these phrases will point to the specified URL)<br /><input type=\"text\" name=\"linker_word\" value=\"".$linkto_word."\" /><br />
Specify the destination URL, please (starting with the <em>http://</em> or <em>https://</em> prefix)<br /><input type=\"text\" name=\"linker_uri\" value=\"".$linkto_uri."\" id=\"linkeruri\" /> ".$add_permalinks."<br />
Additionally, you can specify attributes for the link below (say <em><a href=\"#\" onclick=\"document.linkerform.linker_attr.value += ' target=\'_blank\' ';\">target='_blank'</a></em> or <em><a href=\"#\" onclick=\"document.linkerform.linker_attr.value += ' rel=\'nofollow\' ';\">rel='nofollow'</a></em> or whatever - just do <strong>NOT</strong> use double quote ( \" ); Instead, use single quote ( ' ))<br />
<input type=\"text\" name=\"linker_attr\" id=\"linker_attr\" value=\"".$linkto_attr."\" /><br />
<input type=\"submit\" value=\"Cross-link now!\" />
</form></div></div></div>
";

		if($_POST['create_backup']==1)
			{
				$table_name = $wpdb->prefix . $table_backups;
				$time       = time();
				$wpdb->query("insert into $table_name values ( 'NULL' , '".$time."');");
				$last       = $wpdb->get_var("SELECT id FROM $table_name WHERE timestamp = '".$time."' limit 1;");

				$target_table = $wpdb->prefix . $table_backup_main . "_" . $last;
				$source_table = $wpdb->prefix . $table_crosslink_main;
				$wpdb->query("CREATE TABLE $target_table LIKE ".$source_table);
				$wpdb->query("INSERT $target_table SELECT * FROM $source_table;");

				$target_table = $wpdb->prefix . $table_backup_chars . "_" . $last;
				$source_table = $wpdb->prefix . $table_crosslink_chars;
				$wpdb->query("CREATE TABLE $target_table LIKE ".$source_table);
				$wpdb->query("INSERT $target_table SELECT * FROM $source_table;");

				$target_table = $wpdb->prefix . $table_backup_tags . "_" . $last;
				$source_table = $wpdb->prefix . $table_crosslink_tags;
				$wpdb->query("CREATE TABLE $target_table LIKE ".$source_table);
				$wpdb->query("INSERT $target_table SELECT * FROM $source_table;");

				$target_table = $wpdb->prefix . $table_backup_setting . "_" . $last;
				$source_table = $wpdb->prefix . $table_crosslink_setts;
				$wpdb->query("CREATE TABLE $target_table LIKE ".$source_table);
				$wpdb->query("INSERT $target_table SELECT * FROM $source_table;");

				$target_table = $wpdb->prefix . $table_backup_attrb . "_" . $last;
				$source_table = $wpdb->prefix . $table_crosslink_attrb;
				$wpdb->query("CREATE TABLE $target_table LIKE ".$source_table);
				$wpdb->query("INSERT $target_table SELECT * FROM $source_table;");

				echo "<script type=\"text/javascript\" language=\"javascript\">
           <!--
            alert (\"The backup was created successfully!\");
           -->
           </script>
";
			}

		$table_name =  $wpdb->prefix . $table_crosslink_main;

		if($_POST['deactivate']!='')
			{
				$cid = intval($_REQUEST['deactivate']);
				$wpdb->query("UPDATE $table_name SET visible = '0' WHERE id = '".$cid."';");
				echo "<strong>The word/phrase has been deactivated!</strong><br />
";
			}
		if($_POST['activate']!='')
			{
				$cid  = intval($_REQUEST['activate']);
				$word = check_chars($wpdb->get_var("SELECT link_word FROM $table_name WHERE id = '".$cid."';"));
				if($wpdb->get_var("SELECT id FROM $table_name WHERE ( (id<> '".$cid."') AND (visible = '1') AND (link_word = '".$word."') );")!='')
					echo "<strong>Cannot activate the word because the same word is pointing to some URL already. Deactivate that one firstly.</strong><br />
";
						else
							{
								$wpdb->query("UPDATE $table_name SET visible = '1' WHERE id = '".$cid."';");
								echo "<strong>The word/phrase has been activated!</strong><br />
";
							}
			}

		if(($_POST['empty']=='1')&&($_POST['really_empty']=='on'))
			{
				$wpdb->query("TRUNCATE TABLE $table_name;");
				$table_name_addon = $wpdb->prefix . $table_crosslink_attrb;
				$wpdb->query("TRUNCATE TABLE $table_name_addon;");
			}

		if($_POST['linker_word']!='')
			{
				$linkto_word = check_chars($_POST['linker_word']);
				$linkto_uri  = check_chars($_POST['linker_uri']);
				$linkto_attr = check_chars($_POST['linker_attr']);

		if(@strpos($linkto_word,"|")!==false)
			{
				$linkto_array= @explode("|",$linkto_word);
				$linkto_count= @count($linkto_array);
			}
				else
					{
						$linkto_array[0] = $linkto_word;
						$linkto_count    = 1;
					}

		for($z=1;$z<=$linkto_count;$z++)
			{
				$linkto_word = $linkto_array[($z-1)];
				if($linkto_uri=='')
					echo "<script type=\"text/javascript\">
              <!--
               alert('The link is missing!');
              -->
              </script>
";

				if(($linkto_word!='')&&($linkto_uri!=''))
					{
						$found       = "";

						$found       = $wpdb->query("SELECT id FROM $table_name WHERE ( (link_word = '$linkto_word') AND (visible = '1') );");

						if($found!='')
							{
								echo "<strong><font style=\"color: Red;\">".$linkto_word."</font>: Deactivate this word/phrase firstly, please.</strong><br />
";
							}
								else
									{
										$found = $wpdb->query("INSERT INTO $table_name  VALUES ( 'NULL' , '".$linkto_word."' , '".$linkto_uri."' ,'1' );");
										echo "<strong><font style=\"color: Blue;\">".$linkto_word."</font>: The new word/phrase has been hyperlinked successfully.</strong><br />
";
										if($linkto_attr!='')
											{
												$table_name_attrs = $wpdb->prefix . $table_crosslink_attrb;
												$found = $wpdb->get_var("SELECT id FROM $table_name WHERE ( (link_word = '$linkto_word') AND (visible = '1') );");
												$found = $wpdb->query("INSERT INTO $table_name_attrs  VALUES ( '".$found."' , '".$linkto_attr."' );");
											}
									}
							}
					}
				}

			if($_REQUEST['del_word']!='')
				{
					$del_me           = $_REQUEST['del_word'];
					$wpdb->query("DELETE FROM $table_name WHERE id = '".$del_me."';");
					$table_name_attrs = $wpdb->prefix . $table_crosslink_attrb;
					$wpdb->query("DELETE FROM $table_name_attrs WHERE id = '".$del_me."';");
				}

			if($_POST['import_text_links']!='')
				{
					$table            = $wpdb->prefix . $table_crosslink_main;
					$table_name_attrs = $wpdb->prefix . $table_crosslink_attrb;
					echo "<br /><strong><em>Import - Results</em></strong><br />
";
					echo "<textarea cols=\"50\" rows=\"15\" readonly=\"readonly\">
";
					$all_import = explode("\n",$_POST['import_text_links']);
					for($i=0;$i<(((count($all_import)+1)/4)-1);$i++)
						{
							$import_word = substr(stripslashes($all_import[($i*4)]),0,strlen(stripslashes($all_import[($i*4)]))-1);
							$import_url  = substr(stripslashes($all_import[(($i*4)+1)]),0,strlen(stripslashes($all_import[(($i*4)+1)]))-1);
							$import_attr = addslashes(substr(stripslashes($all_import[(($i*4)+2)]),0,strlen(stripslashes($all_import[(($i*4)+2)]))-1));
							$import_act  = substr(stripslashes($all_import[(($i*4)+3)]),0,strlen(stripslashes($all_import[(($i*4)+3)]))-1);
							if(($import_act!='0')&&($import_act!='1'))
								$import_act  = stripslashes($all_import[(($i*4)+3)]);
							$found_word  = 0;
							$found_prob  = $wpdb->get_var("SELECT id FROM $table WHERE ( (link_word = '$import_word') AND (link_url = '$import_url') ) LIMIT 1;",0);
							if($found_prob>0)
								$give_output = "Word: ".$import_word." not imported\n";
									else
										{
											$found_word  = $wpdb->get_var("SELECT id FROM $table WHERE ( (link_word = '$import_word') AND (visible = '1') ) LIMIT 1;",0);
											if($found_word==0)
												{
													$sql       = "INSERT INTO ".$table." values ( 'NULL' , '$import_word' , '$import_url' , '$import_act' );";
													$wpdb->query($sql);
													$found_id  = $wpdb->get_var("SELECT id FROM $table WHERE ( (link_word = '$import_word') AND (visible = '$import_act') AND (link_url = '$import_url') ) order by ID desc LIMIT 1;",0);
													$sql       = "INSERT INTO ".$table_name_attrs." values ( '$found_id' , '$import_attr' );";
													$wpdb->query($sql);

													if($import_act==1)
														$active = "active";
															else
																$active = "inactive";
													$give_output = "Word: ".$import_word." set to ".$active."\n";
												}
													else
														{
															$sql = "INSERT INTO ".$table." values ( 'NULL' , '$import_word' , '$import_url' , '0' );";
															$wpdb->query($sql);
															$found_id  = $wpdb->get_var("SELECT id FROM $table WHERE ( (link_word = '$import_word') AND (visible = '0') AND (link_url = '$import_url') ) order by ID desc LIMIT 1;",0);
															$sql       = "INSERT INTO ".$table_name_attrs." values ( '$found_id' , '$import_attr' );";
															$wpdb->query($sql);

															$give_output = "Word: ".$import_word." set to inactive\n";
														}
												}
											echo $give_output;
										}
									echo "</textarea>
";
								}

							$table_name_attributes = $wpdb->prefix . $table_crosslink_attrb;

							if($_POST['blogroll_import']=='1')
								{
									$table_name =  $wpdb->prefix . $table_crosslink_main;
									$m          = $_POST['blogroll_import_val'];
									for($i=1;$i<=$m;$i++)
										{
											if($_POST['ch'][$i]=='on')
												{
													$url          = check_chars($_POST['blogroll_link_url'][$i]);
													$word         = check_chars($_POST['blogroll_link_title'][$i]);
													$attribute    = check_chars($_POST['blogroll_link_attr'][$i]);
													$next_visible = 1;
													$exists       = 0;

													$exx  = $wpdb->get_var("SELECT id FROM $table_name WHERE ( (link_word = '".$word."') AND (visible = '1') );");
													if($exx!='')
														$next_visible = 0;
													$exx  = $wpdb->get_var("SELECT id FROM $table_name WHERE ( (link_word = '".$word."') AND (visible = '0') );");
													if($exx!='')
														$next_visible = 1;
													$exx  = $wpdb->get_var("SELECT id FROM $table_name WHERE ( (link_word = '".$word."') AND (link_url = '".$url."') );");
													if($exx!='')
														$exists = 1;

													if($exists==1)
														echo "<font style=\"color: red\">Word <strong>".uncheck_word($word)."</strong> and URL <strong>".uncheck_word($url)."</strong> are already connected!</font><br />
";
															else
																{
																	if($next_visible==0)
																		{
																			echo "<font style=\"color: orange\">Word <strong>".uncheck_word($word)."</strong> and URL <strong>".uncheck_word($url)."</strong> were set to inactive because this word is already active for another URL!</font><br />
";
																			$next_visible_1 = 1;
																		}
																			else
																				{
																					echo "<font style=\"color: blue\">Word <strong>".uncheck_word($word)."</strong> and URL <strong>".uncheck_word($url)."</strong> were set to active!</font><br />
";
																					$next_visible_1 = 0;
																				}
																	$wpdb->query("INSERT INTO $table_name VALUES ( 'NULL' , '".$word."' , '".$url."' ,'".$next_visible."' );");
																	if($attribute!='')
																		{
																			$find_last = $wpdb->get_var("SELECT id FROM $table_name WHERE ( (link_word = '".$word."') AND (link_url = '".$url."') AND (visible = '".$next_visible."') ) LIMIT 1;");
																			$wpdb->query("INSERT INTO $table_name_attributes VALUES ( '".$find_last."' , '".$attribute."' );");
																		}
																}
												}
										}
								}

							$table_name       = $wpdb->prefix . $table_crosslink_main;
							$table_name_attrs = $wpdb->prefix . $table_crosslink_attrb;

							if($_POST['modify_id']!='')
								{
									$modify_id   = $_POST['modify_id'];
									$modify_word = $_POST['modify_phrase'];
									$modify_uri  = $_POST['modify_uri'];
									$modify_attr = $_POST['modify_attr'];
									$err_message = "";
									if($modify_word=='')
										$err_message = "The word is void. Invalid request!";
									if($modify_uri=='')
										$err_message = "The URL is void. Invalid request!";
									$old_word = $wpdb->get_var("SELECT link_word FROM $table_name WHERE id = '$modify_id' LIMIT 1",0);
									if($old_word!=$modify_word)
										{
											$existing_problem = $wpdb->get_var("SELECT id FROM $table_name WHERE ( (id <> '$modify_id' ) AND (visible = '1') AND (link_word = '$modify_word') ) LIMIT 1",0);
											if($existing_problem>0)
												$err_message = "The word is already active, deactivate it firstly. Invalid request!";
										}

									if($err_message!='')
										{
											echo "<script type=\"text/javascript\">
              <!--
               alert('The word is void. Invalid request!');
              -->
             </script>
";
										}
											else
												{
													$wpdb->query("UPDATE $table_name SET link_word = '$modify_word', link_url = '$modify_uri' WHERE id = '$modify_id' LIMIT 1;");
													if($wpdb->get_var("SELECT id FROM $table_name_attrs WHERE id = '$modify_id' LIMIT 1",0)!='')
														$wpdb->query("UPDATE $table_name_attrs SET attrib = '$modify_attr' WHERE id = '$modify_id' LIMIT 1;");
															else
																$wpdb->query("INSERT INTO $table_name_attrs values ( '$modify_id' , '$modify_attr' );");
												}

								}
							//end update options

							echo "<script type=\"text/javascript\" language=\"JavaScript\"><!--
                          function HideContent(d)
                           {
                            document.getElementById(d).style.display = \"none\";
                           }
                          function ShowContent(d)
                           {
                            document.getElementById(d).style.display = \"block\";
                           }
                          function ReverseContentDisplay(d)
                           {
                            if(document.getElementById(d).style.display == \"block\")
                             {
                              document.getElementById(d).style.display = \"none\";
                             }
                              else
                               {
                                document.getElementById(d).style.display = \"block\";
                               }
                           }
                          function confirmSubmit(i)
                           {
                            var agree=confirm('Really delete the word ' + i + '?');
                            if (agree)
                             return true ;
                              else
                               return false ;
                           }
                          //-->
         </script>
";

							$current_cookie    = $_COOKIE['blogrollmanagement'];
							if($current_cookie=='1')
								$current_display = "style=\"display:block; position: relative; left: 0px; top: 0px; border: 0px; padding: 0px; margin: 0px;\"";
									else
										$current_display = "style=\"display:none; position: relative; left: 0px; top: 0px; border: 0px; padding: 0px; margin: 0px;\"";

							echo "<div><h3><a href=\"#h_2\" onclick=\"javascript:make_cookie('blogrollmanagement');ReverseContentDisplay('blogrollmanagement');\" name=\"h_2\">Open/Close The Console For Importing Blogroll Links</a></h3>
";
							echo "<div id=\"blogrollmanagement\" ".$current_display.">
<div style=\"border-top: solid 1px #A9A9F2; border-left: solid 1px #B5B5ED; border-bottom: solid 1px #B5B5ED; border-right: solid 1px #A9A9F2; padding: 1em;\">
";

							echo "<form action=\"".$fix_uri."\" method=\"post\" name=\"checkers\">";
							$table_name = $wpdb->prefix . "links";
							$i          = 0;
							$max = $wpdb->get_var("SELECT link_id FROM $table_name order by link_id asc limit 1;");
							while($wpdb->get_var("SELECT link_id FROM $table_name WHERE link_id = '".$max."' order by link_id asc limit 1;")!='')
								{
									$i++;
									echo "<input type=\"text\" name=\"blogroll_link_url[".$i."]\" value=\"".$wpdb->get_var("SELECT link_url FROM $table_name WHERE link_id = '".$max."' limit 1;")."\" /> is linked as <input type=\"text\" name=\"blogroll_link_title[".$i."]\" value=\"".strtolower($wpdb->get_var("SELECT link_name FROM $table_name WHERE link_id = '".$max."' limit 1;"))."\" /> with this attribute <input type=\"text\" name=\"blogroll_link_attr[".$i."]\" id=\"attr".$i."\" value=\"\" /> <input type=\"checkbox\" name=\"ch[".$i."]\" checked=\"checked\" id=\"ch".$i."\" /> import?<br />
";
									$max = $wpdb->get_var("SELECT link_id FROM $table_name WHERE link_id > '".$max."' order by link_id asc limit 1;");
								}

							echo "<script type=\"text/javascript\">
         <!--
          function checkthebox()
           {
            if ( document.formsetting.limitlinking.value == '0' )
             document.formsetting.limitlinkings.checked = false;
              else
               document.formsetting.limitlinkings.checked = true;
           }
          function checktheboxadd()
           {
            if ( document.formsetting.limitlinking.value == '0' )
             {
              alert('Please, select any number firstly! This checkbox is actually completely automated.');
              document.formsetting.limitlinkings.checked = false;
             }
              else
               {
                alert('Please, select the UNLIMITED option firstly! This checkbox is actually completely automated.');
                document.formsetting.limitlinkings.checked = true;
               }
           }
          function checks()
           {
            if(document.checkers.submitchanges.value=='Uncheck All!')
             {
              value = \"Check All!\";
              ass   = false;
             }
              else
               {
                value = \"Uncheck All!\";
                ass   = true;
               }
";
							for($z=1;$z<=$i;$z++)
								echo "   document.checkers.ch".$z.".checked = ass ; ";

							echo "   document.checkers.submitchanges.value = value ;
           }
          function applyallattrs()
           {
";
							for($z=1;$z<=$i;$z++)
								echo "   document.checkers.attr".$z.".value = document.checkers.applyattrtoall.value ; ";

							echo "
           }
         -->
         </script>
";

							echo "<input type=\"button\" name=\"submitchanges\" id=\"submitchanges\" value=\"Uncheck All!\" onclick=\"checks();\" /><br />
         Apply this attribute to all imported links: <input type=\"text\" name=\"apply_attr_to_all\" id=\"applyattrtoall\" value=\"\" /> by clicking <input type=\"button\" value=\"HERE\" onclick=\"applyallattrs();\" /> (as described above, use single quotes instead of double quotes)<br />
";

							echo "<input type=\"hidden\" name=\"blogroll_import_val\" value=\"".$i."\" />
<input type=\"hidden\" name=\"blogroll_import\" value=\"1\" />
<input type=\"submit\" value=\"Import!\" />
";
							echo "</form>
";

							echo "</div></div></div>
";

							$current_cookie    = $_COOKIE['current_connections'];
							if($current_cookie=='1')
								$current_display = "style=\"display:block; position: relative; left: 0px; top: 0px; border: 0px; padding: 0px; margin: 0px;\"";
									else
										$current_display = "style=\"display:none; position: relative; left: 0px; top: 0px; border: 0px; padding: 0px; margin: 0px;\"";

							echo "<div><h3><a href=\"#h_3\" onclick=\"javascript:make_cookie('current_connections');ReverseContentDisplay('current_connections');\" name=\"h_3\">Open/Close The Console For Currently Hyperlinked Words and URLs</a></h3>
";
							echo "<div id=\"current_connections\" ".$current_display.">
<div style=\"border-top: solid 1px #A9A9F2; border-left: solid 1px #B5B5ED; border-bottom: solid 1px #B5B5ED; border-right: solid 1px #A9A9F2; padding: 1em;\">
";

							$found = 0;

							$table_name = $wpdb->prefix . $table_crosslink_main;

							$i     = check_chars($wpdb->get_var("SELECT link_word FROM $table_name ORDER BY link_word ASC LIMIT 1",0));
							$help  = $wpdb->get_var("SELECT id FROM $table_name WHERE link_word = '".$i."' limit 1;",0);
							$sql   = "SELECT * FROM $table_name WHERE link_word = '$i';";
							$z     = 1;

							$show_advanced_att_import = 0;

							echo "<table style=\"border: solid 1px Silver; padding: 2px; margin: 0px;\">
";

							echo "<tr><td style=\"border-right: solid 1px Silver; border-bottom: solid 1px Silver; padding: 1px; margin: 0px; text-align: center;\"><b><small>Modification</small></b></td><td style=\"border-right: solid 1px Silver; border-bottom: solid 1px Silver; padding: 1px; margin: 0px;\"><b><small>Phrase/Word</small></b></td><td style=\"border-right: solid 1px Silver; border-bottom: solid 1px Silver; padding: 1px; margin: 0px;\"><b><small>points to</small></b></td><td style=\"border-right: solid 1px Silver; border-bottom: solid 1px Silver; padding: 1px; margin: 0px;\"><b><small>Attribute</small></b></td><td style=\"border-right: solid 1px Silver; border-bottom: solid 1px Silver; padding: 1px; margin: 0px; text-align: center;\"><b><small>Deactivation</small></b></td><td style=\"border-right: solid 1px Silver; border-bottom: solid 1px Silver; padding: 1px; margin: 0px;\"><b><small>Delete</small></b></td></tr>
";

							while($wpdb->get_var($sql,0)!='')
								{
									$found = 1;
									if($wpdb->get_var($sql,3)=='1')
										$act = "deactivate";
											else
												$act = "activate";

									$current_uri = $_SERVER['REQUEST_URI'];
									$ddd         = "del_word=";
									if(@strpos($current_uri,$ddd)!==false)
										{
											$del_position = @strpos($current_uri,$ddd);
											$current_uri  = @substr($current_uri,0,$del_position-1);
										}

									$this_attribute    = htmlspecialchars($wpdb->get_var($sql,0));
									$current_attribute = uncheck_word($wpdb->get_var("SELECT attrib FROM $table_name_attrs WHERE id = '$this_attribute';"));

									if($current_attribute=='')
										$show_advanced_att_import = 1;

									echo "<tr><td style=\"border-right: solid 1px Silver; border-bottom: solid 1px Silver; padding: 1px; margin: 0px; text-align: center;\"><small>[<a href=\"javascript:ReverseContentDisplay('modifyarea".htmlspecialchars($wpdb->get_var($sql,0))."');\">modify</a>]</small></td><td  style=\"border-right: solid 1px Silver; border-bottom: solid 1px Silver; padding: 1px; margin: 0px;\"><strong>".uncheck_word(htmlspecialchars($wpdb->get_var($sql,1)))."</strong></td><td  style=\"border-right: solid 1px Silver; border-bottom: solid 1px Silver; padding: 1px; margin: 0px;\"><a href=\"".uncheck_word(assign_correct_uri(htmlspecialchars($wpdb->get_var($sql,2))))."\" target=\"_blank\">".uncheck_word(htmlspecialchars(assign_correct_uri(htmlspecialchars($wpdb->get_var($sql,2)))))."</a></td><td style=\"border-right: solid 1px Silver; border-bottom: solid 1px Silver; padding: 1px; margin: 0px;\">".$current_attribute."</td><td style=\"border-right: solid 1px Silver; border-bottom: solid 1px Silver; padding: 1px; margin: 0px; text-align: center;\">
     <form action=\"".$fix_uri."\" method=\"post\"><input type=\"hidden\" name=\"".$act."\" value=\"".uncheck_word(htmlspecialchars($wpdb->get_var($sql,0)))."\" /> <input type=\"submit\" value=\"".ucfirst($act)."\" /></form></td>
";

									if($wpdb->get_var("SELECT value FROM $t4 WHERE setting = 'delete_option' LIMIT 1;",0)=='1')
										echo "<td style=\"border-right: solid 1px Silver; border-bottom: solid 1px Silver; padding: 1px; margin: 0px;\"><small><a onclick=\"return confirmSubmit('".uncheck_word(htmlspecialchars($wpdb->get_var($sql,1)))."');\" href=\"".htmlspecialchars($current_uri)."&amp;".$ddd.htmlspecialchars($wpdb->get_var($sql,0))."\">DELETE</a></small></td>
";
											else
												echo "<td style=\"border-right: solid 1px Silver; border-bottom: solid 1px Silver; padding: 1px; margin: 0px; text-align: center;\"><small>N/A</small></td>
";

									echo "</tr><tr><td colspan=\"6\"><div id=\"modifyarea".htmlspecialchars($wpdb->get_var($sql,0))."\" style=\"display:none; position: relative; left: 0px; top: 0px; border-right: solid 1px Silver; border-bottom: solid 1px Silver; padding: 0px; margin: 0px;\">
            <form action=\"\" method=\"post\">
             <input type=\"text\" name=\"modify_phrase\" value=\"".uncheck_word(htmlspecialchars($wpdb->get_var($sql,1)))."\" /> (phrase)<br />
             <input type=\"text\" name=\"modify_uri\" value=\"".uncheck_word(htmlspecialchars($wpdb->get_var($sql,2)))."\" /> (URL/POST ID)<br />
             <input type=\"text\" name=\"modify_attr\" value=\"".uncheck_word($wpdb->get_var("SELECT attrib FROM $table_name_attrs WHERE id = '$this_attribute';"))."\" /> (attribute - use single quotes, not double quotes, please)<br />
             <input type=\"hidden\" name=\"modify_id\" value=\"".htmlspecialchars($wpdb->get_var($sql,0))."\" />
             <input type=\"submit\" value=\"Modify this record!\" />
            </form>
           </div></td></tr>
";
									$ar[$z] = $help;
									$z++;

									for($x=1;$x<$z;$x++)
										{
											if($x==1)
												{
													if($z>1)
														$add_string = "AND ( ";
															else
																$add_string = " (";
												}
											$add_string .= " (id<>'".$ar[$x]."') ";
											if(($z>1)&&($x!=$z-1))
												$add_string .= " AND ";
										}

									$add_string .= " ) ";

									$q   = "SELECT link_word FROM $table_name WHERE ( (link_word >= '".check_chars($i)."') $add_string ) ORDER BY link_word ASC LIMIT 1";
									$i   = $wpdb->get_var($q,0);
									$help= $wpdb->get_var("SELECT id FROM $table_name WHERE ( (link_word = '".check_chars($i)."') $add_string ) limit 1;",0);
									$sql = "SELECT * FROM $table_name WHERE ( (link_word = '".check_chars($i)."') $add_string );";
								}

							echo "</table>";

							if($found==0)
								echo "<br />No connections found<br />
";
									else
										{
											if($show_advanced_att_import==1)
												echo "<font style=\"color: #875735;\"><em><strong>Some words have no attributes assigned. You may assign the following attribute to all words/phrases without any active attribute:</strong></em></font>
        <form action=\"\" method=\"post\">
         <input type=\"text\" name=\"attrib_assign_to_all\" value=\"\" /> (just don't use double quotes; use single quotes) <input type=\"submit\" value=\"Assign!\" />
        </form>
";
										}

							echo "<form action=\"".$fix_uri."\" method=\"post\">
          <input type=\"hidden\" name=\"empty\" value=\"1\" />
          <input type=\"checkbox\" name=\"really_empty\" /><font style=\"color: red;\">I want to delete all existing words/phrases</font><br />
          <input type=\"submit\" value=\"Delete all existing words/phrases!\" />
         </form>
";

							echo "</div></div></div>
";

							$current_cookie    = $_COOKIE['ignored_html_tags'];
							if($current_cookie=='1')
								$current_display = "style=\"display:block; position: relative; left: 0px; top: 0px; border: 0px; padding: 0px; margin: 0px;\"";
									else
										$current_display = "style=\"display:none; position: relative; left: 0px; top: 0px; border: 0px; padding: 0px; margin: 0px;\"";

							echo "<div><h3><a href=\"#h_4\" onclick=\"javascript:make_cookie('ignored_html_tags');ReverseContentDisplay('ignored_html_tags');\" name=\"h_4\">Open/Close The Console For Managing Ignored HTML tags</a></h3>
";
							echo "<div id=\"ignored_html_tags\" ".$current_display.">
         <div style=\"border-top: solid 1px #A9A9F2; border-left: solid 1px #B5B5ED; border-bottom: solid 1px #B5B5ED; border-right: solid 1px #A9A9F2; padding: 1em;\">
";

							echo "You should also define which HTML tags are ignored for hyperlinking purposes. Example: If you enter <strong>&lt;h</strong> and <strong>&lt;/h</strong> below, then all <strong>h1-h6</strong> will be ignored. Whatever you wrote within these tags, the cross-linker plugin will not hyperlink words/phrases from such tags. If you're not sure about these settings, let them be.
";

							$t2    = $wpdb->prefix . $table_crosslink_tags;

							if($_POST['delete_tag']!='')
								{
									$delete_this = $_POST['delete_tag'];
									$wpdb->query("DELETE FROM $t2 WHERE id = '$delete_this';");
								}

							if(($_POST['add_tag_start']!='')&&($_POST['add_tag_end']!=''))
								{
									$p1 = $_POST['add_tag_start'];
									$p2 = $_POST['add_tag_end'];
									$wpdb->query("INSERT INTO $t2 VALUES ( 'NULL' , '$p2' , '$p1' );");
								}

							$i     = $wpdb->get_var("SELECT * FROM $t2 ORDER BY id asc limit 1;",0);
							$sql   = "SELECT * FROM $t2 WHERE id = '$i';";
							while($wpdb->get_var($sql,0)!='')
								{
									echo "<form action=\"".$fix_uri."\" method=\"post\">
";
									echo "<input type='text' name='t".$i."_s' value='".htmlspecialchars($wpdb->get_var($sql,2))."' readonly='readonly' /> <input type='text' name='t".$i."_e' value='".htmlspecialchars($wpdb->get_var($sql,1))."' readonly='readonly' /><input type='hidden' name='delete_tag' value='".$i."' /><input type='submit' value='Delete!' /></form>
";
									$i   = $wpdb->get_var("SELECT id FROM $t2 WHERE id > '$i' ORDER BY id asc LIMIT 1;",0);
									$sql = "SELECT * FROM $t2 WHERE id = '$i';";
								}
							echo "<input type='text' name='t_def_s' value='&lt;!--nocrosslink_start--&gt;' readonly='readonly' /> <input type='text' name='t_def_e' value='&lt;!--nocrosslink_end--&gt;' readonly='readonly' /> This can't be deleted, it's default! If you want this plugin to ignore any part of the text (if you don't want to automatically hyperlink any text), simply use this code: &lt;!--nocrosslink_start--&gt;<strong>your text here</strong>&lt;!--nocrosslink_end--&gt;
";

							echo "<h3>Add new HTML tags</h3>
         <form action=\"".$fix_uri."\" method=\"post\">
         The tag starts as <input type='text' name='add_tag_start' value='' /> and ends as <input type='text' name='add_tag_end' value='' /><br />
         <input type='submit' value='Add this tag!' />
         </form>
";

							echo "</div></div></div>
";

							$current_cookie    = $_COOKIE['ignored_characters'];
							if($current_cookie=='1')
								$current_display = "style=\"display:block; position: relative; left: 0px; top: 0px; border: 0px; padding: 0px; margin: 0px;\"";
									else
										$current_display = "style=\"display:none; position: relative; left: 0px; top: 0px; border: 0px; padding: 0px; margin: 0px;\"";

							echo "<div><h3><a href=\"#h_5\" onclick=\"javascript:make_cookie('ignored_characters');ReverseContentDisplay('ignored_characters');\" name=\"h_5\">Open/Close The Console For Managing Ignored Characters</a></h3>
";
							echo "<div id=\"ignored_characters\" ".$current_display.">
         <div style=\"border-top: solid 1px #A9A9F2; border-left: solid 1px #B5B5ED; border-bottom: solid 1px #B5B5ED; border-right: solid 1px #A9A9F2; padding: 1em;\">
";

							echo "Words/phrases are hyperlinked if only they are separated by spaces by default. However, dots, commas, slashes and similar characters may be considered as dividing characters as well. Here below you can specify which characters will be used for the algorithm. Example: If you don't specify <strong>.</strong> below, then words/phrases which end with dot will <strong>NOT</strong> be hyperlinked. <strong>Each \"special character\" MUST be divided by space below!</strong>
";

							$t3 = $wpdb->prefix . $table_crosslink_chars;

							if($_POST['ignored_chars']!='')
								{
									$p2 = $_POST['ignored_chars'];
									$wpdb->query("UPDATE $t3 SET characters = '$p2' WHERE id = '1';");
								}

							echo "<form action=\"".$fix_uri."\" method=\"post\">
          <input type='text' value=\"".htmlspecialchars($wpdb->get_var("SELECT characters FROM $t3 WHERE id = 1 LIMIT 1;",0))."\" name='ignored_chars' size='50' /><br />
          <input type='submit' value='Modify!' /></form>
";

							echo "</div></div></div>
";

							$t4 = $wpdb->prefix . $table_crosslink_setts;

							if($wpdb->get_var("SELECT value FROM $t4 WHERE setting = 'link_to_thrusites' LIMIT 1;",0)=='1')
								$checked = "checked='checked'";
									else
										$checked = "";
							if($wpdb->get_var("SELECT value FROM $t4 WHERE setting = 'link_first_word' LIMIT 1;",0)=='1')
								$checked_1 = "checked='checked'";
									else
										$checked_1 = "";
							if($wpdb->get_var("SELECT value FROM $t4 WHERE setting = 'link_comments' LIMIT 1;",0)=='1')
								$checked_2 = "checked='checked'";
									else
										$checked_2 = "";
							if($wpdb->get_var("SELECT value FROM $t4 WHERE setting = 'delete_option' LIMIT 1;",0)=='1')
								$checked_3 = "checked='checked'";
									else
										$checked_3 = "";
							//unusual case
							if($wpdb->get_var("SELECT value FROM $t4 WHERE setting = 'limit_links' LIMIT 1;",0)>0)
								{
									$checked_4 = "checked='checked'";
									$find_sel  = $wpdb->get_var("SELECT value FROM $t4 WHERE setting = 'limit_links' LIMIT 1;",0);
								}
									else
										{
											$find_sel  = 0;
											$checked_4 = "";
										}
							//end unusual case
							if($wpdb->get_var("SELECT value FROM $t4 WHERE setting = 'link_to_permalinks' LIMIT 1;",0)=='1')
								$checked_5 = "checked='checked'";
									else
										$checked_5 = "";
							if($wpdb->get_var("SELECT value FROM $t4 WHERE setting = 'link_to_itself' LIMIT 1;",0)=='1')
								$checked_6 = "checked='checked'";
									else
										$checked_6 = "";

							for($i=1;$i<=99;$i++)
								{
									if($i==$find_sel)
										$check_this_1 = " selected=\"selected\" ";
											else
												$check_this_1 = "";
									$show_limits .= "<option value=\"".$i."\" ".$check_this_1.">".$i."</option> ";
								}

							if($find_sel==0)
								$check_this_1 = " selected=\"selected\" ";
									else
										$check_this_1 = "";

							$show_limits = "<select name=\"limitlinking\" onchange=\"checkthebox();\">
<option value=\"0\" ".$check_this_1.">Unlimited</option>".$show_limits."</select>";

							if($wpdb->get_var("SELECT value FROM $t4 WHERE setting = 'cut_empty_spaces' LIMIT 1;",0)==1)
								$core_selection = "<option value=\"1\" selected=\"selected\">1.4.2+</option>
<option value=\"0\">1.4.1</option>";
									else
										$core_selection = "<option value=\"1\">1.4.2+</option>
<option value=\"0\" selected=\"selected\">1.4.1</option>";

							$current_cookie    = $_COOKIE['manage_settings'];
							if($current_cookie=='1')
								$current_display = "style=\"display:block; position: relative; left: 0px; top: 0px; border: 0px; padding: 0px; margin: 0px;\"";
									else
										$current_display = "style=\"display:none; position: relative; left: 0px; top: 0px; border: 0px; padding: 0px; margin: 0px;\"";

							echo "<div><h3><a href=\"#h_6\" onclick=\"javascript:make_cookie('manage_settings');ReverseContentDisplay('manage_settings');\" name=\"h_6\">Open/Close The Console For Managing Settings</a></h3>
";
							echo "<div id=\"manage_settings\" ".$current_display.">
         <div style=\"border-top: solid 1px #A9A9F2; border-left: solid 1px #B5B5ED; border-bottom: solid 1px #B5B5ED; border-right: solid 1px #A9A9F2; padding: 1em;\">
";

							echo "If you are using thus plugin, it will be <strong>cool</strong> if you link back to <a href=\"http://www.aqua-fish.net/\">Aqua-Fish.Net</a>. This is a project of Jan Hvizdak, the author of this plugin. For this purpose you can activate the following setting. A small link will be added to the footer of your blog. Of course, you can add a link to your blogroll instead of activating this option. <em>This is just a small <b>thank you</b> to the author!</em>
   <br /><br /><form action=\"".$fix_uri."\" method=\"post\" name=\"formsetting\">
   <input type='checkbox' name='link_to_thrusites' ".$checked." /> Link to <a href=\"http://www.aqua-fish.net/\">Aqua-Fish.Net</a>? (recommended)<br />
   <select name=\"core_s\">
   ".$core_selection."
   </select>Use this core!<br />
   <input type='checkbox' name='link_first_word' ".$checked_1." /> Hyperlink 1 word only? (not recommended; valid for 1 post)<br />
   <input type='checkbox' name='link_comments' ".$checked_2." /> Apply Cross-Linker to comments? (recommended)<br />
   <input type='checkbox' name='delete_option' ".$checked_3." /> Show the <em>DELETE</em> option for words which were cross-linked?<br />
   <input type='checkbox' name='limitlinkings' id='limitlinkings' ".$checked_4." onclick='checktheboxadd();'/> Hyperlink only $show_limits link(s) on each page? (may be useful; bear in mind that this feature restricts linking of each phrase, not of all phrases together!)<br />
   <input type='checkbox' name='link_to_permalinks' ".$checked_5." /> Allow <b>direct linking to posts</b>? This may waste your server's system resources if there are thousands of posts (but only when you're working with this control panel, otherwise everything will work fine - for your visitors)!!!<br />
   <input type='checkbox' name='link_to_itself' ".$checked_6." /> Allow linking to the same page? For example, if you've configured the word <b>seo</b> to link to <b>http://www.something.tld/seo</b>, this word isn't hyperlinked on <b>http://www.something.tld/seo</b> by default. By activating this option, our imaginary word <b>seo</b> will be hyperlinked too when people visit <b>http://www.something.tld/seo</b>; Although it will point to itself only.<br />
   <input type='hidden' name='up_set' value='1' />
   <input type='submit' value='Update settings!' />
   </form>
";

							echo "<br /><font style=\"color: red\"><strong>IMPORTANT!</strong></font><br />
         If you're upgrading this plugin, then there is no need to deactivate it! Just upload new files and rewrite old files.
";

							echo "</div></div></div>
";

							$current_cookie    = $_COOKIE['manage_backups'];
							if($current_cookie=='1')
								$current_display = "style=\"display:block; position: relative; left: 0px; top: 0px; border: 0px; padding: 0px; margin: 0px;\"";
									else
										$current_display = "style=\"display:none; position: relative; left: 0px; top: 0px; border: 0px; padding: 0px; margin: 0px;\"";

							echo "<div><h3><a href=\"#h_7\" onclick=\"javascript:make_cookie('manage_backups');ReverseContentDisplay('manage_backups');\" name=\"h_7\">Open/Close The Console For Managing Backups</a></h3>
";
							echo "<div id=\"manage_backups\" ".$current_display.">
<div style=\"border-top: solid 1px #A9A9F2; border-left: solid 1px #B5B5ED; border-bottom: solid 1px #B5B5ED; border-right: solid 1px #A9A9F2; padding: 1em;\">
";

							echo "<p><strong>Backup</strong>
";
							echo "<br />Backup is a safe way how to keep your data always available.</p>
";
							echo "<form action=\"".$fix_uri."\" method=\"post\">
          <input type=\"hidden\" name=\"create_backup\" value=\"1\" />
          <input type=\"submit\" value=\"Create the backup now!\" />
</form>
";

							echo "<p><strong>Backup Restore</strong>
";
							echo "<br />Backup Restore is a safe way how to restore your data from the database.</p>
";

							echo "<form action=\"".$fix_uri."\" method=\"post\">
";
							$table_name = $wpdb->prefix . $table_backups;
							$max        = $wpdb->get_var("SELECT timestamp FROM $table_name ORDER BY timestamp desc limit 1;");
							$z          = 0;
							while($wpdb->get_var("SELECT id FROM $table_name WHERE timestamp = '".$max."' ORDER BY timestamp desc limit 1;")!='')
								{
									$z++;
									if($z==1)
										echo "<select name=\"restore_backup\">
";
									echo "<option value=\"".$max."\">".date("F j, Y, g:i a", $max)."</option>
";
									$max = $wpdb->get_var("SELECT timestamp FROM $table_name WHERE timestamp < '".$max."' ORDER BY timestamp desc limit 1;");
								}
							if($z!=0)
								{
									echo "</select>
";
									echo "<br /><input type=\"checkbox\" name=\"agree\" /> <font style=\"color: red\">I understand that all current settings will be overwritten!</font>
";
									echo "<br /><input type=\"submit\" value=\"Restore chosen backup!\" />
";
								}
							echo "</form>
";

							echo "<p><strong>Delete Backup</strong>
";
							echo "<br />Use this function only if you're sure what you're doing!</p>
";

							echo "<form action=\"".$fix_uri."\" method=\"post\">
";
							$table_name = $wpdb->prefix . $table_backups;
							$max        = $wpdb->get_var("SELECT timestamp FROM $table_name ORDER BY timestamp desc limit 1;");
							$z          = 0;
							while($wpdb->get_var("SELECT id FROM $table_name WHERE timestamp = '".$max."' ORDER BY timestamp desc limit 1;")!='')
								{
									$z++;
									if($z==1)
										echo "<select name=\"delete_backup\">
";
									echo "<option value=\"".$max."\">".date("F j, Y, g:i a", $max)."</option>
";
									$max = $wpdb->get_var("SELECT timestamp FROM $table_name WHERE timestamp < '".$max."' ORDER BY timestamp desc limit 1;");
								}
							if($z!=0)
								{
									echo "</select>
";
									echo "<br /><input type=\"checkbox\" name=\"agree\" /> <font style=\"color: red\">I understand that this backup will be deleted for ever!</font>
";
									echo "<br /><input type=\"submit\" value=\"Delete chosen backup!\" />
";
								}
							echo "</form>
";

							echo "</div></div></div>
";

							$current_cookie    = $_COOKIE['manage_imports'];
							if($current_cookie=='1')
								$current_display = "style=\"display:block; position: relative; left: 0px; top: 0px; border: 0px; padding: 0px; margin: 0px;\"";
									else
										$current_display = "style=\"display:none; position: relative; left: 0px; top: 0px; border: 0px; padding: 0px; margin: 0px;\"";

							echo "<div><h3><a href=\"#h_8\" onclick=\"javascript:make_cookie('manage_imports');ReverseContentDisplay('manage_imports');\" name=\"h_8\">Open/Close The Console For Managing Imports/Exports</a></h3>
";
							echo "<div id=\"manage_imports\" ".$current_display.">
<div style=\"border-top: solid 1px #A9A9F2; border-left: solid 1px #B5B5ED; border-bottom: solid 1px #B5B5ED; border-right: solid 1px #A9A9F2; padding: 1em;\">
";

							echo "<em>This feature allows you to import/export hyperlinked URLs and words along with attributes of such links between two or more blogs. Firstly you have to export data from one blog and then simply import these data into another blog. If exported word is already used in the target database, and if that word is active, all same exported words will be given the inactive attribute. If that already-existing word is inactive, attributes (meant as active/inactive) of exported words will be kept as in the original database. If exported word is already present in the target database, and if it points to the same URL as in the source database, no matter if such a word is active or not, it won't be imported (duplicate rows aren't needed, right?). Make sure that you don't store megabytes of data as you could experience problems when exporting these data into the textarea element. Generally, 2000 or 3000 words should be fine.</em>
";

							echo "<div style=\"padding: 1em;\"><form action=\"".$fix_uri."\" method=\"post\">
<input type=\"hidden\" name=\"export_links_into_textfile\" value=\"1\" /><input type=\"submit\" value=\"Export now!\" /> <strong><em>Please, bear in mind that export may require some time if your cross-linker contains plenty of data!</em></strong>
</form>
";
							if($_POST['export_links_into_textfile']=='1')
								{
									echo "<script type=\"text/javascript\">
           <!--
           function select_all(obj)
            {
             var text_val=eval(obj);
             text_val.focus();
             text_val.select();
            }
           -->
           </script>
           <strong><em>Copy&amp;paste entire text from the below-shown box and put it into the box that's designed for import. If you're not going to import right now, simply save the generated text in the <font style=\"color: #9B0004;\">txt</font> format and later use it for importing.</em></strong><br />
           <textarea cols=\"50\" rows=\"15\" readonly=\"readonly\" onclick=\"select_all(this)\">
";
									$table           = $wpdb->prefix . $table_crosslink_main;

									$i               = $wpdb->get_var("SELECT * FROM $table WHERE visible = '1' ORDER BY id ASC LIMIT 1;",0);
									$sql             = "SELECT * FROM $table WHERE id = '$i';";
									$j               = 0;
									$table_name_attrs= $wpdb->prefix . $table_crosslink_attrb;
									$remember_i      = $i;

									while($wpdb->get_var($sql,0)!='')
										{
											if($i!=$remember_i)
											echo "\n";
											echo uncheck_word(strtolower(stripslashes($wpdb->get_var("SELECT * FROM $table WHERE id = '".$i."' LIMIT 1;",1))))."\n";
											echo uncheck_word(stripslashes($wpdb->get_var("SELECT * FROM $table WHERE id = '".$i."' LIMIT 1;",2)))."\n";
											echo uncheck_word(stripslashes($wpdb->get_var("SELECT attrib FROM $table_name_attrs WHERE id = '$i' LIMIT 1;")))."\n";
											echo uncheck_word(stripslashes($wpdb->get_var("SELECT visible FROM $table WHERE id = '$i' LIMIT 1;")));

											$i   = $wpdb->get_var("SELECT id FROM $table WHERE id > '$i' ORDER BY id asc LIMIT 1;",0);
											$sql = "SELECT * FROM $table WHERE id = '$i';";
										}
									echo "</textarea>
";
								}
							echo "<br />
           <form action=\"".$fix_uri."\" method=\"post\"><strong>Import - Put output from export into the textarea below</strong><br />
           <textarea name=\"import_text_links\" cols=\"50\" rows=\"15\"></textarea><br /><input type=\"submit\" value=\"Import now!\" /> <strong><em>Please, bear in mind that import may require some time if you import plenty of data!</em></strong>
           </form>
";
							echo "</div>
";

							echo "</div></div></div>
";

							echo "<br />
";
							echo "<div style=\"border-top: solid 1px #A9A9F2; border-left: solid 1px #B5B5ED; border-bottom: solid 1px #B5B5ED; border-right: solid 1px #A9A9F2; padding: 1ex;\">
";
							echo "<h3>Important notice</h3>Since only a few people have donated, it is not necessary to donate any more. Instead, a small link is added to every page that contains at least one automatically hyperlinked word/phrase. If you don't wish this link to be added to such pages, stop using this plugin, please. The link points at <a href=\"http://www.janhvizdak.com/make-donation-cross-linker-plugin-wordpress.php\" target=\"_blank\">janhvizdak.com/make-donation-cross-linker-plugin-wordpress.php</a> - and it's not a commercial website. In the future there will be an option to disable this link after making a donation.
";

							echo "</div>
";

							echo "</div>
";
						}

function interlink_w_u($content)
	{
		global $wpdb, $table_crosslink_main, $table_crosslink_tags, $table_crosslink_chars, $table_crosslink_setts, $table_crosslink_attrb, $can_add_link;

		//speed up the process, don't load data from the database each time this function is executed
		global $linked_word, $linked_uri, $link_attribute, $processed_load, $restrict_linking, $link_to_itself, $find_sel, $no_link_s, $no_link_e, $old_i, $nn, $cut_empty_spaces;
		$processed_load++;
		//end speed up

		$fix_me          = " remove_after_crosslinking ";

		$old_z           = $i;

		$content         = $fix_me.$content.$fix_me;

		$table           = $wpdb->prefix . $table_crosslink_setts;

		if($processed_load<2)
			{
				$restrict_linking= $wpdb->get_var("SELECT value FROM $table WHERE setting = 'link_first_word' LIMIT 1;",0);
				$link_to_itself  = $wpdb->get_var("SELECT value FROM $table WHERE setting = 'link_to_itself' LIMIT 1;",0);

				if($wpdb->get_var("SELECT value FROM $table WHERE setting = 'limit_links' LIMIT 1;",0)>0)
					$find_sel  = $wpdb->get_var("SELECT value FROM $table WHERE setting = 'limit_links' LIMIT 1;",0);
						else
							$find_sel  = 0;
			}

		$content_content = $content;
		$table           = $wpdb->prefix . $table_crosslink_main;

		$i               = $wpdb->get_var("SELECT * FROM $table WHERE visible = '1' ORDER BY id ASC LIMIT 1;",0);
		$sql             = "SELECT * FROM $table WHERE id = '$i';";
		$j               = 0;
		$table_name_attrs= $wpdb->prefix . $table_crosslink_attrb;

		if($processed_load<2)
			{
				while($wpdb->get_var($sql,0)!='')
					{
						$j++;
						$linked_word[$j]    = uncheck_word(strtolower(stripslashes($wpdb->get_var("SELECT * FROM $table WHERE ( (id = '".$i."') AND (visible = '1') ) LIMIT 1;",1))));
						$linked_uri[$j]     = uncheck_word(stripslashes($wpdb->get_var("SELECT * FROM $table WHERE ( (id = '".$i."') AND (visible = '1') ) LIMIT 1;",2)));
						$link_attribute[$j] = uncheck_word(stripslashes($wpdb->get_var("SELECT attrib FROM $table_name_attrs WHERE id = '$i' LIMIT 1;")));
						$cur_j = $j;

						if(strpos($linked_word[$cur_j],"'")!==false)
							{
								$j++;
								$linked_word[$j]    = str_replace("'","&#8217;",$linked_word[$cur_j]);
								$linked_uri[$j]     = $linked_uri[$cur_j];
								$link_attribute[$j] = $link_attribute[$cur_j];
							}
						if(strpos($linked_word[$cur_j],"’")!==false)
							{
								$j++;
								$linked_word[$j]    = str_replace("’","&#8217;",$linked_word[$cur_j]);
								$linked_uri[$j]     = $linked_uri[$cur_j];
								$link_attribute[$j] = $link_attribute[$cur_j];
							}

						if($cut_empty_spaces==1)
							{
								for($pi=$cur_j;$pi<=$j;$pi++)
									{
										while(substr($linked_word[$pi],strlen($linked_word[$pi])-1,1)==' ')
											$linked_word[$pi] = substr($linked_word[$pi],0,strlen($linked_word[$pi])-1);
										while(substr($linked_word[$pi],0,1)==' ')
											$linked_word[$pi] = substr($linked_word[$pi],1,strlen($linked_word[$pi])-1);
										while(substr($linked_uri[$pi],strlen($linked_uri[$pi])-1,1)==' ')
											$linked_uri[$pi] = substr($linked_uri[$pi],0,strlen($linked_uri[$pi])-1);
										while(substr($linked_uri[$pi],0,1)==' ')
											$linked_uri[$pi] = substr($linked_uri[$pi],1,strlen($linked_uri[$pi])-1);
									}
							}

						$i   = $wpdb->get_var("SELECT id FROM $table WHERE id > '$i' ORDER BY id asc LIMIT 1;",0);
						$sql = "SELECT * FROM $table WHERE id = '$i';";
					}
				$change = 1;
				$i      = $j;
				$old_i  = $j;
				while($change==1)
					{
						$change = 0;
						for($j=1;$j<$i;$j++)
							{
								if( @strlen($linked_word[$j]) < @strlen($linked_word[($j+1)]) )
									{
										$t1                  = $linked_word[($j+1)];
										$linked_word[($j+1)] = $linked_word[$j];
										$linked_word[$j]     = $t1;

										$t1                  = $linked_uri[($j+1)];
										$linked_uri[($j+1)]  = $linked_uri[$j];
										$linked_uri[$j]      = $t1;

										$t1                     = $link_attribute[($j+1)];
										$link_attribute[($j+1)] = $link_attribute[$j];
										$link_attribute[$j]     = $t1;

										$change              = 1;
									}
							}
					}
			}

		$t2  = $wpdb->prefix . $table_crosslink_tags;

		if($processed_load<2)
			{
				$i   = $wpdb->get_var("SELECT * FROM $t2 ORDER BY id ASC LIMIT 1;",0);
				$sql = "SELECT * FROM $t2 WHERE id = '$i';";
				$nn  = 0;
				while($wpdb->get_var($sql,0)!='')
					{
						$nn++;
						$no_link_s[$nn] = $wpdb->get_var($sql,1);
						$no_link_e[$nn] = $wpdb->get_var($sql,2);

						$i   = $wpdb->get_var("SELECT * FROM $t2 WHERE id > '$i' ORDER BY id asc LIMIT 1;",0);
						$sql = "SELECT * FROM $t2 WHERE id = '$i';";
					}
				$nn++;
				$no_link_s[$nn] = "<!--nocrosslink_end-->";
				$no_link_e[$nn] = "<!--nocrosslink_start-->";
				$nn++;
			}

		for($j=1;$j<=$old_i;$j++)
			{
				$r_extra      = 0;
				$starting_pos = 0;

				while(@strpos(strtolower($content_content),$linked_word[$j],$starting_pos)!==false)
					{
						$temporary_uri= $linked_uri[$j];
						$previous_pos = $starting_pos - @strlen($linked_word[$j]);
						$starting_pos = @strpos(strtolower($content_content),$linked_word[$j],$starting_pos) + @strlen($linked_word[$j]);
						$remain       = @substr($content_content,$starting_pos);
						$presiel      = 1;

						for($x=1;$x<$nn;$x++)
							{
								if(@strpos(strtolower($remain),$no_link_s[$x])!==false)
									{
										if(@strpos(strtolower($remain),$no_link_e[$x])===false)
											$presiel = 0;
												else
													{
														if(@strpos(strtolower($remain),$no_link_s[$x])<@strpos(strtolower($remain),$no_link_e[$x]))
															$presiel = 0;
													}
									}
							}

						$remember_slash = "";
						if($temporary_uri[(@strlen($temporary_uri)-1)]=='/')
							{
								$remember_slash = "/";
								$temporary_uri  = @substr($temporary_uri,0,(@strlen($temporary_uri)-1));
							}
						if($presiel==1)
							{
								$new_slash  = 0;
								while(@strpos($temporary_uri,"/",$new_slash)!==false)
									$new_slash = @strpos($temporary_uri,"/",$new_slash) + 1;
								$point_to   = @substr($temporary_uri,$new_slash);

								$this_uri   = $_SERVER['REQUEST_URI'];

								$remember_slash1 = "";
								if($this_uri[(@strlen($this_uri)-1)]=='/')
									{
										$this_uri = @substr($this_uri,0,(@strlen($this_uri)-1));
									}

								$new_slash  = 0;
								while(@strpos($this_uri,"/",$new_slash)!==false)
									$new_slash = @strpos($this_uri,"/",$new_slash) + 1; // +1 lebo / ma dlzku 1
										$current_uri = @substr($this_uri,$new_slash);

								$var             = "post:";
								$new_point_to    = $point_to;
								$is_the_same_uri = 0;
								if(@substr($point_to,0,strlen($var))==$var)
									{
										$pageURL = 'http';
										if ($_SERVER["HTTPS"] == "on")
											{$pageURL .= "s";}
										$pageURL .= "://";
										$real_current_uri = $pageURL.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

										if($real_current_uri==assign_correct_uri($point_to))
											$is_the_same_uri = 1;
												else
													$is_the_same_uri = 0;
									}
										else
											$is_the_same_uri = 0;

								if( ( (assign_correct_uri($point_to)==$current_uri)&&($link_to_itself!=1) ) || (($link_to_itself!=1)&&($is_the_same_uri==1)) )
									{
										$presiel = 0;
									}
							}

						$z  = 1;

						$t3 = $wpdb->prefix . $table_crosslink_chars;

						$upgrade_fatal_01 = $wpdb->get_var("SELECT characters FROM $t3 WHERE id = 1 LIMIT 1;",0);
						if($upgrade_fatal_01[0]!=' ')
							$upgrade_fatal_01 = " ".$upgrade_fatal_01;
						$ending_part                        = @explode(" ", $upgrade_fatal_01);
						$ending_part_t                      = $wpdb->get_var("SELECT characters FROM $t3 WHERE id = '1' LIMIT 1;",1);
						$ending_part[@count($ending_part)]  = " ";
						$ending_part[@count($ending_part)]  = chr(10);

						$z                                = @count($ending_part);
						$found_z                          = 0;

						for($l=1;$l<$z;$l++)
							if($content_content[($starting_pos)]==$ending_part[$l])
								$found_z = 1;

						$found_z1         = 0;
						for($l=1;$l<$z;$l++)
							if($content_content[($starting_pos-@strlen($linked_word[$j])-1)]==$ending_part[$l])
								$found_z1 = 1;

						if(($found_z!=1)||($presiel!=1)||($found_z1!=1))
							$presiel = 0;

						if($presiel==1)
							{
								$r_extra++;
								$original_word      = @substr($content_content,$starting_pos-@strlen($linked_word[$j]),@strlen($linked_word[$j]));
								$supplemental       = $original_word;

								$old_part         = @substr($content_content,($previous_pos+@strlen($linked_word[$j])),($starting_pos-$previous_pos-@strlen($linked_word[$j])));
								$new_part         = @str_replace($supplemental,"<a href=\"".assign_correct_uri($temporary_uri.$remember_slash)."\" ".$link_attribute[$j].">".$supplemental."</a>",$old_part);
								$starting_pos     = @strlen($new_part)-@strlen($old_part)+$starting_pos;

								$found_invalid_code = 0;

								if(($find_sel!=0)&&($find_sel<$r_extra))
									$stop_linking = 1;
										else
											$stop_linking = 0;
								$can_link_to_src = 0;
								if( ( ( ($restrict_linking==1) && ($r_extra<2) ) || ($restrict_linking==0) ) && ( $stop_linking == 0 ) && ( $found_invalid_code == 0 ) )
									{
										$can_link_to_src = 1;
										$content_content = @str_replace($old_part,$new_part,$content_content);
									}
							}
					}
			}

		$content_content = str_replace($fix_me,"",$content_content);

		if( ($can_add_link==0) && ($can_link_to_src==1) )
			{
				$content_content .= "<p style=\"opacity:0.5;padding:0;margin:0;display:inline;\"><sub><a href=\"http://www.janhvizdak.com/make-donation-cross-linker-plugin-wordpress.php\" onclick=\"window.open('http://www.janhvizdak.com/make-donation-cross-linker-plugin-wordpress.php'); return false;\" target=\"_blank\" style=\"cursor:help;\"><b>&#187;crosslinked&#171;</b></a></sub></p>";
				$can_add_link     = 1;
			}
		return $content_content;
	}

 function interlink_uninstall()
	{
		global $wpdb;
		global $table_crosslink_main;
		global $table_crosslink_tags;
		global $table_crosslink_chars;
		global $table_crosslink_setts;

		$j     = 1;
		$t[$j] = $wpdb->prefix . $table_crosslink_main;  $j++;
		$t[$j] = $wpdb->prefix . $table_crosslink_tags;  $j++;
		$t[$j] = $wpdb->prefix . $table_crosslink_chars; $j++;
		$t[$j] = $wpdb->prefix . $table_crosslink_setts; $j++;

		for($i=1;$i<$j;$i++)
			{
				if($wpdb->get_var("SHOW TABLES LIKE '$t[$i]'") == $t[$i])
					{
						$sql = "DROP TABLE " . $t[$i];
						$wpdb->query($sql);
					}
			}
	}

 function test_func()
	{
		global $wpdb;
		global $table_crosslink_setts;

		$t4 = $wpdb->prefix . $table_crosslink_setts;

		if($wpdb->get_var("SELECT value FROM $t4 WHERE setting = 'link_to_thrusites' LIMIT 1;",0)=='1')
			echo "<p align='center' style='font-size: 10px;'>This blog uses the cross-linker plugin developed by Jan Hvizdak, owner of <a href=\"http://www.aqua-fish.net\">Aqua-Fish.Net</a></p>";
	}

 $processed_load = 0;

 //core settings
 $settings_table   = $wpdb->prefix . $table_crosslink_setts;
 $cut_empty_spaces = $wpdb->get_var("SELECT value FROM $settings_table WHERE setting = 'cut_empty_spaces' LIMIT 1;",0);
 //end core settings

 table_interlinker_install();

 if(function_exists('wp_footer'))
	add_action(wp_footer,test_func);

 if(function_exists('add_filter'))
	{
		$can_add_link = 1;
		add_filter('the_content','interlink_w_u');
		$t4 = $wpdb->prefix . $table_crosslink_setts;
		$can_add_link = 0;
		if(($wpdb->get_var("SELECT id FROM $t4 WHERE ( (setting = 'link_comments') AND (value = '1') ) LIMIT 1;",0)!='')&&(function_exists('comment_text')))
			{
				add_filter('comment_text','interlink_w_u');
			}
	}
?>
