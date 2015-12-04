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

require '../lib/testonaut/Utils/Git.php';


if (!isset($_POST['function'])) {
  die("You must specify a function");
}
if (!function_exists($_POST['function'])) {
  die("Function not found");
} else {
  call_user_func($_POST['function']);
}

function getStatus() {

  $output = array(
    'composer'           => file_exists('composer.phar'),
    'composer_extracted' => file_exists('extracted'),
    'installer'          => file_exists('installer.php'),
  );
  header("Content-Type: text/json; charset=utf-8");
  echo json_encode($output);
}

function downloadComposer() {

  $installerURL = 'https://getcomposer.org/installer';
  $installerFile = 'installer.php';
  if (!file_exists($installerFile)) {
    echo 'Downloading ' . $installerURL . PHP_EOL;
    flush();
    $ch = curl_init($installerURL);
    curl_setopt($ch, CURLOPT_CAINFO, __DIR__ . '/cacert.pem');
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
    curl_setopt($ch, CURLOPT_FILE, fopen($installerFile, 'w+'));
    if (curl_exec($ch)) {
      echo 'Success downloading ' . $installerURL . PHP_EOL;
    } else {
      echo 'Error downloading ' . $installerURL . PHP_EOL;
      die();
    }
    flush();
  }
  echo 'Installer found : ' . $installerFile . PHP_EOL;
  echo 'Starting installation...' . PHP_EOL;
  flush();
  $argv = array();
  include $installerFile;
  flush();
}

function extractComposer() {

  if (file_exists('composer.phar')) {
    echo 'Extracting composer.phar ...' . PHP_EOL;
    flush();
    $composer = new Phar('composer.phar');
    $composer->extractTo('extracted');
    echo 'Extraction complete.' . PHP_EOL;
  } else {
    echo 'composer.phar does not exist';
  }
}

function getWorkingDir() {
  $dir = str_replace('\\', '/', __DIR__);
  return str_replace('installer', '', $dir);
}

function getBashDir() {
  $dir = str_replace('\\', '/', __DIR__);
  return str_replace('installer', 'bash', $dir);
}

function gitPull() {

  $username = $_POST['username'];
  $password = $_POST['password'];

  $command = 'git pull';

  exec($command);
}

function command() {
  $outputHandler = fopen('php://output', 'w');
  command:
  set_time_limit(-1);
  putenv('COMPOSER_HOME=' . __DIR__ . '/extracted/bin/composer');

  $allowedCommand = array(
    'install',
    'update',
    'dry-run'
  );
  if (in_array($_POST['command'], $allowedCommand)) {
    if (file_exists('extracted')) {
      $command = $_POST['command'];
      require_once(__DIR__ . '/extracted/vendor/autoload.php');
      $param = '';
      if ($_POST['dryrun'] == 'true') {
        $param = ' --dry-run';
      }
      $outputRes = '';
      if ($command == 'update') {
          $git = new testonaut\Utils\Git(getWorkingDir(), getBashDir());
          $outputRes = $git->pull();
      }
         fwrite($outputHandler, $outputRes);
      
      
      $path = getWorkingDir();
      
      $input = new Symfony\Component\Console\Input\StringInput($command . ' -vvv -d ' . $path . $param);
      $output = new Symfony\Component\Console\Output\StreamOutput($outputHandler);
      $app = new Composer\Console\Application();
      $app->run($input, $output);
    } else {
      echo 'Composer not extracted.';
      extractComposer();
      goto command;
    }
  } else {
    print('Command not allowd');
  }

}

?>
