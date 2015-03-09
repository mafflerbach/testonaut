<?php

namespace phpSelenium\Selenese;

class Runner {

  /** @var Test */
  public $test;

  /** @var string */
  public $hubUrl;
  public $pageUrl;

  private $result = array();

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

    foreach ($test->commands as $command) {
      // todo: verbosity option
      $commandStr = str_replace('phpSelenium\\Selenese\\Command\\', '', get_class($command));
      $result[] = "Running: | " . $commandStr . ' | ' . $command->arg1 . ' | ' . $command->arg2 . ' | ';

      if ($commandStr == 'captureEntirePageScreenshot') {
        $imageDir = $this->pagePath."/__IMAGES";

        $path = $imageDir."/src/".$capabilities->getBrowserName();

        if (!file_exists($imageDir)) {
          mkdir($path, 775, true);
          mkdir($imageDir."/comp/".$capabilities->getBrowserName(), 775, true);
          mkdir($imageDir."/ref/".$capabilities->getBrowserName(), 775, true);
        }

        $command->arg1 = $path."/".$command->arg1;
      }

      try {
        $commandResult = $command->runWebDriver($webDriver);
      } catch (\Exception $e) {
        $commandResult = new CommandResult(false, false, $e->getMessage());
      }

      // todo: screenshots after each command option

      $result[] = ($commandResult->success ? 'SUCCESS | ' : 'FAILED | ') . $commandResult->message;
      $results[] = array(
        $command,
        $commandResult
      );

      if ($commandResult->continue === false) {
        break;
      }
      // todo: screenshot on fail option
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
    $return= array();

    if (is_array($this->test)) {
      for ($i = 0; $i < count($this->test); $i++) {
        $return[] = $this->_run($this->test[$i], $capabilities);
      }
    } else {
      $return[] = $this->_run(array($this->test), $capabilities);
    }
    return $return;
  }


}