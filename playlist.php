<?php

	require_once("configuration/main.php");

	if($_POST['clear'])
	{
		/*if(!$_POST['current'])
		{
			exit;
		}*/

		$mysql->query("DELETE FROM `playlist` WHERE `playlist` = '" . escape($_COOKIE['mc_playlist']) . "'");

		//$mysql->query("INSERT INTO `playlist` (`playlist`, `path`, `playing`) VALUES ('" . escape($_COOKIE['mc_playlist']) . "', '" . escape($_POST['current']) . "', '1')");
	}
	else
	{
		if(!$_POST['path'] || !$_POST['color'])
		{
			exit;
		}
		
		$mysql->query("INSERT INTO `playlist` (`playlist`, `path`, `color`) VALUES ('" . escape($_COOKIE['mc_playlist']) . "', '" . escape($_POST['path']) . "', '" . escape($_POST['color']) . "')");
	}

?>