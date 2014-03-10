ciderbit.langHandler = function(lang) {
  input = $('input#node-lang-' + lang + '-enable');
  enableBtn = $('#node-lang-' + lang + '-enable-btn');
  disableBtn = $('#node-lang-' + lang + '-disable-btn');
  textInputGroup = $('.node-lang-' + lang + '-group');

  if (input.is(':checked')) {
    enableBtn.removeClass('btn-disabled');
    disableBtn.addClass('btn-disabled');
    textInputGroup.show();
  }
  else {
    enableBtn.addClass('btn-disabled');
    disableBtn.removeClass('btn-disabled');
    textInputGroup.hide();
  }
};

ciderbit.setBehavior('edit-node', function() {
  $('.node-lang-control').click(function() {
    $('input[name="current-lang"]').val($(this).data('lang'));
  });
  $('input[name="current-lang"]').val();
  $('.node-lang-enable-disable-btn').click(function() {
    input = $('input#node-lang-' + $(this).data('lang') + '-enable');
    action = $(this).data('action');

    if (action == 'enable') {
      input.prop('checked', true);
    }
    else {
      input.prop('checked', false);
    }
    input.trigger('change');
  });
  
  $('input.node-lang-enable').each(function() {
    $(this).change(function() {
      ciderbit.langHandler($(this).data('lang'));
    });
    ciderbit.langHandler($(this).data('lang'));
  });
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
//  $('.show-hide-class').each(function() {
//    if ($(this).hasClass('expanded')) {
//      $('.' + $(this).attr('id')).show();
//    } else {
//      $('.' + $(this).attr('id')).hide();
//    }
//  });
//  $('.show-hide-class').click(function() {
//    if ($(this).hasClass('expanded')) {
//      $('.' + $(this).attr('id')).hide();
//    } else {
//      $('.' + $(this).attr('id')).show();
//    }
//    $(this).toggleClass('expanded');
//    return false;
//  })
//});