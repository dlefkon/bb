<?php 
session_start(); 

class itemClass {
	
	private $line_prefix = '<span class="line_prefix">&#8227;</span>';    // round => &#8226; , white => &#9702;, triangle => &#8227;
	private $user_id;
	private $authentication_key = false;
	
	function __construct(){
		require('connect.php');
		$this->conn = $conn;
		$this->sanitizeRequest();
		if(isset($_SESSION['user_id'])){
			$this->user_id = $_SESSION['user_id'];
		} elseif($this->action != 'login') {
			$this->ajaxError('ERROR: user_id is no longer in the session.  Please refresh the page.');
		}
	}
	
	private function sanitizeRequest(){
		foreach($_REQUEST as $key => $val){
			$this->$key = mysqli_real_escape_string($this->conn, $val);
		}
		if(!isset($this->item_id) || $this->item_id == '') $this->item_id = 1;
	}
	
	private function ajaxError($msg, $details = ''){
	
	    $details = empty($details) ? $msg : $details;
	
	    header("HTTP/1.1 400 $details");
	    echo $msg;
	    if(mysqli_error($this->conn)){
	        die(mysqli_error($this->conn) . ": $details");
	    }
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
		if(!$result) $this->ajaxError('ERROR: ', $sql);
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
//echo 'blue'; echo $this->authentication_key; 
		if($authentication_key){
			
			$sql = "SELECT count(*) cnt, user_connection_user_id user_id
					FROM dlbb_user_connection 
					WHERE user_connection_key = '$authentication_key'";
			$result = mysqli_query($this->conn, $sql);
			if(!$result) $this->ajaxError('ERROR: ', $sql);		
		    $obj = mysqli_fetch_object($result);
			if ($obj->cnt == 0) return 'false';
			$_SESSION['user_id'] = $obj->user_id;
			
		} else {
			
			$username = $this->username;
			$password = md5($this->password);
			
			if(strlen($username) < 4 || strlen($password) < 4){
		        $this->ajaxError('ERROR: ', "Username and Password must be greater than 3 characters");
			}
			$sql = "SELECT count(*) cnt, user_id
					FROM dlbb_user 
					WHERE user_username = '$username'
					AND user_password = '$password'";
			$result = mysqli_query($this->conn, $sql);
			if(!$result) $this->ajaxError('ERROR: ', $sql);		
		    $obj = mysqli_fetch_object($result);
			if ($obj->cnt == 0) return 'false';
			
			$ip = $_SERVER['REMOTE_ADDR'];
			$authentication_key = $this->createAuthenticationKey($obj->user_id, $ip);
			$user_id = $obj->user_id;
			
			$sql = "INSERT INTO dlbb_user_connection
			        (user_connection_key, user_connection_user_id, user_connection_ip)
			        VALUES ('$authentication_key', '$user_id', '$ip')";
			
			$result = mysqli_query($this->conn, $sql);
			if(!$result) $this->ajaxError('ERROR: ', $sql);	
				
			setcookie('authentication_key', $authentication_key, time() + (COOKIE_DURATION)); 
			$_SESSION['user_id'] = $obj->user_id;
			
		}
		return 'true';
	}
	
	public function logout(){
    	session_destroy();
		setcookie('authentication_key', $_COOKIE['authentication_key'], time() - (11111111));
    	return 'true';
//		header('Location: /');
	}
	
//	public function setUserId(){
//		$password = md5($this->password);
//		$sql = "INSERT INTO dlbb_user (user_username, user_password, user_ip, user_created)
//	            VALUES ('$this->username', '$password', '{$_SERVER['REMOTE_ADDR']}', '" . date("Y-m-d H:i:s") ."') 
//	            ON DUPLICATE KEY UPDATE user_ip = '{$_SERVER['REMOTE_ADDR']}', user_id = LAST_INSERT_ID(user_id)";
//		$result = mysqli_query($this->conn, $sql);
//		if(!$result) $this->ajaxError('ERROR: ', $sql);
//		$inserted_user_id = mysqli_insert_id($this->conn);
//		$_SESSION['user_id'] = $inserted_user_id;
//		return true;//$inserted_user_id;
//	}
	
