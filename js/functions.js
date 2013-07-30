var default_val = '';
var add_child_item_input_default_val = "Add a New Child Item ...";
var	add_parent_item_input_default_val = "Add a New Parent Item";
var registration_username_input_default_val = "Enter a Username";
var registration_email_input_default_val = "Enter your Email";
var registration_password_input_default_val = "Enter a Password"
var registration_password_confirm_input_default_val = "Re-enter a Password"
var special_list = false;
var previous_list_item_id = false; // use to display list when item is removed
var mode = 'list'; // edit, edit_list
var hide_list_button = false;

$(document).ready(function() {
	
	var searchItems_input_default_val = $('#searchItems_input').val();
	
	var username_input_default_val = $('#username_input').val();
	var password_input_default_val = $('#password_input').val();
	
	document.addEventListener("touchmove",   function(e){touchMove(e);},   false);
	document.addEventListener("touchstart",  function(e){touchStart(e);},  false);
	document.addEventListener("touchend",    function(e){touchEnd(e);},    false);
	document.addEventListener("touchcancel", function(e){touchCancel(e);}, false);

	$('.inside_label').live('focus', function() {
		
		var str = $(this).attr('id') + '_default_val';
		var default_val = eval(str);
		
		$(this).css('color', '#ddd');

	}).live('blur', function() {
		
		var str = $(this).attr('id') + '_default_val';
		var default_val = eval(str);
		
/* 		$(this).val(default_val).css('color', '#999'); */
		
	}).live('keypress', function(){
		
        // todo: next two lines are same as ones for focus ... (make this common function?)
		var str = $(this).attr('id') + '_default_val';
		var default_val = eval(str);
		
		$(this).css('color', '#2173A1');

		if($(this).val() == default_val) $(this).val('');
	});
	
	/*
$('.login_input').live('keypress', function(){
		$(this).css('color', '#2173A1');
	});
*/

	$('.toggle').live('click', function(){
		toggle(this);
	});

	$('.list_item_toggle').live('click', function(){
		if(this.name == 'remove'){
			removeItem(this.id, event.target.innerHTML, true);
		} else {
			var this_input = [];
			this_input['id'] = this.name + 'Toggle_button';
			toggle(this_input, this.id, $('.editItem_button').val()); //attr('name'));
		}
	});
	
	$('.button').live('click', function(){
		var function_name = this.name.replace(/_button/, '');
		eval( function_name + '(' + $('#item_id').val() + ')' );
	});

	$('.editItem_button').live('click', function(){
		mode = 'edit';
	});

	$('.listItems_button, .edit_parent').live('click', function(){
		mode = 'list';
	});

	$.ajaxSetup({"error":function(XMLHttpRequest, textStatus, errorThrown) {
		alert(XMLHttpRequest.responseText);
//		displayError(XMLHttpRequest.responseText);
	}});

	$('.update_field').live('change', function(){
		updateField($(this), this.id, $('#item_id').val(), $(this).val(), true);
	});

	$('#add_child_item_input').live('change', function(){
		addItem(this);
	});

	$('#add_parent_item_input').live('change', function(){
		addNewParentItem(this);
	});
	
	$('.edit_parent').live('click', function(){
		listItems(this.id.replace('edit_parent_',''), $(this).html());
	});
	
	$('.remove_parent').live('click', function(){
    	removeChildOrParent(this.id, 'parent');
	});
	
	$('.edit_item').live('click', function(event){
		hideListItemButtons($(this).attr('id')); // $('.action_bar_buttons').hide();
		showListItemButtons($(this).attr('id'));
	});
	
	$('.list_item').live('click', function(event){
		listItems(this.id, $(this).attr('id'), 3, 1, null, null, null, null, 'list');
	});
	
	$('.filter_select').val('');
	//setTimeout('hideAddressBar();', 2000);
	$('#page_title').css('display', 'inline-block');

	$('#searchItems_input').live('keyup', function(event) {
		if($('#searchItems_input').val().length > 2)
			search();
	});
	
	$('#searchItems_input').live('click', function(){
		$('#searchItems_input').val(searchItems_input_default_val);
		searchItems();
	});
	
	$('.item_counter').live('click', function(){
		$(this).css({height:'15px',fontWeight:'bold'});
	}).live('change', function(){
		if(updateField($(this), 'item_count', $(this).next('a').attr('id'), this.value, false)) alert('ef');
		$(this).css({height:'10px',fontWeight:'normal'});
	});
	
	$('a, .button').live('hover', function(){
		$(this).toggleClass('hovered');
	});
	
	$('#item_due_date_checkbox').live('click', function(){
	
		var dt = new Date();
		var str = dt.toYMD() + ' 00:00:00';
		
		if($(this).attr('checked')) {
			$('#item_due_date').val(str).change();
			$('#item_due_date_type_exact').show();
		} else {
			$('#item_due_date').val('null').change();
			$('#item_due_date_type_exact').hide();
		}
		
	});
	
	$('#username_input, #password_input, #login_submit').live('keypress', function(event) {
	    if(event.keyCode == 13 || event.keyCode == 10) {
			login();
	    }
	});
	
	$('#login_submit').live('click', function(){
		login();
	});
	
	$('#registration_register_link').live('click', function() {
		register();
	});
	
	$('.registration_page').live('keypress', function(event) {
	    if(event.keyCode == 13 || event.keyCode == 10) {
			register();
	    }
	});
	
	$('#register_link').live('click', function(){
		showPage(document.getElementById('registration_page')); 
		$('#registration_username_input').val(registration_username_input_default_val);
		$('#registration_email_input').val(registration_email_input_default_val);
		$('#registration_password_input').val(registration_password_input_default_val);
		$('#registration_password_confirm_input').val(registration_password_confirm_input_default_val);
	});
	
	$('#registration_password_input, #registration_password_confirm_input').live('focus', function(){
		$(this).get(0).type = 'password'; //attr('type', 'password');
	
	});
	
	new function($) {
	  $.fn.setCursorPosition = function(pos) {
	    if ($(this).get(0).setSelectionRange) {
	      $(this).get(0).setSelectionRange(pos, pos);
	    } else if ($(this).get(0).createTextRange) {
	      var range = $(this).get(0).createTextRange();
	      range.collapse(true);
	      range.moveEnd('character', pos);
	      range.moveStart('character', pos);
	      range.select();
	    }
	  }
	}(jQuery);
	
	Date.prototype.toYMD = Date_toYMD;
	function Date_toYMD() {
	    var year, month, day;
	    year = String(this.getFullYear());
	    month = String(this.getMonth() + 1);
	    if (month.length == 1) {
	        month = "0" + month;
	    }
	    day = String(this.getDate());
	    if (day.length == 1) {
	        day = "0" + day;
	    }
	    return year + "-" + month + "-" + day;
	}


});







