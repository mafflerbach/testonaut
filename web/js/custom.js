$(document).ready(function () {

  $('div.box.hide h5').click(function () {
    $(this).parent('div').toggleClass('hide');
    $(this).parent('div').toggleClass('show');
  })

  $('div.box.show h5').click(function () {
    $(this).parent('div').toggleClass('hide');
    $(this).parent('div').toggleClass('show');
  })

  $("form[name='compForm']").submit(function (e) {
    var content;
    content = editor.getValue();
    $("input[name='content']").attr('value', content);
  });

  $('.run-test').click(function (e) {
    e.preventDefault();

    var url = $(this).data('path');
    var run = $(this).attr('href');
    var browser = $(this).data('browser');

    $.ajax({
      method: "GET",
      url: run
    }).always(function (e) {
      $('.pulsarbox .pulsar.' + browser).remove();
      $('table.' + browser).remove();

    }).done(function () {
      window.location.reload();
    });
    poll();
    invokePulsar(browser);
  });

  $("a[data-action='run']").click(function (e) {
    e.preventDefault();
    $(this).toggleClass('active')
    $('.nodes').toggleClass('hide');
    $('.nodes').toggleClass('show');
  });

  $("#runAll").click(function (e) {
    e.preventDefault();
    var index = 0;
    invokePulsar('all');

    $.each($('.run-test'), function () {
      var href = $(this).attr('href');
      $.ajax({
          method: "get",
          url: href
        })
        .done(function (data) {
          index++;
          if (index == $('.run-test').length) {
            window.location.reload();
          }
        });
    });
  })
})


function uploadFrame() {

  var frame = '    <div data-role="dialog" class="upload dialog"  data-close-button="true" data-width="400px">' +
    '<form action="" method="post" enctype="multipart/form-data" id="uploadfileForm">' +
    '<div class="panel">' +
    '<div class="heading">' +
    '<span class="title">Fileupload</span>' +
    '</div>' +
    '<div class="content padding30">' +
    '<div class="flex-grid">' +
    '<div class="row">' +
    '<div class="input-control file full-size" data-role="input">' +
    '<input type="file" name="files" multiple="multiple" id="fileupload"/>' +
    '<button class="button"><span class="mif-folder"></span></button>' +
    '</div>' +
    '</div>' +
    '<div class="row">' +
    '<div class="progress small" data-role="progress"></div>' +
    '</div>' +
    '<div class="row" id="filelist"></div>' +
    '</div>' +
    '</div>' +
    '</div>' +
    '<input type="hidden" value="upload" name="action"/>' +
    '</form>' +
    '</div>';


  $('#editform').append(frame);
  fileupload();
  window.setTimeout(function () {
    var dialog = $('.upload').data('dialog');
    dialog.open();

    $('.dialog-close-button').click(function () {
      $('#fileupload').fileupload('destroy');
      $('#editform').empty();
    })
  }, 500);
}

function fileupload() {

  $('#fileupload').fileupload({
    url: window.location.href,
    dataType: 'json',
    sequentialUploads: true,
    done: function (e, data) {
      var dialog = $('.upload').data('dialog');
      dialog.close();
      $('#editform').empty();
      $.Notify({
        caption: data.result.message,
        content: data.result.file,
        keepOpen: true,
        type: 'success'
      });

    },
    progressall: function (e, data) {
      var progress = parseInt(data.loaded / data.total * 100, 10);
      $('.bar.default').css(
        'width',
        progress + '%'
      );
    }
  })
}


function invokePulsar(browser) {
  var content = '<div class="pulsar ' + browser + '"><div class="ring"></div><div class="ring"></div><div class="ring"></div><div class="ring"></div></div>';
  $('.pulsarbox').append(content);
}


