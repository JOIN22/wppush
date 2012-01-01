<?php
	if ($just_draft) {$ucp_post_status = draft;}else {$ucp_post_status = future;}
	$takethem = new WP_Query("post_status=$ucp_post_status&order=ASC&showposts=$ucp_num");
	if ($takethem->have_posts()){ while ($takethem->have_posts()) :$takethem->the_post();	$do_not_duplicate = $post->ID;?>
			<span id="ucp_content">
				<ul>
					<li>
						<span class="ucp_showtitle"><?php the_title(); ?></span>
						<div class="ucp_showtime"><?php if ($show_time) {the_time("$ucp_time");}?></div>
						<div class="ucp_showexcerpt"><?php if ($show_excerpt) {the_excerpt();} ?></div>
					</li>
				</ul>
			</span>
	<?php endwhile; } 
	else { ?>
					<ul>
						<li><?php echo $ucp_nopost; ?></li>
					</ul>	
	<?php } ?>
	



