<?php
namespace phpSelenium\Page\Provider;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Delete implements ControllerProviderInterface {
  public function connect(Application $app) {
    $edit = $app['controllers_factory'];
    $edit->get('/', function (Request $request, $path) use ($app) {
      $app['request'] = array(
        'path' => $path,
        'baseUrl' => $request->getBaseUrl(),
        'mode' => 'show'
      );

      $foo = $app['twig']->render('delete.twig');
      return $foo;
    });

    $edit->post('/', function (Request $request, $path) use ($app) {
      $page = new \phpSelenium\Page($path);
      $content = $page->delete();
      return $app->redirect($request->getBaseUrl() . '/');
    });
    return $edit;
  }
}