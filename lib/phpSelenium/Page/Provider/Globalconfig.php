<?php
namespace phpSelenium\Page\Provider;

use phpSelenium\Page\Breadcrumb;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

/**
 *
 *
 *
 * $seleniumAddress = 'http://selenium-hub.dim:4444';
 * $config = \phpSelenium\Config::getInstance();
 * $config->define('Path', dirname(dirname(__FILE__)));
 * $config->define('wikiPath', dirname(dirname(__FILE__)) . '/root');
 * $config->define('imageRoot', dirname(dirname(__FILE__)) . '/images');
 * $config->define('result', dirname(dirname(__FILE__)) . '/result');
 * $config->define('seleniumHub', $seleniumAddress.'/wd/hub');
 * $config->define('seleniumConsole', $seleniumAddress.'/grid/console');
 * $config->define('appPath', '');
 * $config->define('Cache', FALSE);
 * $config->define('seleniumAddress', $seleniumAddress);
 *
 *
 */
class Globalconfig implements ControllerProviderInterface {
  public function connect(Application $app) {
    $edit = $app['controllers_factory'];
    $edit->get('/', function (Request $request) use ($app) {
      $conf = $this->getConfig();

      var_dump($conf);
      $app['request'] = array(
        'baseUrl' => $request->getBaseUrl(),
        'mode'    => 'edit',
        'settings' => $conf
      );

      return $app['twig']->render('globalconfig.twig');

    });

    $edit->post('/', function (Request $request, $path) use ($app) {
      $content = $request->request->get('content');

      $page = new \phpSelenium\Page($path);
      $content = $page->content($content, TRUE);
      return $app->redirect($request->getBaseUrl() . '/' . $path);
    });
    return $edit;
  }

  protected function getConfig() {

    $config = \phpSelenium\Config::getInstance()->Path . '/config';
    if (file_exists($config)) {
      $configuration = json_decode(file_get_contents($config), true);
    } else {

      $configuration = array(
        'Path'            => '',
        'appPath'         => '',
        'Cache'           => '',
        'seleniumAddress' => '');
      file_put_contents($config, json_encode($configuration));

    };

    return $configuration;
  }

}