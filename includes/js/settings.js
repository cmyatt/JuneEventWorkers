
$(document).ready(function() {

	var delete_func = function(button) {
		var color, text,
			num = button.attr('id').substr(14);

		if (button.val() === 'Delete') {
			color = 'rgba(230, 130, 130, 1)';
			text = 'Undo';
		} else {
			color = 'rgba(230, 230, 230, 0)';
			text = 'Delete';
		}

		$('#type_'+num).parent().css('background-color', color);
		$('#desc_'+num).parent().css('background-color', color);
		$('#pay_'+num).parent().css('background-color', color);
		$('#number_'+num).parent().css('background-color', color);
		$('#online_apps_'+num).parent().css('background-color', color);
		$('#shifts_'+num).parent().css('background-color', color);
		button.parent().css('background-color', color);

		$('#delete_'+num).val(text);
		button.val(text);
	};

	$('#add_worker').click(function() {
		var type_num  = parseInt($('#num_new_types').val()),
			heading   = '<td class="heading"><input type="text" id="type_'+type_num+'" name="type_',
			row_td    = '<td class="inner_input"><input type="',
			desc_col  = '<td class="inner_input" style="padding: 0px;"><textarea rows="3" cols="17" id="desc_'+type_num+'" name="desc_',
			pay_col   = 'number" value="0.00" min="0.00" step="0.01" id="pay_'+type_num+'" name="pay_',
			num_col   = 'number" value="0" min="0" step="1" id="number_'+type_num+'" name="number_',
			app_col   = 'checkbox" id="online_apps_'+type_num+'" name="online_apps_',
			shift_col = 'checkbox" id="shifts_'+type_num+'" name="shifts_',
			del_col   = '<input type="button" id="delete_button_'+type_num+'" value="Delete" class="button-primary" />';

		heading  += type_num + '" style="width: 100px;" /></td>';
		desc_col += type_num + '"></textarea></td>';
		pay_col   = row_td + pay_col + type_num + '" /></td>';
		num_col   = row_td + num_col + type_num + '" /></td>';
		app_col   = row_td + app_col + type_num + '" /></td>';
		shift_col = row_td + shift_col + type_num + '" /></td>';
		del_col   = row_td + 'hidden" id="delete_'+type_num+'" name="delete_'+type_num+'" value="Delete" />' + del_col + '</td>';

		$('#num_new_types').val(type_num+1);
		$('#new_worker_col').before(heading);
		$('#new_desc_col').before(desc_col);
		$('#new_pay_col').before(pay_col);
		$('#new_numbers_col').before(num_col);
		$('#new_applications_col').before(app_col);
		$('#new_shifts_col').before(shift_col);
		$('#new_remove_col').before(del_col);

		$('#delete_button_'+type_num).click(function() {
			delete_func($(this));
		});

		$('#type_'+type_num).focus();
	});
	
	$('input[type=button]').each(function() {
		if ($(this).val() === 'Delete') {
			$(this).click(function() {
				delete_func($(this));
			});
		}
	});
});