<?php 

session_start(); 
require 'config.php';?>

<html>

  <head>
  
    <title>Black Book</title>
    
    <meta name="viewport" content="width=device-width,minimum-scale=1.0,maximum-scale=1.0,user-scalable=no"/>
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />

    <link type="text/css" rel="stylesheet" href="themes/default/default.css" /> 
    <link type="text/css" rel="stylesheet" href="themes/<?php echo THEME ?>/theme.css" />
    <link rel="apple-touch-icon" href="images/favicon.ico">
    <link rel="shortcut icon" href="themes/<?php echo THEME ?>/images/favicon.png">
    
    <script type="text/javascript" src="js/jquery.js"></script>
    <script type="text/javascript" src="js/functions.js"></script>
   
  </head>

  <body>
  
  <?php  
  
  	if (!isset($_COOKIE['authentication_key'])) {
  		
    	include('templates/login.php'); 
    	
	} elseif(!isset($_SESSION['user_id'])) {
		
		echo '<script type="text/javascript">login();</script>';
		
	} else {
		require('templates/universal_layout.php');
	} ?>
	    	         
  </body>
  
</html>