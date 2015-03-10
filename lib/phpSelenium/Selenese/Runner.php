<?php

namespace phpSelenium\Selenese;

use phpSelenium\Selenese\Command\captureEntirePageScreenshot;

class Runner {

  /** @var Test */
  public $test;

  /** @var string */
  public $hubUrl;
  public $pageUrl;

  private $result = array();
  private $screenshotsAfterEveryStep = FALSE;

  /**
   * @param Test $test
   * @param string $hubUrl
   */
  public function __construct(Array $test, $hubUrl, $pagePath = '') {
    $this->test = $test;
    $this->hubUrl = $hubUrl;
    $this->pagePath = $pagePath;
  }

  public function result() {
    return $this->result;
  }

  protected function _run(Test $test, \DesiredCapabilities $capabilities) {

    $webDriver = \RemoteWebDriver::create($this->hubUrl, $capabilities, 5000);
    $results = array();

    $k = 1;
    foreach ($test->commands as $command) {
      // todo: verbosity option
      $commandStr = str_replace('phpSelenium\\Selenese\\Command\\', '', get_class($command));
      $result[] = "Running: | " . $commandStr . ' | ' . $command->arg1 . ' | ' . $command->arg2 . ' | ';

      $imageDir = $this->pagePath . "/__IMAGES";
      $path = $imageDir . '/' . $capabilities->getBrowserName() . "/src/";

      if ($commandStr == 'captureEntirePageScreenshot') {
        if (!file_exists($imageDir)) {
          mkdir($path, 775, TRUE);
          mkdir($imageDir . '/' . $capabilities->getBrowserName() . "/comp/", 775, TRUE);
          mkdir($imageDir . '/' . $capabilities->getBrowserName() . "/ref/", 775, TRUE);
        }
        $command->arg1 = $path . "/" . $command->arg1;
      }

      try {
        $commandResult = $command->runWebDriver($webDriver);
        if ($this->screenshotsAfterEveryStep) {
          $screenCommand = new captureEntirePageScreenshot();
          $screenCommand->arg1 = $path . 'image'.$k.'.png';
          $screenCommand->runWebDriver($webDriver);
        }

      } catch (\Exception $e) {
        $commandResult = new CommandResult(FALSE, FALSE, $e->getMessage());
      }

      // todo: screenshots after each command option

      $result[] = ($commandResult->success ? 'SUCCESS | ' : 'FAILED | ') . $commandResult->message;
      $results[] = array(
        $command,
        $commandResult
      );

      if ($commandResult->continue === FALSE) {
        break;
      }
      // todo: screenshot on fail option
      $k++;
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

}