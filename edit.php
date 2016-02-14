<?php

	require_once("configuration/main.php");

	if(!$_POST['path'] || !$_POST['title'] || !$_POST['artist'])
	{
		exit;
	}

	$mysql->query("UPDATE `songs` SET `title` = '" . escape($_POST['title']) . "', `artist` = '" . escape($_POST['artist']) . "', `album` = '" . escape($_POST['album']) . "' WHERE `path` = '" . escape($_POST['path']) . "'");

?>