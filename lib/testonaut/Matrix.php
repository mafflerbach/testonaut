<?php

/**
 * Created by PhpStorm.
 * User: maren
 * Date: 19.04.2015
 * Time: 21:25
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
}
