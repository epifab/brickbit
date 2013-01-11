$(document).ready(function() {
	$('.show-hide-class').each(function() {
		if ($(this).hasClass('expanded')) {
			$('.' + $(this).attr('id')).show();
		} else {
			$('.' + $(this).attr('id')).hide();
			$(this).css('opacity', 0.2);
		}
	});
	$('.show-hide-class').click(function() {
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
	})
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