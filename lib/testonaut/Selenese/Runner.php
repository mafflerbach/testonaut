<?php

namespace testonaut\Selenese;

use testonaut\Image;
use testonaut\Page;
use testonaut\Selenese\Command\captureEntirePageScreenshot;

class Runner {

  /** @var Test */
  public $test;

  /** @var string */
  public $hubUrl;
  public $pageUrl;
  public $imagePath;
  private $baseUrl;

  private $result = array();
  private $screenshotsAfterEveryStep = FALSE;
  private $screenshotsAfterTest = FALSE;
  private $polling = FALSE;

  /**
   * @param Test $test
   * @param string $hubUrl
   */
  public function __construct(Array $test, $hubUrl, $pagePath, $imagePath) {
    $this->test = $test;
    $this->hubUrl = $hubUrl;
    $this->pagePath = $pagePath;
    $this->imagePath = $imagePath;

    $this->polling = $this->pagePath . '/poll';

  }

  public function result() {
    return $this->result;
  }

  public function setBaseUrl($baseUrl) {
    $this->baseUrl = $baseUrl;
  }

  public function getBaseUrl() {
    return $this->baseUrl;
  }

  protected function _run(Page $content, \DesiredCapabilities $capabilities) {
    $browserResult = TRUE;
    $test = new Test();
    $test->setBaseUrl($this->baseUrl);
    $test->loadFromSeleneseHtml($content);

    if ($test->commands == '') {
      return NULL;
    }

    $browserName = str_replace(' ', '_', $capabilities->getBrowserName());

    $path = $content->getImagePath() . '/' . $browserName . "/src/";
    $this->polling .= '-' . $browserName;
    $k = 1;

    $webDriver = \RemoteWebDriver::create($this->hubUrl, $capabilities, 5000);

    $res[] = $result = "<tr><th colspan='3'>" . $browserName . "</th></tr>";
    $this->addToPoll($result);

    if ($this->screenshotsAfterTest) {
      $image = $path .'afterTest.png';
      $this->setupImageDir($browserName);
      $this->invokeCommand($image, $webDriver);
    }

    foreach ($test->commands as $command) {
      // todo: verbosity option

      $commandStr = str_replace('testonaut\Selenese\Command\\', '', get_class($command));
      $res[] = $result = "<tr><td>Running: " . $commandStr . ' </td><td> ' . $command->arg1 . ' </td><td> ' . $command->arg2 . ' </td> ' . "</tr>";
      $this->addToPoll($result);

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
        $res[] = $result = '<tr class="success"><td>SUCCESS</td><td colspan="2">' . $commandResult->message . '</td></tr>';
      } else {
        $res[] = $result = '<tr class="failed"><td>FAILED</td><td colspan="2">' . $commandResult->message . '</td></tr>';
        $browserResult = FALSE;
      }

      if ($commandStr == 'CaptureEntirePageScreenshot') {
        $compareResult = $this->captureAndCompare($command, $browserName, $webDriver);
        if ($compareResult['result']) {
          $res[] = $result = '<tr class="success"><td>SUCCESS</td><td colspan="2">' . $compareResult['message'] . '</td></tr>';
        } else {
          $res[] = $result = '<tr class="failed"><td>FAILED</td><td colspan="2">' . $compareResult['message'] . '</td></tr>';
          $browserResult = FALSE;
        }
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
    try {
      $webDriver->close();
    } catch (\Exception $e) {
      //nothing todo cause session is close
    }
    return array(
      'run'           => $res,
      'browserResult' => $browserResult
    );
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
        $result = $this->_run($this->test[$i], $capabilities);
        if ($result != NULL) {
          $return[] = $result;
        }
      }
    } else {
      $result = $this->_run(array($this->test), $capabilities);
      if ($result != NULL) {
        $return[] = $result;
      }
    }

    return $return;
  }

  public function screenshotsAfterEveryStep() {
    $this->screenshotsAfterEveryStep = TRUE;
  }

  public function screenshotsAfterTest() {
    $this->screenshotsAfterTest = TRUE;
  }

  protected function addToPoll($content) {
    $this->writeToFile($this->polling, $content, FILE_APPEND);
  }

  protected function invokeCommand($image, $webDriver) {
    $screenCommand = new captureEntirePageScreenshot();
    
    $screenCommand->arg1 = $image;
    $screenCommand->runWebDriver($webDriver);
  }
  
  private function setupImageDir($browserName) {
    $imageDir = $this->imagePath;
    $path = $imageDir . '/' . $browserName . "/src/";
    
    if (!file_exists($path)) {
      mkdir($path, 0775, TRUE);
      mkdir($imageDir . '/' . $browserName . "/comp/", 0775, TRUE);
      mkdir($imageDir . '/' . $browserName . "/ref/", 0775, TRUE);
    }  
  }
  

  protected function captureAndCompare($command, $browserName, $webDriver) {
    $this->setupImageDir($browserName);
    
    $imageDir = $this->imagePath;
    $path = $imageDir . '/' . $browserName . "/src/";
    
    $tmp = $command->arg1;
    $command->arg1 = $path . $tmp;
    $this->invokeCommand($command->arg1, $webDriver);
    
    if ($comp = $this->compare($browserName, $tmp . '.png')) {
      $result = "Compare: " . $command->arg1;
      $this->writeToFile($this->polling, $result, FILE_APPEND);
    } else {
      $result = "Cant Compare: " . $command->arg1;
      $this->writeToFile($this->polling, $result, FILE_APPEND);
    }

    return array(
      'result'  => $comp,
      'message' => $result
    );
  }

  protected function compare($browserName, $imgName) {
    $imageDir = $this->imagePath;
    $path = $imageDir . '/' . $browserName . "/src/" . $imgName;
    $pathref = $imageDir . '/' . $browserName . "/ref/" . $imgName;
    $comp = $imageDir . '/' . $browserName . "/comp/" . $imgName;

    if (file_exists($pathref)) {
      if (file_exists($comp)) {
        unlink($comp);
      }
      if (class_exists('\\Imagick')) {
        $compare = new Image();
        return $compare->compare($path, $pathref, $comp);
      } else {
        return FALSE;
      }
    } else {
      return FALSE;
    }
  }

  protected function writeToFile($path, $content, $option = 0) {
    file_put_contents($path, $content, $option);
  }

}