function initEditor() {

  var editor = new wysihtml5.Editor("textarea", {
    toolbar: "toolbar",
    parserRules: wysihtml5ParserRules,
    stylesheets: ["{{ app.request.baseUrl }}/css/style.css"]
  });

  $(document).ready(function () {
    $("a[data-wysihtml5-action='change_view']").click(function () {

      if ($('#textarea:visible').length == 1) {
        $('#textarea').css('background-color', '#fff');
        $('#textarea').css('color', '#000');
        $('#textarea').css('cssText', 'font-family: "Segoe UI","Open Sans",sans-serif,serif !important; color:#000; background-color:#fff;');
      } else {
        $('#textarea').css('cssText', 'font-family: monospace !important; color:#fff; background-color:#000;');
      }
    })

    $("a[data-wysihtml5-command='uploadFile']").click(function () {
      uploadFrame();
    });
    $("a[data-wysihtml5-command='createLink']").click(function () {
      $("div[data-wysihtml5-dialog='createLink']").slideToggle();
    });

    $("a[data-wysihtml5-dialog-action='search']").click(function () {
      $("div[data-wysihtml5-dialog='search']").slideToggle('fast', function () {
        if ($("div[data-wysihtml5-dialog='search']:visible").length == 1) {
          $("a[data-wysihtml5-dialog-action='search'] button").css('display', 'block');
        } else {
          $("a[data-wysihtml5-dialog-action='search'] button").css('display', 'none');
        }
      });

      searchFileEvent();
    });
    $("a[data-wysihtml5-dialog-action='searchImage']").click(function () {
      $("div[data-wysihtml5-dialog='searchImage']").slideToggle('fast', function () {
        if ($("div[data-wysihtml5-dialog='searchImage']:visible").length == 1) {
          $("a[data-wysihtml5-dialog-action='searchImage'] button").css('display', 'block');
        } else {
          $("a[data-wysihtml5-dialog-action='searchImage'] button").css('display', 'none');
        }
      });
      searchImageEvent();
    });


    $("a[data-wysihtml5-dialog-action='save']").click(function () {
      $("div[data-wysihtml5-dialog='searchImage']").css('display', 'none');
      $("div[data-wysihtml5-dialog='search']").css('display', 'none');
      $("a[data-wysihtml5-dialog-action='searchImage'] button").css('display', 'none');
      $("a[data-wysihtml5-dialog-action='search'] button").css('display', 'none');
    })

    function searchFileEvent() {

      $("div[data-wysihtml5-dialog='search'] a").click(function (e) {
        e.preventDefault();
        var link = $(this).attr('href');
        $("input[data-wysihtml5-dialog-field='href']").val(link);
        $("div[data-wysihtml5-dialog='search']").slideToggle();
      })
    }

    function searchImageEvent() {
      $("div[data-wysihtml5-dialog='searchImage'] a").click(function (e) {
        e.preventDefault();
        var link = $(this).attr('href');
        $("input[data-wysihtml5-dialog-field='src']").val(link);
        $("div[data-wysihtml5-dialog='searchImage']").slideToggle();
      })
    }


//type: image filename: tum*
    $("input[name='search']").keyup(function () {
      if ($(this).val().length >= 3) {

        var server = baseUrl + 'search/image/' + $(this).val();

        $.ajax({
          url: server,
          data: {
            'type': 'image',
            'term': $(this).val()
          },
          dataType: 'json',
          method: 'post'
        }).done(function (data) {

          $('#listFile').empty();

          var content = '<div class="listview">';

          for (var i = 0; i < data.length; i++) {
            var path = data[i].path.replace('/web', '');
            var link = baseUrl + path + '/' + data[i].filename;
            link = link.replace('//', '/');
            link = link.replace('\\', '/');

            var cssclass = 'mif-file-empty';
            var type = data[i].type;

            if (type.indexOf('image') > -1) {
              cssclass = 'mif-file-image';
            }
            if (type.indexOf('pdf') > -1) {
              cssclass = 'mif-file-pdf';
            }

            content += '<div class="list">' +
              '<span class="' + cssclass + ' mif-2x"></span>' +
              '<span class="list-title"><span class="filename" data-url="' + link + '">' + data[i].filename + '</span></span>' +
              '</div>';
          }

          content += '</div>';

          $('#listFile').append(content);

          $('.list').click(function () {

            var url = $($(this).find('.filename')[0]).data('url');
            $("div[data-wysihtml5-dialog='createLink'] input").val(url);
          });

          searchImageEvent();
        })
      }
    });

    $("input[name='searchImage']").keyup(function () {
      if ($(this).val().length >= 3) {

        var server = baseUrl + 'search/image/' + $(this).val();

        $.ajax({
          url: server,
          data: {
            'type': 'image',
            'term': $(this).val()
          },
          dataType: 'json',
          method: 'post'
        }).done(function (data) {

          $('#listimage').empty();
          for (var i = 0; i < data.length; i++) {
            var path = data[i].path.replace('/web', '');
            var link = "'" + baseUrl + path + '/' + data[i].filename + "'";
            link = link.replace('//', '/');
            link = link.replace('\\', '/');
            var content =
              '<div class="tile fg-white text-shadow" data-role="tile">' +
              '<div class="tile-content zooming-out">' +
              '<div class="slide">' +
              '<div class="image-container image-format-square" style="width: 100%;">' +
              '<div class="frame">' +
              '<div style="width: 100%; height: 150px; border-radius: 0px; background-image: url(' + link + '); background-size: cover; background-repeat: no-repeat;"></div>' +
              '</div>' +
              '</div>' +
              '</div>' +
              '<div class="tile-label">' + data[i].filename + '</div>' +
              '</div>' +
              '</div>';
            $('#listimage').append(content);
          }

          $('.frame div').click(function () {
            var url = $(this).css('background-image');
            url = url.replace('url("', '');
            url = url.replace('")', '');
            $("div[data-wysihtml5-dialog='insertImage'] input").val(url);
          });

          searchImageEvent();
        })
      }
    });
  });
}

