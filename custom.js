$(document).ready(function () {

  $('import').doconaut({
    menu: {
      appendTo: $('#menu'),
      fixedAt: 380,
      format: true,
      smoothScrolling: true

    },
    appendix: {
      appendTo: $('#appendix')
    }
  });

  $(window).bind('scroll', function () {
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


