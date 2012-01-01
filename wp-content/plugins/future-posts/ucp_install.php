<?php
/*
Plugin Name: Future Posts
Plugin URI: http://webloader.com/561/jedi-wordpress-solutions/
Version: 1.4
Description: Trigger/unpack and preview future scheduled posts, based on mrlive work (deactivate Sociable plugin when run this on blank WP page)
Author: Mark Kaz
Author URI: http://webloader.com/
*/
function ucp_headaction()
{
	echo '<link rel="stylesheet" href="'.get_bloginfo('wpurl').'/'.PLUGINDIR.'/'.dirname( plugin_basename(__FILE__) ).'/fut.css" type="text/css" />';
}

function ucp_control_gen($ucp_title, $ucp_num, $ucp_nopost, $ucp_time ,$show_time, $show_excerpt)  /* setup */
{
?>
<p>
	<label for="ucp-title"><?php _e('Title:'); ?>
		<input class="widefat" id="ucp-title" name="ucp-title" type="text" value="<?php echo attribute_escape($ucp_title); ?>" />
	</label>
</p>
<p>
	<label for="ucp-num"><?php _e('Number of posts to show:'); ?>
		<input class="widefat" id="ucp-num" name="ucp-num" type="text" value="<?php echo attribute_escape($ucp_num); ?>" />
	</label>
</p>
<p>
	<label for="ucp_nopost"><?php _e('If no post:'); ?>
		<input class="widefat" id="ucp-nopost" name="ucp-nopost" type="text" value="<?php echo attribute_escape($ucp_nopost); ?>"	/>
	</label>
</p>
<p>
	<label for="ucp_time"><?php _e('Time format:'); ?>
		<input class="widefat" id="ucp-time" name="ucp-time" type="text" value="<?php echo attribute_escape($ucp_time); ?>"	/>
	</label>
</p>
<p>
	<label for="show-time">
		<input class="checkbox" type="checkbox"
			id="show-time" name="show-time"
			<?php echo $show_time ? 'checked="checked"' : ''; ?>
		>
		<?php _e('Show Timestamp'); ?>
	</label>
</p>
<p>
	<label for="show-excerpt">
		<input class="checkbox" type="checkbox"
			id="show-excerpt" name="show-excerpt"
			<?php echo $show_excerpt ? 'checked="checked"' : ''; ?>
		>
		<?php _e('Show Excerpt'); ?>
	</label>
</p>
<input type="hidden" id="ucp-widget-submit" name="ucp-widget-submit"   value="1">
<?php
}

function ucp_control() {
	$options = $newoptions = get_option('ucp_content_gen');
	if ($_POST['ucp-widget-submit']) {
			$newoptions['ucp-title'] = strip_tags(stripslashes($_POST['ucp-title']));
			$newoptions['ucp-num'] = strip_tags(stripslashes($_POST['ucp-num']));
			$newoptions['ucp-nopost'] = strip_tags(stripslashes($_POST['ucp-nopost']));
			$newoptions['ucp-time'] = strip_tags(stripslashes($_POST['ucp-time']));
			$newoptions['show-time'] = isset($_POST['show-time']);
			$newoptions['show-excerpt'] = isset($_POST['show-excerpt']);
	}

	if ($options != $newoptions) {
		$options = $newoptions;
		update_option('ucp_content_gen', $options);
	}

	ucp_control_gen(
		$options['ucp-title'],
		$options['ucp-num'],
		$options['ucp-nopost'],
		$options['ucp-time'],
		$options['show-time'],
		$options['show-excerpt']);
	}

function ucp_content_gen($args)  /* show content */
{
	extract($args);
	$options = get_option('ucp_content_gen');
	$ucp_title =empty($options['ucp-title']) ?
		__('Future Posts') :
		$options['ucp-title'];
	$ucp_num =empty($options['ucp-num']) ?
		__('5') :
		$options['ucp-num'];
	$ucp_nopost =empty($options['ucp-nopost']) ?
		__('No Future Posts ...') :
		$options['ucp-nopost'];
	$ucp_time =empty($options['ucp-time']) ?
		__('d.m.Y') :
		$options['ucp-time'];
	$show_time = $options['show-time'] ? true: false;
	$show_excerpt = $options['show-excerpt'] ? true: false;
	echo $before_widget ;
	echo $before_title . $ucp_title . $after_title;
	include(PLUGINDIR.'/'.dirname( plugin_basename(__FILE__) ).'/fp_widget.php');
	echo $after_widget;
}

function ucp_show($ucp_title, $ucp_num, $ucp_nopost, $ucp_time, $show_time, $show_excerpt) {
	echo $before_title . $ucp_title . $after_title;
	include(PLUGINDIR.'/'.dirname( plugin_basename(__FILE__) ).'/fp_widget.php');
	echo $after_widget;
}

function ucp_init() /* register */
{
	register_sidebar_widget(__('Future Posts'),'ucp_content_gen');
	register_widget_control(__('Future Posts'),'ucp_control');
	
}
add_action("plugins_loaded", "ucp_init");
add_action("wp_head","ucp_headaction");

?>
