<?php
namespace phpSelenium\Page\Provider;

use phpSelenium\Page\Base;
use phpSelenium\Page\Breadcrumb;
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
      $this->path = $path;
      $content = $request->request->get('content');

      var_dump($request->request);
      $browserUrls = $request->request->get('browser');
      $activeBrowser = $request->request->get('active');
      $type = $request->request->get('type');

      $browserSettings = array_merge(array('urls' => $browserUrls), array('active' => $activeBrowser ));
      $this->browserSettings($browserSettings);
      $this->pageSettings($type);
      die;



      $page = new \phpSelenium\Page($path);
      $content = $page->content($content, TRUE);
      return $app->redirect($request->getBaseUrl() . '/' . $path);
    });
    return $config;
  }

  protected function browserSettings($settings = NULL) {
    $pathArray = explode('.', $this->path);
    if (count($pathArray) == 1) {
      if ($settings != NULL) {
        $bSettings = new \phpSelenium\Settings\Browser($this->path);
        return $bSettings->setSettings($settings);
      } else {
        $bSettings = new \phpSelenium\Settings\Browser($this->path);
        return $bSettings->getSettings();
      }
    }

  }

  protected function pageSettings($settings = NULL) {
    if ($settings != NULL) {
      $pSettings = new \phpSelenium\Settings\Page($this->path);
      return $pSettings->setSettings($settings);
    } else {
      $pSettings = new \phpSelenium\Settings\Page($this->path);
      return $pSettings->getSettings();
    }

  }

}