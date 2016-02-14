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

		<link rel='stylesheet' href='css/form.css'>
		<link rel='stylesheet' href='css/navigation.css'>
		<link rel='stylesheet' href='css/songs.css'>
		<link rel='stylesheet' href='css/player.css'>
		<link rel='stylesheet' href='css/playlist.css'>
		<link rel='stylesheet' href='css/popup.css'>
		<link rel='stylesheet' href='css/context.css'>
		<link rel='stylesheet' href='css/topbar.css'>

		<script src='js/jquery.js'></script>
		<script src='js/ui.js'></script>
	</head>

	<body ontouchstart=''>
		<?php

			require_once("mysql.php");

			define("SONG_PATH", "songs/greg");
			define("SONGS_PER_LOAD", 100);

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

			function songContainer($path, $title, $artist, $album, $length, $background, $class = "songContainer", $liked = 0)
			{
				if(!$artist)
				{
					$artist = "Unknown";
				}

				if($liked)
				{
					$title = " " . $title;
				}

				$tileTextSize = (isMobile()) ? 17 : 14;
				$tileTextSize .= "px;";

				$containerStyle = "background: $background;";

				echo "<div data-path='$path' data-title='$title' data-artist='$artist' data-album='$album' data-length='$length' data-liked='$liked' class='$class' style='$containerStyle'>";

				if($class == "songContainer")
				{
					echo "<div class='songInformation'>";

					if(!isMobile())
					{
						echo "<div style='font-size: $tileTextSize'> <span class='songLength'>$length</span></div>";
					}

					echo "<div style='font-size: $tileTextSize'> <span class='songArtist'>$artist</span></div>";

					if($album)
					{
						echo "<div style='font-size: $tileTextSize'> <span class='songAlbum'>$album</span></div>";
					}

					echo "<br> </div>";

				}

				echo "<div style='font-size: $tileTextSize'><span class='songTitle'>$title</span></div>";

				echo "</div>";
			}

			$tileColors = array(
				"#FF0097",
				"#A200FF",
				"#00ABA9",
				"#8CBF26",
				"#A05000",
				"#E671B8",
				"#F09609",
				"#1BA1E2",
				"#E51400",
				"#339933"
			);

		?>