function touchStart(event)  {
    curX = event.targetTouches[0].pageX;
}

function touchMove(event) {
    thisX = event.targetTouches[0].pageX;
}

function touchEnd(event) {
	if(thisX != undefined){
	    var left;
	    if ( curX - thisX > 150 ) {
	       left = false;
	    } else if ( curX - thisX < -150 ) {
	       left = true;
	    } else {
	       return;
	    }
	    if(event.target.id != undefined){
		    if(left){
		    	swipeRight(event.target.id);
		    } else if(!left) {
		    	swipeLeft(event.target.id);
		    } else {
		    	alert('not left or right');
		    }
	    }
	}
}

function swipeLeft(item_id){
	hideListItemButtons(item_id);
}

function swipeRight(item_id){
	hideListItemButtons(item_id);
	showListItemButtons(item_id);
}

function showButtons(item){
	var item_id = $(item).attr('id');
	$(item).parent('li').css('background-color', '#FFA');
	$('#item_id').val(item_id);
	showActionBarElements(['listHomeItems_button', 'searchWeb_button', 'completeToggle_button', 'activeToggle_button', 'homeToggle_button', 'listItems_button', 'tagToggle_button', 'homeToggle_button', 'shareItem_button', 'nowToggle_button', 'removeItem_button', 'showAddItemInput_button']); 
}

