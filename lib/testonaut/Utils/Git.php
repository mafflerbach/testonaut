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

namespace testonaut\Utils;

class Git {

  private $gitDir;
  private $bashDir;

  /**
   *
   * @param type $dir
   */
  public function __construct($dir) {
    $this->gitDir = $dir;

  }

  /**
   *
   * @param string $message
   * @param string $email
   * @param string $name
   * @return type
   */
  public function commit($message = '', $email = '', $name = '') {
    if ($message == '') {
      $message = "'" . date('l jS \of F Y h:i:s A') . "'";
    }

    if (!$this->existBaseConfig()) {
      $command = 'cd ' . escapeshellarg($this->gitDir) . '; 
                  git config user.email ' . escapeshellarg($email) . '; 
                  git config user.name ' . escapeshellarg($name) . ';
                  git add .; 
                  git commit -m ' . escapeshellarg($message);
    } else {
      $command = 'cd ' . escapeshellarg($this->gitDir) . '; 
                  git add .;git commit -m ' . escapeshellarg($message);
    }

    exec($command, $output);

    $outputStr = $this->getTerminalOutput($output, 'Executing Git commit');
    return $outputStr;
  }

  /**
   *
   * @return type
   */
  public function init() {
    $command = "git init " . escapeshellarg($this->gitDir);
    exec($command, $output);
    $outputStr = $this->getTerminalOutput($output, 'Executing Git init');
    return $outputStr;
  }

  /**
   *
   * @return type
   */
  public function log() {
    $command = "cd " .escapeshellarg($this->gitDir) ."; git log --all --pretty=format:'%h^%cn^%s^%cr' --abbrev-commit --date=relative";
    exec($command, $output);

    $result = array();
    for ($i = 0; $i < count($output); $i++) {
      $result[] =  explode('^', $output[$i]);
    }

    $foo = array(
      array('9db797c', 'Maren Afflerbach' , 'commit Monday 25th of April 2016 01:54:42 PM' , '20 hours ago' ),
      array( 'f8b7079' , 'Andy Bergin' , 'commit Monday 25th of April 2016 01:35:43 PM' , '20 hours ago' ),
      array( 'd79c5a2' , 'Maren Afflerbach' , 'commit Saturday 23rd of April 2016 11:39:10 PM' , '2 days ago' ),
      array( '459e12d' , 'Maren Afflerbach', 'commit Saturday 23rd of April 2016 10:22:28 PM' , '2 days ago' ),
      array( '3224284' , 'Maren Afflerbach', 'testcommit', '4 days ago' ),
      array( 'edf8dbf' , 'Maren Afflerbach' , 'testcommit' , '4 days ago' ));

    //print_r($result);

    return $result;
  }

  /**
   *
   * @return type
   */
  public function config() {
    $command = 'cd ' . escapeshellarg($this->gitDir) . '; git config --get user.name';
    exec($command, $output);
    return $output;
  }

  /**
   *
   * @param type $revision
   * @return type
   */
  public function revert($revision) {
    $command = 'cd ' . escapeshellarg($this->gitDir) . '; git checkout ' .$revision ;
    exec($command, $output);
    $outputStr = $this->getTerminalOutput($output, 'Executing Git revert to ' . $revision);
    return $outputStr;
  }

  /**
   * @return string
   */
  public function pull() {
    $command = $this->bashDir . '/gitWrapper.sh pull ' . escapeshellarg($this->gitDir);
    exec($command, $output);
    $outputStr = $this->getTerminalOutput($output, 'Executing Git Pull');
    return $outputStr;
  }

  /**
   * @param $rev1
   * @param $rev2
   * @return array
   */
  public function diff($rev1, $rev2, $path) {
    $command = 'cd ' . escapeshellarg($this->gitDir) . '; git diff --word-diff -U1 '. $rev1 . ' ' . $rev2 .' '. escapeshellarg($path);

    $output = shell_exec($command);
    $diff = new Diff($output);
    $content = $diff->buildDiff(false);

    return $content;
  }

  private function getTerminalOutput($output, $barMessage) {
    $outputStr = $this->getDecoratedBarString("Starting " . $barMessage);
    $outputStr .= $this->outputArrayToString($output);
    $outputStr .= $this->getDecoratedBarString($barMessage . ' Ended');
    return $outputStr;
  }

  private function outputArrayToString($output) {
    $outputStr = '';
    for ($i = 0; $i < count($output); $i++) {
      $outputStr .= $output[$i] . "\n";
    }
    return $outputStr;
  }

  private function getDecoratedBarString($content) {
    return "\n" . '===================================================================
                ' . $content . "\n" . '===================================================================' . "\n\n";
  }

  public function exists() {
    return file_exists($this->gitDir . '/.git');
  }

  public function existBaseConfig() {
    $command = 'cd ' . escapeshellarg($this->gitDir) . '; git config --get user.name; git config --get user.email';
    exec($command, $output);
    if (empty($output[0]) || empty($output[1])) {
      return false;
    }
    return true;
  }

}
