<?php
/**
 * Created by PhpStorm.
 * User: maren
 * Date: 01.06.2015
 * Time: 15:33
 */

include_once('../../../../lib/testonaut/Config.php');
include_once('../../../../lib/testonaut/Page.php');
include_once('../../../../lib/testonaut/Settings/Browser.php');

class BrowserTest extends PHPUnit_Framework_TestCase {

}


class BrowserTestProxy extends \testonaut\Settings\Browser{

  protected function getBrowserList() {

  }

}