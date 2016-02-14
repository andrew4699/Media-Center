<?php

	require_once("configuration/main.php");

	if(!$_POST['path'] || (!$_POST['liked'] != 0 && !$_POST['liked'] != 1))
	{
		exit;
	}

	$mysql->query("UPDATE `songs` SET `liked` = '" . escape($_POST['liked']) . "' WHERE `path` = '" . escape($_POST['path']) . "'");

?>