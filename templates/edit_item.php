<div id="edit_item_page" class="panel">

	<input id="item_id" type="hidden" name="item_id" value="">

	<div class="">
		<label for="item_name" id="name_label">Name:</label> 
		<input id="item_name" class="update_field" type="text" size="20" maxlength="25" />
	</div>
	
	<div class="item_field">
		<textarea id='item_note' class="update_field" rows='5' cols='77' style="width: 90%;"></textarea>
	</div>
	
	<div class="item_field">
		<label>Count:</label>
		<input id="item_count" size="5" type="text" class="update_field"> 
		<input type="button" name="decrementItemCount" class="decrementItemCount button inner_button round_button inline-block">
		<input type="button" name="incrementItemCount" class="incrementItemCount button inner_button round_button inline-block">
		<span id="total_count">/&nbsp;</span>
	</div>
	
	<div class="item_field">	
		<table>
			<tr>
				<td>
					<label>Parents:</label>
					<span id="parents_dropdown" style="padding-bottom:10px;"></span>				
					<input id="add_parent_item_input" class="inside_label" maxlength="25" style="display:none;"></input>
					<div id="item_parents"></div> 
				</td>
				<td>
				</td>
			</tr>
		</table>
	</div>
			
	<div class="item_field">
		<label>Due:</label> 
		<span id='item_due_date_content' style='margin-bottom: 20px;'>
		
			<input id="item_due_date_checkbox" type="checkbox" value="On">
					
			<span id="item_due_date_type_exact" style='display:none'>
				<input id="item_due_date" type="text" class="update_field" value="<?= date ("Y-m-d H:i:s"); ?>">
			</span>
			
		</span>
	</div>
	
			
	<div class="item_field">
		<label>Updated:</label> 
		<span id='item_updated_date' style='margin-bottom: 20px;'></span>
	</div>
	
</div>