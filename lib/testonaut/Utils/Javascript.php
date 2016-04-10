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
          script.src = 'https://" . $_SERVER['SERVER_ADDR'] . "/testonaut/html2canvas.js';
          d.getElementsByTagName('head')[0].appendChild(script);
    
      }, 1000);
     ";

    $this->webDriver->executeScript($js, array());

    return $this->webDriver;


  }


  /**
   * @TODO replace jquery with nanoajax
   */
  public function invokeNanoajax() {
    $js = "
      setTimeout(function () {
          var d = document;
          var script = d.createElement('script');
          script.type = 'text/javascript';
          script.src = 'https://ajax.googleapis.com/ajax/libs/jquery/2.2.2/jquery.min.js';
          d.getElementsByTagName('head')[0].appendChild(script);
          
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
            $.ajax({
                method: 'POST',
                url: 'https://" . $_SERVER['SERVER_ADDR'] . "/testonaut/server.php',
                xhrFields: {
                    withCredentials: true
                },
                data: { canvas: canvas.toDataURL('image/png'), path:'" . $srcImage . "'}
            })
            .done(function(msg) {
            console.log(msg);
            });
          }
        })
      }, 1500);";

    $this->webDriver->executeScript($js, array());
    return $this->webDriver;
  }

}