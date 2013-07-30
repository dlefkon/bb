<?php 

ini_set('display_errors','on');
error_reporting(E_ALL);

require 'config.php';
require('connect.php');

$m = new Mongo();
$db_name = DB_NAME;
$db = $m->$db_name;	

$dlbb_item_collection = $db->dlbb_item;
$dlbb_user_collection = $db->dlbb_user;
$dlbb_user_connection_collection = $db->dlbb_user_connection;
	
$dlbb_item_collection->drop();
$dlbb_user_collection->drop();
$dlbb_user_connection_collection->drop();

output("Inserting dlbb_item documents");
$sql = "SELECT * FROM dlbb_item";
$result = mysqli_query($conn, $sql);
while($item_obj = mysqli_fetch_object($result)) {
	$dlbb_item_collection->insert($item_obj);
}
	
output('Inserting dlbb_item_item dbrefs into dlbb_item_collection');
$sql = "SELECT * FROM dlbb_item_item";
$result = mysqli_query($conn, $sql);
while($item_item_obj = mysqli_fetch_object($result)) {
	
	output('Processing item_item_id ' . $item_item_obj->item_item_id);
	
    output("Finding child in dlbb_item_collection");
	$child_item = $dlbb_item_collection->findOne(array('item_id' => $item_item_obj->item_item_child_item_id));
	$child_item_dbref = $dlbb_item_collection->createDBRef($child_item);
	
	output("Finding parent in dlbb_item_collection");
	$parent_item = $dlbb_item_collection->findOne(array('item_id' => $item_item_obj->item_item_parent_item_id));
	$parent_item_dbref = $dlbb_item_collection->createDBRef($parent_item);

	output("Updating child_item with parent _id: " . $parent_item['_id']);
	
	$dlbb_item_collection->update(array('_id' => new MongoID($child_item['_id'])), array('$addToSet' => array('item_parent_items' => $parent_item_dbref)));

	output("Updating parent_item with child _id: " . $child_item['_id']);
	$dlbb_item_collection->update(array('_id' => new MongoID($parent_item['_id'])), array('$addToSet' => array('item_child_items' => $child_item_dbref)));

}

output("Inserting dlbb_user_collection documents and update");
$sql = "SELECT * FROM dlbb_user";
$result = mysqli_query($conn, $sql);
while($user_obj = mysqli_fetch_object($result)) {
	
<<<<<<< .mine
	$sql = "SELECT * FROM $table";
	$result = mysqli_query($conn, $sql);
// 	$i = 0;
	while($obj = mysqli_fetch_object($result)) {
		echo $i++ . ' ';
// var_dump($obj->item_due_date); 
        if($collection == 'dlbb.dlbb_item_item') {
// var_dump($obj); exit;
        	unset($obj->item_item_id);
//        	$obj[] = $db->dlbb_item['_id'];
        }
		$collection->insert($obj); 
	}
	echo "Collection $collection refilled<br>";
=======
	output("Inserting dlbb_user_collection documents");
	$dlbb_user_collection->insert($user_obj);
dump($user_obj);
	output("Updating dlbb_item_collection with user _id dbref for user_id: " . $user_obj->user_id);
>>>>>>> .r510
	
	$user_dbref = $dlbb_item_collection->createDBRef($user_obj);
	
	$item_cursor = $dlbb_item_collection->find(array('item_user_id' => $user_obj->user_id));
	foreach($item_cursor as $obj){
		output('updating dlbb_item_collection dbref with user _id: ' . $obj['_id']);
		$dlbb_item_collection->update(array('_id' => new MongoID($obj['_id'])), array('$set' => array('item_user' => $user_dbref)));
	}
}

output("Inserting dlbb_user_connection_collection documents and update");
$sql = "SELECT * FROM dlbb_user_connection";
$result = mysqli_query($conn, $sql);
while($user_connection_obj = mysqli_fetch_object($result)) {

	output('Inserting dlbb_user_connection_collection documents');
	$id = $dlbb_user_connection_collection->insert($user_connection_obj);

	output("Updating dlbb_user_connection with dbref to dlbb_user _id for user_id: " . $user_connection_obj->user_connection_user_id);
	$user = $dlbb_user_collection->findOne(array('user_id' => $user_connection_obj->user_connection_user_id));
	$user_dbref = $dlbb_user_collection->createDBRef($user);
	
	$dlbb_user_connection_collection->update(array('_id' => new MongoID($user_connection_obj->_id)), array('$set' => array('user_connection_user' => $user_dbref)));
}


output("Unset unneeded fields");
$cursor = $dlbb_item_collection->find();
foreach($cursor as $obj){
	$dlbb_item_collection->update(array('item_id' => $obj['item_id']), array('$unset' => array('item_id' => 1, 'item_user_id' => 1)), array('safe'));
}

$cursor = $dlbb_user_collection->find();
foreach($cursor as $obj){
	$dlbb_user_collection->update(array('user_id' => $obj['user_id']), array('$unset' => array('user_id' => 1)), array('safe'));
}

$cursor = $dlbb_user_connection_collection->find();
foreach($cursor as $obj){
	$dlbb_user_connection_collection->update(array('user_connection_user_id' => $obj['user_connection_user_id']), array('$unset' => array('user_connection_user_id' => 1)), array('safe'));
}


output("Check updated correctly");
$cursor = $dlbb_item_collection->findOne();
dump($cursor);

$cursor = $dlbb_user_collection->findOne();
dump($cursor);

$cursor = $dlbb_user_connection_collection->findOne();
dump($cursor);


output("dlbb_item_collection now contains {$dlbb_item_collection->count()} items<br>");
output("dlbb_user_collection now contains {$dlbb_user_collection->count()} items<br>");
output("dlbb_user_connection_collection now contains {$dlbb_user_connection_collection->count()} items<br>");
	
function output($text){
	echo "<br>$text<br>";
}

function dump($var){
	echo '<pre>';
	print_r($var);
	echo '</pre>';
}