$(document).ready(function() {

	// On publish form submit start
	$('#publishForm').on('submit', function(e){

		var devmode = false;

		if(devmode){
			e.preventDefault();
		}

		if($(".cke_contents").length){

			if(devmode){
				console.log('.cke_contents: ' + $(".cke_contents").length);
			}

			$(".cke_contents").each(function(i, el){
				var elem = $(el);

				if(devmode){
					console.log(elem);
				}

				if(elem.find("iframe").length){

					if(devmode){
						console.log('iframe: ' + elem.find("iframe"));
					}

					var frame = elem.find("iframe");
					var body = frame.contents().find("body");
					var textarea = textarea = elem.parent().parent().parent().find("textarea");

					if(devmode){
						console.log('textarea : ' + textarea.attr('id'));
					}

					if(body.text() == ""){

						if(devmode){
							console.log('Vaciar: ' + textarea.attr('id'));
						}

					}
				}

			});
		}

		if(devmode){
			return false;
		}

	});
	// On publish form submit end

});
/* Location: ./system/expressionengine/third_party/iv_empty_expresso_fields/acc.iv_empty_expresso_fields.js */