	public function clearSession(){
		session_destroy();
		setcookie("username", '', time() - 1); 
	}

	public function updateField(){
		
//		if($this->field_name == 'add_item'){
//			$this->item_id = $this->addItem(); 
//		} else {
			// SET sql_mode = 'STRICT_ALL_TABLES'; is done so not able to insert char data into int field
	        $sql = "SET sql_mode = 'STRICT_ALL_TABLES'";
	        $result = mysqli_query($this->conn, $sql);
	        if(!$result) $this->ajaxError('ERROR: ', $sql);
	        $field_value = $this->field_value == 'null' ? 'null' : "'" . $this->field_value . "'";
	        
	        $sql = "UPDATE dlbb_item
		            SET $this->field_name = $field_value
		            WHERE item_id = '$this->item_id' 
			        AND item_user_id = '{$this->user_id}'
		            LIMIT 1";
		            
	        $result = mysqli_query($this->conn, $sql);
	        if(!$result) $this->ajaxError('ERROR: ', $sql);
        	return $this->item_id;	
//		}
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
        if(!$result) $this->ajaxError('ERROR: ', $sql);
        $this->item_id = mysqli_insert_id($this->conn);
        if($add_parent && $this->item_item_parent_item_id) $this->addParent();        
        return $this->item_id;
	}

	public function addParent(){
		$sql = "INSERT INTO dlbb_item_item (item_item_child_item_id, item_item_parent_item_id, item_item_user_id) 
				VALUES ('$this->item_id', '$this->item_item_parent_item_id', '{$this->user_id}') ON DUPLICATE KEY UPDATE item_item_id = item_item_id";
        $result = mysqli_query($this->conn, $sql);
        if(!$result) $this->ajaxError('ERROR: ', $sql);
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
        if(!$result) $this->ajaxError('ERROR: ', $sql);
        return 'Successful';
	}
	
