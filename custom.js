$(document).ready(function () {
  importHTML();
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
  window.setTimeout(function () {
    $('body').fadeIn('slow');
  }, 500);
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

    $('a[href^="#"]').on('click', function (e) {
      e.preventDefault();
      var target = this.hash;
      var $target = $(target);
      selector = "*[id='" + this.hash.replace('#', '') + "']";
      $('html, body').stop().animate({
        'scrollTop': $target.offset().top
      }, 400, 'swing', function () {
        window.location.hash = target;
      });
    });

  }
}

function importHTML() {
  var deferreds = [];
  $('import').each(function () {
    var href = $(this).attr('href');
    var link = href.split('#');
    var _this = $(this);

    var mee = {
      "link": link,
      "obj": _this
    };
    /**
     * WTF: You can't use a multidimensional array for response, because you don't get the fucking responseText from the ajax return.
     */
    deferreds.push(
      $.get(link[0])
    )
    deferreds.push(
      mee
    )
  });
  $.when.apply($, deferreds).then(function () {
    for (var i = 0; i < deferreds.length; i = i + 2) {
      var content = $(deferreds[i].responseText).find('#' + deferreds[i + 1].link[1]);
      deferreds[i + 1].obj.replaceWith($(content.html()));
    }
    buildNavi();
  })

}
