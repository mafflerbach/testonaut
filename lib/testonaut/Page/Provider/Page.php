<?php
namespace testonaut\Page\Provider;

use testonaut\Generate\Toc;
use testonaut\Matrix;
use testonaut\Page\Breadcrumb;
use testonaut\Page\Compiler;
use testonaut\Settings\Browser;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Page implements ControllerProviderInterface {
  private $request;
  private $path;

  public function connect(Application $app) {
    $page = $app['controllers_factory'];
    $page->get('/', function (Request $request, $path) use ($app) {
      $this->request = $request;
      $this->path = $path;
      $settings = new \testonaut\Settings\Page($path);
      $page = new \testonaut\Page($path);

      $content = $this->getContent($page);

      $browserSettings = new Browser($path);
      $browsers = $browserSettings->getSettings();

      $matrix = new Matrix($page, $browsers);
      $lastRun = $matrix->read();

      $images = $page->getImages();

      $root = \testonaut\Config::getInstance()->Path;

      $app['request'] = array(
        'content'   => $content,
        'path'      => $path,
        'baseUrl'   => $request->getBaseUrl(),
        'mode'      => 'show',
        'browsers'  => $browsers,
        'images'    => $images,
        'imagePath' => '/'.\testonaut\Config::getInstance()->appPath.str_replace($root, '', $page->getImagePath()),
        'type'      => $settings->getType(),
        'lastResult'      => $lastRun
      );
      $toc = $this->getToc($page->transCodePath());
      $app['menu'] = $toc;

      $crumb = new Breadcrumb($path);
      $app['crumb'] = $crumb->getBreadcrumb();

      $foo = $app['twig']->render('page.twig');
      return new Response($foo , 200, array(
        'Cache-Control' => 'maxage=3600',
      ));

    });

    return $page;
  }



  protected function getContent(\testonaut\Page $page) {
    $compile = new Compiler($page);
    $variables = array(
      '{{ app.request.baseUrl }}' => $this->request->getBaseUrl(),
      '{{ app.request.path }}'    => $this->path,
    );
    $content = $compile->compile($variables);
    return $content;
  }

  protected function getToc($path) {
    $toc = new Toc($path);
    $toc->page($this->path);
    $toc->runDir();
    return $toc->generateMenu();
  }
}