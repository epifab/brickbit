//(function ($) {
	var ciderbit = {
		"translate": function(sentence,args) {
			return sentence;
		},

		"behaviors": new Array(),

		"setBehavior": function(k,f) {
			ciderbit.behaviors[k] = f;
		},
		
		"init": function(javascript) {
			$(document).ready(function() {
				var node = $(document);
				ciderbit.waitDialogInit();

				$('.system-panel-form', node).each(function() {
					formId = $(this).attr("id");
					formName = $(this).attr("name");
					ciderbit.addComponentForm(formId, formName);
				});

				for (x in ciderbit.behaviors) {
					ciderbit.behaviors[x]();
				}

	//			$(".event_description").hide();
	//			InitTwitter(document,"script","twitter-wjs");
	//
	//			$('#banners').nivoSlider({
	//				effect: 'random', // Specify sets like: 'fold,fade,sliceDown'
	//				slices: 15, // For slice animations
	//				boxCols: 8, // For box animations
	//				boxRows: 4, // For box animations
	//				animSpeed: 500, // Slide transition speed
	//				pauseTime: 6000, // How long each slide will show
	//				startSlide: 0, // Set starting Slide (0 index)
	//				directionNav: true, // Next & Prev navigation
	//				directionNavHide: true, // Only show on hover
	//				controlNav: false, // 1,2,3... navigation
	//				controlNavThumbs: false, // Use thumbnails for Control Nav
	//				pauseOnHover: true, // Stop animation while hovering
	//				manualAdvance: false, // Force manual transitions
	//				prevText: 'Prev', // Prev directionNav text
	//				nextText: 'Next', // Next directionNav text
	//				randomStart: true, // Start on a random slide
	//				beforeChange: function(){}, // Triggers before a slide transition
	//				afterChange: function(){}, // Triggers after a slide transition
	//				slideshowEnd: function(){}, // Triggers after all slides have been shown
	//				lastSlide: function(){}, // Triggers when last slide is shown
	//				afterLoad: function(){} // Triggers when slider has loaded
	//			});
	//			
	//			$('.datepicker').datepicker();
	//			
	//			$('textarea.rich_text').tinymce({
	//				// Location of TinyMCE script
	//				script_url : 'js/tinymce/jscripts/tiny_mce/tiny_mce.js',
	//
	//				width: 600,
	//				height: 300,
	//
	//				// General options
	//				theme : "advanced",
	//				
	//				content_css : "css/pstyle.css",
	//				
	//				plugins : "youtubeIframe,autolink,lists,spellchecker,pagebreak,style,layer,table,save,advhr,advimage,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template",
	//				theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect", //fontselect,fontsizeselect",
	//				theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,link,unlink,image,youtubeIframe,code",
	//				theme_advanced_buttons3 : "undo,redo,|,hr,removeformat,|,sub,sup,|,charmap,|,print,|,ltr,rtl,|,insertdate,inserttime",
	////				theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,spellchecker,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,blockquote,pagebreak,|,insertfile,insertimage",
	//
	//				theme_advanced_toolbar_location : "top",
	//				theme_advanced_toolbar_align : "left",
	//				theme_advanced_statusbar_location : "bottom",
	//				theme_advanced_resizing : true
	//			});
	//			
	//			$('textarea.rich_text_light').tinymce({
	//				// Location of TinyMCE script
	//				script_url : 'js/tinymce/jscripts/tiny_mce/tiny_mce.js',
	//
	//				width: 600,
	//				height: 250,
	//				
	//				// General options
	//				theme : "advanced",
	//				
	//				plugins : "style,preview,lists,media,searchreplace,contextmenu,paste,noneditable,visualchars,nonbreaking,xhtmlxtras,template,advlist",
	//
	//				theme_advanced_buttons1 : "bold,italic,underline,|,cut,copy,paste,|,undo,redo,|,bullist,numlist",
	//				theme_advanced_buttons2 : "",
	//				theme_advanced_buttons3 : "",
	//				theme_advanced_buttons4 : "",
	//
	//				// Theme options
	//				content_css : "css/pstyle.css",
	//
	//				theme_advanced_toolbar_location : "top",
	//				theme_advanced_toolbar_align : "left",
	//				theme_advanced_statusbar_location : "bottom",
	//				theme_advanced_resizing : true
	//			});
	//
	//			buttonClasses = '.system-controll, button';
	//
	//			/*	$(buttonClasses, node).button("destroy"); */
	//
	//			$(buttonClasses, node).each(function(id,el) {
	//				var text = false;
	//				var icon = "ui-icon-";
	//
	//				if ($(el).hasClass("read")) {
	//					icon += "search";
	//				} else if ($(el).hasClass("create")) {
	//					icon += "document";
	//				} else if ($(el).hasClass("update")) {
	//					icon += "wrench";
	//				} else if ($(el).hasClass("delete")) {
	//	//				icon += "trash";
	//					icon += "closethick";
	//				} else if ($(el).hasClass("heart")) {
	//					icon += "heart";
	//				} else if ($(el).hasClass("key")) {
	//					icon += "key";
	//				} else if ($(el).hasClass("star")) {
	//					icon += "star";
	//				} else if ($(el).hasClass("mail")) {
	//					icon += "mail-closed";
	//				} else if ($(el).hasClass("cancel")) {
	//					icon += "close";
	//				} else if ($(el).hasClass("home")) {
	//					icon += "home";
	//				} else if ($(el).hasClass("search")) {
	//					icon += "search";
	//				} else if ($(el).hasClass("asc")) {
	//					icon += "triangle-1-n";
	//				} else if ($(el).hasClass("desc")) {
	//					icon += "triangle-1-s";
	//				} else {
	//					icon = false;
	//				}
	//
	//				if ($(el).hasClass("full")) {
	//					text = true;
	//				} else {
	//					text = false;
	//				}
	//
	//				if (icon == false) {
	//					$(el).button({
	//						text: text
	//					})
	//				} else {
	//					$(el).button({
	//						text: text,
	//						icons: {
	//							primary: icon
	//						}
	//					});
	//				}
	//			});

				if (javascript != undefined) {
					jQuery.globalEval(javascript);
				}
			});
		},

		"getResponseObject": function(componentResponse) {
	//		var responseCustomArgs = new Array();
	//		$('customargs customarg', componentResponse).each(function() {
	//			if ($(this).attr("value") != undefined) {
	//				responseCustomArgs[$(this).attr("name")] = $(this).attr("value");
	//			} else {
	//				responseCustomArgs[$(this).attr["name"]] = $(this).contents();
	//			}
	//		});

			var ciderbitResponse = {
				// Contenuto della risposta AJAX
				'content': $('content', $(componentResponse)).contents(),

				// Tipo di risposta: FORM, NOTIFY, ERROR, READ
				'type': $(componentResponse).attr("type"),

				// Identificativo univoco della risposta
				'id': $(componentResponse).attr("id"),

				'editFormId': $(componentResponse).attr("editFormId"),
				'panelFormId': $(componentResponse).attr("panelFormId"),
				'panelFormName': $(componentResponse).attr("panelFormName"),

				// Indirizzo del componente
				'url': $(componentResponse).attr("url"),
				// Titolo
				'title': $(componentResponse).attr("title"),
				// Argomento di ritorno
	//			'customArgs': responseCustomArgs,
				// Codice javascript da eseguire al caricamento
				'javascript': $('javascript', $(componentResponse)).text()
			};

			return ciderbitResponse;
		},

	//	"waitDialog": function() {
	//		return $('<div><h4>' + ciderbit.translate('Please wait') + '</h4></div>');
	//	},

		"waitDialog": $('<div><h4>Please wait</h4></div>'),

		"waitDialogInit": function() {
			ciderbit.waitDialog.dialog({
				'dialogClass': 'system-dialog system-wait-dialog',
				'position': 'center',
				'title': ciderbit.translate('Please wait'),
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
				question = ciderbit.translate("Are you sure?");
			}

			target = $("<div><h2>" + title + "</h2><p class=\"alert\">" + question + "</p></div>");

			okLabel = ciderbit.translate('Ok');
			cancelLabel = ciderbit.translate('Cancel');

			buttons = {};
			buttons[ciderbit.translate('Ok')] = function() {
				target.dialog("close");
				ciderbit.request(options);
			}
			buttons[ciderbit.translate('Cancel')] = function() {
				target.dialog("close");
			}

			target.dialog({
				'dialogClass': 'system-dialog untitled',
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
				'buttons': buttons
			});

			target.dialog("open");
		},

		"dialogNotify": function(target, title, width, onclose) {
			if (width == undefined) {
				width = 600;
			}
			if (title == undefined) {
				cl = 'system-dialog untitled';
			} else {
				cl = 'system-dialog';
			}


			buttons = {};
			buttons[ciderbit.translate('Ok')] = function() {
				target.dialog("close");
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
				'buttons': buttons
			});

			target.dialog("open");
		},

		"addComponentForm": function(formId, formName) {
			$("#" + formId).ajaxForm({
				success: function(data) {
					ciderbitResponse = ciderbit.getResponseObject(data);

					if (ciderbitResponse.type == undefined) {
						dialog = $("<div></div>");
						dialog.append($('<h3>' + ciderbit.translate('Bad server response.') + '</h3>'));
						ciderbit.dialogNotify(dialog, ciderbit.translate('Fatal error'));
					}
					else if (ciderbitResponse.type == "ERROR") {
						dialog = $("<div></div>");
						dialog.append(ciderbitResponse.content);
						ciderbit.dialogNotify(dialog, ciderbitResponse.title, 400);
					}
					else {
						// Reload every panel
						$('div.system-panel.' + formName).each(function() {
							var id = $(this).attr('id');
							$(this).children().remove();
							$(this).append(
								$(id, $(data)).children()
							);
						});
						ciderbit.init(ciderbitResponse.javascript);
					}
				}
			});
		},

		"reloadComponents": function() {
			$("form.system-panel-form").each(function() {
				$(this).submit();
			});
		},

		"_setSort": function(options) {
			$('.sorts', $("#" + options.formId)).remove();
			$("#" + options.formId).append($('<input type="hidden" class="system-sort" name="' + options.prefix + '_sort[0][path]" value="' + options.path + '"/>'));
			$("#" + options.formId).append($('<input type="hidden" class="system-sort" name="' + options.prefix + '_sort[0][type]" value="' + options.type + '"/>'));
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
				ciderbit._inputFilters[options.ctrlId]["path"] =  $('<input type="hidden" class="system-filter system-filter-path"  name="' + options.prefix + '_filters[' + options.ctrlId + '][path]"  value="' + options.path + '"/>');
				ciderbit._inputFilters[options.ctrlId]["lop"] =   $('<input type="hidden" class="system-filter system-filter-lop"   name="' + options.prefix + '_filters[' + options.ctrlId + '][lop]"   value="AND"/>');
				ciderbit._inputFilters[options.ctrlId]["rop"] =   $('<input type="hidden" class="system-filter system-filter-rop"   name="' + options.prefix + '_filters[' + options.ctrlId + '][rop]"   value="' + options.rop + '"/>');
				ciderbit._inputFilters[options.ctrlId]["value"] = $('<input type="hidden" class="system-filter system-filter-value" name="' + options.prefix + '_filters[' + options.ctrlId + '][value]" value=""/>');

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
				// will reset pager
				$(".system-pager", $("#" + options.formId)).remove();
				$("#" + options.formId).submit();
			}
		},

		"paging": function (formId, page) {
			$(".system-pager", $("#" + formId)).remove();
			$("#" + formId).append($('<input type="hidden" class="system-pager system-pager-page" name="pager[page]" value="' + page + '"/>'));
			$("#" + formId).submit();
		},

		"stdHandler": function(componentResponse, defaults) {
			// prima cosa: svuoto il contenuto del target
			defaults.target.children().remove();
			if (defaults.popup) {
	//			defaults.target.dialog("destroy");
			}

			ciderbitResponse = ciderbit.getResponseObject(componentResponse);

			if (ciderbitResponse.type == undefined) {
				defaults.target.append($('<h3>' + ciderbit.translate('Bad server response.') + '</h3>'));
				ciderbit.dialogNotify(defaults.target, ciderbit.translate('Fatal error'))
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
						$("#" + ciderbitResponse.editFormId).ajaxSubmit({
							url: ciderbitResponse.url,
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
						'dialogClass': "system-dialog",
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
					formControls = $('<div class="system-controls"></div>');
					for (i = 0; i < buttons.length; i++) {

						initClick = function (click) {
							button = $('<button class="system-control">' + buttons[i]['text'] + '</button>');
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
				ciderbit.dialogNotify(dialog, ciderbitResponse.title, 400);

				defaults.onError(ciderbitResponse);
			}
		},

		"request": function(options) {
			var defaults = {
				'url': undefined,
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
				'waitMessagesLabel': ciderbit.translate('Please wait')
			};

			$.extend(defaults, options);

			if (defaults.url == undefined) {
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
				'url': defaults.url,
				'data': defaults.args,
				success: function(componentResponse) {
					ciderbit.stdHandler(componentResponse, defaults);
				}
			});
		}
	}

	ciderbit.init();
//})(jQuery);