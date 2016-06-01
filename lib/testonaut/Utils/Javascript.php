<?php
/**
 * Created by PhpStorm.
 * User: maren
 * Date: 06.04.2016
 * Time: 09:45
 */

namespace testonaut\Utils;


class Javascript {

  private $webDriver;


  /**
   * Javascript constructor.
   * @param $webDriver
   */
  public function __construct($webDriver) {
    $this->webDriver = $webDriver;

  }

  /**
   *
   */
  public function invokeHtml2Canvas() {

    $js = " 
      setTimeout(function () {
          var d = document;
          var script = d.createElement('script');
          script.type = 'text/javascript';
          script.src = 'https://" . $_SERVER['SERVER_NAME'] . "/testonaut/html2canvas.js';
          d.getElementsByTagName('head')[0].appendChild(script);
          console.log('invoke canvas' + script.src);
      }, 1000);
     ";

    $this->webDriver->executeScript($js, array());

    return $this->webDriver;
  }


  public function invokeNanoajax() {
    $js = "
      setTimeout(function () {
          var d = document;
          var script = d.createElement('script');
          script.type = 'text/javascript';
          script.src = 'https://" . $_SERVER['SERVER_NAME'] . "/testonaut/qwest.min.js';
          d.getElementsByTagName('head')[0].appendChild(script);
          console.log('invoke nano');
      }, 1000);
    ";
    $this->webDriver->executeScript($js, array());
    return $this->webDriver;
  }

  /**
   * @param $srcImage
   */
  public function invokeTakeScreenshot($srcImage) {
    if (DIRECTORY_SEPARATOR == '\\') {
      $srcImage = str_replace('\\', '\\\\', $srcImage);
      $srcImage = str_replace('/', '\\\\', $srcImage);
    } else {
      $srcImage = str_replace(DIRECTORY_SEPARATOR, DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR, $srcImage);
    }

    $js = "
      setTimeout(function () {
        html2canvas(document.html, {
          onrendered: function(canvas) {
          qwest.post('https://" . $_SERVER['SERVER_NAME'] . "/testonaut/server.php', {
              canvas: canvas.toDataURL('image/png'),
              path:'" . $srcImage . "'
              }).then(function(xhr, response) {})
          }
        })
      }, 1500);";

    $this->webDriver->executeScript($js, array());
    sleep(5);
    return $this->webDriver;
  }

  public function setPixelRatio($ratio){

    $js = '
      console.log(window.devicePixelRatio='.$ratio.')  
    ';
    $this->webDriver->executeScript($js, array());
    return $this->webDriver;
  }

}