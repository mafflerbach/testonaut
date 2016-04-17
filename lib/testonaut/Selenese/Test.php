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



namespace testonaut\Selenese;

use testonaut\Page;
use testonaut\Selenese\Command\Command;
use testonaut\Selenese\Command\Stub;

class Test {

  /**
   * @var Command[]
   */
  public $commands = array();

  /**
   * @var string
   */
  private  $baseUrl = '';

  protected $path = '';



  /**
   * @param string $file
   * @return Command[]
   * @throws \InvalidArgumentException
   * @throws \Exception
   */
  public function loadFromSeleneseHtml(Page $file) {
    if (!file_exists($file->transCodePath())) {
      throw new \InvalidArgumentException("$file does not exist");
    }

    if (!is_readable($file->transCodePath())) {
      throw new \InvalidArgumentException("$file is not readable");
    }

    $this->path = $file->getPath();
    libxml_use_internal_errors(true);
    $dom = new \DOMDocument;
    $compiledPage = $file->getCompiledPage(); 
    
    $dom->loadHTML($compiledPage);
    
    // get the base url
    if ($this->baseUrl == '') {
      if ($dom->getElementsByTagName('link')->length > 0) {
        $this->baseUrl = $dom->getElementsByTagName('link')->item(0)->getAttribute('href');
        $this->baseUrl = rtrim($this->baseUrl, '/');
      }
    }

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

  /**
   * @return string
   */
  public function getPath() {
    return $this->path;
  }


  public function setBaseUrl($baseUrl) {
    $this->baseUrl = $baseUrl;
  }
}
