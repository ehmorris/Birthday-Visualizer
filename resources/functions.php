<?php

function get_graph_json($uid, $token, $method = null) {		
	if ($method)
		$url = "https://graph.facebook.com/$uid/$method?access_token=$token";
	else
		$url = "https://graph.facebook.com/$uid?access_token=$token";
	
	return json_decode(file_get_contents($url));
}

// take birthday format given by facebook and convert to unix timestamp
// then return an array with all given info
function format_birthday($date) {
	// birthday includes day, month, and year (ex: 6/28/1988)
	if (strlen($date) > 6) {
		$unix_date = strtotime($date);
		// return date("F jS Y", $unix_date);
		return array('month' => date("F", $unix_date),
					 'day' => date("j", $unix_date),
					 'year' => date("Y", $unix_date));
	} 
	// birthday only includes day and month (ex: 6/28)
	else {
		// add on spoof year to get strtotime() to work
		$unix_date = strtotime($date . '2000');
		// don't display spoof year
		// return date("F jS", $unix_date);
		return array('month' => date("F", $unix_date),
					 'day' => date("j", $unix_date));
	}
}

// uses facebook API to get user objects of all the current user's friends
// returns an array of user objects
function get_friend_data($uid, $token) {
	// get list of all friends
	$friends = get_graph_json($uid, $token, 'friends');
	
	// fill $batch with relative urls for facebook to batch process
	$batch = array();
	// keep track of requests: max of 20 items per batch request
	$requests = 0;
	// store all fetched request data in one variable
	$all_data = array();
	// cycle through friends, build batch requests, and get data
	foreach ($friends->data as $friend) {
		// url used is relative to batch request endpoint
		// see: https://developers.facebook.com/docs/api/batch/
		$friend_user_object_url = $friend->id;
		$batch[] = array(
			"method" => "GET", 
			"relative_url" => $friend_user_object_url
		);
		
		$requests++;
		
		// do a batch request every 20 cycles, max items per request is 20
		if ($requests == 20) {
			// required fb params for batch request
			$params = array(    
				'access_token' => $token,
				'batch' => json_encode($batch)
			);
			
			// cURL options
			$options_arr = array(
			    CURLOPT_URL => "https://graph.facebook.com/",
			    CURLOPT_POSTFIELDS => $params,
			    CURLOPT_RETURNTRANSFER => 1
			);
			
			// cURL request
			$curl_session = curl_init();
			curl_setopt_array($curl_session, $options_arr);
			$result = curl_exec($curl_session);
			curl_close($curl_session);
			
			$result = json_decode($result);
			
			// get important info from result and save in $all_data
			foreach($result as $value)
				$all_data[] = json_decode($value->body);
			
			// reset requests counter and batch array
			$requests = 0;
			$batch = array();
		}
	}
	
	// remove friends who don't share their birthday info
	foreach($all_data as $key => $friend) {
		if (!$friend->birthday)
			unset($all_data[$key]);
	}
	
	return $all_data;
}

// sort friend list, used in usort function
function compare_friend_date($a, $b) {
	// extract numbers from html strings
	$a = intval(preg_replace("/[^0-9]/","", $a));
	$b = intval(preg_replace("/[^0-9]/","", $b));
	
	if ($a > $b)
		return 1;
	else if ($a < $b)
		return -1;
	else
		return 0;
}