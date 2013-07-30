<?php 
ini_set('display_errors','on');
error_reporting(E_ALL);

session_start(); 

class itemClass {
	
	private $line_prefix = '<span class="line_prefix">&#8227;</span>';    // round => &#8226; , white => &#9702;, triangle => &#8227;
	private $user_id;
	private $m;
	private $db;
	
	function __construct(){
	
		$m = new Mongo();
// 		$m::setProfilingLevel(1); //::setProfilingLevel ( 1);

		$db_name = DB_NAME;
		
		$this->db = $m->$db_name;
// var_dump($this->db->dlbb_item->find()->count());		
		//require('connect.php');
// 		$this->conn = $conn;
		$this->sanitizeRequest();
		if(isset($_SESSION['user_id'])){
			$this->user_id = $_SESSION['user_id'];
		} elseif($this->action != 'login') {
			$this->ajaxError('ERRORuser_id is no longer in the session.  Please refresh the page.');
		}
	}
	
	private function sanitizeRequest(){
		foreach($_REQUEST as $key => $val){
			$this->$key = $val; //mysqli_real_escape_string($this->conn, $val);
		}
	//	if(!isset($this->item_id) || $this->item_id == '') $this->item_id = 1;
	}
	
	private function ajaxError($msg, $details = ''){
	
	    $details = empty($details) ? $msg : $details;
	
	    header("HTTP/1.1 400 $details");
	    echo "$msg: $details";
//	    if(mysqli_error($this->conn)){
//	        die(mysqli_error($this->conn) . ": $details");
//	    }
	    exit;
	}
	
	public function createAuthenticationKey($user_id, $ip){
		$input_string = $user_id . time() . $ip;
		return sha1($input_string);
	}
	
	public function register() {
		$password = md5($this->password);
		$sql = "INSERT INTO dlbb_user (user_username, user_email, user_password, user_ip, user_created)
	            VALUES ('$this->username', '$this->email', '$password', '{$_SERVER['REMOTE_ADDR']}', '" . date("Y-m-d H:i:s") ."') 
	            ON DUPLICATE KEY UPDATE user_email = '$this->email', user_ip = '{$_SERVER['REMOTE_ADDR']}', user_id = LAST_INSERT_ID(user_id)";
		$result = mysqli_query($this->conn, $sql);
		if(!$result) $this->ajaxError('ERROR', $sql);
		$inserted_user_id = mysqli_insert_id($this->conn);
		$_SESSION['user_id'] = $inserted_user_id;
		
		$this->item_name = 'Remove this sample item';
		$this->item_home = 1;
//		$this->item_now = 0;
//		$this->item_now = 0;
		$this->addItem();
		return true;
	}
	
	public function login(){

		$authentication_key = $this->authentication_key;
		if($authentication_key){
//var_dump($this->db->dlbb_user_connection);			
			$cursor = $this->db->dlbb_user_connection->findOne(array('user_connection_key' => $authentication_key)); //->count();
//var_dump($cursor);            
         // using find() ... //if($cursor->count() == 0) return false;
            if(is_null($cursor)) {
            	return 'false'; 
            } else {
  var_dump($cursor); exit;
            	$_SESSION['user_id'] = $cursor['_id'];
            }
//     var_dump($_SESSION['user_id']);        
//	foreach($cursor as $obj){
//				
//var_dump($obj['user_connection_user_id']);
//			}
//				
//		exit;		
//			
//			$sql = "SELECT count(*) cnt, user_connection_user_id user_id
//					FROM dlbb_user_connection 
//					WHERE user_connection_key = '$authentication_key'";
//			$result = mysqli_query($this->conn, $sql);
//			if(!$result) $this->ajaxError('ERROR', $sql);		
//		    $obj = mysqli_fetch_object($result);
//			if ($obj->cnt == 0) return 'false';
//			$_SESSION['user_id'] = $obj->user_id;
			
		} else {
			
			$username = $this->username;
			$password = md5($this->password);
			
			if(strlen($username) < 4 || strlen($password) < 4){
		        $this->ajaxError('ERROR', "Username and Password must be longer than 3 characters");
			}
			
			$user = $this->db->dlbb_user->findOne();
			
			if (!$user_id = $user['_id']) return 'false';
			
			$ip = $_SERVER['REMOTE_ADDR'];
			$authentication_key = $this->createAuthenticationKey($user_id, $ip);
			
			$this->db->dlbb_user_connection->insert(array('user_connection_key' => $authentication_key, 'user_connection_user_id' => $user_id, 'user_connection_ip' => $ip));

			setcookie('authentication_key', $authentication_key, time() + (COOKIE_DURATION));
			
			$_SESSION['user_id'] = $user_id;
			
		}
		return 'true';
	}
	
