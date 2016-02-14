<!DOCTYPE html>

<html lang='en'>
	<head>
		<title>Media Center</title>

		<meta charset='UTF-8'>

		<meta name='keywords' content='Media,Audio,Music,Song,Video,Movie,Center,Site,Player,Stream'>
		<meta name='description' content='Media Center'>
		<meta name='author' content='Andrew Guterman'>

		<link rel='Shortcut Icon' href='images/favicon.png'>

		<link rel='stylesheet' href='css/global.css'>

		<link rel='stylesheet' href='css/font-awesome.css'>
		<link rel='stylesheet' href='css/fonts/lato/stylesheet.css'>
		<link rel='stylesheet' href='css/fonts/segoeui/segoeui.css'>
		<link rel='stylesheet' href='css/fonts/segoeuisl/segoeuisl.css'>

		<link rel='stylesheet' href='css/topbar.css'>
		<link rel='stylesheet' href='css/navigation.css'>
		<link rel='stylesheet' href='css/menu.css'>
		<link rel='stylesheet' href='css/player.css'>
		<link rel='stylesheet' href='css/search.css'>

		<script src='js/jquery.js'></script>
		<script src='js/ui.js'></script>
		<script src='js/jquery.jplayer.min.js'></script>
	</head>

	<body ontouchstart=''>
		<?php

			require_once("mysql.php");

			define("SONG_PATH", "../songs/greg");
			define("SONGS_PER_LOAD", 60);

			function redirect($page, $time = 0)
			{
				echo "<meta http-equiv='Refresh' content='$time; url=$page'>";
				exit;
			}

			function password($text)
			{
				for($hashIndex = 0; $hashIndex < 1000; $hashIndex++)
				{
					$text = hash("whirlpool", $text);
				}

				return $text;
			}

			function cookie($name, $value)
			{
				return setcookie($name, $value, time() + 2592000);
			}

			function successMessage($message)
			{
				echo
				"<div class='formSuccessNotice'>
					<span class='formNoticeIcon'></span> $message
				</div>

				<br>";
			}

			function errorMessage($message)
			{
				echo
				"<div class='formErrorNotice'>
					<span class='formNoticeIcon'></span> $message
				</div>

				<br>";
			}

			function isMobile()
			{   
			    return preg_match('/(alcatel|amoi|android|avantgo|blackberry|benq|cell|cricket|docomo|elaine|htc|iemobile|iphone|ipad|ipaq|ipod|j2me|java|midp|mini|mmp|mobi|motorola|nec-|nokia|palm|panasonic|philips|phone|sagem|sharp|sie-|smartphone|sony|symbian|t-mobile|telus|up\.browser|up\.link|vodafone|wap|webos|wireless|xda|xoom|zte)/i', $_SERVER['HTTP_USER_AGENT']);
			}

			function songContainer($path, $title, $artist, $album, $length, $class = "songContainer", $liked = 0)
			{
				echo "<div data-path='../$path' data-title='$title' data-artist='$artist' data-album='$album' data-length='$length' class='menuItem $class'>";

				echo $title;

				echo "</div>";
			}

		?>