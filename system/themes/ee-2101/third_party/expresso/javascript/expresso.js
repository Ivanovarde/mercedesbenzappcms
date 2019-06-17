function expresso(field, file_uploads, config)
{
	var defaults =
	{
		uiColor: "#ECF1F4",
		height: 80,
		entities_additional: "", //"#39,#123,#125",
		forcePasteAsPlainText: true,
		toolbarCanCollapse: false,
		resize_enabled: true,
		extraPlugins: "headers,mediaembed,autogrow,youtube",
		removePlugins: "elementspath,resize",
		removeFormatTags: "b,big,code,del,dfn,em,font,i,ins,kbd,q,samp,small,span,strike,strong,sub,sup,tt,u,var,h1,h2,h3,h4,h5,h6",
		resize_maxWidth: "100%",
		dialog_backgroundCoverColor: "#262626",
		dialog_backgroundCoverOpacity: "0.85",
		autoGrow_onStartup: true,
		autoGrow_minHeight: 80,
		autoGrow_maxHeight: 600,
		jqueryOverrideVal: false,
		allowedContent: true,
		autoParagraph: false
	};

	config = $.extend(defaults, config);

	$("textarea#" + field).ckeditor(config);
}


function customise_dialogs(file_uploads)
{
	// customise dialogs
	CKEDITOR.on('dialogDefinition', function(ev)
	{
		var dialogName = ev.data.name;
		var dialogDefinition = ev.data.definition;
		var infoTab = dialogDefinition.getContents('info');

		// link dialog
		if (dialogName == 'link')
		{
			// remove browse button
			infoTab.remove('browse');

 			var urlOptionsPanel = infoTab.get('urlOptions');

			// add extra link dropdowns
			$.each(extra_links, function(key, links)
			{
	            urlOptionsPanel.children.push(
	            {
						type : 'select',
						label : links['name'],
						id : links['name'],
						items : links['links'],
						onChange : function()
						{
							var currentDialog = CKEDITOR.dialog.getCurrent();
							currentDialog.getContentElement('info', 'url').setValue(this.getValue());
							currentDialog.getContentElement('info', 'protocol').setValue('');
						}
				});
			});

			/*
			// add open in new window checkbox
            urlOptionsPanel.children.push(
            {
					type : 'checkbox',
					label : 'Open link in a new window',
					id : 'expresso_open_in_new_window',
					style : 'margin-top:13px;',
					onClick : function()
					{
						var linkTargetType = CKEDITOR.dialog.getCurrent().getContentElement('target', 'linkTargetType');

						if (this.getValue()) {
							linkTargetType.setValue('_blank');
						}
						else {
							linkTargetType.setValue('notSet');
						}
					}
			});
			*/

			// if file uploads allowed
			if (file_uploads)
			{
				// add browse button
	            urlOptionsPanel.children.push(
	            {
						type : 'button',
						label : 'Browse Files',
						id : 'expresso_browse_button',
						style : 'margin-top:13px;'
				});

				// on dialog load (after it has been created)
				dialogDefinition.onLoad = function()
				{
					// add filebrowser trigger to browse button
					add_filebrowser_trigger($("#" + this.getContentElement('info', 'expresso_browse_button').domId), $("#" + this.getContentElement('info', 'url').domId).find("input"), 'all', 'all');
					//loadAssetsSheet(null, 'all', 'image');
				}
			}
		}

		// image dialog
		else if (dialogName == 'image')
		{
			// remove browse button
			infoTab.remove('browse');

			// if file uploads allowed
			if (file_uploads)
			{
				// add browse button
	            infoTab.elements[0].children[0].children.push(
	            {
					type : 'button',
					label : 'Browse Files',
					id : 'expresso_browse_button',
					style : 'display:inline-block; margin-top:13px;'
				});

				// on dialog load (after it has been created)
				dialogDefinition.onLoad = function()
				{
					// add filebrowser trigger to browse button
					add_filebrowser_trigger($("#" + this.getContentElement('info', 'expresso_browse_button').domId), $("#" + this.getContentElement('info', 'txtUrl').domId).find("input"), 'images', 'all');
				}
			}
		}
	});
}



/**
 * Load Assets Sheet
 */
function loadAssetsSheet(params, filedir, kind, field) {
	var sheet = new Assets.Sheet({
		filedirs: (filedir == 'all' ? filedir : [filedir]),
		kinds: (kind == 'any' ? kind : [kind]),

		onSelect: function(files) {
			CKEDITOR.tools.callFunction(params.CKEditorFuncNum, files[0].url);

			if (files[0].id) {
		    	$(field).val(files[0].url);
		    	$(field).focus();

		    	if (txtUrl = CKEDITOR.dialog.getCurrent().getContentElement('info', 'txtUrl'))
		    	{
		    		txtUrl.fire('change');
		    	}
			}
		}
	});

	sheet.show();
};


function add_filebrowser_trigger(button, field, content_type, directory)
{
	// $(button).click(function() {
	// 	loadAssetsSheet({content_type: content_type}, 'all', 'image', field);
	// });

	$.ee_filebrowser.add_trigger($(button), field, {content_type: content_type, directory: directory}, function(file, field)
	{
		var url = EE["upload_directories"][file.upload_location_id]["url"] + file.file_name;

		if ($(field).is('textarea'))
		{
			var selection = "";

			if (!CKEDITOR.env.ie)
			{
			   selection = $(field).ckeditorGet().getSelection().getNative().toString();
			}

			var link = selection ? selection : file.file_name;
	    	var html = file.is_image ? '<img src="' + url + '" alt="' + file.file_name + '" />' : '<a href="' + url + '">' + link + '</a>';

			$(field).ckeditorGet().insertHtml(html);
	    }

	    else
	    {
	    	$(field).val(url);
	    	$(field).focus();

	    	if (txtUrl = CKEDITOR.dialog.getCurrent().getContentElement('info', 'txtUrl'))
	    	{
	    		txtUrl.fire('change');
	    	}
	    }
	});
}
