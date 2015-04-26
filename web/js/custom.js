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
    }).always(function () {
      $('.pulsarbox .pulsar.' + browser).remove();
      $('table.' + browser).remove();
    });

    invokePulsar(browser);
    getContent(0, url, browser);

    $('.pulsar').on('mouseenter', function () {
      $('.result').css('display', 'block');
    });
    $('.pulsar').on('mouseleave', function () {
      $('.result').css('display', 'none');
    });
  });


  $("a[data-action='run']").click(function(e) {
    e.preventDefault();
    $(this).toggleClass('active')
    $('.nodes').toggleClass('hide');
    $('.nodes').toggleClass('show');
  });

})


function invokePulsar(browser) {
  var content = '<div class="pulsar ' + browser + '"><div class="ring"></div><div class="ring"></div><div class="ring"></div><div class="ring"></div></div>';
  $('.pulsarbox').append(content);
}

function getContent(timestamp, url, browser) {

  var queryString = {timestamp: timestamp, url: url, browser: browser};
  if ($('table.' + browser).length <= 0) {
    $('.result').append('<table class="' + browser + '"/>');
  }

  var server = 'http://' + location.host + '' + '/server.php';

  $.ajax({
    url: server,
    data: queryString
  }).done(function (data) {
    var obj = jQuery.parseJSON(data);
    $('table.' + browser).html(obj.data_from_file);
    getContent(obj.timestamp, url, browser);
  })
}