function initConfig() {
  $('input[type="checkbox"]').click(function () {
    if ($(this).prop("checked") == false) {
      console.log($(this).parent().parent().find('input[type="text"]').val(''))
    }
  });
}

function initCompare() {

  $('a[data-revert]').click(function (e) {
    e.preventDefault();
    var href = $(this).attr('href');
    $.ajax({
      method: "get",
      url: href
    }).done(function (data) {
      $.Notify({
        caption: 'Checkout',
        content: data,
        type: 'success'
      });
    });
  })


  $('a[data-compare]').click(function (e) {
    e.preventDefault();
    $(this).toggleClass('inactive');
    $(this).toggleClass('active');


    if ($('a[data-compare].active').length > 2 || $('a[data-compare].active').length < 2) {
      $('#compareoutput').empty();
    }

    if ($('a[data-compare].active').length == 2) {

      var rev1 = $($('a[data-compare].active').get(0)).data('compare');
      var rev2 = $($('a[data-compare].active').get(1)).data('compare');
      var href = baseUrl + "history/" + path + "/compare/" + rev2 + "/" + rev1;

      $.ajax({
        method: "get",
        url: href,
        dataType: 'json'

      }).done(function (data) {
        console.log(data);
        $('#dialog-content').empty().append(data[0].content);
        var dialog = $('#dialog').data('dialog');
        dialog.open();
      });
    }

    $('#myModal').on('hidden.bs.modal', function (e) {
      $('a[data-compare].active').toggleClass('active');
    })
  })

}