	public function logout(){
    	session_destroy();
		setcookie('authentication_key', $_COOKIE['authentication_key'], time() - (11111111));
    	return 'true';
	}
	
	public function clearSession(){
		session_destroy();
		setcookie("username", '', time() - 1); 
	}

	public function updateField(){
	    $sql = "SET sql_mode = 'STRICT_ALL_TABLES'";
	    $result = mysqli_query($this->conn, $sql);
	    if(!$result) $this->ajaxError('ERROR', $sql);
	    $field_value = $this->field_value == 'null' ? 'null' : "'" . $this->field_value . "'";
	          
	    $sql = "UPDATE dlbb_item
		            SET $this->field_name = $field_value
		            WHERE item_id = '$this->item_id' 
			        AND item_user_id = '{$this->user_id}'
		            LIMIT 1";
		            
	    $result = mysqli_query($this->conn, $sql);
	    if(!$result) $this->ajaxError('ERROR', $sql);
        return $this->item_id;
	}

	public function addItem($add_parent = true){
		
		if(!$this->item_item_parent_item_id){
			
			$this->item_home = isset($this->item_home) ? $this->item_home : 0;
			$this->item_now = isset($this->item_now) ? $this->item_now : 0;
			$this->item_active = isset($this->item_active) ? $this->item_active : 0;
			
	        $sql = "INSERT INTO dlbb_item (item_name, item_user_id, item_home, item_now, item_active) 
			        VALUES ('$this->item_name', '{$this->user_id}', '$this->item_home', '$this->item_now', '$this->item_active') 
			        ON DUPLICATE KEY UPDATE item_id = item_id, item_count = item_count + 1, item_complete = 0, item_home = 1, item_now = $this->item_now, item_active = $this->item_active";	
	
		} else {
			
	        $sql = "INSERT INTO dlbb_item (item_name, item_user_id, item_home, item_now, item_active) 
			        VALUES ('$this->item_name', '{$this->user_id}', '$this->item_home', '$this->item_now', '$this->item_active')
			        ON DUPLICATE KEY UPDATE item_id = item_id, item_count = item_count + 1, item_complete = 0, item_home = $this->item_home, item_now = $this->item_now, item_active = $this->item_active";
		}
		
        $result = mysqli_query($this->conn, $sql);
        if(!$result) $this->ajaxError('ERROR', $sql);
        $this->item_id = mysqli_insert_id($this->conn);
        if($add_parent && $this->item_item_parent_item_id) $this->addParent();        
        return $this->item_id;
	}

	public function addParent(){
		$sql = "INSERT INTO dlbb_item_item (item_item_child_item_id, item_item_parent_item_id, item_item_user_id) 
				VALUES ('$this->item_id', '$this->item_item_parent_item_id', '{$this->user_id}') ON DUPLICATE KEY UPDATE item_item_id = item_item_id";
        $result = mysqli_query($this->conn, $sql);
        if(!$result) $this->ajaxError('ERROR', $sql);
        return 'Successful';
	}

	public function addNewParentItem(){
				
		// add item
		$this->addItem(false);
		
		// add parent
		$this->item_item_parent_item_id = $this->item_id;
		$this->item_id = $this->item_item_child_item_id;
		$this->addParent();
		
	}
	
