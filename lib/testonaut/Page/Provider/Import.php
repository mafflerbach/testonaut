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


namespace testonaut\Page\Provider;

use mafflerbach\Http\Request;
use mafflerbach\Page\ProviderInterface;
use mafflerbach\Routing;


class Import extends Base implements ProviderInterface {

  public function connect() {
    $this->routing = new Routing();
    $this->response = array(
      'system' => $this->system()
    );


    $this->routing->route('.*/(.*)', function ($path) {
      $path = urldecode($path);
      $this->response['path'] = $path;

      $request = new Request();

      if (!empty($request->post) && !empty($request->files)) {
        $this->response = $this->importFile($request, $path);
      }

      $this->routing->response($this->response);

      print(json_encode($this->response)); die;
    });


  }

  protected function importFile($files, $page) {

    $fileDir = \testonaut\Config::getInstance()->Path;
    $filename = $files->files['files']['name'];
    $tmppath = md5($filename);
    $locationTo = $fileDir . '/tmp/' . $tmppath;

    mkdir($locationTo);
    move_uploaded_file($files->files['files']['tmp_name'], $locationTo . '/' . $files->files['files']['name']);

    $message = 'File was successfully uploaded!';

    $page = new \testonaut\Page($page);

    $import = new \testonaut\File\Import($locationTo, $filename, $page);
    try {
      $import->doImport();
    } catch (\Exception $e) {
      $message = $e->getMessage();
    }

    return
      array(
      'message' => $message,
      'file' => $filename
    );
  }

  protected function buildRespponse() {

  }

}