<?php

	require_once("configuration/main.php");

	if(!isset($_POST['volume']))
	{
		exit;
	}

	cookie("mc_volume", $_POST['volume']);

?>