$(document).ready(function () {

  importHTML();
  buildNavi();
  //$('section div').hide();
  $('h2').click(function () {
    if ($(this).next('div:hidden').length == 1) {
      $(this).next('div:hidden').show();
    } else {
      $(this).next('div').hide();
    }
  })

  $(window).bind('scroll', function () {
    console.log($(window).scrollTop());
    if ($(window).scrollTop() > 400) {
      $('.menu').addClass('fixed');
    } else {
      $('.menu').removeClass('fixed');
    }
  });


})


function buildNavi() {
  if ($("h2[id]").length > 0) {
  var content = '<div class="menu"><ul>';
  $("h2[id]").each(function() {
    content += '<li>';
    content += '<a href="#'+$(this).attr("id")+'">'+$(this).text()+'</a>';
    content += '</li>';
  });
  content += '<div></ul>';

  $('#menu').append(content);
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
        var foo = $(data).find('#'+link[1]);
        _this.replaceWith($(foo.html()));
      }
    });
  })
}