	public function addChild(){
        $sql = "INSERT INTO dlbb_item_item (item_item_parent_item_id, item_item_child_item_id, item_item_user_id) 
				VALUES ('$this->item_id', '$this->item_item_child_item_id', '{$this->user_id}')";
        $result = mysqli_query($this->conn, $sql);
        if(!$result) $this->ajaxError('ERROR', $sql);
        return 'Successful';
	}
	
	public function listItems() {

    	if($this->getNumberChildren() === 0 && 
    	   $this->filter != 'search' && 
    	   $this->filter_name !== 'now' && 
    	   $this->filter_name !== 'home' && 
    	   $this->filter_name !== 'active') {
    			echo '0'; 
    			exit;
    	} 
    	
        if($this->filter === 'search'){
            $sql = "SELECT *
					FROM dlbb_item
					WHERE item_user_id = '{$this->user_id}'
					AND LOCATE('$this->value', item_name) > 0";
        }
     //   $fields_array = array('item_id' => 1, 'item_count' => 1, 'item_name' => 1, 'item_complete' => 1, 'item_now' => 1, 'item_active' => 1, 'item_note' => 1, 'item_priority' => 1, 'item_due_date' => 1);
// var_dump($this->user_id); exit;
		$where_array = array(
				'item_user' => $this->user_id, 
				'item_complete' => '0'
				);
// var_dump($where_array);
        if($this->filter_name == 'now'){
       	    $due_date = date("Y-m-d H:i:s", strtotime('tomorrow'));
        	$where_array['$or'] = array(array('item_now' => '1'), array('item_due_date' => array('$lt' => $due_date)));                 		
        } elseif($this->filter_name == 'active'){
        	$where_array['item_active'] = '1';
        } elseif($this->filter_name == 'home'){
          	$where_array['item_home'] = '1';
        } else {
            $where_array['item_item_parent_item_id'] = $this->filter;
        }
// echo 'ef';
// $sort_array = array($this->sort_field => $this->sort_direction);
// var_dump($sort_array); exit;
// ->sort($sort_array)

//   var_dump($where_array);          
        $cursor = $this->db->dlbb_item->find($where_array)->limit(ITEM_LIMIT);
//   var_dump($this->db->lastError());
//  var_dump($cursor);       
     	$return = '';
     	foreach($cursor as $obj) {
// var_dump($cursor); exit;
            $more_indicator = strlen($obj['item_note']) > 0 ? ':' : '';
            $counter_class = $obj['item_active'] ? 'item_counter active_item_counter' : 'item_counter inactive_item_counter';
            $count = $obj['item_count'];
            $emphasis = floor($count / 10);
            if($emphasis > 10) {            	
            	$emphasis_class = 'emphasis_10';            		
            } elseif($emphasis > 9) {            	
            	$emphasis_class = 'emphasis_9';            		
            } elseif($emphasis > 8) {            	
            	$emphasis_class = 'emphasis_8';            		
            } elseif($emphasis > 7) {            	
            	$emphasis_class = 'emphasis_7';            		
            } elseif($emphasis > 6) {            	
            	$emphasis_class = 'emphasis_6';            		
            } elseif($emphasis > 5) {            	
            	$emphasis_class = 'emphasis_5';            		
            } elseif($emphasis > 4) {            	
            	$emphasis_class = 'emphasis_4';            		
            } elseif($emphasis > 3) {            	
            	$emphasis_class = 'emphasis_3';            		
            } elseif($emphasis > 2) {            	
            	$emphasis_class = 'emphasis_2';            		
            } elseif($emphasis > 1) {            	
            	$emphasis_class = 'emphasis_1';
            } else {
            	$emphasis_class = 'emphasis_0';
            }	
 			$due_date = is_null($obj['item_due_date']) ? date('Y-m-d H:i:s') : $obj['item_due_date'];
            $overdue_class = (strtotime($due_date) < strtotime(date("Y-m-d H:i:s"))) ? ' overdue ' : '';
            
            $size = strlen($count);
            $item_id = $obj['_id'];
            $return .= "<li>
            				<input id='{$item_id}_list_counter' class='$counter_class' type='text' value='$count' size='$size' />
            				<a id='$item_id' class=\"" . 'list_item line now_'.$obj['item_now'] . ' priority_'.$obj['item_priority']. ' completed_'.$obj['item_complete'].' active_'.$obj['item_active'] . ' ' . $overdue_class . '" href="javascript:void(0);">'.$this->line_prefix.'<span id="' . $item_id . '_name" class="' . $emphasis_class . '">' . $obj['item_name'] . '</span>' .$more_indicator . '</a>
            			</li>';
        }
        return $return;
	}
	
