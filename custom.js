$(document).ready(function () {

  importHTML();
  buildNavi();

  $('h2').click(function () {
    if ($(this).next('div:hidden').length == 1) {
      $(this).next('div:hidden').show();
    } else {
      $(this).next('div').hide();
    }
  });

  $(window).bind('scroll', function () {
    if ($(window).scrollTop() > 400) {
      $('.menu').addClass('fixed');
    } else {
      $('.menu').removeClass('fixed');
    }

    if ($(window).scrollTop() > 400) {
      $('.jumper').fadeIn();
    } else {
      $('.jumper').fadeOut();
    }
  });

  window.setTimeout(function() {
    $('body').fadeIn();
  }, 300);
})


function buildNavi() {
  if ($("h2[id]").length > 0) {
    var content = '<div class="menu"><ul>';
    $("h2[id]").each(function () {
      content += '<li>';
      content += '<a href="#' + $(this).attr("id") + '">' + $(this).text() + '</a>';
      content += '</li>';
    });
    content += '<div></ul>';

    $('#menu').append(content);

    $('a[href^="#"]').on('click',function (e) {
      e.preventDefault();

      var target = this.hash;
      var $target = $(target);

      selector = "*[id='"+this.hash.replace('#', '')+"']";

      $('html, body').stop().animate({
        'scrollTop': $target.offset().top
      }, 400, 'swing', function () {
        window.location.hash = target;
      });
    });

  }
}

function importHTML() {
  $('import').each(function () {
    var href = $(this).attr('href');
    var link = href.split('#');
    var _this = $(this);
    $.ajax({
      type: "GET",
      url: link[0],
      dataType: 'html',
      success: function (data) {
        var foo = $(data).find('#' + link[1]);
        _this.replaceWith($(foo.html()));
      }
    });
  })
}
