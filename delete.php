<?php

	require_once("configuration/main.php");

	if(!$_POST['filepath'])
	{
		exit;
	}

	$mysql->query("DELETE FROM `songs` WHERE `path` = '" . escape($_POST['filepath']) . "'");

	unlink($_POST['filepath']);

?>