$(document).ready(function () {
  $('section div').hide();

  $('h2').click(function() {

    console.log($(this).next('div:hidden').length);
    console.log($(this).next('div'));
    if($(this).next('div:hidden').length == 1) {
      $(this).next('div:hidden').show();
    } else {
      $(this).next('div').hide();
    }
  })


})