<?php

	require_once("configuration/main.php");

	if(!$_POST['artist'])
	{
		exit;
	}

	echo "<div id='similarSongs'>";

	$nextColor = 0;
	
	$mQuery = $mysql->query("SELECT * FROM `similar` WHERE `artist` = '" . escape($_POST['artist']) . "'");

	if($mQuery->num_rows)
	{
		while($mData = $mQuery->fetch_assoc())
		{
			$songQuery = $mysql->query("SELECT * FROM `songs` WHERE `artist` = '" . escape($mData['similar']) . "'");

			while($songData = $songQuery->fetch_assoc())
			{
				$nextColor++;

				if($nextColor == count($tileColors))
				{
					$nextColor = 0;
				}

				songContainer($songData['path'], $songData['title'], $songData['artist'], $songData['album'], $songData['length'], $tileColors[$nextColor], "songContainer", $songData['liked']);
			}
		}
	}
	else
	{
		$xmlHandle = simplexml_load_string(file_get_contents("http://ws.audioscrobbler.com/2.0/?method=artist.getsimilar&artist=" . urlencode($_POST['artist']) . "r&autocorrect=1&api_key=170ca8cf6781c668254a6524c3713d56"));

		if($xmlHandle)
		{
			foreach($xmlHandle->children() as $arrayValue)
			{
				foreach($arrayValue->children() as $arrayValueEx)
				{
					foreach($arrayValueEx->children() as $arrayValueExEx)
					{
						if($arrayValueExEx->getName() == "name")
						{
							$mysql->query("INSERT INTO `similar` (`artist`, `similar`) VALUES ('" . escape($_POST['artist']) . "', '" . escape($arrayValueExEx) . "')");
						}
					}
				}
			}
		}
		else
		{
			die("There was a problem while attempting to retrieve similar songs.");
		}

		$mQuery = $mysql->query("SELECT * FROM `similar` WHERE `artist` = '" . escape($_POST['artist']) . "' OR `similar` = '" . escape($_POST['artist']) . "'");

		if($mQuery->num_rows)
		{
			while($mData = $mQuery->fetch_assoc())
			{
				$songQuery = $mysql->query("SELECT * FROM `songs` WHERE `artist` = '" . escape($mData['artist']) . "' OR `artist` = '" . escape($mData['similar']) . "'");

				while($songData = $songQuery->fetch_assoc())
				{
					$nextColor++;

					if($nextColor == count($tileColors))
					{
						$nextColor = 0;
					}

					songContainer($songData['path'], $songData['title'], $songData['artist'], $songData['album'], $songData['length'], $tileColors[$nextColor], "songContainer", $songData['liked']);
				}
			}
		}
		else
		{
			die("No similar songs were found in your library.");
		}
	}

	echo "</div>";

?>