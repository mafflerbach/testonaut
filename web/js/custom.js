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
    }).done(function () {
      window.location.reload();
    });
    invokePulsar(browser);
  });

  $("a[data-action='run']").click(function(e) {
    e.preventDefault();
    $(this).toggleClass('active')
    $('.nodes').toggleClass('hide');
    $('.nodes').toggleClass('show');
  });

  $("#runAll").click(function (e) {
    e.preventDefault();
    var index = 0;
    invokePulsar('all');

    $.each( $('.run-test'), function() {
      var href = $(this).attr('href');
      $.ajax({
        method: "get",
        url: href
        })
      .done(function( data) {
        index++;
        if (index == $('.run-test').length) {
          window.location.reload();
        }
      });
    });
  })
})

function invokePulsar(browser) {
  var content = '<div class="pulsar ' + browser + '"><div class="ring"></div><div class="ring"></div><div class="ring"></div><div class="ring"></div></div>';
  $('.pulsarbox').append(content);
}
