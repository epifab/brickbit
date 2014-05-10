//(function ($) {
  var brickbit = {
    '_data': {},
    
    'data': function(key, value) {
      if (value === undefined) {
        // get data
        if (key in brickbit._data) {
          return brickbit._data[key];
        }
        else {
          return null;
        }
      }
      else {
        // set data
        brickbit._data[key] = value;
      }
    },
    
    'translate': function(sentence, args) {
      return sentence;
    },

    '_behaviors': {},

    'setBehavior': function(k, f) {
      brickbit._behaviors[k] = f;
    },
    
    'init': function(javascript) {
      $(document).ready(function() {
        var node = $(document);
        brickbit.waitDialogInit();

        $('.system-block-form', node).each(function() {
          formId = $(this).attr('id');
          formName = $(this).attr('name');
          brickbit.addComponentForm(formId, formName);
        });

        for (x in brickbit._behaviors) {
          brickbit._behaviors[x]();
        }

        if (javascript !== undefined) {
          jQuery.globalEval(javascript);
        }
      });
    },

    'getResponseObject': function(componentResponse) {
  //    var responseCustomArgs = new Array();
  //    $('customargs customarg', componentResponse).each(function() {
  //      if ($(this).attr('value') != undefined) {
  //        responseCustomArgs[$(this).attr('name')] = $(this).attr('value');
  //      } else {
  //        responseCustomArgs[$(this).attr['name']] = $(this).contents();
  //      }
  //    });

      var brickbitResponse = {
        // Contenuto della risposta AJAX
        'content': $('content', $(componentResponse)).contents(),

        // Tipo di risposta: FORM, NOTIFY, ERROR, READ
        'type': $(componentResponse).attr('type'),

        // Identificativo univoco della risposta
        'id': $(componentResponse).attr('id'),

        'editFormId': $(componentResponse).attr('editFormId'),

        // Indirizzo del componente
        'url': $(componentResponse).attr('url'),
        // Titolo
        'title': $(componentResponse).attr('title'),
        // Argomento di ritorno
  //      'customArgs': responseCustomArgs,
        // Codice javascript da eseguire al caricamento
        'javascript': $('javascript', $(componentResponse)).text()
      };

      return brickbitResponse;
    },

  //  'waitDialog': function() {
  //    return $('<div><h4>' + brickbit.translate('Please wait') + '</h4></div>');
  //  },

    'waitDialog': $('<div><h4>Please wait</h4></div>'),

    'waitDialogInit': function() {
      brickbit.waitDialog.dialog({
        'dialogClass': 'system-dialog system-wait-dialog',
        'position': 'center',
        'title': brickbit.translate('Please wait'),
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

    'waitDialogShow': function() {
      brickbit.waitDialog.dialog('open');
    },

    'waitDialogHide': function() {
      brickbit.waitDialog.dialog('close');
    },

    'confirm': function(title, question, options) {
      width = 400;

      if (question == undefined || question == '') {
        question = brickbit.translate('Are you sure?');
      }

      target = $('<div><h2>' + title + '</h2><p class="alert">' + question + '</p></div>');

      okLabel = brickbit.translate('Ok');
      cancelLabel = brickbit.translate('Cancel');

      buttons = {};
      buttons[brickbit.translate('Ok')] = function() {
        target.dialog('close');
        brickbit.request(options);
      }
      buttons[brickbit.translate('Cancel')] = function() {
        target.dialog('close');
      }

      target.dialog({
        'dialogClass': 'system-dialog untitled',
        'title': title,
        'position': 'center',
        'modal': true,
        'autoOpen': false,
        'width': width,
        'onClose': function() {
          target.dialog('destroy');
          target.remove();
          if (onclose != undefined) {
            onclose();
          }
        },
        'buttons': buttons
      });

      target.dialog('open');
    },

    'dialogNotify': function(target, title, width, onclose) {
      if (width == undefined) {
        width = 600;
      }
      if (title == undefined) {
        cl = 'system-dialog untitled';
      } else {
        cl = 'system-dialog';
      }


      buttons = {};
      buttons[brickbit.translate('Ok')] = function() {
        target.dialog('close');
      }

      target.dialog({
        'dialogClass': cl,
        'position': 'center',
        'title': title,
        'modal': true,
        'autoOpen': false,
        'width': width,
        'onClose': function() {
          target.dialog('destroy');
          target.remove();
          if (onclose != undefined) {
            onclose();
          }
        },
        'buttons': buttons
      });

      target.dialog('open');
    },

    'addComponentForm': function(formId, formName) {
      $('#' + formId).ajaxForm({
        success: function(data) {
          brickbitResponse = brickbit.getResponseObject(data);

          if (brickbitResponse.type == undefined) {
            dialog = $('<div></div>');
            dialog.append($('<h3>' + brickbit.translate('Bad server response.') + '</h3>'));
            brickbit.dialogNotify(dialog, brickbit.translate('Oops! Something went wrong'));
          }
          else if (brickbitResponse.type == 'ERROR') {
            dialog = $('<div></div>');
            dialog.append(brickbitResponse.content);
            brickbit.dialogNotify(dialog, brickbitResponse.title);
          }
          else {
            $('#' + formName).children().remove();
            $('#' + formName).append(brickbitResponse.content);
            brickbit.init(brickbitResponse.javascript);
          }
        }
      });
    },

    'reloadComponents': function() {
      $('form.system-block-form').each(function() {
        $(this).submit();
      });
    },

    '_setSort': function(options) {
      $('.sorts', $('#' + options.formId)).remove();
      $('#' + options.formId).append($('<input type="hidden" class="system-sort" name="' + options.prefix + '_sort[0][path]" value="' + options.path + '"/>'));
      $('#' + options.formId).append($('<input type="hidden" class="system-sort" name="' + options.prefix + '_sort[0][type]" value="' + options.type + '"/>'));
    },

    'sort': function(options) {
      if (options.prefix == undefined) {
        options.prefix = '';
      }
      brickbit._setSort(options);
      $('#' + options.formId).submit();
    },

    '_inputFilters': new Array(),

    '_setFilter': function(options) {
      if (brickbit._inputFilters[options.ctrlId] == undefined) {
        brickbit._inputFilters[options.ctrlId] = new Array();
        brickbit._inputFilters[options.ctrlId]['path'] =  $('<input type="hidden" class="system-filter system-filter-path"  name="' + options.prefix + '_filters[' + options.ctrlId + '][path]"  value="' + options.path + '"/>');
        brickbit._inputFilters[options.ctrlId]['lop'] =   $('<input type="hidden" class="system-filter system-filter-lop"   name="' + options.prefix + '_filters[' + options.ctrlId + '][lop]"   value="AND"/>');
        brickbit._inputFilters[options.ctrlId]['rop'] =   $('<input type="hidden" class="system-filter system-filter-rop"   name="' + options.prefix + '_filters[' + options.ctrlId + '][rop]"   value="' + options.rop + '"/>');
        brickbit._inputFilters[options.ctrlId]['value'] = $('<input type="hidden" class="system-filter system-filter-value" name="' + options.prefix + '_filters[' + options.ctrlId + '][value]" value=""/>');

        $('#' + options.formId).append(brickbit._inputFilters[options.ctrlId]['path']);
        $('#' + options.formId).append(brickbit._inputFilters[options.ctrlId]['lop']);
        $('#' + options.formId).append(brickbit._inputFilters[options.ctrlId]['rop']);
        $('#' + options.formId).append(brickbit._inputFilters[options.ctrlId]['value']);
      }
      if (brickbit._inputFilters[options.ctrlId]['value'].val() == $('#' + options.ctrlId).val()) {
        return false;
      } else {
        brickbit._inputFilters[options.ctrlId]['value'].val($('#' + options.ctrlId).val());
        return true;
      }
    },

    '_unsetFilter': function(ctrlId) {
      if (brickbit._inputFilters[ctrlId] != undefined) {
        brickbit._inputFilters[ctrlId]['path'].remove();
        brickbit._inputFilters[ctrlId]['lop'].remove();
        brickbit._inputFilters[ctrlId]['rop'].remove();
        brickbit._inputFilters[ctrlId]['value'].remove();
        delete brickbit._inputFilters[ctrlId];
        return true;
      }
      return false;
    },

    'filter': function(options) {
      // {formId, path, rop, ctrlId, prefix}
      if (options.prefix == undefined) {
        options.prefix = '';
      }
      var reload = false;
      if ($('#' + options.ctrlId).val() == '') {
        brickbit._unsetFilter(options.ctrlId);
        reload = true;
      } else {
        reload = brickbit._setFilter(options);
      }
      if (reload) {
        // will reset pager
        $('.system-pager', $('#' + options.formId)).remove();
        $('#' + options.formId).submit();
      }
    },

    'paging': function (formId, page) {
      $('.system-pager', $('#' + formId)).remove();
      $('#' + formId).append($('<input type="hidden" class="system-pager system-pager-page" name="pager[page]" value="' + page + '"/>'));
      $('#' + formId).submit();
    },

    'stdHandler': function(componentResponse, defaults) {
      // prima cosa: svuoto il contenuto del target
      defaults.target.children().remove();
      if (defaults.popup) {
  //      defaults.target.dialog('destroy');
      }

      brickbitResponse = brickbit.getResponseObject(componentResponse);

      if (brickbitResponse.type == undefined) {
        defaults.target.append($('<h3>' + brickbit.translate('Bad server response.') + '</h3>'));
        brickbit.dialogNotify(defaults.target, brickbit.translate('Fatal error'))
      }

      else if (brickbitResponse.type == 'READ') {

        defaults.target.append(brickbitResponse.content);

        if (defaults.popup) {
          brickbit.dialogNotify(defaults.target, brickbitResponse.title, defaults.width);
        }

        brickbit.init(brickbitResponse.javascript);

        defaults.onRead(brickbitResponse);

      }

      else if (brickbitResponse.type == 'FORM') {

        defaults.target.append(brickbitResponse.content);

        // La risposta del componente è un form
        buttons = new Array();

        if (defaults.okButton) {
          okButtonStdHandler = function() {
            var sent = 0;
            if (defaults.waitMessages) {
              brickbit.waitDialogShow();
            }
            $('#' + brickbitResponse.editFormId).ajaxSubmit({
              url: brickbitResponse.url,
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
                  brickbit.waitDialogHide();
                }
                brickbit.stdHandler(newComponentResponse, defaults);
              }
            });
          }
          clickHandler = function() {defaults.okButtonOnClick(okButtonStdHandler);};
          buttons.push({'text': defaults.okButtonLabel, 'click': clickHandler});
        }

        if (defaults.koButton) {
          koButtonStdHandler = function() {
            if (defaults.popup) {
              defaults.target.dialog('close');
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
            'dialogClass': 'system-dialog',
            'position': 'center',
            'title': brickbitResponse.title,
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

          defaults.target.dialog('open');
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

        brickbit.init(brickbitResponse.javascript);
        defaults.onForm(brickbitResponse);
      }

      else if (brickbitResponse.type == 'NOTIFY') {
        if (defaults.popup) {
          defaults.target.remove();
        } else {
          defaults.target.children().remove();
        }

        brickbit.reloadComponents();

        if (defaults.showResponse) {
          dialog = $('<div></div>');
          dialog.append(brickbitResponse.content);
          brickbit.dialogNotify(dialog, brickbitResponse.title);
          setTimeout(function() {dialog.dialog('close');}, 2000);
        }

        defaults.onSuccess(brickbitResponse);
      }

      else if (brickbitResponse.type == 'ERROR') {
        if (defaults.popup) {
          defaults.target.remove();
        } else {
          defaults.target.children().remove();
        }

        dialog = $('<div></div>');
        dialog.append(brickbitResponse.content);
        brickbit.dialogNotify(dialog, brickbitResponse.title);

        defaults.onError(brickbitResponse);
      }
    },

    'request': function(options) {
      var defaults = {
        'url': undefined,
        'args': null,
        'width': 600,
        'height': 400,
        'maxHeight': 800,
        'maxWidth': 1000,

        'target': null,
        'popup': true,

        'onForm': function(brickbitResponse) {},
        'onRead': function(brickbitResponse) {},
        'onSuccess': function(brickbitResponse) {},
        'onError': function(brickbitResponse) {},

        'okButton': true,
        'okButtonLabel': 'Save',
        'okButtonOnClick': function(stdHandler) {stdHandler();},

        'koButton': true,
        'koButtonLabel': 'Cancel',
        'koButtonOnClick': function(stdHandler) {stdHandler();},

        'showResponse': true,

  //      'customControls': null,

        'waitMessages': true,
        'waitMessagesLabel': brickbit.translate('Please wait')
      };

      $.extend(defaults, options);

      if (defaults.url == undefined) {
        return;
      }
      if (defaults.target == null) {
        // creo un nuovo elemento
        defaults.target = $('<div></div>');
      } else {
        defaults.target = $('#' + defaults.target);
      }

      $.ajax({
        'dataType': 'html',
        'url': defaults.url,
        'data': defaults.args,
        success: function(componentResponse) {
          brickbit.stdHandler(componentResponse, defaults);
        }
      });
    }
  }

  brickbit.init();
//})(jQuery);