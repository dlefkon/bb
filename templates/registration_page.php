<div id="registration_page" class="panel">

	<div class="panel_content">
	
		<div id="login_div">
		
			<div class="message" style="display: none"></div>
			
			<input id="authentication_key" type="hidden" name="authentication_key" value="<?= isset($_COOKIE['authentication_key']) ?  $_COOKIE['authentication_key'] : '' ?>"></input>
		
	  		<div>
	  			<input id="registration_username_input" type="text" class="inside_label registration_page" style="opacity:.8"></input>
	  		</div>
	  		
	  		<div>
	  			<input id="registration_email_input" class="inside_label registration_page" style="opacity:.8"></input>
	  		</div>
	  		
	  		<div>
	  			<input id="registration_password_input" class="inside_label registration_page" style="opacity:.8"></input>
	  		</div>
	  		
	  		<div>
	  			<input id="registration_password_confirm_input" class="inside_label registration_page" style="opacity:.8"></input>
	  		</div>
	  		
	  		<div>
	  			<input id="registration_register_link" class="pointer" type="button" value="Register"></input>
	  		</div>
	  		
	  	</div>
	
	</div>	
</div>