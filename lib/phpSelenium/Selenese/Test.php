<?php

namespace phpSelenium\Selenese;

use phpSelenium\Selenese\Command\Command;
use phpSelenium\Selenese\Command\Stub;

class Test {

  /**
   * @var Command[]
   */
  public $commands = array();

  /**
   * @var string
   */
  private  $baseUrl = '';

  /**
   * @param string $file
   * @return Command[]
   * @throws \InvalidArgumentException
   * @throws \Exception
   */
  public function loadFromSeleneseHtml($file) {

    if (!file_exists($file)) {
      throw new \InvalidArgumentException("$file does not exist");
    }

    if (!is_readable($file)) {
      throw new \InvalidArgumentException("$file is not readable");
    }

    libxml_use_internal_errors(true);
    $dom = new \DOMDocument;
    $dom->loadHTMLFile($file);

    // get the base url
    if ($this->baseUrl == '') {
      if ($dom->getElementsByTagName('link')->length > 0) {
        $this->baseUrl = $dom->getElementsByTagName('link')->item(0)->getAttribute('href');
        $this->baseUrl = rtrim($this->baseUrl, '/');
      }
    }

    // todo: catch loading of things NOT selenese
    if($dom->getElementsByTagName('tbody')->length == 0) {
      $this->commands = '';
      return;
    }

    $tables = $dom->getElementsByTagName('tbody');
    foreach($tables as $table) {
      $rows = $table->getElementsByTagName('tr');
      $this->parseTable($rows);
    }
  }

  protected function parseTable($rows) {
    // extract the commands
    foreach ($rows as $row) {
      /** @var \DOMElement $row */
      $tds = $row->getElementsByTagName('td');

      $command = $tds->item(0)->nodeValue;
      $target = $tds->item(1)->nodeValue;
      $value = $tds->item(2)->nodeValue;

      $command = str_replace('andWait', '', $command);

      $commandClass = 'phpSelenium\\Selenese\\Command\\' . $command;
      if (class_exists($commandClass)) {
        /** @var Command $command */
        $commandObj = new $commandClass();
        $commandObj->arg1 = ($command == 'open' ? $this->baseUrl : '') . $target;
        $commandObj->arg2 = $value;
        $this->commands[] = $commandObj;
      } else {
        $unknowncmd = new Stub();
        $unknowncmd->command = $command;
        $this->commands[] = $unknowncmd;
      }
    }
  }
  public function setBaseUrl($baseUrl) {
    $this->baseUrl = $baseUrl;
  }
}
