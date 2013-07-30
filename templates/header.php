<h5 id="action_bar">		

	<div class="action_bar_buttons">

		<input title="List Home Items" type="button" class="listHomeItems_button button inner_button round_button" name="listHomeItems_button" value="0">
		<input title="Log Out" type="button" class="logoutUser_button button inner_button round_button" name="logoutUser_button">
	
		<input title="Display Search" id="searchItems_input" class="inside_label" maxlength="25" style="display:none;" value="Search ...">
		<input title="Add Child Item" id="add_child_item_input" class="inside_label" maxlength="25" style="display:none;" value="Add an item ...">
		
		<input title="Show Edit View" type="button" class="editList_button button inner_button round_button" name="editList_button"> 
		<input title="Show List View" type="button" class="listList_button button inner_button" name="listList_button">
		
		<!-- <input title="" type="button" class="filterItems_button button inner_button round_button" name="filterItems_button">  -->
		<input title="Search Items" type="button" class="searchItems_button button inner_button round_button" name="searchItems_button">
		
		<input title="List Now Items" type="button" class="listNow_button button inner_button" name="listNow_button">
		<input title="List Active Items" type="button" class="listActive_button button inner_button" name="listActive_button">
		
		<input title="Decrement Item Count" type="button" class="decrementItemCount button inner_button round_button" name="decrementItemCount">
		<input title="Increment Item Count" type="button" class="incrementItemCount button inner_button round_button" name="incrementItemCount">
		<input title="List Items" type="button" class="listItems_button button inner_button round_button" name="listItems_button">
		<input title="Search the Web" type="button" class="searchWeb_button button inner_button round_button" name="searchWeb_button">
		<!--  <input title="asdf" type="button" class="filterSubmit_button button inner_button round_button" name="filterSubmit_button"> -->
		
		<input title="Edit Item" type="button" class="editItem_button button inner_button" name="editItem_button">
		<input title="Edit List" type="button" class="editListItem_button button inner_button" name="editListItem_button">
		
		<input title="Toggle Now" type="button" class="nowToggle_button inner_button toggle round_button" name="nowToggle_button">
		<input title="Toggle Active" type="button" class="activeToggle_button inner_button toggle round_button" name="activeToggle_button">	
		<input title="Toggle Home" type="button" class="homeToggle_button inner_button toggle round_button" name="homeToggle_button">	
		<input title="Toggle Complete" type="button" class="completeToggle_button inner_button toggle round_button" name="completeToggle_button">
		<input title="Toggle On Hold" type="button" class="on_holdToggle_button inner_button toggle round_button" name="on_holdToggle_button">
		
		<input title="Remove Item" type="button" class="removeItem_button button inner_button round_button" name="removeItem_button">
		
		<input title="Share Item" type="button" class="shareItem_button button inner_button round_button" name="shareItem_button">
		
		<input title="Show Add Item Input" type="button" class="showAddItemInput_button button inner_button round_button" name="showAddItemInput_button">
	</div>	
</h5>

<script type="text/javascript">
var saved_action_bar_html = $('#action_bar').html();
/* console.log(saved_action_bar_html); */
</script>

<?php //require('templates/share_bar.php'); ?>