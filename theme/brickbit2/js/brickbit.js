brickbit.langHandler = function(lang) {
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

brickbit.setBehavior('collapse-control', function () {
  $('a.collapse-control').each(function() {
    if ($(this).data('target')) {
      if ($(this).hasClass('expanded')) {
        $($(this).data('target')).show();
      } else {
        $($(this).data('target')).hide();
        $(this).css('opacity', 0.2);
      }
    }
  });
  $('a.collapse-control').click(function() {
    if (!$(this).hasClass('expanded')) {
      $($(this).data('target')).show();
      $('a.collapse-control.expanded').each(function() {
        $(this).removeClass('expanded');
        $(this).animate({opacity: 0.25},'slow');
        $($(this).data('target')).hide();
      });
      $(this).addClass('expanded');
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

brickbit.setBehavior('edit-node', function() {
  // Default language
  $('.node-lang-control').click(function() {
    $('input[name="current-lang"]').val($(this).data('lang'));
  });
  $('#node-lang-control-' + $('input[name="current-lang"]').val()).trigger('click');
  
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
      brickbit.langHandler($(this).data('lang'));
    });
    brickbit.langHandler($(this).data('lang'));
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