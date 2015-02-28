<?php
namespace phpSelenium\Page\Provider;

use phpSelenium\Page\Base;
use phpSelenium\Page\Breadcrumb;
use phpSelenium\Parser\Config\Browser;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Config extends Base implements ControllerProviderInterface {

  private $path;

  public function connect(Application $app) {

    $config = $app['controllers_factory'];
    $config->get('/', function (Request $request, $path) use ($app) {
      $this->path = $path;
      $page = new \phpSelenium\Page($path);
      $content = $page->content();

      $crumb = new Breadcrumb($path);
      $app['crumb'] = $crumb->getBreadcrumb();

      $app['browser'] = $this->browserSettings();
      $app['type'] = $this->pageSettings();

      $app['request'] = array(
        'content' => $content,
        'path' => $path,
        'baseUrl' => $request->getBaseUrl(),
        'mode' => 'show'
      );

      return $app['twig']->render('config.twig');
    });

    $config->post('/', function (Request $request, $path) use ($app) {
      $content = $request->request->get('content');
      $page = new \phpSelenium\Page($path);
      $content = $page->content($content, TRUE);
      return $app->redirect($request->getBaseUrl() . '/' . $path);
    });
    return $config;
  }

  protected function browserSettings() {
    $pathArray = explode('.', $this->path);
    if (count($pathArray) == 1) {
      $bSettings = new \phpSelenium\Settings\Browser($this->path);
      return $bSettings->getSettings();
    }
  }

  protected function pageSettings() {
    $pSettings = new \phpSelenium\Settings\Page($this->path);
    return $pSettings->getSettings();
  }
}