function toggle(el, selected_el, list_item_id){
	item_id = selected_el ? selected_el : $('#item_id').val();	
	name = el.name.replace(/Toggle_button/, '');
	$.ajax({
		dataType:"text", 
		url:'ajax.php?action=toggle&item_id=' + item_id + '&name=' + name + '&value=' + $('.' + name + 'Toggle_button').val(), 
		success: function(response) {
			$('.' + name + 'Toggle_button').val(response);
			if(mode != 'edit' && mode != 'edit_list') showListItemButtons(item_id);
			changeToggleColor();
			$('#' + item_id).toggleClass(name + '_1');
	 		$('#edit_item_page').toggleClass(name + '_1');
		}
	});
}  

function changeToggleColor(){
	$.each($('.toggle'), function(){
		$(this).toggleClass('toggled', this.value === '1');
	});
}
  
function searchWeb() {
	window.location = 'http://www.google.com/search?q=' + $('#item_name').val(); // q=definition of ' + $('#item_name').val();
}

function register(){
	if($('#registration_password_input').val() != $('#registration_password_confirm_input').val()){
		alert('Passwords do not match');
		return false;		
	}
	var emailReg = /^([\w-\.]+@([\w-]+\.)+[\w-]{2,4})?$/;
	if( !emailReg.test( $('#registration_email_input').val() ) ) {
		alert('Please enter a valid email address');
		return false;
	}
	var url ="ajax.php?action=register&username=" + $('#registration_username_input').val() + "&password=" + $('#registration_password_input').val() + "&email=" + $('#registration_email_input').val();
	$.ajax({
	    dataType:"html",
		url:url,
		success: function(data) {
			if(data){
				alert('Registration was Successful');
				window.location.reload(true);	  
			} else {
				alert('Registration was Not successful');
				window.location.reload(true);
			}
		}
	});
}

function login() {
	var url ="ajax.php?action=login&username=" + $('#username_input').val() + "&password=" + $('#password_input').val() + "&authentication_key=" + $('#authentication_key').val();
	$.ajax({
	    dataType:"html",
		url:url,
		success: function(data)
		{
			if(data == 'true'){
				window.location.reload(true);	  
			}		
			else {
				alert('Login was not successful');
				clearCookies('authentication_key');
				window.location.reload(true);
			}
		}
	});
}
    
function logoutUser(){
	$.ajax({
        dataType:"html",
        url:"ajax.php?action=logout",
        success: function(data) {
	    	if(data == 'true'){
	    		window.location.reload(true);	  
		    }
	    }
	});
}

function showAddItemInput(){
	showActionBarElements(['listHomeItems_button', 'add_child_item_input', 'showAddItemInput_button']);
	$('#add_child_item_input').focus();
}
    
function clearCookies(c_name){
	var exdate = new Date();
	exdate.setDate(exdate.getDate() - 1);
	var c_value = "expires=" + exdate.toUTCString();
	document.cookie = c_name + "=;" + c_value;
}
	 
function removeItem(item_id, item_name, no_prompt) {
	item_id = item_id ? item_id : $('#item_id').val();
	item_name = item_name ? item_name : $('#item_name').val();
	no_prompt = no_prompt ? no_prompt : false;
	
	if(!no_prompt){
		if(confirm('Really remove "' + item_name + '"?')){
			$.ajax({
				url:"ajax.php?action=removeItem&item_id=" + item_id,
				success: function() {
				
					if(special_list == 'now') { 
						listNow(); 			 		
					} else if(special_list == 'active'){
						listActive();						
					} else if(special_list == 'home') { 
						listHomeItems();						
					} else if(previous_list_item_id){
						listItems(previous_list_item_id);
					} else {
						alert('fix this');
					}
				}
			});
		}
	}
}
  
function removeChildOrParent(element_id, relationship) {
	element_name = $('#' + element_id).prev().html();
	replace_string = 'remove_' + relationship + '_';
	item_item_id = element_id.replace(replace_string, '');
	if(confirm("Remove " + relationship + ": '" + element_name + "' ?")) {
		$.ajax({
		    dataType:"html",
		    url:"ajax.php?action=removeItemItem&item_item_id=" + item_item_id,
		    success: function() { 
		  		refreshItemInfo($('#item_id').val());
		    }
		});
	}
}
  
function showTotalCount(item_id) {
	$.ajax({
		dataType:"html",
		url:"ajax.php?action=showTotalCount&item_id=" + item_id,
		success: function(response) {
			$('#total_count').html(response);
		}
	});
}
  
