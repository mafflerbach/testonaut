<?php
/**
 *
 * GNU GENERAL PUBLIC LICENSE testonautterm Copyright (C) 2016 Afflerbach
 * This program is free software: you can redistribute it and/or modify it under the terms 
 * of the GNU General Public License as published by the Free Software Foundation, 
 * either version 3 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
 * See the GNU General Public License for more details.
 *
 */

$loader = require __DIR__ . '/vendor/autoload.php';
$loader->add('testonaut', __DIR__ . '/lib/');

/**
 * arguments -d [directory]
 *           -i [imagedirectory]
 *           -c [configFile][testonaut.xml]
 *           -s [seleniumhub][http://localhost:4444/wd/hub]
 *
 */

$yaml ='{
  "testonaut": {
    "browsers": [
      {
      "name": "chrome"
      }
    ],
    "config": {
      "imageDir":"images",
      "testDir": "tests2",
      "hub" : "http://localhost:4444/wd/hub"
    }
  }
}
';

$args = testonaut\Command\Line::parseArgs($_SERVER['argv']);

if (isset($args['c'])) {
  $conf = json_decode(file_get_contents($args['c']), true);
} else {
  $conf = json_decode($yaml, true);
}


$browsers =  $conf['testonaut']['browsers'];

if (!isset($args['i']) && isset($conf['testonaut']['config']['imageDir'])) {
  $args['i'] = $conf['testonaut']['config']['imageDir'];
}

if (!isset($args['d'])) {
  $args['d'] = $conf['testonaut']['config']['testDir'];
}

if (isset($args['s'])) {
  $args['s'] = $conf['testonaut']['config']['hub'];
}

for($i = 0; $i < count($browsers); $i++) {
  $runner = new \testonaut\Command\Line\Runner($args, $browsers[$i]);
  $runner->run();
}


?>