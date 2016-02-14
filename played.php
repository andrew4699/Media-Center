<?php

	require_once("configuration/main.php");

	if(!$_POST['filepath'])
	{
		exit;
	}

	$mysql->query("UPDATE `songs` SET `played` = `played` + '1' WHERE `path` = '" . escape($_POST['filepath']) . "'");

?>