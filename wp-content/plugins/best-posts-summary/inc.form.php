<?php 
if (!defined('WP_ADMIN')) {
    die("Do not run this file directly!");
}
?>
<div class="wrap">
	<h2>Settings</h2>
	<table width="100%" border="0" cellpadding="0" cellspacing="0">
		<?php
		if($msg){
			echo "<tr>
					<td><span style='font-size:16px'>".$msg."</span></td>
				</tr>";
		}
		?>
		<tr>
			<td>
			<form name="form1" method="post" action="">
			  <table width="100%" border="0" cellspacing="5" cellpadding="5">
                <tr>
                  <td width="20%" align="right">Post Frequency: </td>
                  <td width="80%">
				  <select name="wpbo_frequency"  id="wpbo_frequency" onchange="wpbo_check_period()">
					  <option value="0" <? if($this->frequency==BP_FREQUENCY_NEVER) echo "selected"; ?> >[ Disabled ]</option>
					  <option value="1" <? if($this->frequency==BP_FREQUENCY_DAILY) echo "selected"; ?> >Daily</option>
					  <option value="2" <? if($this->frequency==BP_FREQUENCY_WEEKLY) echo "selected"; ?> >Weekly</option>
					  <option value="3" <? if($this->frequency==BP_FREQUENCY_MONTHLY) echo "selected"; ?> >Monthly</option>
				  </select>
				  </td>
                </tr>
				<tr>
                  <td width="20%" align="right">Posts from Period: </td>
                  <td width="80%">
				  <select name="wpbo_period" id="wpbo_period"  onchange="wpbo_check_period()">
					  <option value="1" <? if($this->period==BP_FREQUENCY_DAILY) echo "selected"; ?> >Previous Day</option>
					  <option value="2" <? if($this->period==BP_FREQUENCY_WEEKLY) echo "selected"; ?> >Previous Week</option>
					  <option value="3" <? if($this->period==BP_FREQUENCY_MONTHLY) echo "selected"; ?> >Previous Month</option>
				  </select>
				  </td>
                </tr>
				<tr>
                  <td align="right">Number of Posts: </td>
                  <td>
				  	<input type="text" name="wpbo_posts" value="<? echo $this->num_posts; ?>" size="3"/>
				  </td>
                </tr>
				<tr>
                  <td align="right">Strict Criteria? </td>
                  <td>
				  	<input type="checkbox" name="wpbo_strict" value="1" <? if($this->strict == 1) echo "checked"; ?>/> (When this box is checked, posts with no comments will not be included, when the comments criteria is selected)
				  </td>
                </tr>
				<tr>
                  <td align="right">Selected Categories: </td>
                  <td>
				  <input type="checkbox" value="-1" name="wpbo_allcats" <? if(empty($this->post_cats)) echo "checked"; ?>/>All Categories?<br /><br />
				  <select name="wpbo_categories[]" multiple="multiple" size="5">
					  <?php
					  $categories=  get_categories('hide_empty=0'); 
					  foreach ($categories as $cat) {
					  	$sel = "";
						if(in_array($cat->cat_ID , $this->post_cats)) $sel = "selected";
						
						$option = '<option value="'.$cat->cat_ID.'" '.$sel.'>';
						$option .= $cat->cat_name;
						$option .= '</option>';
						echo $option;
					  }
					  ?>
				  </select>
				  </td>
                </tr>
				<tr>
                  <td align="right" valign="top">Title of Post: </td>
                  <td>
				  	<input type="text" name="wpbo_post_title" value="<? echo $this->post_title; ?>" style="width:99%"/>
				  	<br />
					<br />
			  	    <strong>Tags :</strong> <br />
		  	      [blog-name] : Blog name <br />
				  [day] : Which day the posts belong to i.e Mon to Sun<br />
				  [date] : Which date the posts belong to i.e 1st to 31st<br />
		  	      [week] : Which week the posts belong to i.e 3rd March - 9th March<br />
		  	      [month] : Which month the posts belong to i.e March<br />
				  [year] : Which year the posts belong to i.e 2008
				  </td>
                </tr>
				<tr>
                  <td align="right">Ordering: </td>
                  <td>
				  	<select name="wpbo_ordering">
					  <option value="1" <? if($this->ordering==BP_ORDERING_TITLE) echo "selected"; ?> >Title</option>
					  <option value="2" <? if($this->ordering==BP_ORDERING_DATE) echo "selected"; ?> >Date</option>
					  <option value="3" <? if($this->ordering==BP_ORDERING_RANDOM) echo "selected"; ?> >Random</option>
					  <option value="4" <? if($this->ordering==BP_ORDERING_CRITERIA) echo "selected"; ?> >Sort By Criteria</option>
				  	</select>
				  </td>
                </tr>
				<tr id="tr_words" > <!-- style="display:<? echo $tr_display ?>" -->
                  <td align="right">Number of Words from Text: </td>
                  <td>
				  	<input type="text" size="3" name="wpbo_wordnumber" value="<? echo $this->max_words ?>" />
				  </td>
                </tr>
				<tr>
                  <td align="right">Criteria: </td>
                  <td>
				  	<select name="wpbo_criteria">
					  <option value="1" <? if($this->criteria==BP_CRITERIA_COMMENTS) echo "selected"; ?> >Number Of Comments</option>
					  <? if($this->is_popularity_contest_installed()) { ?>
					  <option value="2" <? if($this->criteria==BP_CRITERIA_VIEWS) echo "selected"; ?> >Number Of Visits</option>
					  <? } ?>
				  	</select>
				  </td>
                </tr>
				<tr>
                  <td align="right" valign="top">Post Template: </td>
                  <td><p>
                    <textarea name="wpbo_text" style="width:99%;height:300px"><? echo htmlentities($this->template); ?></textarea>
                    <br>
                      <strong><br />
                      Template Tags:</strong><br />
                    
					[title] : Post Title <br />
                    [link] : Post Link<br />
					[date] : Post Date<br />
					[author] : Post Author<br />
					[category] : Post Category<br />
                    [text] : Post Content <br />
                  [VisitsOrComments] : Shows &quot;Comments&quot; or &quot;Views&quot; based on critaria<br />
                  [count] : Shows comment or view count </p>
                    <p><br />
                      <strong>			      Example:</strong><br />
                      &lt;div&gt;start text&lt;/div&gt;<br />
                      &lt;ul&gt;<br />
                      [loop_start]<br />
                      &lt;li&gt;<br />
                      &lt;b&gt;&lt;a href='[link]'&gt;[title]&lt;/a&gt;&lt;/b&gt;<br />
                      &lt;div&gt;Posted on [date] in [category]&lt;/div&gt;<br />
                      &lt;div&gt;&lt;b&gt;[VisitsOrComments]&lt;/b&gt;: ([count])&lt;/div&gt;<br />
                      &lt;div&gt;[text]&lt;/div&gt;<br />
                      &lt;/li&gt;<br />
                      [loop_end]<br />
                      &lt;/ul&gt;<br />
                      &lt;div&gt;end text&lt;/div&gt;</p>
                  </td>
                </tr>
				<tr>
                  <td>&nbsp;</td>
                  <td><input type="submit" value="Save Settings"></td>
                </tr>
              </table>
            </form>
			</td>
		</tr>
	</table>
    <?php 
        if (!wp_next_scheduled('wpbo_on_cron_event')) {
            $this->schedule_event();
        }
    ?>
    <small>Next scheduled check: <? echo wp_next_scheduled('wpbo_on_cron_event'); ?></small>
</div>
<script>
	function wpbo_check_period(){
		freq = document.getElementById('wpbo_frequency').value;
		period = document.getElementById('wpbo_period').value;
		if(freq > 0){
			if(freq < period){
				alert('WARNING: The period selected is higher than the frequency, this will cause duplicate results.');
			}
		}
	}
</script>			
