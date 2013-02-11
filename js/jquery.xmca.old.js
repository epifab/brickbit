var ciderbit = {
	"rootPath": "/cambiamentodue.it/",
	
	"init": function(javascript) {

			$(document).ready(function() {
			var node = $(document);
			$(".event_description").hide();
			ciderbit.waitDialogInit();

			InitTwitter(document,"script","twitter-wjs");

			$('#banners').nivoSlider({
				effect: 'random', // Specify sets like: 'fold,fade,sliceDown'
				slices: 15, // For slice animations
				boxCols: 8, // For box animations
				boxRows: 4, // For box animations
				animSpeed: 500, // Slide transition speed
				pauseTime: 6000, // How long each slide will show
				startSlide: 0, // Set starting Slide (0 index)
				directionNav: true, // Next & Prev navigation
				directionNavHide: true, // Only show on hover
				controlNav: false, // 1,2,3... navigation
				controlNavThumbs: false, // Use thumbnails for Control Nav
				pauseOnHover: true, // Stop animation while hovering
				manualAdvance: false, // Force manual transitions
				prevText: 'Prev', // Prev directionNav text
				nextText: 'Next', // Next directionNav text
				randomStart: true, // Start on a random slide
				beforeChange: function(){}, // Triggers before a slide transition
				afterChange: function(){}, // Triggers after a slide transition
				slideshowEnd: function(){}, // Triggers after all slides have been shown
				lastSlide: function(){}, // Triggers when last slide is shown
				afterLoad: function(){} // Triggers when slider has loaded
			});
			
			$('.ciderbit_reload_form', node).each(function() {
				formId = $(this).attr("id");
				destId = $(this).attr("name");
				ciderbit.addReloadForm(formId, destId);
			});
			
			$('.datepicker').datepicker();
			
			$('textarea.rich_text').tinymce({
				// Location of TinyMCE script
				script_url : ciderbit.rootPath + 'js/tinymce/jscripts/tiny_mce/tiny_mce.js',

				width: 600,
				height: 300,

				// General options
				theme : "advanced",
				
				content_css : "css/pstyle.css",
				
				plugins : "youtubeIframe,autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
				theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect", //fontselect,fontsizeselect",
				theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,image,youtubeIframe,code",
				theme_advanced_buttons3 : "undo,redo,|,hr,removeformat,|,sub,sup,|,charmap,|,print,|,ltr,rtl,|,insertdate,inserttime",
//				theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",

				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left",
				theme_advanced_statusbar_location : "bottom",
				theme_advanced_resizing : true
			});
			
			$('textarea.rich_text_light').tinymce({
				// Location of TinyMCE script
				script_url : ciderbit.rootPath + 'js/tinymce/jscripts/tiny_mce/tiny_mce.js',

				width: 600,
				height: 250,
				
				// General options
				theme : "advanced",
				
				plugins : "style,preview,lists,media,searchreplace,contextmenu,paste,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",

				theme_advanced_buttons1 : "bold,italic,underline,|,cut,copy,paste,|,undo,redo,|,bullist,numlist",
				theme_advanced_buttons2 : "",
				theme_advanced_buttons3 : "",
				theme_advanced_buttons4 : "",

				// Theme options
				content_css : "css/pstyle.css",

				theme_advanced_toolbar_location : "top",
				theme_advanced_toolbar_align : "left",
				theme_advanced_statusbar_location : "bottom",
				theme_advanced_resizing : true
			});

			buttonClasses = '.ciderbit_control, button';

			$(buttonClasses, node).button("destroy");

			$(buttonClasses, node).each(function(id,el) {
				var text = false;
				var icon = "ui-icon-";

				if ($(el).hasClass("read")) {
					icon += "search";
				} else if ($(el).hasClass("create")) {
					icon += "document";
				} else if ($(el).hasClass("update")) {
					icon += "wrench";
				} else if ($(el).hasClass("delete")) {
	//				icon += "trash";
					icon += "closethick";
				} else if ($(el).hasClass("heart")) {
					icon += "heart";
				} else if ($(el).hasClass("star")) {
					icon += "star";
				} else if ($(el).hasClass("mail")) {
					icon += "mail-closed";
				} else if ($(el).hasClass("cancel")) {
					icon += "close";
				} else if ($(el).hasClass("home")) {
					icon += "home";
				} else if ($(el).hasClass("search")) {
					icon += "search";
				} else if ($(el).hasClass("asc")) {
					icon += "triangle-1-n";
				} else if ($(el).hasClass("desc")) {
					icon += "triangle-1-s";
				} else {
					icon = false;
				}

				if ($(el).hasClass("full")) {
					text = true;
				} else {
					text = false;
				}

				if (icon == false) {
					$(el).button({
						text: text
					})
				} else {
					$(el).button({
						text: text,
						icons: {
							primary: icon
						}
					});
				}
			});
			
			if (javascript != undefined) {
				jQuery.globalEval(javascript);
			}
		});
	},

	"getResponseObject": function(componentResponse) {
		var responseCustomArgs = new Array();
		$('customargs customarg', componentResponse).each(function() {
			if ($(this).attr("value") != undefined) {
				responseCustomArgs[$(this).attr("name")] = $(this).attr("value");
			} else {
				responseCustomArgs[$(this).attr["name"]] = $(this).contents();
			}
		});

		var ciderbitResponse = {
			// Contenuto della risposta AJAX
			'content': $('content', $(componentResponse)).contents(),

			// Tipo di risposta: FORM, NOTIFY, ERROR, READ
			'type': $(componentResponse).attr("type"),

			// Nome del componente
			'name': $(componentResponse).attr("name"),
			// Identificativo univoco della risposta
			'id': $(componentResponse).attr("id"),

			// Identificativo univico del form del componente
			'formId': $(componentResponse).attr("formId"),
			// Identificativo univoco del livello principale del componente
			'contId': $(componentResponse).attr("contId"),

			// Indirizzo del componente
			'address': $(componentResponse).attr("address"),
			// Titolo
			'title': $(componentResponse).attr("title"),
			// Argomento di ritorno
			'customArgs': responseCustomArgs,
			// Codice javascript da eseguire al caricamento
			'javascript': $('javascript', $(componentResponse)).text()
		};
		
		return ciderbitResponse;
	},
	
	"waitDialog": $('<div><h4>Please wait...</h4></div>'),

	"waitDialogInit": function() {
		ciderbit.waitDialog.dialog({
			'dialogClass': 'ciderbit_dialog ciderbit_wait_dialog',
			'position': 'center',
			'title': 'Please wait',
			'resizable': false,
			'draggable': false,
			'stack': true,
			'modal': true,
			'autoOpen': false,
			'width': 400,
			'height': 130,
			'closeOnEscape': false
		});
	},

	"waitDialogShow": function() {
		ciderbit.waitDialog.dialog("open");
	},

	"waitDialogHide": function() {
		ciderbit.waitDialog.dialog("close");
	},
	
	"confirm": function(title, question, options) {
		width = 400;
		
		if (question == undefined || question == '') {
			question = "Continuare?";
		}
		
		target = $("<div><h2>" + title + "</h2><p class=\"alert\">" + question + "</p></div>");

		target.dialog({
			'dialogClass': 'ciderbit_dialog untitled',
			'position': 'center',
			'modal': true,
			'autoOpen': false,
			'width': width,
			'onClose': function() {
				target.dialog("destroy");
				target.remove();
				if (onclose != undefined) {
					onclose();
				}
			},
			'buttons': {
				'Ok': function() {
					target.dialog("close");
					ciderbit.request(options);
				},
				'Annulla': function() {
					target.dialog("close");
				}
			}
		});
		
		target.dialog("open");
	},
	
	"dialogNotify": function(target, title, width, onclose) {
		if (width == undefined) {
			width = 600;
		}
		if (title == undefined) {
			cl = 'ciderbit_dialog untitled';
		} else {
			cl = 'ciderbit_dialog';
		}
		target.dialog({
			'dialogClass': cl,
			'position': 'center',
			'title': title,
			'modal': true,
			'autoOpen': false,
			'width': width,
			'onClose': function() {
				target.dialog("destroy");
				target.remove();
				if (onclose != undefined) {
					onclose();
				}
			},
			'buttons': {
				'Ok': function() {
					target.dialog("close");
				}
			}
		});
		
		target.dialog("open");
	},
	
	"addReloadForm": function(formId, destId) {
		$("#" + formId).ajaxForm({
			success: function(data) {
				
				ciderbitResponse = ciderbit.getResponseObject(data);
				
				if (ciderbitResponse.type == undefined) {
					dialog = $("<div></div>");
					dialog.append($('<h3>La risposta inviata dal server non e\' stata interpretata correttamente</h3>'));
					ciderbit.dialogNotify(dialog, 'Errore interno')
				}
				else if (ciderbitResponse.type == "ERROR") {
					dialog = $("<div></div>");
					dialog.append(ciderbitResponse.content);
					ciderbit.dialogNotify(dialog, ciderbitResponse.title);
				}
				else {
					$("#" + destId).children().remove();
					$("#" + destId).append($("#" + ciderbitResponse.contId, $(data)).children());
					ciderbit.init(ciderbitResponse.javascript);
				}
			}
		});
	},

	"reloadComponents": function() {
		$(".ciderbit_reload_form").each(function() {
			$(this).submit();
		});
	},
	
	"_setSort": function(options) {
		$('.sorts', $("#" + options.formId)).remove();
		$("#" + options.formId).append($('<input type="hidden" class="sorts" name="' + options.prefix + 'sorts[0]" value="' + options.path + "|" + options.type + '"/>'));
	},

	"sort": function(options) {
		if (options.prefix == undefined) {
			options.prefix = '';
		}
		ciderbit._setSort(options);
		$("#" + options.formId).submit();
	},

	"_inputFilters": new Array(),
	
	"_setFilter": function(options) {
		if (ciderbit._inputFilters[options.ctrlId] == undefined) {
			ciderbit._inputFilters[options.ctrlId] = new Array();
			ciderbit._inputFilters[options.ctrlId]["path"] = $('<input type="hidden" class="ciderbit_filter ciderbit_filter_path" name="' + options.prefix + 'filters[' + options.ctrlId + '][path]" value="' + options.path + '"/>');
			ciderbit._inputFilters[options.ctrlId]["lop"] = $('<input type="hidden" class="ciderbit_filter ciderbit_filter_lop" name="' + options.prefix + 'filters[' + options.ctrlId + '][lop]" value="AND"/>');
			ciderbit._inputFilters[options.ctrlId]["rop"] = $('<input type="hidden" class="ciderbit_filter ciderbit_filter_rop" name="' + options.prefix + 'filters[' + options.ctrlId + '][rop]" value="' + options.rop + '"/>');
			ciderbit._inputFilters[options.ctrlId]["value"] = $('<input type="hidden" class="ciderbit_filter ciderbit_filter_value" name="' + options.prefix + 'filters[' + options.ctrlId + '][value]" value=""/>');

			$("#" + options.formId).append(ciderbit._inputFilters[options.ctrlId]["path"]);
			$("#" + options.formId).append(ciderbit._inputFilters[options.ctrlId]["lop"]);
			$("#" + options.formId).append(ciderbit._inputFilters[options.ctrlId]["rop"]);
			$("#" + options.formId).append(ciderbit._inputFilters[options.ctrlId]["value"]);
		}
		if (ciderbit._inputFilters[options.ctrlId]["value"].val() == $("#"+options.ctrlId).val()) {
			return false;
		} else {
			ciderbit._inputFilters[options.ctrlId]["value"].val($("#"+options.ctrlId).val());
			return true;
		}
	},
	
	"_unsetFilter": function(ctrlId) {
		if (ciderbit._inputFilters[ctrlId] != undefined) {
			ciderbit._inputFilters[ctrlId]["path"].remove();
			ciderbit._inputFilters[ctrlId]["lop"].remove();
			ciderbit._inputFilters[ctrlId]["rop"].remove();
			ciderbit._inputFilters[ctrlId]["value"].remove();
			delete ciderbit._inputFilters[ctrlId];
			return true;
		}
		return false;
	},

	"filter": function(options) {
		// {formId, path, rop, ctrlId, prefix}
		if (options.prefix == undefined) {
			options.prefix = '';
		}
		var reload = false;
		if ($("#" + options.ctrlId).val() == "") {
			ciderbit._unsetFilter(options.ctrlId);
			reload = true;
		} else {
			reload = ciderbit._setFilter(options);
		}
		if (reload) {
			$(".ciderbit_paging", $("#" + options.formId)).remove();
			$("#" + options.formId).submit();
		}
	},

	"paging": function (formId, page) {
		$(".ciderbit_paging", $("#" + formId)).remove();
		$("#" + formId).append($('<input type="hidden" class="ciderbit_paging" name="paging[page]" value="' + page + '"/>'));
		$("#" + formId).submit();
	},

	"stdHandler": function(componentResponse, defaults) {

		// prima cosa: svuoto il contenuto del target
		defaults.target.children().remove();
		if (defaults.popup) {
			defaults.target.dialog("destroy");
		}

		ciderbitResponse = ciderbit.getResponseObject(componentResponse);

		if (ciderbitResponse.type == undefined) {
			defaults.target.append($('<h3>La risposta inviata dal server non e\' stata interpretata correttamente</h3>'));
			ciderbit.dialogNotify(defaults.target, 'Errore interno')
		}
		
		else if (ciderbitResponse.type == "READ") {
			
			defaults.target.append(ciderbitResponse.content);
			
			if (defaults.popup) {
				ciderbit.dialogNotify(defaults.target, ciderbitResponse.title, defaults.width);
			}
			
			ciderbit.init(ciderbitResponse.javascript);
			
			defaults.onRead(ciderbitResponse);
			
		}

		else if (ciderbitResponse.type == "FORM") {

			defaults.target.append(ciderbitResponse.content);

			// La risposta del componente è un form
			buttons = new Array();

			if (defaults.okButton) {
				okButtonStdHandler = function() {
					var sent = 0;
					if (defaults.waitMessages) {
						ciderbit.waitDialogShow();
					}
					$("#" + ciderbitResponse.formId).ajaxSubmit({
						url: ciderbitResponse.address,
						beforeSubmit: function() {
							sent++;
							if (sent > 1) {
								sent--;
								return false;
							} else {
								return true;
							}
						},
						success: function(newComponentResponse) {
							if (defaults.waitMessages) {
								ciderbit.waitDialogHide();
							}
							ciderbit.stdHandler(newComponentResponse, defaults);
						}
					});
				}
				clickHandler = function() {defaults.okButtonOnClick(okButtonStdHandler);};
				buttons.push({'text': defaults.okButtonLabel, 'click': clickHandler});
			}

			if (defaults.koButton) {
				koButtonStdHandler = function() {
					if (defaults.popup) {
						defaults.target.dialog("close");
					} else {
						defaults.target.children().remove();
					}
				}
				clickHandler = function() {defaults.koButtonOnClick(koButtonStdHandler);};
				buttons.push({'text': defaults.koButtonLabel, 'click': clickHandler});
			}

			if (defaults.controls != null) {
				for (label in defaults.controls) {
					buttons.push({'text': label, 'click': defaults.controls[label]});
				}
			}

			if (defaults.popup) {
				defaults.target.dialog({
					'dialogClass': "ciderbit_dialog",
					'position': "center",
					'title': ciderbitResponse.title,
					'modal': true,
					'buttons': buttons,
					'width': defaults.width,
					'height': defaults.height,
					'maxHeight': defaults.maxHeight,
					'maxWidth': defaults.maxWidth,
					'autoOpen': false,
					'onClose': function() {
						defaults.target.remove();
					}
				});

				defaults.target.dialog("open");
			} else {
				formControls = $('<div class="form_controls"></div>');
				for (i = 0; i < buttons.length; i++) {
					
					initClick = function (click) {
						button = $('<button class="ui_button">' + buttons[i]['text'] + '</button>');
						button.click(function() {click();return false;});
						formControls.append(button);
					}
					initClick(buttons[i]['click']);
				}
				defaults.target.append(formControls);
			}

			ciderbit.init(ciderbitResponse.javascript);
			defaults.onForm(ciderbitResponse);
		}

		else if (ciderbitResponse.type == "NOTIFY") {
			if (defaults.popup) {
				defaults.target.remove();
			} else {
				defaults.target.children().remove();
			}
			
			ciderbit.reloadComponents();

			if (defaults.showResponse) {
				dialog = $("<div></div>");
				dialog.append(ciderbitResponse.content);
				ciderbit.dialogNotify(dialog, ciderbitResponse.title);
				setTimeout(function() {dialog.dialog("close");}, 2000);
			}

			defaults.onSuccess(ciderbitResponse);
		}

		else if (ciderbitResponse.type == "ERROR") {
			if (defaults.popup) {
				defaults.target.remove();
			} else {
				defaults.target.children().remove();
			}
			
			dialog = $("<div></div>");
			dialog.append(ciderbitResponse.content);
			ciderbit.dialogNotify(dialog, ciderbitResponse.title);

			defaults.onError(ciderbitResponse);
		}
	},

	"request": function(options) {
		var defaults = {
			'component': undefined,
			'args': null,
			'width': 600,
			'height': 'auto',
			'maxHeight': 800,
			'maxWidth': 1000,

			'target': null,
			'popup': true,

			'onForm': function(ciderbitResponse) {},
			'onRead': function(ciderbitResponse) {},
			'onSuccess': function(ciderbitResponse) {},
			'onError': function(ciderbitResponse) {},

			'okButton': true,
			'okButtonLabel': 'Save',
			'okButtonOnClick': function(stdHandler) {stdHandler();},

			'koButton': true,
			'koButtonLabel': 'Cancel',
			'koButtonOnClick': function(stdHandler) {stdHandler();},

			'showResponse': true,

//			'customControls': null,

			'waitMessages': true,
			'waitMessagesLabel': 'Please wait'
		};

		$.extend(defaults, options);

		if (defaults.component == undefined) {
			return;
		}
		if (defaults.target == null) {
			// creo un nuovo elemento
			defaults.target = $('<div></div>');
		} else {
			defaults.target = $("#" + defaults.target);
		}
		
		$.ajax({
			'dataType': "html",
			'url': ciderbit.rootPath + defaults.component + ".html",
			'data': defaults.args,
			success: function(componentResponse) {
				ciderbit.stdHandler(componentResponse, defaults);
			}
		});
	}
}

ciderbit.init();

function ShowContent(content_id) {
	$("#content_preview_" + content_id).hide();
	$(".full_content_" + content_id).show();
}
function HideContent(content_id) {
	$("#content_preview_" + content_id).show();
	$(".full_content_" + content_id).hide();
}
function HideShowEventDescription(event_id) {
	$("#event_description_" + event_id).toggle();
}
function	ChangeUrl() {
	letters_from = "àèéìòù";
	letters_to = "aeeiou";
	val = $("#edit_content_input_url").val();
	val = val.toLowerCase();
	val = val.replace(/[àèìòù]/g, function(x){
		return letters_to.charAt(letters_from.indexOf(x));
	});
	val = val.replace(/([^a-z0-9])/g, "_");
	$("#edit_content_input_url").val(val);
	$("#edit_content_label_url").html(val);
}
function InitTwitter(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}