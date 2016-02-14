<?php

	require_once("configuration/main.php");

	if(!$_POST['search'])
	{
		exit;
	}

	echo "<div id='songsSearched'>";

	$songQuery = $mysql->query("SELECT * FROM `songs` WHERE `title` = '" . escape($_POST['search']) . "' OR `artist` = '" . escape($_POST['search']) . "' OR `album` = '" . escape($_POST['search']) . "'");

	while($songData = $songQuery->fetch_assoc())
	{
		if($songData['album'] != $lastAlbum || !isset($lastAlbum))
		{
			echo "</span> <span class='albumGroup'>";
			$lastAlbum = $songData['album'];
		}

		songContainer($songData['path'], $songData['title'], $songData['artist'], $songData['album'], $songData['length']);
	}

	echo "</div>";

?>