	public function listItems() {
	
		$limit = ITEM_LIMIT;
		
		// show edit view
    	if($this->getNumberChildren() === '0' && $this->filter != 'search' && $this->filter_name !== 'now' && $this->filter_name !== 'home' && $this->filter_name !== 'active') {echo '0'; exit;} //$this->filter != 'search' || (
    	
        if($this->filter === 'search'){
            $sql = "SELECT *
					FROM dlbb_item
					WHERE item_user_id = '{$this->user_id}'
					AND LOCATE('$this->value', item_name) > 0";
        }
        else {
            switch($this->timeframe) {
                case 2:
                    $start = date('Y-m-d').' 00:00:00';
                    $end = date('Y-m-d').' 23:59:59';
                    $timeframe = " AND item_updated > '".$start."' AND item_updated < '".$end."' ";
                    break;

                case 3:
                    $start = date('Y-m-d',mktime(0,0,0,date("m") ,date("d")-1,date("Y"))).' 00:00:00';
                    $end = date('Y-m-d',mktime(0,0,0,date("m") ,date("d")-1,date("Y"))).' 23:59:59';
                    $timeframe = " AND item_updated > '".$start."' AND item_updated < '".$end."' ";
                    break;

                case 4:
                    $newdate = strtotime ( '-1 week' , strtotime(date ("Y-m-d H:i:s") )) ;
                    $start = date('Y-m-d', $newdate) . ' 00:00:00';
                    $end = date ("Y-m-d H:i:s");
                    $timeframe = " AND item_updated > '".$start."' AND item_updated < '".$end."' ";
                    break;

                case 5:
                    $newdate = strtotime ( '-1 month' , strtotime(date ("Y-m-d H:i:s") )) ;
                    $start = date('Y-m-d', $newdate) . ' 00:00:00';
                    $end = date ("Y-m-d H:i:s");
                    $timeframe = " AND item_updated > '".$start."' AND item_updated < '".$end."' ";
                    break;

                case 6:
                    $newdate = strtotime ( '-1 year' , strtotime(date ("Y-m-d H:i:s") )) ;
                    $start = date('Y-m-d', $newdate) . ' 00:00:00';
                    $end = date ("Y-m-d H:i:s");
                    $timeframe = " AND item_updated > '".$start."' AND item_updated < '".$end."' ";
                    break;

                case 7:
                    $newdate = strtotime ( '-5 years' , strtotime(date ("Y-m-d H:i:s") )) ;
                    $start = date('Y-m-d', $newdate) . ' 00:00:00';
                    $end = date ("Y-m-d H:i:s");
                    $timeframe = " AND item_updated > '".$start."' AND item_updated < '".$end."' ";
                    break;

                default:
                    $timeframe = '';
            }
            if($this->sort_field === '' || $this->sort_direction === ''){
//                $order_by_clause = 'ORDER BY item_now DESC, item_active DESC, item_priority DESC, item_count DESC, item_updated DESC, item_name ASC ';
           		$order_by_clause = 'ORDER BY item_due_date DESC, item_now DESC, item_active DESC, item_count DESC, item_updated DESC, item_name ASC ';
            }
            else {
                $order_by_clause = "ORDER BY $this->sort_field $this->sort_direction";
            }
            switch($this->complete)
            {
//                case 1:
//                    $completed_clause = ' ';
//                    break;
//                case 2:
//                    $completed_clause = ' AND item_complete = 1 ';
//                    $order_by_clause = ' ORDER BY item_updated ';
//                    break;
//                case 3:
//                    $completed_clause = ' AND item_complete = 0 ';
//                    break;
                default:
                    $completed_clause = ' AND item_complete = 0 ';
            }
            if((!is_null($this->filter) && $this->filter != '') || $this->filter_name)
            {
//echo $this->filter; exit;             
                switch($this->filter) {
//                    case 'noitems':
//                        $filter_clause = ' item_item_parent_item_id IS NULL ';
//                        break;
//                    case 'now':
//                        $filter_clause = " AND item_now = '1'";
//                        $filter_name = 'Now';
//                        break;
//                    case 'active':
//                        $filter_clause = " AND item_active = '1'";
//                        $filter_name = 'Active';
//                        break;
                    default:
                    	
                    	if($this->filter_name == 'now'){
                        	$filter_clause = " AND (item_now = '1' OR item_due_date < DATE_ADD(NOW(), INTERVAL 1 DAY)) ";                    		
                    	} elseif($this->filter_name == 'active'){
                        	$filter_clause = " AND item_active = '1'"; 
                    	} elseif($this->filter_name == 'home'){
                        	$filter_clause = " AND item_home = '1'";     
                    	} else {
                        	$filter_clause = " AND item_item_parent_item_id = '$this->filter'";	
                    	}
       
                        if(isset($this->filter2) && isset($filter2op)) {
                            if($filter2op == 'or'){ 
                                $filter_clause .= " OR item_item_parent_item_id = '$this->filter2'";
                            }
                            elseif($filter2op == 'and'){
                                $filter_clause .= " AND item_item_parent_item_id = '$this->filter2'";
                            }
                        }
                }
                $sql = "SELECT SQL_CALC_FOUND_ROWS item_id, item_count, item_name, item_complete, item_now, item_active, item_note, item_priority, item_due_date
                  FROM dlbb_item a 
                  LEFT OUTER JOIN dlbb_item_item b ON a.item_id = b.item_item_child_item_id
                  WHERE a.item_user_id = '{$this->user_id}'
                  $filter_clause
                  $completed_clause
                  $timeframe
                  GROUP BY item_id
                  $order_by_clause
                  LIMIT $limit";
            }
            else
            {
                $sql = "SELECT SQL_CALC_FOUND_ROWS a.item_id, item_count, item_name, item_complete, item_now, item_active, item_note, item_priority, item_due_date
                  FROM dlbb_item a  
                  WHERE item_user_id = '{$this->user_id}'
                  $completed_clause
                  $timeframe
                  GROUP BY item_id
                  $order_by_clause
                  LIMIT $limit";
            }
        } 
//  echo $sql; 
        $result = mysqli_query($this->conn, $sql);
//        $result2 = mysqli_query($this->conn, "SELECT FOUND_ROWS() fr");
        if(!$result) $this->ajaxError('ERROR: ', $sql);
//        if(!$result2) $this->ajaxError('ERROR: ', $sql);
//        $num_rows = mysqli_fetch_object($result2) or die(mysqli_error($this->conn));
//        $display = $limit < $num_rows->fr ? "$limit / ".$num_rows->fr : $num_rows->fr;
     	$return = '';
        while($obj = mysqli_fetch_object($result)) {
            $more_indicator = strlen($obj->item_note) > 0 ? ':' : '';
            $counter_class = $obj->item_active ? 'item_counter active_item_counter' : 'item_counter inactive_item_counter';
            $count = $obj->item_count;
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
 			$due_date = is_null($obj->item_due_date) ? date('Y-m-d H:i:s') : $obj->item_due_date;
            $overdue_class = (strtotime($due_date) < strtotime(date("Y-m-d H:i:s"))) ? ' overdue ' : '';
            
            
            
/*

  echo '---------------------<br>';
  echo $due_date . '<br>';
  echo date("Y-m-d H:i:s") . '<br>';
 var_dump(strtotime($due_date)); echo '<br>';
 var_dump(strtotime(strtotime(date("Y-m-d H:i:s")))); echo '<br>';          
 var_dump(strtotime($due_date) < strtotime(date("Y-m-d H:i:s"))); echo '<br>';
 
*/
            $size = strlen($count);
            $item_id = $obj->item_id; //'item_id_' . $obj->item_id;
            $return .= "<li>
            				<input id='{$item_id}_list_counter' class='$counter_class' type='text' value='$count' size='$size' />
            				<a id='$item_id' class=\"" . 'list_item line now_'.$obj->item_now . ' priority_'.$obj->item_priority.' completed_'.$obj->item_complete.' active_'.$obj->item_active.' ' . $overdue_class . '" href="javascript:void(0);">'.$this->line_prefix.'<span id="' . $item_id . '_name" class="' . $emphasis_class . '">' . $obj->item_name. '</span>' .$more_indicator . '</a>
            			</li>';
        }
        return $return;
	}
	
