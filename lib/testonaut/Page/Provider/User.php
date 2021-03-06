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
use Toyota\Component\Ldap\Core\Manager;
use Toyota\Component\Ldap\Platform\Native\Driver;
use Toyota\Component\Ldap\Platform\Native\Search;

/**
 * Class User
 * @package testonaut\Page\Provider
 */
class User extends Base implements ProviderInterface {


  public function connect() {
    $this->routing = new Routing();
    $this->response = array(
      'system' => $this->system()
    );
    $this->response['menu'] = $this->getMenu('', 'user');


    $this->routing->route('.*/(\d)/edit', function ($id) {
      $foo = $this->editUser($this->response, $id);

      $this->response['mode'] = 'edit';

      $this->routing->response($this->response);
      $this->routing->render('user.xsl');
    });


    $this->routing->route('.*/(\d)/inactivate', function ($id) {

      $foo = $this->setStatus($id, false);
      $this->response['mode'] = 'inactivate';

      $this->routing->response($this->response);
      $this->routing->render('user.xsl');
    });


    $this->routing->route('.*/(\d)/delete', function ($id) {

      $foo = $this->deleteUser($id);

      $this->routing->response($this->response);
      $this->routing->render('user.xsl');
    });
    $this->routing->route('.*/(\d)/activate', function ($id) {

      $foo = $this->setStatus($id, true);
      $this->response['mode'] = 'activate';

      $this->routing->response($this->response);
      $this->routing->render('user.xsl');
    });
    $this->routing->route('register', function () {

      $foo = $this->register($this->response);
      $this->response['mode'] = 'register';

      $this->routing->response($this->response);
      $this->routing->render('register.xsl');
    });

    $this->routing->route('', function () {

      $this->response['mode'] = 'list';

      $this->response['user'] = $this->getUserList();

      $this->routing->response($this->response);
      $this->routing->render('user.xsl');
    });


  }

  protected function getUserList() {
    $user = new \testonaut\User();
    return $user->getAll();
  }

  protected function editUser(&$response, $id) {
    $user = new \testonaut\User();
    $userData = $user->get($id);

    $response['userdata'] = array(
      'email' => $userData['email'],
      'displayName' => $userData['displayName'],
      'password' => $userData['password'],
      'group' => $userData['group'],
    );

    $messageBody = "";
    $request = new Request();
    if (!empty($request->post)) {

      $user = new \testonaut\User();

      if (isset($request->post['group'])) {
        $group = 1;
      } else {
        $group = 0;
      }

      if ($user->save($request->post['email'], $request->post['password'], $request->post['displayname'], $group, $id)) {
        $messageBody = "Edit User";
        $result = 'success';
      } else {
        $messageBody = "Can't edit User.";
        $result = 'fail';
      }
      $message = array(
        'result' => $result,
        'message' => $messageBody,
        'messageTitle' => 'Save'
      );

      print(json_encode($message));
      die;
    }

  }

  protected function deleteUser($id) {

    $user = new \testonaut\User();
    $request = new Request();
    $messageBody = "";
    $result = 'fail';

    if (!empty($request->post)) {
      if ($user->delete($id)) {
        $messageBody = "Delete User";
        $result = 'success';
      } else {
        $messageBody = "Can't delete User";
        $result = 'fail';
      }
    }

    $message = array(
      'result' => $result,
      'message' => $messageBody,
      'messageTitle' => 'Save'
    );

    print(json_encode($message));
    die;

  }

  protected function setStatus($id, $bool) {
    $user = new \testonaut\User();

    $request = new Request();
    $messageBody = "";
    $result = 'fail';

    if (!empty($request->post)) {
      if ($user->changeStatus($id, $bool)) {
        $messageBody = "Change User status ";
        $result = 'success';
      } else {
        $messageBody = "Can't change User status";
        $result = 'fail';
      }
    }

    $message = array(
      'result' => $result,
      'message' => $messageBody,
      'messageTitle' => 'Save'
    );

    print(json_encode($message));
    die;

  }


  protected function register(&$response) {

    $request = new Request();
    $messageBody = "";
    $result = 'fail';

    if (!empty($request->post)) {
      $user = new \testonaut\User();

      if (!$user->exist($request->post['email'])) {
        $user->add($request->post['email'], $request->post['password'], $request->post['displayName']);
        $messageBody = "Add User";
        $result = 'success';
      } else {
        $messageBody = "Can't create User. User exists";
        $result = 'fail';
      }

      $response['message'] = $messageBody;

      $this->routing->response($response);
      $this->routing->render('register.xsl');

    }


  }

}