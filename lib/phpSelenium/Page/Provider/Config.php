<?php
namespace phpSelenium\Page\Provider;

use phpSelenium\Parser\Config\Browser;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Config implements ControllerProviderInterface {
  public function connect(Application $app) {
    $edit = $app['controllers_factory'];
    $edit->get('/', function (Request $request, $path) use ($app) {
      $page = new \phpSelenium\Page($path);
      $content = $page->content();


      $pathArray = explode('.', $path);

      if (count($pathArray) == 1) {
        $browser = new Browser();
        $browser->config(\phpSelenium\Config::getInstance()->seleniumConsole);
        $app['browser'] = $browser->browser;
        $bSettings = new \phpSelenium\Settings\Browser($path);
        var_dump($bSettings->getSettings());

      }

      $app['request'] = array(
        'content' => $content,
        'path' => $path,
        'baseUrl' => $request->getBaseUrl(),
        'mode' => 'show'
      );


      return $app['twig']->render('config.twig');
    });

    $edit->post('/', function (Request $request, $path) use ($app) {
      $content = $request->request->get('content');
      $page = new \phpSelenium\Page($path);
      $content = $page->content($content, TRUE);
      return $app->redirect($request->getBaseUrl() . '/' . $path);
    });
    return $edit;
  }
}