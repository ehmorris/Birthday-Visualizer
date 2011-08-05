<?php

session_start();

require '../facebook_sdk/src/facebook.php';
require '../resources/config.php';
require '../resources/functions.php';

if ($me) {
	// only call data from facebook once, then store it in the user session
	if (!isset($_SESSION['all_data'])) {
		$all_data = get_friend_data($uid, $session['access_token']);
		$_SESSION['all_data'] = $all_data;
	} else {
		$all_data = $_SESSION['all_data'];
	}
}

/* TODO
 * 
 * - friends by zoomable timeline that shows friends by month when you zoom into
 *   a particular year, and then friends by day in that month
 * - friends by sign
 *
 */

?>

<?php if ($me) : ?>

<div id="by_month" class="section">
	<?php
	
	$months = array(
		'january', 
		'february', 
		'march', 
		'april', 
		'may', 
		'june', 
		'july', 
		'august', 
		'september', 
		'october', 
		'november', 
		'december'
	);
	
	foreach($months as $month) {
		// get all friends for this month and their info
		$friend_count = 0;
		$friend_list = array();
		foreach($all_data as $friend) {
			$birthday_array = format_birthday($friend->birthday);
			if (strtolower($birthday_array['month']) == $month) {
				$friend_count++;
				$birthday_day = $birthday_array['day'];
				$friend_list[] = "<span class=\"day\">$birthday_day</span>".
								 "<span class=\"name\">$friend->first_name $friend->last_name</span>";
			}
		}
		
		usort($friend_list, "compare_friend_date");
		
		// format friend list array into html list
		$friend_list_html = '';
		foreach($friend_list as $friend)
			$friend_list_html .= "<li>$friend</li>";
		
		echo '<div>'.
			 "<p class=\"friend_count\" data-count=\"$friend_count\">$friend_count <span>friends</span></p>".
			 "<ul class=\"friend_list\">$friend_list_html</ul>".
			 "<p class=\"month\">$month</p>".
			 '</div>';
		
		// reset for next loop
		unset($friend_count);
	}
	
	?>
</div>
	
<div id="by_year" class="section">
	<?php
	
	// check for friends from 1920 on
	for($year_start = 1920; $year_start < date('Y'); $year_start++) {
		// get all friends for this year and their info
		$friend_count = 0;
		$friend_list = array();
		foreach($all_data as $friend) {
			$birthday_array = format_birthday($friend->birthday);
			if (strtolower($birthday_array['year']) == $year_start) {
				$friend_count++;
				$birthday_year = $birthday_array['year'];
				$friend_list[] = "<span class=\"name\">$friend->first_name $friend->last_name</span>";
			}
		}	
		
		sort($friend_list, SORT_STRING);
		
		// format friend list array into html list
		$friend_list_html = '';
		foreach($friend_list as $friend)
			$friend_list_html .= "<li>$friend</li>";
		
		if ($friend_count >= 10) {
			echo '<div>'.
				 "<p class=\"friend_count\" data-count=\"$friend_count\">$friend_count <span>friends</span></p>".
				 "<ul class=\"friend_list\">$friend_list_html</ul>".
				 "<p class=\"year\">$year_start</p>".
				 '</div>';
		} 
		else if ($friend_count) {
			echo '<div>'.
				 "<p class=\"friend_count\" data-count=\"$friend_count\">&nbsp;</p>".
				 "<ul class=\"friend_list\">$friend_list_html</ul>".
				 "<p class=\"year\">$year_start</p>".
				 '</div>';
		}
		
		
		// reset for next loop
		unset($friend_count);
	}
	
	?>
</div>

<?php endif; ?>