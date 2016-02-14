<?php

	require_once("configuration/main.php");

	echo "<div id='songsLoaded'>";

	$mQuery = $mysql->query("SELECT `srecord` FROM `record` WHERE `unique_id` = '" . $_SESSION['unique_id'] . "'");
	$mData = $mQuery->fetch_assoc();

	$mQuery = $mysql->query("SELECT * FROM `albums` " . substr($mData['srecord'], 0, -5) . " ORDER BY RAND()");

	while($mData = $mQuery->fetch_assoc())
	{
		$songQuery = $mysql->query("SELECT * FROM `songs` WHERE `album` = '" . $mData['album'] . "'");

		while($songData = $songQuery->fetch_assoc())
		{
			$songsLoaded++;

			if($songData['album'] != $lastAlbum || !isset($lastAlbum))
			{
				echo "</span> <span class='albumGroup'>";
				$lastAlbum = $songData['album'];
			}

			songContainer($songData['path'], $songData['title'], $songData['artist'], $songData['album'], $songData['length']);
		}

		$mysql->query("UPDATE `record` SET `srecord` = CONCAT(srecord, '`id` <> " . $mData['id'] . " AND ') WHERE `unique_id` = '" . $_SESSION['unique_id'] . "'");

		if($songsLoaded >= SONGS_PER_LOAD)
		{
			break;
		}
	}

	echo "</div>";

?>