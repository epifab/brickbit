ciderbit.setBehavior('core-buttons', function () {
	$('input:button .button').each(function(id,el) {
		var text = false;
		var icon = "ui-icon-";
		
		if ($(el).hasClass("read") || $(el).hasClass("search")) {
			icon += "search";
		} else if ($(el).hasClass("create") || $(el).hasClass("document")) {
			icon += "document";
		} else if ($(el).hasClass("update")) {
			icon += "wrench";
		} else if ($(el).hasClass("delete") || $(el).hasClass("trash")) {
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
});

//ciderbit.setBehavior('core-file-upload', function () {
//	'use strict';
//
//	// Initialize the jQuery File Upload widget:
//	$('#fileupload').fileupload({
//		// Uncomment the following to send cross-domain cookies:
//		//xhrFields: {withCredentials: true},
//		url: '/file/upload'
//	});
//
//	// Enable iframe cross-domain access via redirect option:
//	$('#fileupload').fileupload(
//		'option',
//		'redirect',
//		'/file/upload'
//		
////		window.location.href.replace(
////			/\/[^\/]*$/,
////			'/cors/result.html?%s'
////		)
//	);
//
//
//	// Load existing files:
//	$.ajax({
//		// Uncomment the following to send cross-domain cookies:
//		//xhrFields: {withCredentials: true},
//		url: $('#fileupload').fileupload('option', 'url'),
//		dataType: 'json',
//		context: $('#fileupload')[0]
//	}).done(function (result) {
//		$(this).fileupload('option', 'done').call(this, null, {
//			result: result
//		});
//	});
//
//	// Initialize the Image Gallery widget:
//	$('#fileupload .files').imagegallery();
//});

ciderbit.setBehavior('show-hide-class', function () {
	$('a.show-hide-class').each(function() {
		if ($(this).hasClass('expanded')) {
			$('.' + $(this).attr('id')).show();
		} else {
			$('.' + $(this).attr('id')).hide();
			$(this).css('opacity', 0.2);
		}
	});
	$('a.show-hide-class').click(function() {
		if (!$(this).hasClass('expanded')) {
			$('.' + $(this).attr('id')).show();
			$('.show-hide-class.expanded').each(function() {
				$(this).toggleClass('expanded');
				$(this).animate({opacity: 0.25},'slow');
				$('.' + $(this).attr('id')).hide();
			});
			$(this).toggleClass('expanded');
			$(this).animate({opacity: 1},'slow');
		}
		return false;
	});
	
	$('input.show-hide-class').each(function() {
		if ($(this).is(':checked')) {
			$('.' + $(this).attr('id')).show();
		} else {
			$('.' + $(this).attr('id')).hide();
		}
		$(this).click(function() {
			if ($(this).is(':checked')) {
				$('.' + $(this).attr('id')).show();
			} else {
				$('.' + $(this).attr('id')).hide();
			}
		});
	});
});
//$(document).ready(function() {
//	$('.show-hide-class').each(function() {
//		if ($(this).hasClass('expanded')) {
//			$('.' + $(this).attr('id')).show();
//		} else {
//			$('.' + $(this).attr('id')).hide();
//		}
//	});
//	$('.show-hide-class').click(function() {
//		if ($(this).hasClass('expanded')) {
//			$('.' + $(this).attr('id')).hide();
//		} else {
//			$('.' + $(this).attr('id')).show();
//		}
//		$(this).toggleClass('expanded');
//		return false;
//	})
//});