<?php
namespace phpSelenium\Page\Provider;

use phpSelenium\Capabilities;
use phpSelenium\Page;
use phpSelenium\Page\Breadcrumb;
use phpSelenium\Selenium\Api;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use phpSelenium\Selenese\Test;
use phpSelenium\Selenese\Runner;

class Run implements ControllerProviderInterface {
  private $basePath;
  /**
   * @var \phpSelenium\Page $page
   */
  private $page;
  private $imagePath;
  private $dirArray = array();
  private $path;
  private $browser = '';

  public function connect(Application $app) {
    $edit = $app['controllers_factory'];
    $edit->get('/', function (Request $request, $path) use ($app) {
      $this->path = $path;
      $this->page = new \phpSelenium\Page($path);
      $this->basePath = $this->page->transCodePath();
      $this->imagePath = $this->page->getImagePath();

      $this->browser = $request->query->get('browser');
      if ($this->browser == '') {
        $this->browser = 'all';
      }

      if ($request->query->get('suite') == 'true') {
        $result = $this->runSuite($this->page);
      } else {
        $result = $this->run($this->page);
      }

      $this->writeResultFile($result);

      $app['request'] = array(
        'path'    => $path,
        'baseUrl' => $request->getBaseUrl(),
        'mode'    => 'edit'
      );
      $crumb = new Breadcrumb($path);
      $app['crumb'] = $crumb->getBreadcrumb();
      $app['result'] = $result;

      return $app['twig']->render('run.twig');
    });
    return $edit;
  }

  protected function writeResultFile($content) {
    $path = $this->page->getResultPath();
    if (!file_exists($path)) {
      mkdir($path, 0775, TRUE);
    }

    $fileName = 'result_' . $this->browser . '_' . date('Y-m-d_H-i-s');
    file_put_contents($path . '/' . $fileName, json_encode($content));
  }

  protected function runSuite($path) {
    $this->collect($path->transCodePath());

    $testCollect = array();

    $content = file_get_contents($path->transCodePath() . '/content');
    if (strpos($content, '<table') !== FALSE) {
      $this->dirArray[] = $path;
    }

    for ($i = 0; $i < count($this->dirArray); $i++) {
      $testCollect[] = $this->dirArray[$i];
    }

    $result = $this->_run($testCollect);

    return $result;
  }

  protected function run($path) {
    $testCollect[] = $path;

    return $this->_run($testCollect);
  }

  protected function screenshotSettings() {
    $conf = $this->page->config();
    switch ($conf['screenshots']) {
      case 'step';
        return 2;
        break;
      case 'test';
        return 1;
        break;
      case 'none';
        return 0;
        break;
      default:
        return 0;
        break;
    }
  }

  protected function baseUrlSettings($capabilities) {
    $conf = $this->page->config();
    if (isset($conf['browser']['active']) && ($conf['type'] == 'suite' || $conf['type'] == 'project')) {
      if (in_array($capabilities->getBrowserName(), $conf['browser']['active'])) {
        return $conf['browser']['urls'][$capabilities->getBrowserName()];
      }
    } else {
      return '';
    }
  }

  private function _run(array $tests) {

    try {
      $capabilities = $this->getCapabilities();
      $runner = new Runner($tests, \phpSelenium\Config::getInstance()->seleniumHub, $this->basePath, $this->imagePath);

      $browserUrl = $this->baseUrlSettings($capabilities);
      $runner->setBaseUrl($browserUrl);

      if ($this->screenshotSettings() == 2) {
        $runner->screenshotsAfterEveryStep();
      }
      if ($this->screenshotSettings() == 1) {
        $runner->screenshotsAfterTest();
      }

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
            $wikipath = \phpSelenium\Config::getInstance()->wikiPath . '/';
            $path = str_replace($wikipath, '', $outerDir . "/" . $d);
            $path = str_replace('/', '.', $path);

            $page = new Page($path);
            $this->dirArray[] = $page;
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

    $DesiredCapabilities = new \DesiredCapabilities();

    if ($this->browser == 'all') {
      $api = new Api();
      $list = $api->getBrowserList();
      $capabilities = array();

      for ($i = 0; $i < count($list); $i++) {
        $browserName = $this->normalizeBrowserName($list[$i]['browserName']);
        if (method_exists($DesiredCapabilities, $browserName)) {
          $capabilities[] = \DesiredCapabilities::$browserName();
        }
      }
    } else {
      $browserName = $this->normalizeBrowserName($this->browser);
      if (method_exists($DesiredCapabilities, $browserName)) {
        $capabilities = \DesiredCapabilities::$browserName();
      }
    }
    return $capabilities;
  }

  private function normalizeBrowserName($browserString) {
    if (strpos($browserString, ' ') > 0) {
      $expl = explode(' ', $browserString);
      $browserName = $expl[0] . ucfirst($expl[1]);
    } else if (strpos($browserString, '_') > 0) {
      $expl = explode('_', $browserString);
      $browserName = $expl[0] . ucfirst($expl[1]);
    } else {
      $browserName = $browserString;
    }
    return $browserName;
  }
}