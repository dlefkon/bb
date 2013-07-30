<?php 
//ini_set('display_errors', 1)
//var_dump($_SERVER); ?>
<link rel="stylesheet" href="http://<?= $_SERVER['HTTP_HOST'] ?>/bb/themes/full_screen/my.css" type="text/css">


		<div class="spacer">
		<div class="page_el frame_out" style="display: block;">
		<div class="frame_mid white_back">
		<div class="frame_in">
		<div class="content_div">
<?php
		
require 'templates/header.php';
//require 'templates/home_page.php';
require 'templates/edit_item.php';
require 'templates/list_items.php'; 
require 'templates/filter.php';

?>
</div>
</div>
</div>
</div>
</div>