function initScreenshots() {
  function closeDialog() {
    $('.window').hide()
    $('.window-caption-title').text('');
    $('.window-content .message').text('');
  }

  function openDialog(messageTitle, message, href) {
    $('.window-caption-title').text(messageTitle);
    $('.window-content .message').text(message);

    $('.window').center();
    $('.window').show();

    $('.btn-close, .cancel').click(function () {
      closeDialog();
      $(this).unbind('click');
    })

    $('.button.ok').click(function () {
      closeDialog();
      $(this).unbind('click');
      console.log(href);
      handelAjax(href);
    })
  }


  function handelAjax(href) {
    $.ajax({
      method: "get",
      url: href,
      dataType: 'json'
    }).done(function (data) {
      var type = 'success';
      if (data.result != 'success') {
        type = 'alert';
      }

      $.Notify({
        caption: data.messageTitle,
        content: data.message,
        type: type
      });

      window.setTimeout(function () {
        location.reload()
      }, 1500);
    });
  }


  $('.copyImage').click(function (e) {
    e.preventDefault();
    var href = $(this).attr('href');
    openDialog('Copy', 'Are you sure you want to set a new reference image?', href);
  });

  $('.deleteImage').click(function (e) {
    e.preventDefault();
    var href = $(this).attr('href');
    openDialog('Delete', 'Are you sure you want to delete the image?', href);
  });

  $('#all').click(function () {
    $('.button').removeClass('active');
    $(this).addClass('active');
    $('.imagePanel').show();
  });
  $('#success').click(function () {
    $('.button').removeClass('active');
    $(this).addClass('active');
    $('.imagePanel.success').show();
    $('.imagePanel:not(.success)').hide();
  });
  $('#fail').click(function () {
    $('.button').removeClass('active');
    $(this).addClass('active');
    $('.imagePanel.alert').show();
    $('.imagePanel:not(.alert)').hide();
  });
}

function initGlobalconfig() {

  $('#addProfile-form form').submit(function (e) {
    e.preventDefault();

    if ($('#profileName').val() == '') {
      $('#profileName').parent().addClass('has-error');
    } else {
      $('#profileName').parent().removeClass('has-error');
    }

    if ($('#browsers').val() == '') {
      $('#browsers').parent().addClass('has-error');
    } else {
      $('#browsers').parent().removeClass('has-error');
    }

    if ($('#addProfile-form .has-error').length == 0) {
      $(this).unbind('submit').submit()
    }

  });


  $('#addProfile').click(function () {
    $('#addProfile-form').slideDown();
  });

  $('#browsers').change(function () {
    val = $('#browsers').val();
    
    if (val.indexOf('chrome') > -1) {
      $('#devices').show();
      $('#dimension').show();
    } else {
      $('#dimension').show();
      $('#devices').hide();
    }

  });

  $('#devices select').change(function () {
    $('#dimension').hide();
    if ($('#devices select').val() == '') {
      $('#dimension').show();
    }
  });

  $('#width').blur(function () {
    $('#devices').hide();
    if ($('#width').val() == '' && $('#height').val() == "") {
      $('#devices').show();
    }
  });

  $('#height').blur(function () {
    $('#devices').hide();
    if ($('#width').val() == '' && $('#height').val() == "") {
      $('#devices').show();
    }
  });

  $("a[data-action='delete']").click(function (e) {
    e.preventDefault();
    var href = $(this).attr('href');
    modalHandling(href, "Delete Profile", '');
  });

  $("a[data-action='edit']").click(function (e) {
    e.preventDefault();
    var href = $(this).attr('href');
    $('#addProfile-form').slideDown();

    $.ajax({
      method: "get",
      url: href
    }).done(function (data) {
      console.log(data);
      $('#profileName').val(data.name);

      if (data.driverOptions.dimensions != undefined) {
        $('#dimension').show();
        $('#devices').hide();
        $('#width').val(data.driverOptions.dimensions.width);
        $('#height').val(data.driverOptions.dimensions.height);
      }
      $('#browsers').val(data.browser);
      if (data.capabilities.experimental != undefined) {
        $('#dimension').hide();
        $('#devices').show();
        $('#device').val(data.capabilities.experimental.mobileEmulation.deviceName);
      }
    });
  });


}


jQuery.fn.center = function () {
  this.css("position", "absolute");
  this.css("top", Math.max(0, (($(window).height() - $(this).outerHeight()) / 2) +
      $(window).scrollTop()) + "px");
  this.css("left", Math.max(0, (($(window).width() - $(this).outerWidth()) / 2) +
      $(window).scrollLeft()) + "px");
  return this;
}