function createFilterItemsDropdown() {
	$('#filter_items_dropdown').html('');
	$('#filter_items_dropdown2').html('');
	$("<option>").attr("value", "").text('Select item').appendTo("#filter_items_dropdown");
	$("<option>").attr("value", "").text('Select item').appendTo("#filter_items_dropdown2");
	$.each(all_items, function(key, value){
		$("<option>").attr("value", value.item_id).text(value.name).appendTo("#filter_items_dropdown");
		$("<option>").attr("value", value.item_id).text(value.name).appendTo("#filter_items_dropdown2");
	});
}
  
function showListItemButtons(item_id){
	$('#' + item_id).removeClass('list_item').addClass('list_item_class_removed');
	if($('#' + item_id).hasClass('edit_item')) $('#' + item_id).removeClass('edit_item').addClass('edit_item_class_removed');
alert('zzz');
	$.ajax({
		url: "ajax.php?action=getItemInfo&item_id=" + item_id,
		dataType: 'json',
		success: function(data){
			$('#' + item_id).append(saved_action_bar_html);
			showActionBarElements(['shareItem_button', 'nowToggle_button', 'decrementItemCount', 'incrementItemCount', 'listItems_button', 'searchWeb_button', 'removeItem_button', 'activeToggle_button', 'completeToggle_button', 'listHomeItems_button', 'showAddItemInput_button', 'editListItem_button']); 
			$('#item_count').parent('li').html(data[0].item_count);
			$('#item_name').val(data[0].item_name)
			$('#item_id').val(item_id);
console.log('ef');
			$.each($('.toggle'), function(){
console.log('bb');
console.log($(this));
				$(this).val(data[0].$(this).attr('name'));
			});
			$('.tagToggle_button').val(data[0].item_tag); 
			$('.activeToggle_button').val(data[0].item_active); 
			$('.completeToggle_button').val(data[0].item_complete);
			$('.nowToggle_button').val(data[0].item_now);		
			$('.homeToggle_button').val(data[0].item_home);
			changeToggleColor();
			$('.editListItem_button').focus();
		}	
	});	
}

function editListItem(el) {
	mode = 'edit';
	listItems(el, $('#' + el + '_name').html(), 3, 1, null, null, null, null, 'list');
}

function hideListItemButtons(item_id) {
	$('#action_bar').slideUp();
	$('.action_bar_buttons').hide(); //remove();
	$('#' + item_id).addClass('list_item').removeClass('list_item_class_removed');
}

function refreshItemInfo(item_id, field_name) {
	$.ajax({
		url: "ajax.php?action=getItemInfo&item_id=" + item_id,
		dataType: 'json',
		success: function(data){
		    if(field_name){
		    	str = "$('#" + field_name + "').val(data[0]." + field_name + ");";
		    	eval(str);
		    	$('#item_updated_date').html(data[0].item_updated);
		    } else {		    	
				$('#item_name').prev().hide();
				createCountInputLine(item_id);
				populateParentsDropdown(item_id);
				if(data.length != 0){
					$('#list_items_page').html('');
					$('#item_note').val(data[0].item_note);
					$('#item_due_date').val(data[0].item_due_date);
					$('#item_updated_date').html(data[0].item_updated);
					$('#item_max_children').val(data[0].item_max_children);
					$('#item_count').html(data[0].item_count);
					$('#item_name').val(data[0].item_name);
					$('#item_id').val(item_id); 
					$('.activeToggle_button').val(data[0].item_active);
					$('.nowToggle_button').val(data[0].item_now);
					$('.completeToggle_button').val(data[0].item_complete);
					$('.on_holdToggle_button').val(data[0].item_on_hold);
					$('#edit_item_page').removeClass('overdue')
										.removeClass('active_0')
										.removeClass('active_1')
										.removeClass('now_0')
										.removeClass('now_1')
										.removeClass('complete_0')
										.removeClass('complete_1')
										.addClass('active_' + data[0].item_active)
										.addClass('now_' + data[0].item_now)
										.addClass('complete_' + data[0].item_complete)
										.addClass('on_hold_' + data[0].item_on_hold)
					if(data[0].overdue)	$('#edit_item_page').addClass('overdue');
					$('.homeToggle_button').val(data[0].item_home);
					$('.item_field').show();					
					showActionBarElements(['listHomeItems_button', 'searchWeb_button', 'completeToggle_button', 'on_holdToggle_button', 'activeToggle_button', 'listItems_button', 'tagToggle_button', 'homeToggle_button', 'shareItem_button', 'nowToggle_button', 'removeItem_button', 'showAddItemInput_button']); 
					if(hide_list_button == true) $('.listItems_button').hide();
					changeToggleColor();
				    $('#item_note').focus();
				    $('#add_parent_item_input').val(add_parent_item_input_default_val);

				    if($('#item_due_date').val()) {
				    	$('#item_due_date_type_exact').show();
				    	$('#item_due_date_type').val('exact');
						$('#item_due_date_checkbox').attr('checked', true);
				    } else {
				    	$('#item_due_date_type_exact').hide();
						$('#item_due_date_checkbox').attr('checked', false);
				    }

				} else {
					showActionBarElements(['listHomeItems_button', 'searchWeb_button', 'completeToggle_button', 'on_holdToggle_button', 'removeItem_button', 'activeToggle_button', 'priorityToggle_button', 'tagToggle_button', 'homeToggle_button', 'shareItem_button', 'nowToggle_button', 'showAddItemInput_button']); 
				}
		    }
		} 
	});
}

