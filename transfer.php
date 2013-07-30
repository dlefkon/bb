<?php
ini_set('max_execution_time', 4000);

$db_host = 'mysql.allverticals.com';
$db_user = 'dlefkon';
$db_user_pw = 'bec45zak';
$db_defdb = 'lefkon';

$conn = mysqli_connect($db_host,$db_user,$db_user_pw) or die ("Could not connect");
mysqli_select_db($conn, $db_defdb) or die ("Could not select DB");

function  translateTaskId($conn, $task_id){
	// get name of original task_id 
	$sql = "SELECT name from lefkon.tasks WHERE id = $task_id";
	echo $sql . '<br><br>';
	$result = mysqli_query($conn, $sql);
	if(!$result) die(mysqli_error($conn) . ": $sql");
	$obj = mysqli_fetch_object($result);
	$original_task_name = mysqli_real_escape_string($conn, $obj->name);
	
	// get id of name in dlbb with same name
	$sql = "SELECT item_id from dlbb.dlbb_item WHERE item_name = '$original_task_name'";
	echo $sql . '<br><br>';
	$result = mysqli_query($conn, $sql);
	if(!$result) die(mysqli_error($conn) . ": $sql");
	$obj = mysqli_fetch_object($result);
	$new_task_id = $obj->item_id;
	
	echo "<br>Translated task id was: $new_task_id<br>";
	
	return $new_task_id;
}

//echo '<br>---- INSERTING TAGS -----<br>';
//$sql = "SELECT *, tags.name tag_name FROM tags";
//$result = mysqli_query($conn, $sql);
//if(!$result) die(mysqli_error($conn) . ": $sql");
//while($obj = mysqli_fetch_object($result)){
//	$tag_name = mysqli_real_escape_string($conn, $obj->tag_name);
//	$sql = "INSERT INTO dlbb.dlbb_item (item_id, item_user_id, item_name, item_home, item_tag) values ($obj->id, 1, '$tag_name', $obj->home_link, 1) ON DUPLICATE KEY UPDATE item_id = item_id, item_tag = 1";
//	echo $sql . '<br><br>';
//	$result3 = mysqli_query($conn, $sql);
//	if(!$result3) die(mysqli_error($conn) . ": $sql");
//}
//
//sleep(2);
//
//
//echo '<br>---- INSERTING TAG RELATIONSHIPS -----<br>';
//$sql = "SELECT * FROM tags_relationships";
//$result = mysqli_query($conn, $sql);
//if(!$result) die(mysqli_error($conn) . ": $sql");
//while($obj = mysqli_fetch_object($result)){
//	$sql = "INSERT INTO dlbb.dlbb_item_item (item_item_user_id, item_item_child_item_id, item_item_parent_item_id ) values (1, $obj->subtag, $obj->supertag) ON DUPLICATE KEY UPDATE item_item_id = item_item_id";
//	echo $sql . '<br><br>';
//	$result3 = mysqli_query($conn, $sql);
//	if(!$result3) die(mysqli_error($conn) . ": $sql");
//}
//
//sleep(2);
//
//echo '<br>---- INSERTING TASKS -----<br>';
//$sql = "SELECT * FROM tasks";
//$result = mysqli_query($conn, $sql);
//if(!$result) die(mysqli_error($conn) . ": $sql");
//while($obj = mysqli_fetch_object($result)){
//	$task_name = mysqli_real_escape_string($conn, $obj->name);
//	$note = mysqli_real_escape_string($conn, $obj->note);
//	$max_children = is_null($obj->max_children) ? ' NULL ' : $obj->max_children;
//	$sql = "INSERT INTO dlbb.dlbb_item (item_user_id, item_name, item_priority, item_note, item_count, item_active, item_completed, item_max_children, item_updated) values (1, '$task_name', $obj->priority_id, '$note', $obj->count, $obj->active, $obj->completed, $max_children, '$obj->updated') ON DUPLICATE KEY UPDATE item_id = item_id";
//	echo $sql . '<br><br>';
//	$result3 = mysqli_query($conn, $sql);
//	if(!$result3) die(mysqli_error($conn) . ": $sql");
//}
//
//sleep(2);

echo '<br>---- INSERTING TASK_TAG RELATIONSHIPS -----<br>';
$sql = "SELECT * FROM lefkon.tasks_tags WHERE task_id > 0";
echo $sql . '<br><br>';
$result = mysqli_query($conn, $sql);
if(!$result) die(mysqli_error($conn) . ": $sql");
while($obj = mysqli_fetch_object($result)){
	$translated_task_id = translateTaskId($conn, $obj->task_id);
	$sql = "INSERT INTO dlbb.dlbb_item_item (item_item_user_id, item_item_child_item_id, item_item_parent_item_id ) values (1, $translated_task_id, $obj->tag_id) ON DUPLICATE KEY UPDATE item_item_id = item_item_id";
	echo $sql . '<br><br>';
	$result3 = mysqli_query($conn, $sql);
	if(!$result3) die(mysqli_error($conn) . ": $sql");
}

echo '<br>---- SCRIPT COMPLETED -----<br>';