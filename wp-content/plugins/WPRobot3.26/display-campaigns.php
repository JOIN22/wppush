<div class="wrap">
<style type="text/css">
h3 a,h2 a {font-size:80%;text-decoration:none;margin-left:10px;}
</style>

<?php wpr_get_versions(); ?>
<h2><?php _e("WP Robot Campaigns","wprobot") ?> <a href="?page=wpr-add"><?php _e("&rarr; Create Campaign","wprobot") ?></a></h2>

	<?php $records = $wpdb->get_results("SELECT * FROM " . $wpr_table_campaigns . " ORDER BY id ASC"); 
	if ($records) {
	?>
	<form id="campaigns" method="post">	
<table class="widefat post fixed" cellspacing="0">	
	<thead>
		<tr>
			<th id="cb" class="manage-column column-cb check-column" style="" scope="col">
				<input type="checkbox"/>
			</th>
			<th id="name" class="manage-column column-name" style="" scope="col"><?php _e("Name","wprobot") ?></th>
			<th id="title" class="manage-column column-title" style="" scope="col"><?php _e("Keywords","wprobot") ?></th>
			<th id="categories" class="manage-column column-categories" style="" scope="col"><?php _e("Categories","wprobot") ?></th>
			<th id="author" class="manage-column column-author" style="width:60px;" scope="col"><?php _e("Posts Created","wprobot") ?></th>			
			<th id="date" class="manage-column column-date" style="width:160px;" scope="col"><?php _e("Next Post","wprobot") ?></th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<th class="manage-column column-cb check-column" style="" scope="col"><input type="checkbox"/>
			</th>
			<th class="manage-column column-name" style="" scope="col"><?php _e("Name","wprobot") ?></th>
			<th class="manage-column column-title" style="" scope="col"><?php _e("Keywords","wprobot") ?></th>
			<th class="manage-column column-categories" style="" scope="col"><?php _e("Categories","wprobot") ?></th>
			<th class="manage-column column-author" style="width:60px;" scope="col"><?php _e("Posts Created","wprobot") ?></th>			
			<th class="manage-column column-date" style="width:160px;" scope="col"><?php _e("Next Post","wprobot") ?></th>
		</tr>
	</tfoot>	
	<tbody>	
      <?php
		$red = 0;
         
         foreach ($records as $record) {
			$keywords = unserialize($record->keywords);		
			$categories = unserialize($record->categories);		
			
		 	$time = $record->cinterval;
			$span = $record->period;
	 
		 ?>	
	
		<tr class="alternate author-self status-publish iedit" valign="top" <?php if($record->ctype == "keyword") {echo 'style="background:#EFFBFB;"';} elseif($record->ctype == "rss") {echo 'style="background:#F5FBEF;"';} elseif($record->ctype == "nodes") {echo 'style="background:#F5EFFB;"';} ?>>		
			<th class="check-column" scope="row">
				<input type="checkbox" value="<?php echo $record->id; ?>" name="delete[]"/>
			</th>
			<td class="post-title column-title">
				<strong><a title="View Details" href="admin.php?page=wpr-single&id=<?php echo $record->id; ?>"><?php echo $record->name; ?></a><?php if($record->ctype == "rss") {echo " (RSS)";} ?><?php if($record->ctype == "nodes") {echo " (BrowseNodes)";} ?></strong>
							
				<div class="row-actions">
					<span class="copy">
					<a title="<?php _e("Copy Campaign","wprobot") ?>" href="admin.php?page=wpr-add&ccopy=<?php echo $record->id; ?>"><?php _e("Copy","wprobot") ?></a>
					|
					</span>				
					<span class="edit">
					<a title="<?php _e("Edit Campaign","wprobot") ?>" href="admin.php?page=wpr-add&edit=<?php echo $record->id; ?>"><?php _e("Edit","wprobot") ?></a>
					|
					</span>
					<?php if($record->pause == 0) { ?>
					<span class="pause">
					<a title="<?php _e("Pause Campaign","wprobot") ?>" href="admin.php?page=wpr-campaigns&pause=<?php echo $record->id; ?>"><?php _e("Pause","wprobot") ?></a>
					|
					<?php } else { ?>
					<span class="pause">
					<a title="<?php _e("Continue Campaign","wprobot") ?>" href="admin.php?page=wpr-campaigns&unpause=<?php echo $record->id; ?>"><?php _e("Continue","wprobot") ?></a>
					|						
					</span>		
					<?php } ?>					
					<span class="delete">
					<!--<a class="submitdelete" href="admin.php?page=wpr-campaigns&delete=<?php echo $record->id; ?>" title="<?php _e("Delete this campaign","wprobot") ?>"><?php _e("Delete","wprobot") ?></a>-->
					<a class="submitdelete" onclick="return confirm('<?php _e("Are you sure you want to delete this campaign?","wprobot") ?>')" href="admin.php?page=wpr-campaigns&delete=<?php echo $record->id; ?>" title="<?php _e("Delete this campaign","wprobot") ?>"><?php _e("Delete","wprobot") ?></a>
					|
					</span>
					<span class="view">
					<a rel="permalink" title="<?php _e("Create Post","wprobot") ?>" href="admin.php?page=wpr-campaigns&wpr_runnow=<?php echo $record->id; ?>"><?php _e("Post Now","wprobot") ?></a>
					</span>
				</div>				
				
			</td>
			
			<td class="keywords column-categories"><?php 
			$kwnum = count($keywords);
			if($record->ctype == "keyword") {
				$max = 3;
				$more = $kwnum-$max;
				for ($i = 0; $i < $max; $i++) {
					echo $keywords[$i][0];
					if($keywords[$i+1][0]) {echo ", ";}
				} 
				if($more > 0) {echo ' <a href="admin.php?page=wpr-single&id='.$record->id.'">'.$more.' '.__("more keywords","wprobot").'</a>';}
			} elseif($record->ctype == "rss") {
				echo ' <a href="admin.php?page=wpr-single&id='.$record->id.'">'.$kwnum.' '.__("RSS feeds","wprobot").'</a>';
			} elseif($record->ctype == "nodes") {
				echo ' <a href="admin.php?page=wpr-single&id='.$record->id.'">'.$kwnum.' '.__("BrowseNodes","wprobot").'</a>';
			} 
			?></td>	
			
			<td class="categories column-categories"><?php 
			$kwnum = count($categories);
			$max = 3;
			$more = $kwnum-$max;
			for ($i = 0; $i < $max; $i++) {
				echo $categories[$i]["name"];
				if($categories[$i+1]["id"]) {echo ", ";}
			} 
			if($more > 0) {echo ' <a href="admin.php?page=wpr-single&id='.$record->id.'">'.$more.' '.__("more categories","wprobot").'</a>';}				
			?></td>
			<td class="author column-author">
				<?php echo '<strong>'.$record->posts_created.'</strong>'; ?>		
			</td>

			<td class="date column-date">
			<?php if($record->pause == 0) { ?>
			<?php echo date('m/j/Y H:i:s',wp_next_scheduled("wprobothook",array($record->id)));?><br/><span style="color:#666;">(<?php _e("every","wprobot") ?> <?php echo $time." ".$span; ?>)</span>
			<?php } else { ?>
			<?php _e("Campaign <b>paused</b>","wprobot") ?>
			<?php } ?>
			</td>
		</tr>	
		<?php } ?>
	</tbody>
</table>	

	<div>
		<div style="margin-top: 20px;height:70px;padding:5px;float:left;margin-right: 2%;width:65%;border:1px solid #e3e3e3;-moz-border-radius:4px;">
			<div style="float:left;margin-right: 50px;">
			<b><?php _e("Bulk Post","wprobot") ?></b><br/>
			<?php _e("Number of Posts:","wprobot") ?> <input size="3" style="background:#fff;" name="wpr_bulk" type="text" value="1"/><br/>	
			<input style="margin: 2px;" class="button-secondary" type="submit" name="wpr_runnow" value="<?php _e("Post Now","wprobot") ?>"/>	
			</div>		
			<div style="float:left;margin-right: 10px;">			
			<input name="backdate" type="checkbox" id="backdate" value="yes" /> <b><?php _e("Backdate?","wprobot") ?></b><br/>
			<?php _e("Start Date:","wprobot") ?> <input size="11" style="background:#fff;" name="time" type="text" id="time" value="<?php echo date('Y-m-d'); ?>"/><br/>
			<?php _e("Between Posts:","wprobot") ?> <input size="3" style="background:#fff;" name="timespace" type="text" id="time" value="1"/> <?php _e("to","wprobot") ?> <input size="3" style="background:#fff;" name="timespace2" type="text" id="time" value="2"/> <?php _e("day(s)","wprobot") ?>
			</div>			
		</div>
		<div style="margin-top: 20px;height:70px;padding:5px;float:left;width:30%;border:1px solid #e3e3e3;-moz-border-radius:4px;">
			<ul>
				<li><input class="button-secondary" type="submit" onclick="return confirm('<?php _e("Are you sure you want to delete all selected campaigns?","wprobot") ?>')" name="deleteall" value="<?php _e("Delete Selected Campaigns","wprobot") ?>"/></li>
				<li><input class="button-secondary" type="submit" name="pause" value="<?php _e("Pause Selected","wprobot") ?>"/> <input class="button-secondary" type="submit" name="continue" value="<?php _e("Continue Selected","wprobot") ?>"/></li>
			</ul>
		</div>	
	</div>
	<div style="clear:both;"></div>
<!--
	<div style="height:75px;padding:8px 12px;margin: 20px 20px 0 0;border:1px solid #e3e3e3;-moz-border-radius:5px;float:left;">
		<div style="float:left;margin-right: 50px;">
		<b><?php _e("Bulk Post","wprobot") ?></b><br/>
		<?php _e("Number of Posts:","wprobot") ?> <input size="3" style="background:#fff;" name="wpr_bulk" type="text" value="1"/><br/>	
		<input style="margin: 2px;" class="button-secondary" type="submit" name="wpr_runnow" value="<?php _e("Post Now","wprobot") ?>"/>	
		</div>		
		<div style="float:left;margin-right: 10px;">			
		<input name="backdate" type="checkbox" id="backdate" value="yes" /> <b><?php _e("Backdate?","wprobot") ?></b><br/>
		<?php _e("Start Date:","wprobot") ?> <input size="11" style="background:#fff;" name="time" type="text" id="time" value="<?php echo date('Y-m-d'); ?>"/><br/>
		<?php _e("Between Posts:","wprobot") ?> <input size="3" style="background:#fff;" name="timespace" type="text" id="time" value="1"/> <?php _e("to","wprobot") ?> <input size="3" style="background:#fff;" name="timespace2" type="text" id="time" value="2"/> <?php _e("day(s)","wprobot") ?>
		</div>	
	</div>	
	<div style="clear:both;"></div>		
-->
</form>	
		 <?php } else {_e('<br/><br/>You have not set up any campaigns yet. Start now by <a href="?page=wpr-add"><b>creating your first campaign</b></a>!',"wprobot");} ?>

	 
		 
	<div class="updated" style="margin-top: 60px;">
		<div style="float:left;margin-right:15px;">		
			<h3 style="margin: 10px 0 0 0;"><?php _e("Quick Links","wprobot") ?></h3>
			<p><a target="_blank" href="http://wprobot.net/">WP Robot</a> | <a target="_blank" href="http://wprobot.net/documentation"><?php _e("Documentation","wprobot") ?></a> | <a target="_blank" href="http://wprobot.net/forum"><?php _e("Support","wprobot") ?></a> | <a target="_blank" href="http://wprobot.net/robotpal/sendnew.php"><?php _e("New Download","wprobot") ?></a></p>
		</div>	
		<div style="float:left;">
			<h3 style="margin: 10px 0 0 0;"><?php _e("Other Services","wprobot") ?></h3>
			<p><a target="_blank" href="http://wpshoppingpages.com/">WP Shopping Pages</a> | <a target="_blank" href="http://blogthemesclub.com/">BlogThemesClub</a> | <a target="_blank" href="http://wpscoop.com/">WP Scoop</a></p>
		</div>
		<div style="clear:left;"> </div>		
	</div>
	
	<div class="updated" style="margin-top: 20px;">
		<h3 style="margin: 10px 0 0 0;"><?php _e("New: Manage a large number of WP Robot autoblogs simultaneously with the WP Robot Control Center!","wprobot") ?></h3>
		<p><a href="http://cc.wprobot.net/"><strong><?php _e("More Information >>","wprobot") ?></strong></a></p>
	</div>		
		 
	<div class="updated" style="margin-top: 20px;">
		<h3 style="margin: 10px 0 0 0;"><?php _e("License Information","wprobot") ?></h3>
		<p><?php wpr_cc($options['wpr_core']); ?></p>
	</div>
		
</div>
