<?php

namespace phpSelenium\Selenese;

class Runner {

  /** @var Test */
  public $test;

  /** @var string */
  public $hubUrl;

  private $result = array();

  /**
   * @param Test $test
   * @param string $hubUrl
   */
  public function __construct(Test $test, $hubUrl) {
    $this->test = $test;
    $this->hubUrl = $hubUrl;
  }

  public function result() {
    return $this->result;
  }

  /**
   * Run the test!
   *
   * @return array An array of arrays containing the command and the commandResult
   */
  public function run($capabilities) {
    $webDriver = \RemoteWebDriver::create($this->hubUrl, $capabilities, 5000);
    $results = array();
    foreach ($this->test->commands as $command) {
      // todo: verbosity option
      $result[] = "Running: | " . str_replace('phpSelenium\\Selenese\\Command\\', '', get_class($command)) . ' | ' . $command->arg1 . ' | ' . $command->arg2 . ' | <br/>';

      try {
        $commandResult = $command->runWebDriver($webDriver);
      } catch (\Exception $e) {
        $commandResult = new CommandResult(false, false, $e->getMessage());
      }

      // todo: screenshots after each command option

      $result[] = ($commandResult->success ? 'SUCCESS | ' : 'FAILED | ') . $commandResult->message . "\n";
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

}