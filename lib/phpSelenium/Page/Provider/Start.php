<?php
namespace phpSelenium\Page\Provider;

use phpSelenium\Generate;
use phpSelenium\Page\Breadcrumb;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Start implements ControllerProviderInterface {
  private $edit = FALSE;
  public function __construct($edit = FALSE) {
    $this->edit = $edit;
  }


  public function connect(Application $app) {
    $start = $app['controllers_factory'];
    $start->post('/', function (Request $request) use ($app) {
      $path = $request->request->get('path');
      $content = $request->request->get('content');
      $page = new \phpSelenium\Page($path);
      $page->content($content, TRUE);
      return $app->redirect($request->getBaseUrl() . '/');
    });

    $start->get('/', function (Request $request) use ($app) {
      $toc  = $this->getToc();
      $app['menu'] = $toc;

      $page = new \phpSelenium\Page('/web');
      $content = $page->content();

      var_dump($content);

      if ($this->edit) {
        $app['request'] = array(
          'content' => $content,
          'path' => 'edit',
          'baseUrl' => $request->getBaseUrl(),
          'mode' => 'edit'
        );
        $crumb = new Breadcrumb('edit');
        $app['crumb'] = $crumb->getBreadcrumb();

        return $app['twig']->render('edit.twig');
      } else {
        $app['request'] = array(
          'content' => $content,
          'path' => '',
          'baseUrl' => $request->getBaseUrl(),
          'mode' => 'show',
        );
        return $app['twig']->render('index.twig');
      }


      var_dump('start');

    });
    return $start;
  }

  protected function getToc() {
    $toc = new Generate\Toc(\phpSelenium\Config::getInstance()->wikiPath);
    $toc->runDir();
    return $toc->generateMenu();
  }

  protected function editpage() {

  }



}