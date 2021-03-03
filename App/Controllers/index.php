<?php 

	// Permanent 301 Redirect via PHP
	//echo ;
	//var_dump($_SERVER);exit;
	
	header("HTTP/1.1 301 Moved Permanently");
	header("Location: https://".$_SERVER['SERVER_NAME']);
	exit();
?>
