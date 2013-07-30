
const username_input_default_val = $('#username_input').val();
const password_input_default_val = $('#password_input').val();

$('.inside_label').live('focus', function(){
	el = this.id + '_default_val';
	default_val = eval(el);
	if($(this).val() == default_val) $(this).val('');
}).live('blur', function(){
	if($(this).val() == '') $(this).val(default_val);
});


$(document).ready(function() {
	$('#username_input').focus();
});
