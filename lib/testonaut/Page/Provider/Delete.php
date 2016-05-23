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


class Delete extends Base implements ProviderInterface {


  public function connect() {
    $this->routing = new Routing();
    $this->response = array(
      'system' => $this->system()
    );


    $this->routing->route('.*/(.*)/do$', function ($path) {
      $path = urldecode($path);

      $page = new \testonaut\Page($path);
      $deleted = $page->delete();

      if ($deleted) {
        $result = 'success';
        $messageBody = 'delete';
      } else {
        $result = 'fail';
        $messageBody = 'can not delete';
      }

      $message = array(
        'result' => $result,
        'message' => $messageBody,
        'messageTitle' => 'Delete Page'
      );

      print(json_encode($message));
      die;

    });

    $this->routing->route('.*/(.*)$', function ($path) {
      $path = urldecode($path);
      $request = new Request();

      if (!empty($request->post)) {
        $this->handelPostData($path, $request);
      } else {

        $message = array(
          'question' => array(
            'content' => 'Are you sure that you want to delete <strong>' . $path . '</strong>? 
            This also applies to all subdirectories',
            'title' => 'Delete Page'
          )
        );

        $this->response = $message;
        $this->response['path'] = $path;
        $this->response;
      }
      print(json_encode($this->response));
      die;
    });
  }
}