	public function getItemCount() {
		return $this->db->dlbb_item->find(array('item_id' => $this->item_id, 'item_user_id' => $this->user_id))->count();
	}
	
	public function incrementItemCount(){
        if($this->item_id)
        {
            $sql = "UPDATE dlbb_item
					SET item_count = item_count + 1
					WHERE item_id = '$this->item_id'
					AND item_user_id = '{$this->user_id}'";
            $result = mysqli_query($this->conn, $sql);
            if(!$result) $this->ajaxError('ERROR', $sql);
            return $this->getItemCount();
        }
        else
        {
            return "error incrementItemCount .. item_id was $this->item_id";
        }
	}
	
	public function decrementItemCount(){
        if($this->item_id)
        {
            $sql = "UPDATE dlbb_item
					SET item_count = item_count - 1
					WHERE item_id = '$this->item_id'
					AND item_user_id = '{$this->user_id}'";
            $result = mysqli_query($this->conn, $sql);
            if(!$result) $this->ajaxError('ERROR', $sql);
            return $this->getItemCount();
        }
        else
        {
            return "error decrementItemCount .. item_id was $this->item_id";
        }
	}
		
	public function getItemInfo(){
		$cursor = $this->db->dlbb_item->find(
				array(
					'_id' => new MongoID($this->item_id), 
					'item_user' => $this->user_id
				)); 
//         $sql = "SELECT *, NOW() > item_due_date AS overdue FROM dlbb_item
//             	WHERE item_id = '$this->item_id'
//                 AND item_user_id = '{$this->user_id}'";
//         $result = mysqli_query($this->conn, $sql);
//         if(!$result) $this->ajaxError('ERROR', $sql);
        $arr = array();
//         while($obj = mysqli_fetch_object($result)) {

        foreach($cursor as $obj) {
            $arr[] = $obj;
        }
        return json_encode($arr);
	}
	
	public function getAllItems(){
		$sql = "SELECT item_id, item_name 
        		FROM dlbb_item
                WHERE item_user_id = '{$this->user_id}'";
        $result = mysqli_query($this->conn, $sql);
        if(!$result) $this->ajaxError('ERROR', $sql);
        while($obj = mysqli_fetch_object($result)) {
            $arr[] = $obj;
        }
        return json_encode($arr);
	}
	
	public function getParentsDropdown(){
		$all_tags = $this->getAllParents();
		$parents = $this->getParents();
		
		$parents_array = array();
		
		foreach($parents as $obj){
			$parents_array[] = $obj->item_id;
		}

        $return = '<option value="">Add a Parent:</option>';
        $return .= '<option value="new_parent">Add a New Parent</option>';
        foreach($all_tags as $obj){
        	if(!in_array($obj->item_id, $parents_array)){
        		$return .= '<option value="'.$obj->item_id.'">'.$obj->item_name.'</option>';	
        	}
        }
		return $return;
	}
	
