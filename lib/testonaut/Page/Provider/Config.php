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
      $this->path = urldecode($path);

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
        $this->response['originUrl'] = $this->originUrl();
      }

      $this->routing->response($this->response);
      $this->routing->render('config.xsl');
    });
    
    
    
  }


  protected function handelPostData($path, Request $request) {

    if ($request->post['pagesettings'] == 'project' || $request->post['pagesettings'] == 'suite') {
      if (isset($request->post['browser']) && isset($request->post['active'])) {
        $browserSettings = array_merge(array('urls' => $request->post['browser']), array('active' => $request->post['active']));
        $this->browserSettings($browserSettings);
      } else {

        $this->browserSettings(array(
          array(
            'urls' => array(),
            array('active' => false)
          )
        ));
      }
    }

    $this->pageSettings($request->post['pagesettings']);
    $this->screenshotSettings($request->post['screenshotsettings']);

    if (isset($request->post['originUrl'])) {
      $this->originUrl($request->post['originUrl']);
    }
  }

  protected function getContent($path) {
    return array(
      'content' => 'foo',
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