function addItem() {
	var item_home = $('.editItem_button').html() == 'home' ? 1 : 0;
	var item_active = $('.editItem_button').html() == 'active' ? 1 : 0;
	var item_now = $('.editItem_button').html() == 'now' ? 1 : 0;		
	var item_item_parent_item_id = $('.editItem_button').val();
	
	var url = "ajax.php?action=addItem&item_item_parent_item_id=" + item_item_parent_item_id + "&item_home=" + item_home + "&item_active=" + item_active + "&item_now=" + item_now;
	
	if(item_home){
		filter_name = 'home';
	} else if (item_active){
		filter_name = 'active';
	} else if (item_now) {
		filter_name = 'now';
	} else {
		var filter_name = item_item_parent_item_id; //$('.editItem_button').html() ? $('.editItem_button').html() : 'home';
	}
	 
	$.ajax({
		dataType:"text",
		type:"POST",
		data: {'item_name': $.trim($('#add_child_item_input').val())},
		url:url,
		success: function(item_id_response) {	 
			listItems(item_item_parent_item_id, filter_name, null, null, null, null, null, item_id_response, 'add');
		}
	});
}

function addNewParentItem() {
	var child_item_id = $('#item_id').val();
	var url = "ajax.php?action=addNewParentItem&item_name=" + $('#add_parent_item_input').val() + "&item_item_child_item_id=" + child_item_id;
	$.ajax({
		dataType:"text",
		url:url,
		success: function(item_id_response) {	
			refreshItemInfo(child_item_id);
		}
	});
}

function search() {
	$.ajax({
		url:"ajax.php?action=listItems&filter=search&value=" + $('#searchItems_input').val(),
		success: function(lines){
			$("#list_items_page").html(lines);
			showPage(document.getElementById('list_items_page'));   
		}
	});
}
  
function editItem() {
	displayItem($('.editItem_button').val());
}
  
/*
function filterSubmit() {
	createFilterItemsDropdown();
	setTimeout('$("#filter_items_dropdown").focus();', 400);
	showActionBarElements(['shareItem_button', 'decrementItemCount', 'incrementItemCount', 'listHomeItems_button', 'filter_submit_button', 'showAddItemInput_button']); 
	showPage(document.getElementById('filter_page'));
}
*/
  
function populateParentsDropdown(item_id, selected_val) {
	if(typeof(selected_val) == 'undefined') selected_val = '';
	$("#parents_dropdown").html('');
	$("<select>").attr("id", "parents_dropdown_selector"). attr("class", "items_dropdown items_page").appendTo("#parents_dropdown");
	$.ajax({
		dataType:"html",
	    url:"ajax.php?action=getParentsDropdown&item_id=" + item_id,
	    success: function(data) {
			$("#parents_dropdown_selector").append(data);
			$('#parents_dropdown').show();
			populateParents(item_id, selected_val);
			$('#parents_dropdown_selector').change(function(){
				addParent($(this).val());
			});
		}
	});    
}

