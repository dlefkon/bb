<?php 
require 'templates/registration_page.php';
?>

<div id="login_page" class="panel">

	<div class="panel_content">	
	
		<div id="login_div">
		
			<div class="message" style="display: none"></div>
			
			<input id="authentication_key" type="hidden" name="authentication_key" value="<?= isset($_COOKIE['authentication_key']) ?  $_COOKIE['authentication_key'] : '' ?>"></input>
		
	  		<div>
	  			<input id="username_input" type="text" class="inside_label" style="opacity:.8" value="Username"></input>
	  		</div>
	  		
	  		<div>
	  			<input id="password_input" type="password" class="inside_label" style="opacity:.8" value="Password"></input>
	  		</div>
	  		
	  		<div>
	  			<input id="login_submit" class="pointer" type="button" value="Enter"></input>
	  			<input id="register_link" class="pointer" type="button" value="Register"></input>
	  		</div>
	  		
	  	</div>
	  	
	</div>	
	
</div>

<script type="text/javascript"> <?php  
	if(isset($_COOKIE['authentication_key']) && !isset($_SESSION['user_id'])) {
		echo 'login();';
	} ?>
</script>

<script type="text/javascript" src="js/login.js"></script>