
$(document).ready(function() {
	$('#show_msgs').click(function() {
		var checked = ($(this).attr('checked') === 'checked');
		$('.debug_msg').each(function() {
			$(this).css('display', checked? 'block' : 'none');
		});
	});

	$('#show_errors').click(function() {
		var checked = ($(this).attr('checked') === 'checked');
		$('.error_msg').each(function() {
			$(this).css('display', checked? 'block' : 'none');
		});
	});
});