	public function getItemCount() {
		if($this->item_id) {
		    $sql = "SELECT item_count count
					FROM dlbb_item
					WHERE item_id = '$this->item_id'
					AND item_user_id = '{$this->user_id}'";

	 	    $result = mysqli_query($this->conn, $sql);
		    if(!$result) $this->ajaxError('ERROR: ', $sql);
		    $obj = mysqli_fetch_object($result);
		    $count = $obj->count;
		    $current_count = is_null($count) ? '0' : $count;
		} else {
			$current_count = 0;
		}
		return $current_count;
//		"<a id='decrementItemCount_button' class='button' href='javascript:void(0);'>-</a>
//		      		  $current_count
//		      		<a id='incrementItemCount_button' class='button' href='javascript:void(0);'>+</a>";
	}
	
//	public function eipItemCount(){
//        if($this->item_id && $this->update_value)
//        {
//            $sql = "UPDATE dlbb_item
//					SET item_count = '$this->update_value'
//					WHERE item_id = '$this->item_id'
//					AND item_user_id = '{$this->user_id}'";
//            $result = mysqli_query($this->conn, $sql);
//            if(!$result) $this->ajaxError('ERROR: ', $sql);
//            return $this->update_value;
//        }
//        else
//        {
//            return "eipItemCount error  .. item_id was $this->item_id and update value was $this->update_value";
//        }
//	}
	
