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
use testonaut\Settings\Browser;


class Config extends Base implements ProviderInterface {

  public function connect() {
    $this->routing = new Routing();
    $this->response = array(
      'system' => $this->system()
    );

    $this->routing->route('.*/(.+(?:\..+)*)', function ($path) {
      $this->path = $path;
      $path = urldecode($path);
      $request = new Request();

      if (!empty($request->post)) {
        $this->handelPostData($path, $request);
      }

      $this->response['page'] = $this->getContent($path);
      $this->response['menu'] = $this->getMenu($path);
      $this->response['system']['breadcrumb'] = $this->getBreadcrumb($path);

      $settings = $this->pageSettings();
      $this->response['pagesettings'] = $settings;


      $screenshotSettings = $this->screenshotSettings();
      $this->response['screenshotsettings'] = $screenshotSettings;


      if ($settings['suite'] || $settings['project']) {
        $this->response['browser'] = $this->browserSettings();
      }

      $this->routing->response($this->response);
      $this->routing->render('config.xsl');
    });
  }

  protected function getContent($path) {

    return array('content' => 'foo',
    'path' => $path
  );

  }


  protected function browserSettings($settings = NULL) {
    $pathArray = explode('.', $this->path);
    $bSettings = new Browser($this->path);

    if ($settings != NULL) {
      return $bSettings->setSettings($settings);
    } else {
      return $bSettings->getSettings();
    }
  }

  protected function pageSettings($settings = NULL) {
    $pSettings = new \testonaut\Settings\Page($this->path);
    if ($settings != NULL) {
      return $pSettings->setSettings($settings);
    } else {
      return $pSettings->getSettings();
    }
  }

  protected function screenshotSettings($settings = NULL) {
    $pSettings = new \testonaut\Settings\Page($this->path);
    if ($settings != NULL) {
      return $pSettings->setScreenshotSettings($settings);
    } else {
      return $pSettings->getScreenshotSettings();
    }
  }

  protected function originUrl($originUrl = NULL) {
    $settings = new \testonaut\Settings\Page($this->path);
    if ($originUrl != NULL) {
      return $settings->setOriginUrl($originUrl);
    } else {
      return $settings->getOriginUrl();
    }
  }
}


/**
 * public function connect(Application $app) {
 * $config = $app['controllers_factory'];
 * $config->get('/', function (Request $request, $path) use ($app) {
 * $this->path = $path;
 * $page = new \testonaut\Page($path);
 * $content = $page->content();
 * $crumb = new Breadcrumb($path);
 * $app['crumb'] = $crumb->getBreadcrumb();
 * $app['type'] = $this->pageSettings();
 * if ($app['type']['project'] || $app['type']['suite']) {
 * $app['browser'] = $this->browserSettings();
 * }
 *
 * $app['screenshots'] = $this->screenshotSettings();
 * $app['request'] = array(
 * 'content' => $content,
 * 'path'    => $path,
 * 'originUrl'    => $this->originUrl(),
 * 'baseUrl' => $request->getBaseUrl(),
 * 'mode'    => 'show'
 * );
 *
 * return $app['twig']->render('config.twig');
 * });
 *
 * $config->post('/', function (Request $request, $path) use ($app) {
 * $this->path = $path;
 * $page = new \testonaut\Page($path);
 * $content = $page->content();
 * $browserUrls = $request->request->get('browser');
 * $activeBrowser = $request->request->get('active');
 *
 * $type = $request->request->get('type');
 * $browserSettings = array_merge(array('urls' => $browserUrls), array('active' => $activeBrowser));
 *
 * if ($type == 'project' || $type == 'suite') {
 * if ($this->browserSettings($browserSettings)) {
 * $message = 'Saved';
 * } else {
 * $message = 'Can not save browser config';
 * }
 *
 * $originUrl = $request->request->get('originurl');
 * if ($originUrl != '') {
 * $this->originUrl($originUrl);
 * }
 * }
 *
 * $screenshot = $request->request->get('screenshot');
 * if ($this->screenshotSettings($screenshot)) {
 * $message = 'Saved';
 * } else {
 * $message = 'Can not save page config';
 * }
 * if ($this->pageSettings($type)) {
 * $message = 'Saved';
 * } else {
 * $message = 'Can not save page config';
 * }
 * $crumb = new Breadcrumb($path);
 *
 * $app['crumb'] = $crumb->getBreadcrumb();
 * $app['type'] = $this->pageSettings();
 * if ($app['type']['project'] || $app['type']['suite']) {
 * $app['browser'] = $this->browserSettings();
 * }
 *
 * $app['screenshots'] = $this->screenshotSettings();
 * $app['request'] = array(
 * 'content' => $content,
 * 'path'    => $path,
 * 'baseUrl' => $request->getBaseUrl(),
 * 'mode'    => 'show',
 * 'originUrl'    => $this->originUrl(),
 * 'message' => $message,
 * );
 *
 * return $app['twig']->render('config.twig');
 * })
 * ;
 *
 * return $config;
 * }
 *
 * protected function browserSettings($settings = NULL) {
 * $pathArray = explode('.', $this->path);
 * $bSettings = new Browser($this->path);
 *
 * if ($settings != NULL) {
 * return $bSettings->setSettings($settings);
 * } else {
 * return $bSettings->getSettings();
 * }
 * }
 *
 *
 * protected function pageSettings($settings = NULL) {
 * $pSettings = new \testonaut\Settings\Page($this->path);
 * if ($settings != NULL) {
 * return $pSettings->setSettings($settings);
 * } else {
 * return $pSettings->getSettings();
 * }
 * }
 *
 * protected function screenshotSettings($settings = NULL) {
 * $pSettings = new \testonaut\Settings\Page($this->path);
 * if ($settings != NULL) {
 * return $pSettings->setScreenshotSettings($settings);
 * } else {
 * return $pSettings->getScreenshotSettings();
 * }
 * }
 *
 * protected function originUrl($originUrl = NULL) {
 * $settings = new \testonaut\Settings\Page($this->path);
 * if ($originUrl != NULL) {
 * return $settings->setOriginUrl($originUrl);
 * } else {
 * return $settings->getOriginUrl();
 * }
 * }
 *
 */