<?php
namespace phpSelenium\Page\Provider;

use phpSelenium\Page\Breadcrumb;
use phpSelenium\Settings\Browser;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Page implements ControllerProviderInterface {
  public function connect(Application $app) {
    $page = $app['controllers_factory'];
    $page->get('/', function (Request $request, $path) use ($app) {
      $settings = new \phpSelenium\Settings\Page($path);
      $page = new \phpSelenium\Page($path);
      $content = $page->content();

      $browserSettings = new Browser($path);
      $browsers = $browserSettings->getSettings();

      $app['request'] = array(
        'content' => $content,
        'path' => $path,
        'baseUrl' => $request->getBaseUrl(),
        'mode' => 'show',
        'type' => $settings->getType(),
        'browsers' => $browsers
      );

      $crumb = new Breadcrumb($path);
      $app['crumb'] = $crumb->getBreadcrumb();

      $foo = $app['twig']->render('page.twig');
      return $foo;
    });

    return $page;
  }
}