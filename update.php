<?php

	require_once("configuration/main.php");

	require_once("getid3/getid3.php");

	set_time_limit(1500);

	$mysql->query("DELETE FROM `songs`");
	$mysql->query("DELETE FROM `albums`");

	$getID3 = new getID3;

	function readFileDirectory($path)
	{
		global $mysql, $getID3, $albumSongs;

		foreach(scandir($path) as $currentFile)
		{
			if($currentFile == "." || $currentFile == "..")
			{
				continue;
			}

			$fullPath = $path."/".$currentFile;

			if(is_dir($fullPath))
			{
				readFileDirectory($fullPath);
			}
			else
			{
				$fileExtension = pathinfo($currentFile, PATHINFO_EXTENSION);

				if($fileExtension == "mp3" || $fileExtension == "wav" || $fileExtension == "ogg")
				{
					$songInfo = $getID3->analyze($fullPath);

					getid3_lib::CopyTagsToComments($songInfo);

					if(!$songInfo['comments_html']['title'][0])
					{
						$songInfo['comments_html']['title'][0] = basename($currentFile);
					}

					if($songInfo['tags']['id3v2']['album'][0])
					{
						$albumSongs[escape($songInfo['tags']['id3v2']['album'][0])] = true;
					}
					else
					{
						$albumSongs[escape($songInfo['comments_html']['artist'][0])] = true;
					}

					$mysql->query("INSERT INTO `songs` (`path`, `title`, `artist`, `album`, `length`) VALUES ('$fullPath', '" . escape($songInfo['comments_html']['title'][0]) . "', '" . escape($songInfo['comments_html']['artist'][0]) . "', '" . escape($songInfo['tags']['id3v2']['album'][0]) . "', '" . escape($songInfo['playtime_string']) . "')");
				}
			}
		}
	}

	readFileDirectory(SONG_PATH);

	foreach($albumSongs as $arrayIndex => $arrayValue)
	{
		$mysql->query("INSERT INTO `albums` (`album`) VALUES ('" . $arrayIndex . "')");
	}

	echo "<meta http-equiv='Refresh' content='0; url=index'>";

?>