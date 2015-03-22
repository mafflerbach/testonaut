<?php
namespace phpSelenium\Page\Provider;

use phpSelenium\Page\Base;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Image extends Base implements ControllerProviderInterface {

  private $path;

  public function connect(Application $app) {

    $image = $app['controllers_factory'];

    $image->get('/copy/{browser}/{image}/{path}', function (Request $request, $browser, $image, $path) use ($app) {
      $this->path = $path;

      $src = $this->getImagePath() . '/' . $browser . '/src/' . $image;
      $ref = $this->getImagePath() . '/' . $browser . '/ref/' . $image;

      if (copy($src, $ref)) {
        $app['request']['success'] = TRUE;
      } else {
        $app['request']['fail'] = TRUE;
      }

      return $app['twig']->render('empty.twig');
    });

    $image->get('/delete/{browser}/{image}/{path}', function (Request $request, $browser, $image, $path) use ($app) {
      $this->path = $path;
      print($image);
      print($path);
      print($this->getImagePath() . '/' . $browser . '/' . $image);
      print('delete');
      return $app['twig']->render('empty.twig');
    });
    return $image;
  }

  public function getImagePath() {
    return \phpSelenium\Config::getInstance()->imageRoot . "/" . $this->relativePath();
  }

  public function relativePath() {
    return str_replace('.', '/', $this->path);
  }
}