<?php
namespace testonaut\Page\Provider;

use testonaut\Page\Base;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Image extends Base implements ControllerProviderInterface {

  private $path;

  public function connect(Application $app) {

    $image = $app['controllers_factory'];

    $image->get('/copy/{browser}/{image}/{path}', function (Request $request, $browser, $image, $path) use ($app) {
      $this->path = $path;

      $app['request'] = array(
        'path'    => $path,
        'baseUrl' => $request->getBaseUrl(),
        'mode'    => 'edit'
      );

      return $app['twig']->render('copy.twig');
    });

    $image->post('/copy/{browser}/{image}/{path}', function (Request $request, $browser, $image, $path) use ($app) {

      $this->path = $path;

      $src = $this->getImagePath() . '/' . $browser . '/src/' . $image;
      $ref = $this->getImagePath() . '/' . $browser . '/ref/' . $image;

      if (copy($src, $ref)) {
        $message = 'copied';
      } else {
        $message = 'can not copy';
      }

      $app['request'] = array(
        'path'    => $path,
        'baseUrl' => $request->getBaseUrl(),
        'message' => $message,
        'mode'    => 'edit'
      );

      return $app['twig']->render('copy.twig');
    });

    $image->get('/delete/{type}/{browser}/{image}/{path}', function (Request $request, $type, $browser, $image, $path) use ($app) {
      $this->path = $path;

      $app['request'] = array(
        'path'    => $path,
        'baseUrl' => $request->getBaseUrl(),
        'mode'    => 'edit'
      );

      return $app['twig']->render('deleteImage.twig');
    });

    $image->post('/delete/{type}/{browser}/{image}/{path}', function (Request $request, $type, $browser, $image, $path) use ($app) {
      $this->path = $path;
      $src = $this->getImagePath() . '/' . $browser . '/' . $type . '/' . $image;

      if (file_exists($src)) {
        if (unlink($src)) {
          $message = 'delete';
        } else {
          $message = 'can not delete';
        }
      }

      $app['request'] = array(
        'path'    => $path,
        'baseUrl' => $request->getBaseUrl(),
        'message' => $message,
        'mode'    => 'edit'
      );

      return $app['twig']->render('deleteImage.twig');
    });

    return $image;
  }

  public function getImagePath() {
    return \testonaut\Config::getInstance()->imageRoot . "/" . $this->relativePath();
  }

  public function relativePath() {
    return str_replace('.', '/', $this->path);
  }
}