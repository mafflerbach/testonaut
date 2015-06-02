<?php
/**
 * Created by PhpStorm.
 * User: maren
 * Date: 01.06.2015
 * Time: 15:33
 */

$loader = require __DIR__.'/../../../../vendor/autoload.php';
$loader->add('testonaut', __DIR__.'/../../../../lib/');

class BrowserTest extends PHPUnit_Framework_TestCase {

}


class BrowserTestProxy extends \testonaut\Settings\Browser{

  protected function getBrowserList() {

  }

}