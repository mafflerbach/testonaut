<?php
/**
 *
 * GNU GENERAL PUBLIC LICENSE testonaut Copyright (C) 2015 Afflerbach
 * This program is free software: you can redistribute it and/or modify it under the terms
 * of the GNU General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 */



namespace testonaut\Command\Line;

use testonaut\Page;
use testonaut\Selenese\Command\Command;
use testonaut\Selenese\Command\Stub;

class Test {

  /**
   * @var Command[]
   */
  public $commands = array();
  /**
   * @var \SplFileInfo
   */
  public $file;
  /**
   * @var string
   */
  private  $baseUrl = '';

  /**
   * @param \SplFileInfo $file
   * @return Command[]
   * @throws \InvalidArgumentException
   * @throws \Exception
   */
  public function loadFromSeleneseHtml(\SplFileInfo $file) {
    $this->file = $file;
    if (!file_exists($file->getRealPath())) {
      throw new \InvalidArgumentException("$file does not exist");
    }

    if (!is_readable($file->getRealPath())) {
      throw new \InvalidArgumentException("$file is not readable");
    }

    libxml_use_internal_errors(true);
    $dom = new \DOMDocument;
    $compiledPage = file_get_contents($file->getRealPath());
    $dom->loadHTML($compiledPage);


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

      $commandClass = 'testonaut\\Selenese\\Command\\' . $command;
      $command = str_replace(' ', '', $command);
      
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
