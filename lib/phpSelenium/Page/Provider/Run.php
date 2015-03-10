<?php
namespace phpSelenium\Page\Provider;

use phpSelenium\Capabilities;
use phpSelenium\Page\Breadcrumb;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use phpSelenium\Selenese\Test;
use phpSelenium\Selenese\Runner;

class Run implements ControllerProviderInterface {
  private $basePath;
  private $dirArray = array();
  private $path;
  private $browser = '';

  public function connect(Application $app) {
    $edit = $app['controllers_factory'];
    $edit->get('/', function (Request $request, $path) use ($app) {
      $this->path = $path;
      $page = new \phpSelenium\Page($path);
      $this->basePath = $page->transCodePath();

      $this->browser = $request->query->get('browser');
      if ($this->browser == '') {
        $this->browser = 'all';
      }

      if ($request->query->get('suite') == 'true') {
        $result = $this->runSuite($page->transCodePath());
      } else {
        $result = $this->run($page->transCodePath());
      }

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

  protected function runSuite($path) {
    $this->collect($path);

    $testCollect = array();
    for ($i = 0; $i < count($this->dirArray); $i++) {
      $test = new Test();
      $test->loadFromSeleneseHtml($this->dirArray[$i] . '/content');
      $testCollect[] = $test;
    }

    $result = $this->_run($testCollect);

    return $result;
  }

  protected function run($path) {
    $test = new Test();
    $test->loadFromSeleneseHtml($path . '/content');
    $testCollect[] = $test;
    return $this->_run($testCollect);
  }

  private function _run(array $tests) {
    try {
      $capabilities = $this->getCapabilities();
      $runner = new Runner($tests, \phpSelenium\Config::getInstance()->seleniumHub, $this->basePath);
//      $runner->screenshotsAfterEveryStep();

      if (!is_array($capabilities)) {
        $result = $runner->run($capabilities);
        return $result;
      } else {
        for ($i = 0; $i < count($capabilities); $i++) {
          $result[] = $runner->run($capabilities[$i]);
        }
        return $result;
      }
    } catch (\Exception $e) {
      echo 'Test failed: ' . $e->getMessage() . "\n";
    }
  }

  protected function collect($outerDir, $tests = array()) {
    $dirs = array_diff(scandir($outerDir), Array(
      ".",
      ".."
    ));

    $dir_array = Array();
    foreach ($dirs as $d) {
      if (is_dir($outerDir . "/" . $d)) {
        if (file_exists($outerDir . "/" . $d . '/content')) {
          $content = file_get_contents($outerDir . "/" . $d . '/content');
          if (strpos($content, '<table') !== FALSE) {
            $this->dirArray[] = $outerDir . "/" . $d;
          }
        }
        $dir_array[$d] = $this->collect($outerDir . "/" . $d);
      } else {
        $dir_array[$d] = $d;
      }
    }

    return $dir_array;
  }

  private function getCapabilities() {

    $capabilities = '';
    switch ($this->browser) {
      case 'firefox':
        $capabilities = \DesiredCapabilities::firefox();
        break;
      case 'chrome':
        $capabilities = \DesiredCapabilities::chrome();
        break;
      case 'iexplore':
        $capabilities = Capabilities::ieExplorer();
        break;
      default:
        $capabilities = array(
          /*
          \DesiredCapabilities::firefox(),
          \DesiredCapabilities::chrome(),
          Capabilities::ieExplorer() */
        );
        $capabilities = \DesiredCapabilities::firefox();
        break;
    }

    return $capabilities;

  }

}