function populateParents(item_id, selected_val) {
	$.ajax({
		dataType:"json",
		url:"ajax.php?action=populateParents&item_id=" + item_id,
		success: function(data) {
			$('#item_parents').html(data['html']);
		}
	});
}
    
function displayItem(item_id) {
	refreshItemInfo(item_id);
    showPage(document.getElementById("edit_item_page"));
}

function showPage(el){
	$('.panel').hide();
	$(el).fadeIn('slow');
}

function refreshUpdated(item_id){
	$.getJSON("ajax.php?action=getItemFields&item_id=" + item_id,
	    function(data){
	      $("#item_updated_date").html(data[0].item_updated);
        }
	);
}
  
function showActionBarElements(buttons) {
	$.each($('.action_bar_buttons').children('a, input'), function(){
        if($.inArray($(this).attr('name'), buttons) == -1){
	    	$(this).hide();
        }
	}); 
	$.each(buttons, function(index, value){
		$('.' + this).css('display', 'inline-block');
		$('#' + this).css('display', 'inline-block');
		$('*[name="' + value + '"]').css('display', 'inline-block');
    });
}

function include(destination) {
	var e=window.document.createElement('script');
	e.setAttribute('src',destination);
	window.document.body.appendChild(e);
}

function listItems(filter, filter_name, complete, timeframe, sort_field, sort_direction, edit, new_item_id, action) {

	$('#action_bar').slideDown();
	$('.action_bar_buttons').show();
	mode = display = 'list';
	if(typeof(filter) == 'undefined') filter = $('#item_id').val();
	if(complete == null) complete = 0;
	if(timeframe == null) timeframe = 1; 
	if(sort_field == null) sort_field = ''; 
	if(sort_direction == null) sort_direction = ''; 
	$("#filter_items_dropdown").val(filter);
	if(typeof(filter_name) == 'undefined') filter_name = $('.editItem_button').html(); //$("#filter_items_dropdown option:selected").text();
	var filter_items_dropdown2_operator = $('#filter_items_dropdown2_operator').val();
	var filter_items_dropdown2 = $('#filter_items_dropdown2').val();
	var second_filter = filter_items_dropdown2 == null ? '' : "&filter2=" + filter_items_dropdown2 + "&filter2op=" + $('#filter_items_dropdown2_operator').val();

	$.ajax({
		dataType:"html",
		url:"ajax.php?action=listItems&display=" + display + "&edit=" + edit + "&filter=" + filter + "&filter_name=" + filter_name + "&complete="  + complete + "&timeframe="  + timeframe + "&sort_field="  + sort_field + "&sort_direction="  + sort_direction + second_filter,
		success: function(lines) {
		
			if(lines === '0' || mode === 'edit'){

				hide_list_button = true;
				displayItem(filter);
				mode = 'edit';
				
			} else {
			
				previous_list_item_id = filter;
				special_list = filter_name;

				hide_list_button = false;
				
				$('#list_items_page').html(lines);
				showPage($('#list_items_page')); 
			 
				if('home' == filter_name) {
					showActionBarElements(['listNow_button', 'listActive_button', 'editList_button', 'logoutUser_button', 'searchItems_button']); 
				} else if('now' == filter_name) {
					showActionBarElements(['listHomeItems_button', 'listActive_button', 'editList_button', 'searchItems_button']); 
				} else if('active' == filter_name) {
					showActionBarElements(['listHomeItems_button', 'listNow_button', 'editList_button', 'searchItems_button']); 
				} else {
					showActionBarElements(['editItem_button', 'listNow_button', 'listActive_button', 'editList_button', 'listHomeItems_button', 'showAddItemInput_button']); 
				}	
				$('.editItem_button').html(filter_name);
				$('.editItem_button').val(filter);
				$('#add_child_item_input').val(add_child_item_input_default_val);

				$('#' + new_item_id).addClass('new_line');
				
				if(action == 'list'){
					$('#add_child_item_input').focus();
				} else if(action == 'add'){
					$('#' + new_item_id).css('font-size','150%');
					showListItemButtons(new_item_id);
					var new_li_html = $('#' + new_item_id).parent('li').html();
					$('#' + new_item_id).parent('li').remove();
					$('#list_items_page').prepend( '<li>' + new_li_html + '</li>' );
				} else if (	mode == 'edit_list'){  
					editList();
				}
			}
		}
	});
	return true;
}         

