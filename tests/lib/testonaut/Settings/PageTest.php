<?php
/**
 * Created by PhpStorm.
 * User: maren
 * Date: 31.05.2015
 * Time: 22:11
 */


$loader = require __DIR__.'/../../../../vendor/autoload.php';
$loader->add('testonaut', __DIR__.'/../../../../lib/');

class PageTest extends PHPUnit_Framework_TestCase {


  public static function setUpBeforeClass() {
    $config = \testonaut\Config::getInstance();
    $config->define('wikiPath',  'E:/xampp/htdocs/testonaut/tests/root');
  }
  /**
   * @covers \testonaut\Settings\Page::getScreenshotSettings()
   */
  public function testGetScreenshotSettings() {
    $pageSettings = new testonaut\Settings\Page('SettingsPageTest.testGetScreenshotSettings');
    $settings = $pageSettings->getScreenshotSettings();

    $this->assertFalse($settings['none']);
    $this->assertTrue($settings['step']);
    $this->assertFalse($settings['test']);

  }
  /**
   * @covers \testonaut\Settings\Page::getSettings()
   */
  public function testGetSettings() {
    $pageSettings = new testonaut\Settings\Page('SettingsPageTest.testGetSettings');
    $settings = $pageSettings->getSettings();

    $this->assertFalse($settings['static']);
    $this->assertFalse($settings['test']);
    $this->assertFalse($settings['project']);
    $this->assertTrue($settings['suite']);
  }

  /**
   * @covers \testonaut\Settings\Page::getSettings()
   */
  public function testGetSettingsDefault() {
    $pageSettings = new testonaut\Settings\Page('SettingsPageTest.NonExists');
    $settings = $pageSettings->getSettings();

    $this->assertTrue($settings['static']);
    $this->assertFalse($settings['suite']);
    $this->assertFalse($settings['test']);
    $this->assertFalse($settings['project']);
  }
  /**
   * @covers \testonaut\Settings\Page::getScreenshotSettings()
   */
  public function testGetScreenshotSettingsDefault() {
    $pageSettings = new testonaut\Settings\Page('SettingsPageTest.NonExists');
    $settings = $pageSettings->getScreenshotSettings();

    $this->assertTrue($settings['none']);
    $this->assertFalse($settings['step']);
    $this->assertFalse($settings['test']);
  }

  /**
   * @covers \testonaut\Settings\Page::setSettings()
   * @expectedException \Exception
   */
  public function testSetSettingsException() {
    $pageSettings = new testonaut\Settings\Page('SettingsPageTest.testSetSettings');
    $pageSettings->setSettings('foopa');
  }

  /**
   * @covers \testonaut\Settings\Page::setScreenshotSettings()
   * @expectedException \Exception
   */
  public function testSetScreenshotSettingsException() {
    $pageSettings = new testonaut\Settings\Page('SettingsPageTest.testSetSettings');
    $pageSettings->setScreenshotSettings('foopa');
  }
  /**
   * @covers \testonaut\Settings\Page::setSettings()
   */
  public function testSetSettings() {
    $pageSettings = new testonaut\Settings\Page('SettingsPageTest.testSetSettings');
    $settings = $pageSettings->getSettings();

    $this->assertTrue($settings['static']);
    $this->assertFalse($settings['test']);
    $this->assertFalse($settings['suite']);
    $this->assertFalse($settings['project']);

    $pageSettings->setSettings('test');

    $settings = $pageSettings->getSettings();
    $this->assertTrue($settings['test']);
    $this->assertFalse($settings['static']);
    $this->assertFalse($settings['suite']);
    $this->assertFalse($settings['project']);

  }
  /**
   * @covers \testonaut\Settings\Page::getScreenshotSettings()
   */
  public function testSetScreenshotSettings() {
    $pageSettings = new testonaut\Settings\Page('SettingsPageTest.testSetScreenshotSettings');
    $settings = $pageSettings->getScreenshotSettings();
    $this->assertTrue($settings['step']);
    $this->assertFalse($settings['test']);
    $this->assertFalse($settings['none']);

    $pageSettings->setScreenshotSettings('none');
    $settings = $pageSettings->getScreenshotSettings();
    $this->assertTrue($settings['none']);
    $this->assertFalse($settings['step']);
    $this->assertFalse($settings['test']);

  }

  /**
   * @covers \testonaut\Settings\Page::getType()
   */
  public function testGetType() {
    $pageSettings = new testonaut\Settings\Page('SettingsPageTest.testGetType');
    $settings = $pageSettings->getType();
    $this->assertEquals('test', $settings);
  }


  public function tearDown() {
    $pageSettings = new testonaut\Settings\Page('SettingsPageTest.testSetSettings');
    $pageSettings->setSettings('static');

    $pageSettings = new testonaut\Settings\Page('SettingsPageTest.testSetScreenshotSettings');
    $pageSettings->setScreenshotSettings('step');

    $config = \testonaut\Config::getInstance();
    $config->define('wikiPath',  'E:/xampp/htdocs/testonaut/tests/root');
  }
}
 