<?php
namespace phpSelenium\Page\Provider;

use phpSelenium\Page\Breadcrumb;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use phpSelenium\Selenese\Test;
use phpSelenium\Selenese\Runner;

class Run implements ControllerProviderInterface {
  public function connect(Application $app) {
    $edit = $app['controllers_factory'];
    $edit->get('/', function (Request $request, $path) use ($app) {
      $page = new \phpSelenium\Page($path);
      $result = $this->run($page->transCodePath());
      $app['request'] = array(
        'path' => $path,
        'baseUrl' => $request->getBaseUrl(),
        'mode' => 'edit'
      );
      $crumb = new Breadcrumb($path);
      $app['crumb'] = $crumb->getBreadcrumb();
      $app['result'] = $result;

      return $app['twig']->render('run.twig');
    });
    return $edit;
  }

  protected function run($path) {
    try {
      // get the test rolling
      $test = new Test();
      $test->loadFromSeleneseHtml($path.'/content');
      $capabilities = \DesiredCapabilities::firefox();
      $runner = new Runner($test, 'http://localhost:4444/wd/hub');
      return $runner->run($capabilities);
    } catch (\Exception $e) {
      // oops.
      echo 'Test failed: ' . $e->getMessage() . "\n";
    }
  }

}