function editList(){
	mode = 'edit_list';
	$('.list_item').addClass('edit_item');
	$('.edit_item').removeClass('list_item'); //.removeClass('active_1');
	showActionBarElements(['shareItem_button', 'listList_button', 'listHomeItems_button', 'searchItems_button', 'showAddItemInput_button']);
}

function listList(item_id){
	mode = 'list';
	listItems(item_id);
}

function listHomeItems(edit){
	if(mode == 'edit_list'){
		listItems(null, 'home', null, null, null, null, null, null, 'edit');		
	} else {
		listItems(null, 'home');		
	}
}

function listNow(){
	if(mode == 'edit_list'){
		listItems(null, 'now', null, null, null, null, null, null, 'edit');		
	} else {
		listItems(null, 'now');		
	}
}

function listActive(){

	if(mode == 'edit_list'){
		listItems(null, 'active', null, null, null, null, null, null, 'edit');		
	} else {
		listItems($('#item_id').val(), 'active');		
	}
}
    
function updateField(jq_el, field_name, item_id, field_value, refresh) {
	var url = "ajax.php?action=updateField&item_id=" + item_id + "&field_name=" + field_name; //.replace( /\n/g, '\r' );
	$.ajax({
		dataType:"text",
		url:url,
		type:"POST",
		data: {'field_value': $.trim(field_value)},
		success: function(item_id_response) {
		    // the fadeIn causes strict js warning in firebug:reference to undefined property thisCache[name]
			jq_el.hide().fadeIn(1000);
			if(refresh) {
				jq_el.css('color', 'green').css('font-weight', 'bold');
				refreshItemInfo(item_id_response, field_name);
			}
			return true; 
		}
	});	
}
  
function createCountInputLine(item_id) {
	$.ajax({
		dataType:"html",
		url:"ajax.php?action=getItemCount&item_id=" + item_id,
		success: function(lines) {
			$("#item_count").val(lines);
			showTotalCount(item_id);
		}
	});
}

function incrementItemCount(item_id) {
	$.ajax({
		dataType:"html",
		url:"ajax.php?action=incrementItemCount&item_id=" + item_id,
		success: function(lines){
			$("#item_count").val(lines);
			showTotalCount(item_id);
			refreshUpdated(item_id);
			$('#' + item_id + '_list_counter').val(lines);
		}
	});
}

function decrementItemCount(item_id) {
	$.ajax({
		dataType:"html",
		url:"ajax.php?action=decrementItemCount&item_id=" + item_id,
		success: function(lines) {
			$("#item_count").val(lines);
			showTotalCount(item_id);
			refreshUpdated(item_id);
			$('#' + item_id + '_list_counter').val(lines);
		}
    });
}
  
function reset() {    
    $("#item_id").val('');
	$('#item_note').val('');
}
  
function hideAddressBar() { 
    window.scrollTo(0, 1); 
}

function addParent(parent_item_id, item_id) {
	if( $('#parents_dropdown_selector').val() === 'new_parent') {
		$('#add_parent_item_input').show();
	} else {
		if(typeof(item_id) == 'undefined') item_id = $('#item_id').val();
		if(item_id != ''){
			$.ajax({
				dataType:"html",
				url:"ajax.php?action=addParent&item_item_parent_item_id=" + parent_item_id + "&item_id=" + item_id,
				success: function() {
					refreshItemInfo(item_id);
				}
			});		
		}
	}
}
  
function addChild(child_item_id) {
	$.ajax({
		dataType:"html",
		url:"ajax.php?action=addChild&item_item_child_item_id=" + child_item_id + "&item_id=" + $('#item_id').val(),
		success: function() {
			refreshItemInfo($('#item_id').val());
		}
	});
}

function searchItems(){
	showActionBarElements(['listHomeItems_button', 'searchItems_input', 'showAddItemInput_button', 'editList_button']);
	$('#searchItems_input').focus().setCursorPosition(0);
}

function shareItem(){
	$('#share_bar').show();
}
