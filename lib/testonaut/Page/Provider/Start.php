<?php
namespace testonaut\Page\Provider;

use testonaut\Generate;
use testonaut\Page\Breadcrumb;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

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
      $page = new \testonaut\Page('');
      $page->content($content, TRUE);
      return $app->redirect($request->getBaseUrl() . '/');
    });

    $start->get('/', function (Request $request) use ($app) {
      $toc = $this->getToc();
      $app['menu'] = $toc;

      $page = new \testonaut\Page('');
      $content = $page->content();

      if ($this->edit) {
        $app['request'] = array(
          'content' => $content,
          'path'    => 'edit',
          'baseUrl' => $request->getBaseUrl(),
          'mode'    => 'edit'
        );
        $crumb = new Breadcrumb('edit');
        $app['crumb'] = $crumb->getBreadcrumb();

        return $app['twig']->render('edit.twig');
      } else {
        $app['request'] = array(
          'content' => $content,
          'path'    => '',
          'baseUrl' => $request->getBaseUrl(),
          'mode'    => 'show',
          'type' => 'start'
        );
        $foo =  $app['twig']->render('index.twig');
        return new Response($foo , 200, array(
          'Cache-Control' => 'maxage=300',
        ));
      }

    });
    return $start;
  }

  protected function getToc() {
    $toc = new Generate\Toc(\testonaut\Config::getInstance()->wikiPath);
    $toc->runDir();
    return $toc->generateMenu();
  }

  protected function editpage() {

  }

}