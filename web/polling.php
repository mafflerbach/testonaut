<?php
/**
 * Created by PhpStorm.
 * User: maren
 * Date: 17.04.2016
 * Time: 20:22
 */


$file = "../tmp/".urldecode($_REQUEST['path']);

if (file_exists($file)) {
  print(file_get_contents($file));
}
