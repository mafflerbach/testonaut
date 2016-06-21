<?php
/**
 * Created by PhpStorm.
 * User: maren
 * Date: 01.06.2015
 * Time: 15:33
 */

$loader = require __DIR__ . '/../../../../vendor/autoload.php';
$loader->add('testonaut', __DIR__ . '/../../../../lib/');

class BrowserTest extends PHPUnit_Framework_TestCase {

  public static function setUpBeforeClass() {

    $config = \testonaut\Config::getInstance();
    $seleniumAddress = 'http://localhost:4444';
    $config->define('wikiPath', 'C:/xampp/htdocs/testonaut/tests/root');
    $config->define('seleniumAddress', $seleniumAddress);
    $config->define('Path', dirname(dirname(__FILE__)));
    $config->define('seleniumConsole', $seleniumAddress . '/grid/console');
    $config->define('seleniumAddress', $seleniumAddress);
    $config->define('Cache', FALSE);
  }

  /**
   * @covers \testonaut\Settings\Browser::getSettings()
   */
  public function testGetSettings() {

    $browserSettings = new BrowserTestProxy('SettingsBrowserTest.testGetSettings');

    $expected = Array(
      array(
        'platform'         => 'VISTA',
        'seleniumProtocol' => 'WebDriver',
        'browserName'      => 'firefox',
        'maxInstances'     => 5,
        'version'          => '',
      ),
      array(
        'platform'         => 'VISTA',
        'seleniumProtocol' => 'WebDriver',
        'browserName'      => 'chrome',
        'maxInstances'     => 5,
        'version'          => '',
      ),
      array(
        'platform'         => 'VISTA',
        'seleniumProtocol' => 'WebDriver',
        'browserName'      => 'internet explorer',
        'maxInstances'     => 5,
        'version'          => 10,
      ),
      array(
        'platform'         => 'VISTA',
        'seleniumProtocol' => 'WebDriver',
        'browserName'      => 'internet explorer',
        'maxInstances'     => 5,
        'version'          => 9,
      ),
      array(
        'platform'         => 'VISTA',
        'seleniumProtocol' => 'WebDriver',
        'browserName'      => 'internet explorer',
        'maxInstances'     => 5,
        'version'          => 11,
      )

    );
    $this->assertEquals($expected, $browserSettings->getSettings());
  }
}

class BrowserTestProxy extends \testonaut\Settings\Browser {

  public function getBrowserList() {

    return array(
      array(
        'platform'         => 'VISTA',
        'seleniumProtocol' => 'WebDriver',
        'browserName'      => 'firefox',
        'maxInstances'     => 5,
        'version'          => ''
      ),

      array(
        'platform'         => 'VISTA',
        'seleniumProtocol' => 'WebDriver',
        'browserName'      => 'chrome',
        'maxInstances'     => 5,
        'version'          => '',
      ),

      array(
        'platform'         => 'VISTA',
        'seleniumProtocol' => 'WebDriver',
        'browserName'      => 'internet explorer',
        'maxInstances'     => 5,
        'version'          => 10,
      ),
      array(
        'platform'         => 'VISTA',
        'seleniumProtocol' => 'WebDriver',
        'browserName'      => 'internet explorer',
        'maxInstances'     => 5,
        'version'          => 9,
      ),
      array(
        'platform'         => 'VISTA',
        'seleniumProtocol' => 'WebDriver',
        'browserName'      => 'internet explorer',
        'maxInstances'     => 5,
        'version'          => 11,
      )
    );
  }

}