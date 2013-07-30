<?php
require 'config.php';

if(MONGO){
	require 'mongoItemClass.php';
} else {
	require 'itemClass.php';
}

$itemClass = new itemClass();

$action = $_REQUEST['action'];
if(!$action) $this->ajaxError('no action given');  
echo $itemClass->$action();
