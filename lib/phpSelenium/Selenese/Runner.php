<?php

namespace phpSelenium\Selenese;

use phpSelenium\Image;
use phpSelenium\Selenese\Command\captureEntirePageScreenshot;

class Runner {

  /** @var Test */
  public $test;

  /** @var string */
  public $hubUrl;
  public $pageUrl;

  private $result = array();
  private $screenshotsAfterEveryStep = FALSE;
  private $polling = FALSE;

  /**
   * @param Test $test
   * @param string $hubUrl
   */
  public function __construct(Array $test, $hubUrl, $pagePath) {
    $this->test = $test;
    $this->hubUrl = $hubUrl;
    $this->pagePath = $pagePath;

    $this->polling = $this->pagePath . '/poll';

  }

  public function result() {
    return $this->result;
  }

  protected function _run($content, \DesiredCapabilities $capabilities) {
    $test = new Test();
    $test->loadFromSeleneseHtml($content);

    if ($test->commands == '') {
      throw new \Exception('Test not found');
      return NULL;
    }

    $webDriver = \RemoteWebDriver::create($this->hubUrl, $capabilities, 5000);
    $browserName = str_replace(' ', '_', $capabilities->getBrowserName());

    $imageDir = $this->pagePath . "/__IMAGES";
    $path = $imageDir . '/' . $browserName . "/src/";
    $this->polling .= '-' . $browserName;
    $k = 1;

    $result = "<tr><th colspan='3'>" . $browserName . "</th></tr>";
    $this->addToPoll($result);

    foreach ($test->commands as $command) {
      // todo: verbosity option
      $commandStr = str_replace('phpSelenium\Selenese\Command\\', '', get_class($command));
      $result = "<tr><td>Running: " . $commandStr . ' </td><td> ' . $command->arg1 . ' </td><td> ' . $command->arg2 . ' </td> ' . "</tr>";
      $this->addToPoll($result);
      if ($commandStr == 'captureEntirePageScreenshot') {
        $this->captureAndCompare($command, $browserName);
      }

      try {
        // todo: screenshots after each command option settings
        $commandResult = $command->runWebDriver($webDriver);
        if ($this->screenshotsAfterEveryStep) {
          $image = $path . 'image' . $k . '.png';
          $this->invokeCommand($image, $webDriver);
        }
      } catch (\Exception $e) {
        $commandResult = new CommandResult(FALSE, FALSE, $e->getMessage());
      }

      if ($commandResult->success) {
        $result = '<tr class="success"><td>SUCCESS</td><td colspan="2">' . $commandResult->message . '</td></tr>';
      } else {
        $result = '<tr class="failed"><td>FAILED</td><td colspan="2">' . $commandResult->message . '</td></tr>';
      }
      $this->addToPoll($result);

      if ($commandResult->continue === FALSE) {
        break;
      }
      $k++;
    }

    if (file_exists($this->polling)) {
      unlink($this->polling);
    }

    $webDriver->close();
    return $result;
  }

  /**
   * Run the test!
   *
   * @return array An array of arrays containing the command and the commandResult
   */
  public function run($capabilities) {
    $return = array();

    if (is_array($this->test)) {
      for ($i = 0; $i < count($this->test); $i++) {
        $return[] = $this->_run($this->test[$i], $capabilities);
      }
    } else {
      $return[] = $this->_run(array($this->test), $capabilities);
    }
    return $return;
  }

  public function screenshotsAfterEveryStep() {
    $this->screenshotsAfterEveryStep = TRUE;
  }

  protected function getImagePath() {
    var_dump($this->pagePath);
  }

  protected function addToPoll($content) {
    file_put_contents($this->polling, $content, FILE_APPEND);
  }

  protected function invokeCommand($image, $webDriver) {
    $screenCommand = new captureEntirePageScreenshot();
    $screenCommand->arg1 = $image;
    $screenCommand->runWebDriver($webDriver);
  }

  protected function captureAndCompare($command, $browserName) {
    $imageDir = $this->pagePath . "/__IMAGES";
    $path = $imageDir . '/' . $browserName . "/src/";

    print($path);

    if (!file_exists($path)) {
      mkdir($path, 0775, TRUE);
      mkdir($imageDir . '/' . $browserName . "/comp/", 0775, TRUE);
      mkdir($imageDir . '/' . $browserName . "/ref/", 0775, TRUE);
    }
    $tmp = $command->arg1;
    $command->arg1 = $path . $tmp . '.png';
    if ($this->compare($browserName, $tmp . '.png')) {
      $result = "<tr><td colspan='3'>Compare: " . $command->arg1 . " </td></tr>";
      file_put_contents($this->polling, $result, FILE_APPEND);
    } else {
      $result = "<tr><td colspan='3'>Cant Compare: " . $command->arg1 . " </td></tr>";
      file_put_contents($this->polling, $result, FILE_APPEND);
    }
  }

  protected function compare($browserName, $imgName) {
    $imageDir = $this->pagePath . "/__IMAGES";
    $path = $imageDir . '/' . $browserName . "/src/" . $imgName;
    $pathref = $imageDir . '/' . $browserName . "/ref/" . $imgName;
    $comp = $imageDir . '/' . $browserName . "/comp/" . $imgName;

    if (file_exists($pathref)) {
      if (file_exists($comp)) {
        unlink($comp);
      }

      $compare = new Image();
      return $compare->compare($path, $pathref, $comp);
    } else {
      return FALSE;
    }

  }

}