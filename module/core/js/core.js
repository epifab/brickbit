ciderbit.setBehavior('core-file-upload', function () {
	'use strict';

	// Initialize the jQuery File Upload widget:
	$('#fileupload').fileupload({
		// Uncomment the following to send cross-domain cookies:
		//xhrFields: {withCredentials: true},
		url: '/file/upload'
	});

	// Enable iframe cross-domain access via redirect option:
	$('#fileupload').fileupload(
		'option',
		'redirect',
		'/file/upload'
		
//		window.location.href.replace(
//			/\/[^\/]*$/,
//			'/cors/result.html?%s'
//		)
	);


	// Load existing files:
	$.ajax({
		// Uncomment the following to send cross-domain cookies:
		//xhrFields: {withCredentials: true},
		url: $('#fileupload').fileupload('option', 'url'),
		dataType: 'json',
		context: $('#fileupload')[0]
	}).done(function (result) {
		$(this).fileupload('option', 'done').call(this, null, {
			result: result
		});
	});

	// Initialize the Image Gallery widget:
	$('#fileupload .files').imagegallery();
});

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