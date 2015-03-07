<?php
namespace phpSelenium\Page\Provider;

use phpSelenium\Generate;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Start implements ControllerProviderInterface {
  public function connect(Application $app) {

    $start = $app['controllers_factory'];
    $start->post('/', function (Request $request) use ($app) {
      $path = $request->request->get('path');
      $content = $request->request->get('content');

      $post = array(
        'path' => $request->request->get('path'),
        'action' => $request->request->get('action'),
        'content' => $request->request->get('content')

      );

      $page = new \phpSelenium\Page($path);
      $content = $page->content($content);
      return $app->json($post, 201);
    });

    $start->get('/', function (Request $request) use ($app) {
      $app['menu'] = $this->getToc();
      $app['request'] = array(
        'baseUrl' => $request->getBaseUrl(),
        'path' => '',
        'content' => '',
        'mode' => 'show',
        'type' => 'start'
      );
      return $app['twig']->render('index.twig');
    });
    return $start;
  }

  protected function getToc() {
    $toc = new Generate\Toc(\phpSelenium\Config::getInstance()->wikiPath);
    $toc->runDir();
    return $toc->generateMenu();
  }
}