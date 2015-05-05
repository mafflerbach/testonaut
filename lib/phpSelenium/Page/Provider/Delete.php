<?php
namespace phpSelenium\Page\Provider;

use phpSelenium\Page\Breadcrumb;
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
        'mode' => 'delete',
      );

      $crumb = new Breadcrumb($path);
      $app['crumb'] = $crumb->getBreadcrumb();

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