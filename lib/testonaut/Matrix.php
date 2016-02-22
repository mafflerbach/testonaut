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


namespace testonaut;

/**
 * Class Matrix
 *
 * @package testonaut
 */
class Matrix {

  /**
   * @public Page
   */
  private $page;

  /**
   * @public
   */
  private $browsers;

  /**
   * @param Page $page
   * @param      $browsers
   */
  public function __construct(Page $page, $browsers) {

    $this->page = $page;
    $this->browsers = $browsers;
  }

  /**
   * @return array
   */
  public function read() {
    $db = \testonaut\Config::getInstance()->db;

    $dbInst = $db->getInstance();
    $sql = 'select * from history where path=:path group by browser order by date DESC';
    $summery = array();
    $stm = $dbInst->prepare($sql);
    $stm->bindValue(':path', $this->page->getPath());

    $result = $stm->execute();

    while ($row = $result->fetchArray(SQLITE3_ASSOC)){
      $summery[$row['browser']]['result'] = $row['result'];
      $summery[$row['browser']]['run'] = json_decode($row['run']);
    }

    return $summery;
  }

  /**
   * @param $content
   * @param $browser
   */
  public function writeResult($content, $capabilities) {
    if ($capabilities['platform'] != '') {
      $browser = $capabilities['platform'] ."_";
    } else {
      $browser = "";
    }

    $browser .= $capabilities['browser'] ;

    if ($capabilities['version'] != '') {
      $browser .= "_".$capabilities['version'];
    }

    $db = \testonaut\Config::getInstance()->db;
    $dbInst = $db->getInstance();
    $sql = 'insert into history (browser, date, run, path, filename, result) '
        . 'values (:browser, :date, :run, :path, :filename, :result)';

    $runResult = array();
    $flag = TRUE;

    for ($i = 0; $i < count($content); $i++) {
      $runResult[] = $content[$i]['run'];
      if ($content[$i]['browserResult'] == false) {
        $flag = FALSE;
      }
    }

    $dbContent = array(
        'run' => json_encode($runResult),
        'browserResult' => $flag
    );

    $pathDir = $this->page->getResultPath();

    if (!file_exists($pathDir)) {
      mkdir($pathDir, 0775, TRUE);
    }

    $date = new \DateTime();
    $isoDate = $date->format(\DateTime::ISO8601);
    $pagePath = $this->page->getPath();
    $fileName = 'result_' . $browser . '_' . $date->format('Y-m-d_H-i-s');
    $stm = $dbInst->prepare($sql);
    $stm->bindParam(':browser', $browser);
    $stm->bindParam(':date', $isoDate);
    $stm->bindParam(':run',$dbContent['run']);
    $stm->bindParam(':path', $pagePath);
    $stm->bindParam(':filename',$fileName);
    $stm->bindParam(':result',$dbContent['browserResult']);
    $stm->execute();

    file_put_contents($pathDir . '/' . $fileName, json_encode($content));
  }

}
