$(window).on('load', function () {


});

$(document).ready(function () {

	ivMakeGridColorPicker();

});


function ivMakeGridColorPicker(data) {

	console.log('ivMakeGridColorPicker: start');

	var rows = $("b:contains(Color)").parents('table:first').find('tbody > tr').not('.blank_row').not('.empty_field');

	if (rows.length) {

		rows.each(function (i, e) {
			var el = $(e).find('td').eq(4).find('input');

			//console.log(el);
			console.log('Making iv Grid ColorPicker #' + i);

			el.ColorPicker({
					onBeforeShow: function () {
						$(this).ColorPickerSetColor(this.value);
					},
					onSubmit: function (hsb, hex, rgb, el) {
						$(el).val('#' + hex);
						$(el).ColorPickerHide();
					}
				})
				.bind('keyup', function () {
					$(this).ColorPickerSetColor(this.value);
				});

		});

	} else {
		console.log('No iv Grid ColorPicker detected');
	}

	console.log('ivMakeGridColorPicker: end');

}

/* Location: ./system/expressionengine/third_party/iv_grid_color_picker/acc.iv_grid_color_picker.js */
