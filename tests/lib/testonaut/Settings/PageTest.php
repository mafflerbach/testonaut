<?php
/**
 * Created by PhpStorm.
 * User: maren
 * Date: 31.05.2015
 * Time: 22:11
 */

include_once('../../../../lib/testonaut/Config.php');
include_once('../../../../lib/testonaut/Page.php');
include_once('../../../../lib/testonaut/Settings/Page.php');



class PageTest extends PHPUnit_Framework_TestCase {

  public function setUp() {
    $config = \testonaut\Config::getInstance();
    $config->define('wikiPath',  'E:/xampp/htdocs/testonaut/tests/root');
  }

  public function testGetScreenshotSettings() {
    $pageSettings = new testonaut\Settings\Page('SettingsPageTest.testGetScreenshotSettings');
    $settings = $pageSettings->getScreenshotSettings();

    $this->assertFalse($settings['none']);
    $this->assertTrue($settings['step']);
    $this->assertFalse($settings['test']);

  }

  public function testGetScreenshotSettingsDefault() {
    $pageSettings = new testonaut\Settings\Page('SettingsPageTest.baa2');
    $settings = $pageSettings->getScreenshotSettings();

    $this->assertTrue($settings['none']);
    $this->assertFalse($settings['step']);
    $this->assertFalse($settings['test']);
  }

  /**
   * @expectedException \Exception
   */
  public function testSetSettingsException() {
    $pageSettings = new testonaut\Settings\Page('SettingsPageTest.testSetSettings');
    $pageSettings->setSettings('foopa');
  }

  /**
   * @expectedException \Exception
   */
  public function testSetScreenshotSettingsException() {
    $pageSettings = new testonaut\Settings\Page('SettingsPageTest.testSetSettings');
    $pageSettings->setScreenshotSettings('foopa');
  }

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

  public function tearDown() {
    $pageSettings = new testonaut\Settings\Page('SettingsPageTest.testSetSettings');
    $pageSettings->setSettings('static');

    $pageSettings = new testonaut\Settings\Page('SettingsPageTest.testSetScreenshotSettings');
    $pageSettings->setScreenshotSettings('step');
  }


}
 