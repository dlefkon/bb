<?php

$db_host = 'mysql.allverticals.com';
$db_user = 'dlefkon';
$db_user_pw = 'bec45zak';
$db_defdb = 'dlbb';

$conn = mysqli_connect($db_host,$db_user,$db_user_pw) or die ("Could not connect");
mysqli_select_db($conn, $db_defdb) or die ("Could not select DB");

mysqli_query($conn, "SET time_zone = '-4:00';");