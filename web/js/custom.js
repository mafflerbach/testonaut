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

function modalHandling(href, modalTitle, ending) {

  $.ajax({
    method: "get",
    url: href
  }).done(function (data) {
    $('.modal-title').empty().append(modalTitle);
    $('.modal-body').empty().append($(data).find('.content'));
    $('#myModal').modal('show');

    $('.backLink').click(function (ev) {
      ev.preventDefault();
      $('#myModal').modal('hide');
    });

    $('.btn-action').click(function (ev) {
      ev.preventDefault();
      $.ajax({
        method: "post",
        url: href + ending
      }).done(function (data) {
        $('.modal-body').empty().append($(data).find('.content'));
      });
    });
  });
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
    $("a[data-wysihtml5-command='uploadFile']").click(function () {
      uploadFrame();
    });
    $("a[data-wysihtml5-command='createLink']").click(function () {
      $("div[data-wysihtml5-dialog='createLink']").slideToggle();
    });

    $("a[data-wysihtml5-dialog-action='search']").click(function () {
      $("div[data-wysihtml5-dialog='search']").slideToggle();
      searchFileEvent();
    });
    $("a[data-wysihtml5-dialog-action='searchImage']").click(function () {
      $("div[data-wysihtml5-dialog='searchImage']").slideToggle();

      searchImageEvent();
    });

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
        var server = location.href.replace('/web/edit/', '/web/files/') + 'search/' + $(this).val();
        $.ajax({
          url: server
        }).done(function (data) {

          $('#fileListing').empty();

          for (var i = 0; i < data.length; i++) {
            var filename = data[i].filename;
            var path = data[i].path.replace('web/', '');
            var link = '{{ app.request.baseUrl }}' + path + '/' + filename;
            var content = '<a href="' + link + '">' + filename + '</a><br/>'
            $('#fileListing').append(content);
          }
          searchFileEvent();
        })
      }
    });

    $("input[name='searchImage']").keyup(function () {
      if ($(this).val().length >= 3) {
        var term = 'type:image filename: ' + $(this).val();

        var server = location.href.replace('/web/edit/', '/web/files/') + 'search/' + term;
        $.ajax({
          url: server
        }).done(function (data) {

          $('#imageListing').empty();

          for (var i = 0; i < data.length; i++) {
            var filename = data[i].filename;
            var path = data[i].path.replace('web/', '');
            var link = '{{ app.request.baseUrl }}' + path + '/' + filename;
            var content = '<a href="' + link + '">' + filename + '</a><br/>'
            $('#imageListing').append(content);
          }
          searchImageEvent();
        })
      }
    });
  });


}