	public function incrementItemCount(){
        if($this->item_id)
        {
            $sql = "UPDATE dlbb_item
					SET item_count = item_count + 1
					WHERE item_id = '$this->item_id'
					AND item_user_id = '{$this->user_id}'";
            $result = mysqli_query($this->conn, $sql);
            if(!$result) $this->ajaxError('ERROR: ', $sql);
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
            if(!$result) $this->ajaxError('ERROR: ', $sql);
            return $this->getItemCount();
        }
        else
        {
            return "error decrementItemCount .. item_id was $this->item_id";
        }
	}
		
	public function getItemInfo(){
        $sql = "SELECT *, NOW() > item_due_date AS overdue FROM dlbb_item
            	WHERE item_id = '$this->item_id'
                AND item_user_id = '{$this->user_id}'";
        $result = mysqli_query($this->conn, $sql);
        if(!$result) $this->ajaxError('ERROR: ', $sql);
        $arr = array();
        while($obj = mysqli_fetch_object($result)) {
            $arr[] = $obj;
        }
//     var_dump($arr); exit;

// 	array(1) {
//   [0]=>
//   object(stdClass)#4 (18) {
//     ["item_id"]=>
//     string(4) "7717"
//     ["item_user_id"]=>
//     string(1) "1"
//     ["item_name"]=>
//     string(18) "Revert svn version"
//     ["item_priority"]=>
//     string(1) "2"
//     ["item_note"]=>
//     string(38) "temporarily for a month (or fix issue)"
//     ["item_due_date"]=>
//     string(19) "2012-01-11 00:00:00"
//     ["item_updated"]=>
//     string(19) "2012-03-13 04:58:32"
//     ["item_complete"]=>
//     string(1) "0"
//     ["item_active"]=>
//     string(1) "0"
//     ["item_now"]=>
//     string(1) "0"
//     ["item_count"]=>
//     string(1) "1"
//     ["item_max_children"]=>
//     NULL
//     ["item_tag"]=>
//     string(1) "0"
//     ["item_home"]=>
//     string(1) "0"
//     ["item_year_due"]=>
//     string(1) "0"
//     ["item_month_due"]=>
//     string(1) "0"
//     ["item_day_due"]=>
//     string(1) "0"
//     ["item_hour_due"]=>
//     string(1) "0"
//   }
// }
        
        
        return json_encode($arr);
	}
	
