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


namespace lib\testonaut\Generate;

use testonaut\Generate\Toc;

$loader = require __DIR__ . '/../../../../vendor/autoload.php';
$loader->add('testonaut', __DIR__ . '/../../../../lib/');
$loader->add('mafflerbach', __DIR__ . '/../../../../lib/');


class TocTest extends \PHPUnit_Framework_TestCase {


  public function testRunDir() {
    $toc = new Toc('..\..\..\root');
    $toc = $toc->runDir()->saveXML();
    $expected = '<?xml version="1.0"?>
<toc>
  <item name="File">
    <item name="importTest"/>
  </item>
  <item name="SettingsBrowserTest">
    <item name="testGetSettings"/>
  </item>
  <item name="SettingsPageTest">
    <item name="testGetScreenshotSettings"/>
    <item name="testGetSettings"/>
    <item name="testGetType"/>
    <item name="testSetScreenshotSettings"/>
    <item name="testSetSettings"/>
  </item>
</toc>';
    $this->assertXmlStringEqualsXmlString($expected, $toc);
  }
}
