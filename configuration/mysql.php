<?php

	error_reporting(E_ERROR);
	session_start();

	$mysqlSettings[0]['hostname'] = "localhost";
	$mysqlSettings[0]['username'] = "root";
	$mysqlSettings[0]['password'] = "";
	$mysqlSettings[0]['database'] = "mc";

	for($connectionIndex = 0; $connectionIndex > -1; $connectionIndex++)
	{
		if(!$mysqlSettings[$connectionIndex]['hostname'])
		{
			die("No connection could be established to MySQL.");
		}

		$mysql = new mysqli($mysqlSettings[$connectionIndex]['hostname'], $mysqlSettings[$connectionIndex]['username'], $mysqlSettings[$connectionIndex]['password'], $mysqlSettings[$connectionIndex]['database']);

		if($mysql->ping())
		{
			break;
		}
	}

	function escape($item)
	{ 
		if(get_magic_quotes_gpc())
		{ 
	    	$item = stripcslashes($item);
	    } 
	        
		return sanitizeText($item);
	}

	function sanitizeText($text)
	{ 
	    $text = str_replace("<", "&lt;", $text); 
	    $text = str_replace(">", "&gt;", $text); 
	    $text = str_replace("\"", "&quot;", $text); 
	    $text = str_replace("'", "&#039;", $text); 
	    $text = addslashes($text); 
	    return $text; 
	}

	function unescape($text)
	{ 
	    $text =  stripcslashes($text); 
	    $text = str_replace("&#039;", "'", $text); 
	    $text = str_replace("&gt;", ">", $text); 
	    $text = str_replace("&quot;", "\"", $text);    
	    $text = str_replace("&lt;", "<", $text); 
	    return $text; 
	}

?>