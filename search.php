<?php

	require_once("configuration/main.php");

	if(!$_POST['search'])
	{
		exit;
	}

	echo "<div id='songsSearched'>";

	$nextColor = 0;

	$songQuery = $mysql->query("SELECT * FROM `songs` WHERE `title` LIKE '%" . escape($_POST['search']) . "%' OR `artist` LIKE '%" . escape($_POST['search']) . "%' OR `album` LIKE '%" . escape($_POST['search']) . "%'");

	while($songData = $songQuery->fetch_assoc())
	{
		if(!$songColor[($songData['album']) ? $songData['album'] : $songData['artist']])
		{
			$songColor[($songData['album']) ? $songData['album'] : $songData['artist']] = $tileColors[$nextColor];

			$nextColor++;

			if($nextColor == count($tileColors))
			{
				$nextColor = 0;
			}
		}

		if($songData['album'] != $lastAlbum || !isset($lastAlbum))
		{
			echo "</span> <span class='albumGroup'>";
			$lastAlbum = $songData['album'];
		}

		songContainer($songData['path'], $songData['title'], $songData['artist'], $songData['album'], $songData['length'], $songColor[($songData['album']) ? $songData['album'] : $songData['artist']], "songContainer", $songData['liked']);
	}

	echo "</div>";

?>