	public function getAllItems(){
		$sql = "SELECT item_id, item_name 
        		FROM dlbb_item
                WHERE item_user_id = '{$this->user_id}'";
        $result = mysqli_query($this->conn, $sql);
        if(!$result) $this->ajaxError('ERROR: ', $sql);
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
	
	
	
	
	
//	getAllParents();
//		 // query to get items that 1) have not already been selected, 2) are not the current item and 3) that are not listed in the other respecitve dropdown
//        $sql = "SELECT item_id, item_name
//				FROM dlbb_item a
//				LEFT JOIN dlbb_item_item b ON a.item_id = b.item_item_parent_item_id				
//				WHERE item_id NOT IN (  SELECT item_id
//										FROM dlbb_item_item a
//										JOIN dlbb_item b ON a.item_item_parent_item_id = b.item_id
//										WHERE item_item_child_item_id = $this->item_id) 
//				AND item_id != $this->item_id		
//            	AND item_tag = 1
//				AND item_id NOT IN( SELECT item_id
//						            FROM dlbb_item_item a
//						            LEFT JOIN dlbb_item b
//						            ON a.item_item_child_item_id = b.item_id
//						            WHERE item_item_parent_item_id = '$this->item_id')
//                AND a.item_user_id = '{$this->user_id}'  
//                GROUP BY item_id, item_name
//				ORDER BY item_name ASC";
//                
//        $result = mysqli_query($this->conn, $sql);
//        if(!$result) $this->ajaxError('ERROR: ', $sql);
        
        
        
        
        
	
//	public function getChildrenDropdown(){
//        // query to get items that 1) have not already been selected, 2) are not the current item and 3) that are not listed in the other respecitve dropdown
//        $sql = "SELECT item_id, item_name
//				FROM dlbb_item a
//				LEFT JOIN dlbb_item_item b ON a.item_id = b.item_item_child_item_id
//				WHERE item_id NOT IN (  SELECT item_id
//										FROM dlbb_item_item a
//										JOIN dlbb_item b ON a.item_item_child_item_id = b.item_id
//										WHERE item_item_parent_item_id = $this->item_id) 
//				AND item_id != $this->item_id	
//            	AND item_tag = 1
//				AND item_id NOT IN( SELECT item_id
//						            FROM dlbb_item_item a
//						            LEFT JOIN dlbb_item b
//						            ON a.item_item_parent_item_id = b.item_id
//						            WHERE item_item_child_item_id = $this->item_id)    
//                AND a.item_user_id = '{$this->user_id}'  
//                GROUP BY item_id, item_name  
//				ORDER BY item_name ASC";    
//        
//        $result = mysqli_query($this->conn, $sql);
//        if(!$result) $this->ajaxError('ERROR: ', $sql);
//        $return = '';
//        while($obj = mysqli_fetch_object($result)) {
//            $return .= '<option value="'.$obj->item_id.'">'.$obj->item_name.'</option>';
//        }
//		return $return;
//	}
		
	public function getItemFields(){
		$sql = "SELECT item_priority, item_note, item_updated, item_due_date
            FROM dlbb_item
            WHERE item_id = '$this->item_id'
            AND item_user_id = '{$this->user_id}'";
        $result = mysqli_query($this->conn, $sql);
        if(!$result) $this->ajaxError('ERROR: ', $sql);
        
        $arr = array();
        while($obj = mysqli_fetch_object($result)) {
            $arr[] = $obj;
        }
        return json_encode($arr);
	}
	
	public function showTotalCount(){
		  
		// get total count
        $sql = "SELECT count(*) AS count_sum
	            FROM dlbb_item_item
	            JOIN dlbb_item
	            ON dlbb_item.item_id = dlbb_item_item.item_item_child_item_id
	            WHERE item_item_parent_item_id = {$this->item_id}
	            AND item_item_user_id = '{$this->user_id}'	            
				AND item_complete != 1";
        
        $result = mysqli_query($this->conn, $sql);
        if(!$result) $this->ajaxError('ERROR: ', $sql);
        $obj = mysqli_fetch_object($result);
        $total_count = $obj->count_sum;   
        
        
        // get all_total_count
        
        // get this item's count
        $this_item_count = $this->getItemCount();
        
        // get counts of child items
        $sql = "SELECT item_item_child_item_id
		    	FROM dlbb_item_item
		    	JOIN dlbb_item
	            ON dlbb_item.item_id = dlbb_item_item.item_item_child_item_id
		        WHERE item_item_parent_item_id = '$this->item_id'
		        AND item_item_user_id = '{$this->user_id}'           
				AND item_complete != 1";
	    
	    $result = mysqli_query($this->conn, $sql);
	    if(!$result)die(mysqli_error($this->conn).'  The query was: '.$sql);
	    
	    while($obj = mysqli_fetch_object($result)) {
	        $child_item_ids_array[] = $obj->item_item_child_item_id;
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
	        if(!$result) $this->ajaxError('ERROR: ', $sql);
	        $obj = mysqli_fetch_object($result);
	        $all_total_count = $this->item_id == 398 ? $obj->count_sum % 10000 : $obj->count_sum;

	    } else {
	    	$all_total_count = $total_count;
	    }
	    
	    $all_total_count = $this_item_count + $all_total_count;
	    
        return "$total_count / $all_total_count";
        
	}
	
	private function getAllParents(){
		$sql = "SELECT SQL_CALC_FOUND_ROWS item_id, item_name
	            FROM dlbb_item 
	            JOIN dlbb_item_item
	            ON dlbb_item.item_id = dlbb_item_item.item_item_parent_item_id
	            WHERE item_user_id = '{$this->user_id}'
	            GROUP BY item_name
	            HAVING count(item_item_child_item_id) > 0
	            ORDER BY item_name ASC";        
        $result = mysqli_query($this->conn, $sql);
        if(!$result) $this->ajaxError('ERROR: ', $sql);
        $all_tags = array();
        while($obj = mysqli_fetch_object($result)) {
        	$all_tags[] = $obj;
        }
        return $all_tags;
//        $result2 = mysqli_query($this->conn, "SELECT FOUND_ROWS() fr");
//        $num_rows = mysqli_fetch_object($result2) or die(mysqli_error($this->conn));
//        $display = $limit < $num_rows->fr ? "$limit / ".$num_rows->fr : $num_rows->fr;
	}
	
//	public function listAllTags(){
//		$all_tags = $this->getAllParents();
////	 	$limit = TAG_LIMIT;      
//        $return = '';       
//        foreach($all_tags as $obj){
//        	$return .= '<li><a id="item_id_'.$obj->item_id.'" class="list_item" href="#item_list">'.$obj->item_name.'</a></li>';
//        }
//        return $return;
//	}
	
	public function removeItemItem(){
        $sql = "DELETE FROM dlbb_item_item 
        		WHERE item_item_id = '$this->item_item_id' 
                AND item_item_user_id = '{$this->user_id}'
                LIMIT 1";
        $result = mysqli_query($this->conn, $sql);
        if(!$result) $this->ajaxError('ERROR: ', $sql);
	}
	
	private function getParents(){
		$sql = "SELECT DISTINCT item_item_id AS item_id, item_item_parent_item_id, item_name
	            FROM dlbb_item_item a
	            LEFT JOIN dlbb_item b
	            ON a.item_item_parent_item_id = b.item_id
	            WHERE item_item_child_item_id = '$this->item_id'
	            AND item_item_user_id = '{$this->user_id}'"; 
        $result = mysqli_query($this->conn, $sql);
        if(!$result) $this->ajaxError('ERROR: ', $sql);
        $parents = array();
        while($obj = mysqli_fetch_object($result)) {
        	$parents[] = $obj;
        }
        return $parents;
	}
	
	public function populateParents(){
		$parents = $this->getParents();
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
	
//	public function populateChildren(){
//        $sql = "SELECT DISTINCT item_item_id, item_item_child_item_id, item_name
//	            FROM dlbb_item_item a
//	            LEFT JOIN dlbb_item b
//	            ON a.item_item_child_item_id = b.item_id
//	            WHERE item_item_parent_item_id = '$this->item_id'
//	            AND item_item_user_id = '{$this->user_id}'";
//        $result = mysqli_query($this->conn, $sql);
//        if(!$result) $this->ajaxError('ERROR: ', $sql);
//        $html = '';
//        $children_count = 0;
//        while($obj = mysqli_fetch_object($result)) {
//            $html .= '<li>
//					      <a id="edit_child_'.$obj->item_item_child_item_id.'" class="edit_child" href="javascript:void(0);">' . $obj->item_name.'</a>
//					      <a id="remove_child_'.$obj->item_item_id.'" href="javascript:void(0);" style="color:red;" class="remove_child no_under"> &times;</a>
//					    </li>';
//            $children_count++;
//        }
//        $html .= '</ul></div>';
//        $children_count_option_html = "<option value=''>Children ($children_count)</option>";
//
//        $return = json_encode(array('html' => $html, 'children_count' => $children_count_option_html));
//        return $return;
//	}
		
	public function removeItem(){
        $sql = "DELETE FROM dlbb_item 
	       		WHERE item_id = '".$this->item_id."' 
	            AND item_user_id = '{$this->user_id}'
	            LIMIT 1";
        $result = mysqli_query($this->conn, $sql);
        if(!$result) $this->ajaxError('ERROR: ', $sql);
//        if($affected_rows) return 'Successful';		
		header("Status: 200");
		echo 'Successfully removed item';
	}
	
//	public function listHomeItems(){
//		$sql = "SELECT * FROM dlbb_item
//				JOIN dlbb_item_item
//				ON dlbb_item.item_id = dlbb_item_item.item_item_child_item_id
//				WHERE dlbb_item_item.item_item_parent_item_id = '1'
//				AND item_complete != 1
//				AND item_user_id = '{$this->user_id}'";
//        $result = mysqli_query($this->conn, $sql);
//        if(!$result)
//        {
//            $this->ajaxError('ERROR: ', $sql);
//        }
//        else
//        {
//            $lines = array();
//            while($obj = mysqli_fetch_object($result)){
//                $lines[$obj->item_name] = '<li><a id="item_id_'.$obj->item_id.'" class="list_item"  href="javascript:void(0);">'.$obj->item_name.'</a></li>';
//            }
//            ksort($lines);
//            $return = '';
//            foreach($lines as $l){
//                $return .= $l;
//            }
////            if($this->checkAffectedRows())
//            return $return;
//        }
//	}
	
	public function toggle(){   
		$new_tag_value = $this->name == 'priority' ? (($this->value + 1) % 3) : (($this->value + 1) % 2);
		$sql = "UPDATE dlbb_item
	            SET item_" . $this->name . " = '$new_tag_value'
	            WHERE item_id = '$this->item_id' 
	            AND item_user_id = '{$this->user_id}'";
        $result = mysqli_query($this->conn, $sql);
        if(!$result)
        {
            $this->ajaxError('ERROR: ', $sql);
        }	
        
        return $new_tag_value;
//        else
//        {
//        	if($this->checkAffectedRows()) {
//        		if($this->name == 'active'){
//        			if($new_tag_value == 0){
//        				$sql = "DELETE FROM dlbb_item_item 
//		        		WHERE item_item_child_item_id = '$this->item_id' 
//		                AND item_item_parent_item_id = '5912'
//		                LIMIT 1";
//						$result = mysqli_query($this->conn, $sql);
//						if(!$result) $this->ajaxError('ERROR: ', $sql);
//        			} else {
//        				$this->item_item_parent_item_id = 5912;
//	        			$this->addParent();
//        			}
//        		}
//        		return $new_tag_value;
//        	}
//        }        	
	}
	
	private function checkAffectedRows(){
		$affected_rows = $this->conn->affected_rows;
	    if(!$affected_rows) $this->ajaxError('Error.. no affected rows');
	    return true;
	}
	
	public function getNumberChildren(){
		// find out how many items that item currently has
		// if this is already a item of the item, then one more is allowed
		$sql = "SELECT count(*) cnt
                FROM dlbb_item_item ii
                JOIN dlbb_item i ON
                ii.item_item_child_item_id = i.item_id
                WHERE item_item_parent_item_id = '$this->filter'
            	AND item_user_id = '{$this->user_id}'
                AND item_complete = 0";
		$result = mysqli_query($this->conn, $sql);
		if(!$result) $this->ajaxError('ERROR: ', $sql);
		$obj = mysqli_fetch_object($result);
		return $obj->cnt;
	}
	
	public function insertItemItems(){
	    if(is_array($dlbb_item_items) && !empty($dlbb_item_items)){
	        $dlbb_item_items = array_unique($dlbb_item_items);
	        $sql = "DELETE FROM dlbb_item_item 
	        		WHERE item_item_parent_item_id = '".$this->item_id."'
		            AND item_item_user_id = '{$this->user_id}'";
	        $result = mysqli_query($this->conn, $sql);
	        if(!$result) $this->ajaxError('ERROR: ', $sql);
	        foreach($dlbb_item_items as $tt){
	            if($tt != 'new')
	            {
	                $sql = "INSERT INTO dlbb_item_item (item_item_parent_item_id, item_item_child_item_id, item_item_user_id) 
	                        VALUES ('$this->item_id', '$tt', '{$this->user_id}')";
	                $result = mysqli_query($this->conn, $sql);
	                if(!$result) $this->ajaxError('ERROR: ', $sql);
	            }
	        }
	    }
	}
	
}
	
