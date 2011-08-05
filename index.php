<?php

session_start();

require 'facebook_sdk/src/facebook.php';
require 'resources/config.php';

// get rid of unsightly facebook session info in the url
if (isset($_GET['session']))
	header('location: /birthday');

?>

<!DOCTYPE html>
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<title>Birthday Visualizer</title>
		<link href="css/main.css" rel="stylesheet" type="text/css" media="screen" />
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.6.1/jquery.min.js"></script>
		<script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jqueryui/1.8.13/jquery-ui.min.js"></script>
		<script type="text/javascript" src="js/jqueryDataset.js"></script>
		<script type="text/javascript" src="js/display.js"></script>
	</head>
	<body>
		<?php if (!$me) : ?>
		
			<div id="header">
				<h1>Birthday Visualizer</h1>
			</div>
			<div id="login"><a href="<?= $facebook->getLoginUrl($perms) ?>">connect</a></div>
			
		<?php else : ?>
		
			<div id="header">
				<h1>Birthday Visualizer</h1>
			</div>
			<div id="loading">loading <img src="loading.gif" /> <span>may take up to 30 seconds</span></div>
			
		<?php endif; ?>
	</body>
</html>