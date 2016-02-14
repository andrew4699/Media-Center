<?php

	require_once("configuration/main.php");

	if(!$_POST['name'] && (!$_POST['path'] || !$_POST['groupname']) && (!$_POST['remove'] || !$_POST['groupname']))
	{
		exit;
	}

	if($_POST['name'])
	{
		$mQuery = $mysql->query("SELECT `id` FROM `groups` WHERE `name` = '" . escape($_POST['name']) . "'");

		if(!$mQuery->num_rows)
		{
			$mysql->query("INSERT INTO `groups` (`name`) VALUES ('" . escape($_POST['name']) . "')");
		}
	}
	else if($_POST['path'] && $_POST['groupname'])
	{
		$mQuery = $mysql->query("SELECT `id` FROM `groups_songs` WHERE `path` = '" . escape($_POST['path']) . "'");

		if(!$mQuery->num_rows)
		{
			$mQuery = $mysql->query("SELECT `id` FROM `groups` WHERE `name` = '" . escape($_POST['groupname']) . "'");
			$mData = $mQuery->fetch_assoc();

			$mysql->query("INSERT INTO `groups_songs` (`group`, `path`) VALUES ('" . $mData['id'] . "', '" . escape($_POST['path']) . "')");
		}
	}
	else if($_POST['remove'])
	{
		$mQuery = $mysql->query("SELECT `id` FROM `groups` WHERE `name` = '" . escape($_POST['groupname']) . "'");

		if($mQuery->num_rows)
		{
			$mData = $mQuery->fetch_assoc();
			$mysql->query("DELETE FROM `groups_songs` WHERE `group` = '" . $mData['id'] . "' AND `path` = '" . escape($_POST['remove']) . "'");
		}
	}

?>