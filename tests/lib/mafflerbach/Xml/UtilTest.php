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
$loader = require __DIR__ . '/../../../../vendor/autoload.php';
$loader->add('testonaut', __DIR__ . '/../../../../lib/');
$loader->add('mafflerbach', __DIR__ . '/../../../../lib/');

class UtilTest extends PHPUnit_Framework_TestCase {

  public function testNodeCreate() {
    $array = array(
      'test' => 'hallo',
      0 => 'foo',
      'nested' => array(
        'test2' => 'hallo2',
        0 => 'baa'
      )
    );

    $dom = new \mafflerbach\Xml\Util('1.0', 'utf-8');
    $dom->formatOutput = true;
    $dom->nodeCreate($array, null, false, 'data');
    $content = $dom->saveXML();
    $expected = '<?xml version="1.0"?>
                  <data>
                    <test>hallo</test>
                    <item>foo</item>
                    <nested>
                      <test2>hallo2</test2>
                      <item>baa</item>
                    </nested>
                  </data>';

    $this->assertXmlStringEqualsXmlString($expected, $content);

  }

}