	public function getItemFields(){
		$sql = "SELECT item_priority, item_note, item_updated, item_due_date
            FROM dlbb_item
            WHERE item_id = '$this->item_id'
            AND item_user_id = '{$this->user_id}'";
        $result = mysqli_query($this->conn, $sql);
        if(!$result) $this->ajaxError('ERROR', $sql);
        
        $arr = array();
        while($obj = mysqli_fetch_object($result)) {
            $arr[] = $obj;
        }
        return json_encode($arr);
	}
	
	public function showTotalCount(){
		
		$total_count = $this->db->dlbb_item_item->find(array('item_item_parent_item_id' => $this->item_id, 'item_item_user_id' => $this->user_id, 'item_complete' => array('$ne' => 1), array()))->count();
		  
// 		// get total count
//         $sql = "SELECT count(*) AS count_sum
// 	            FROM dlbb_item_item
// 	            JOIN dlbb_item
// 	            ON dlbb_item.item_id = dlbb_item_item.item_item_child_item_id
// 	            WHERE item_item_parent_item_id = {$this->item_id}
// 	            AND item_item_user_id = '{$this->user_id}'	            
// 				AND item_complete != 1";
        
//         $result = mysqli_query($this->conn, $sql);
//         if(!$result) $this->ajaxError('ERROR', $sql);
//         $obj = mysqli_fetch_object($result);
//         $total_count = $obj->count_sum;   
        
        
        // get all_total_count
        
        // get this item's count
        $this_item_count = $this->getItemCount();
        
        // get counts of child items
        $cursor = $this->db->item_item_child_item_id->find(array('item_item_parent_item_id' => $this->item_id, 'item_item_user_id' => $this->user_id, 'item_complete' => array('$ne' => 1), array('item_item_child_item_id' => 1)));
//         $sql = "SELECT item_item_child_item_id
// 		    	FROM dlbb_item_item
// 		    	JOIN dlbb_item
// 	            ON dlbb_item.item_id = dlbb_item_item.item_item_child_item_id
// 		        WHERE item_item_parent_item_id = '$this->item_id'
// 		        AND item_item_user_id = '{$this->user_id}'           
// 				AND item_complete != 1";
	    
// 	    $result = mysqli_query($this->conn, $sql);
// 	    if(!$result)die(mysqli_error($this->conn).'  The query was: '.$sql);
	    
// 	    while($obj = mysqli_fetch_object($result)) {
        foreach($cursor as $obj) {
	        $child_item_ids_array[] = $obj['item_item_child_item_id'];
	    }
	   
	    if(isset($child_item_ids_array) && is_array($child_item_ids_array)){
		    for($i = 1; $i < 10; $i++){
		    	
		    		$sql = 'SELECT item_item_child_item_id
			    			FROM dlbb_item_item
					    	JOIN dlbb_item
				            ON dlbb_item.item_id = dlbb_item_item.item_item_child_item_id
				            WHERE item_item_parent_item_id IN (' . implode(',', $child_item_ids_array) . ")
					        AND item_item_user_id = '{$this->user_id}'           
							AND item_complete != 1";
		
				    $result = mysqli_query($this->conn, $sql);
				    if(!$result)die(mysqli_error($this->conn).'  The query was: '.$sql);
				     
				 	while($obj = mysqli_fetch_object($result)) {
				        $child_item_ids_array[] = $obj->item_item_child_item_id;
				    }
				    
				    $child_item_ids_array = array_unique($child_item_ids_array);
				    
				    if(isset($old_child_item_ids_array) && ($old_child_item_ids_array == $child_item_ids_array)) {
				    	break;
				    } else {
				    	$old_child_item_ids_array = $child_item_ids_array;
				    }
				  	
		    }
		
	        $sql = 'SELECT sum(item_count) AS count_sum
		            FROM dlbb_item
		            WHERE item_id IN (' . implode(',', $child_item_ids_array) . ")
		            AND item_user_id = '{$this->user_id}'
					AND item_complete != 1";
//echo $sql;	        
	        $result = mysqli_query($this->conn, $sql);
	        if(!$result) $this->ajaxError('ERROR', $sql);
	        $obj = mysqli_fetch_object($result);
	        $all_total_count = $this->item_id == 398 ? $obj->count_sum % 10000 : $obj->count_sum;

	    } else {
	    	$all_total_count = $total_count;
	    }
	    
	    $all_total_count = $this_item_count + $all_total_count;
	    
        return "$total_count / $all_total_count";
        
	}
	
	private function getAllParents(){

// 		$keys = array('item_item_parent_item_id');
// 		$initial = array("items" => array());
// 		$reduce = "function (obj, prev) { prev.items.push(obj.name); }";
// 		$condition = array('item_user_id' => $this->user_id);
		
		$cursor = $this->db->dlbb_item_item->find(array('item_item_user_id' => $this->user_id, 'item_item_child_item_id' => array('$size' => 1)));
		//->group($keys, $initial, $reduce, $condition); //->sort(array('item_name' => 1));
		$all_tags = array();
		foreach($cursor as $obj){
var_dump($obj); exit;
			$all_tags[] = $obj;
		}
exit;
// var_dump($all_tags); exit;
		return $all_tags;
		
// 		$sql = "SELECT SQL_CALC_FOUND_ROWS item_id, item_name
// 	            FROM dlbb_item 
// 	            JOIN dlbb_item_item
// 	            ON dlbb_item.item_id = dlbb_item_item.item_item_parent_item_id
// 	            WHERE item_user_id = '{$this->user_id}'
// 	            GROUP BY item_name
// 	            HAVING count(item_item_child_item_id) > 0
// 	            ORDER BY item_name ASC";        
//         $result = mysqli_query($this->conn, $sql);
//         if(!$result) $this->ajaxError('ERROR', $sql);
//         $all_tags = array();
//         while($obj = mysqli_fetch_object($result)) {
//         	$all_tags[] = $obj;
//         }
//         return $all_tags;
	}
	
	public function removeItemItem(){
        $sql = "DELETE FROM dlbb_item_item 
        		WHERE item_item_id = '$this->item_item_id' 
                AND item_item_user_id = '{$this->user_id}'
                LIMIT 1";
        $result = mysqli_query($this->conn, $sql);
        if(!$result) $this->ajaxError('ERROR', $sql);
	}
	
	private function getParents(){
<<<<<<< .mine
		$where = array('item_item_child_item_id' => $this->item_id, 'item_item_user_id' => $this->user_id);
var_dump($where);
//		$fields = array('item_item_id' , 'item_item_parent_item_id', 'item_name');
		$cursor = $this->db->dlbb_item_item->find($where);
//var_dump($cursor); exit;
		$parents = array();
		foreach($cursor as $obj) {
var_dump($obj); exit;
        	$parents[] = $obj;
        }
=======

		$where = array(
			'_id' => new MongoID($this->item_id), 
			'item_user' => $this->user_id
		);
		
		$cursor = $this->db->dlbb_item->findOne($where, array('item_parent_items'));
var_dump($cursor["item_parent_items"]); exit;
// 		$parents = array();
// 		foreach($cursor as $obj) {
// 			foreach($obj['item_parent_items'] as $parent_item){
// 				$parents[] = $parent_item;
// 			}
//         } 

        $where = array(
        		'_id' => array('$in' => $cursor["item_parent_items"]),
        		'item_user' => $this->user_id
        );
        
//         foreach($cursor as $obj){
//         	var_dump($obj); exit;
//         }
        $cursor = $this->db->dlbb_item->find($where);
        var_dump($cursor); exit;
//         $where = array(
//         		'_id' => new MongoID($this->item_id),
//         		'item_user' => $this->user_id
//         );
//         $cursor = $this->db->dlbb_item->find($where, array('item_parent_items'));
//         var_dump($cursor);
//         // exit;
//         $parents = array();
//         foreach($cursor as $obj) {
//         	var_dump($obj); exit;
//         }
        
        
>>>>>>> .r510
//		$sql = "SELECT DISTINCT item_item_id AS item_id, item_item_parent_item_id, item_name
//	            FROM dlbb_item_item a
//	            LEFT JOIN dlbb_item b
//	            ON a.item_item_parent_item_id = b.item_id
//	            WHERE item_item_child_item_id = '$this->item_id'
//	            AND item_item_user_id = '{$this->user_id}'"; 
//        $result = mysqli_query($this->conn, $sql);
//        if(!$result) $this->ajaxError('ERROR', $sql);
//        $parents = array();
//        while($obj = mysqli_fetch_object($result)) {
//        	$parents[] = $obj;
//        }
        return $parents;
	}
	
	public function populateParents(){
		$parents = $this->getParents();
var_dump($parents); exit;
        	$html = '';
        	$parent_count = 0;
        	foreach($parents as $obj){
            		$html .= '<span class="parent_span"><a id="edit_parent_'.$obj->item_item_parent_item_id.'" class="edit_parent" href="javascript:void(0);">' . $obj->item_name . '</a>
            			  <a id="remove_parent_'.$obj->item_id.'" href="javascript:void(0);" style="color:red;" class="remove_parent"> &times;</a></span>';
            		$parent_count++;
        	}
        	$html .= '</ul></div>';
        	$parent_count_option_html = "<option value=''>Parents ($parent_count)</option>";
        
        	$return = json_encode(array('html' => $html, 'parent_count' => $parent_count_option_html));
        	return $return;
	}
	
	public function removeItem(){
        $sql = "DELETE FROM dlbb_item 
	       		WHERE item_id = '".$this->item_id."' 
	            AND item_user_id = '{$this->user_id}'
	            LIMIT 1";
        $result = mysqli_query($this->conn, $sql);
        if(!$result) $this->ajaxError('ERROR', $sql);
		header("Status: 200");
		echo 'Successfully removed item';
	}
	
	public function toggle(){   
		$new_tag_value = $this->name == 'priority' ? (($this->value + 1) % 3) : (($this->value + 1) % 2);
		$sql = "UPDATE dlbb_item
	            SET item_" . $this->name . " = '$new_tag_value'
	            WHERE item_id = '$this->item_id' 
	            AND item_user_id = '{$this->user_id}'";
        $result = mysqli_query($this->conn, $sql);
        if(!$result)
        {
            $this->ajaxError('ERROR', $sql);
        }	
        
        return $new_tag_value;
	}
	
	private function checkAffectedRows(){
		$affected_rows = $this->conn->affected_rows;
	    if(!$affected_rows) $this->ajaxError('Error.. no affected rows');
	    return true;
	}
	
	public function getNumberChildren(){
		// Find out how many items that item currently has.  If this is already a item of the item, then one more is allowed
		return $this->db->dlbb_item_item->find(array('item_item_parent_item_id' => $this->filter, 'item_user_id' => $this->user_id, 'item_complete' => 0))->count();
	}
	
	public function insertItemItems(){
	    if(is_array($dlbb_item_items) && !empty($dlbb_item_items)){
	        $dlbb_item_items = array_unique($dlbb_item_items);
	        $sql = "DELETE FROM dlbb_item_item 
	        		WHERE item_item_parent_item_id = '".$this->item_id."'
		            AND item_item_user_id = '{$this->user_id}'";
	        $result = mysqli_query($this->conn, $sql);
	        if(!$result) $this->ajaxError('ERROR', $sql);
	        foreach($dlbb_item_items as $tt){
	            if($tt != 'new')
	            {
	                $sql = "INSERT INTO dlbb_item_item (item_item_parent_item_id, item_item_child_item_id, item_item_user_id) 
	                        VALUES ('$this->item_id', '$tt', '{$this->user_id}')";
	                $result = mysqli_query($this->conn, $sql);
	                if(!$result) $this->ajaxError('ERROR', $sql);
	            }
	        }
	    }
	}
	
}
	
