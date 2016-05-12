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
use testonaut\Config;
use testonaut\Search\File;
use testonaut\User;
use testonaut\Utils\Git;

class Edit extends Base implements ProviderInterface {

  private $routing;
  private $response;
  protected $path = '';
  /**
   * @var null| Git
   */
  protected $git = NULL;

  public function connect() {
    $this->routing = new Routing();
    $this->response = array(
      'system' => $this->system()
    );

    $this->routing->route('.*/(.+(?:\..+)*)', function ($path) {
      $path = urldecode($path);
      $request = new Request();

      if (!empty($request->post) && empty($_FILES)) {
        $this->handelPostData($path, $request);
      }

      if (!empty($request->post) && !empty($request->files)) {
        $this->handelUpload($path, $request);
      }

      $this->response['page'] = $this->getContent($path);
      $this->response['menu'] = $this->getMenu($path);
      $this->response['system']['breadcrumb'] = $this->getBreadcrumb($path);

      $this->routing->response($this->response);
      $this->routing->render('edit.xsl');
    });
  }

  protected function handelUpload($path, Request $request) {

    $page = new \testonaut\Page($path);

    $fileDir = Config::getInstance()->Path;
    $domain = Config::getInstance()->domain;
    $locationTo = $fileDir . '/web/files/' . $page->relativePath();

    if (!file_exists($locationTo)) {
      mkdir($locationTo, 0777, true);
    }

    move_uploaded_file($request->files['files']['tmp_name'], $locationTo . '/' . $request->files['files']['name']);

    $message = 'File was successfully uploaded to:';

    $search = new File(Config::getInstance()->Path . '/index.db', 'files', Config::getInstance()->fileRoot);
    $search->updateIndex();
    $image = array(
      'file' => $domain . $request->getSelf() . 'files/' . $page->relativePath().'/'. $request->files['files']['name'],
      'message' => $message
    );

    print(json_encode($image));
    die;
  }

  protected function handelPostData($path, $request) {

    $content = $request->request['pageContent'];

    $page = new \testonaut\Page($path);
    $page->content($content, TRUE);

    $this->git = new Git($page->getProjectRoot());

    if (strpos($path, '.') === FALSE && !$this->git->exists()) {
      $this->git = new Git($page->transCodePath());
      $this->gitInit($page->transCodePath());
    } else {
      $this->git = new Git($page->getProjectRoot());
      $this->gitCommit();
    }


  }

  protected function getContent($path) {
    $page = new \testonaut\Page($path);
    return array(
      'content' => $page->content(),
      'path' => str_replace('/', '', $path)
    );
  }

  protected function gitInit($workingDir) {

    $git = new Git($workingDir);
    if (!$git->exists()) {
      $output = $this->git->init();
    }
  }

  protected function gitCommit() {
    $user = new User();
    $loadedUser = $user->get($_SESSION['testonaut']['userId']);

    $message = "commit " . date('l jS \of F Y h:i:s A');
    $output = $this->git->commit($message, $loadedUser['email'], $loadedUser['displayName']);
    return $output;
  }

}