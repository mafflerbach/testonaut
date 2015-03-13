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
  public function __construct(Array $test, $hubUrl, $pagePath) {
    $this->test = $test;
    $this->hubUrl = $hubUrl;
    $this->pagePath = $pagePath;
  }

  public function result() {
    return $this->result;
  }

  protected function _run($content, \DesiredCapabilities $capabilities) {
    $test = new Test();
    $test->loadFromSeleneseHtml($content);

    $webDriver = \RemoteWebDriver::create($this->hubUrl, $capabilities, 5000);
    $results = array();

    $browserName = str_replace(' ', '_', $capabilities->getBrowserName());
    $imageDir = $this->pagePath . "/__IMAGES";
    $path = $imageDir . '/' . $browserName . "/src/";

    $context = new ZMQContext();
    $socket = $context->getSocket(ZMQ::SOCKET_PUSH, 'my pusher');
    $socket->connect("tcp://localhost:8888");

    $k = 1;
    foreach ($test->commands as $command) {
      // todo: verbosity option
      $commandStr = str_replace('phpSelenium\\Selenese\\Command\\', '', get_class($command));
      $result[] = "Running: | " . $commandStr . ' | ' . $command->arg1 . ' | ' . $command->arg2 . ' | ';

      if ($commandStr == 'captureEntirePageScreenshot') {
        if (!file_exists($path)) {
          mkdir($path, 775, TRUE);
          mkdir($imageDir . '/' . $browserName . "/comp/", 775, TRUE);
          mkdir($imageDir . '/' . $browserName . "/ref/", 775, TRUE);
        }
        //clear Arg;
        $tmp = $command->arg1;
        $command->arg1 = $path . $tmp .'.png';
      }

      try {
        // todo: screenshots after each command option settings
        $commandResult = $command->runWebDriver($webDriver);
        if ($this->screenshotsAfterEveryStep) {
          $screenCommand = new captureEntirePageScreenshot();
          $screenCommand->arg1 = $path . 'image'.$k.'.png';
          $screenCommand->runWebDriver($webDriver);
        }

      } catch (\Exception $e) {
        $commandResult = new CommandResult(FALSE, FALSE, $e->getMessage());
      }

      $result[] = ($commandResult->success ? 'SUCCESS | ' : 'FAILED | ') . $commandResult->message;
      $results[] = array(
        $command,
        $commandResult
      );

      $socket->send(json_encode($results));

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