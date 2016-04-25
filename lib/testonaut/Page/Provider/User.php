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

use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;
use Toyota\Component\Ldap\Core\Manager;
use Toyota\Component\Ldap\Platform\Native\Driver;
use Toyota\Component\Ldap\Platform\Native\Search;

/**
 * Class User
 * @package testonaut\Page\Provider
 */
class User implements ControllerProviderInterface {
  public function connect(Application $app) {

    $page = $app['controllers_factory'];
    /**
     * List User
     */
    $page->get('/', function (Request $request) use ($app) {
      return $this->getUserList($request, $app);
    });

    $page->match('/{id}/edit/', function (Request $request, $id) use ($app) {
      $foo = $this->editUser($request, $app, $id);
      return $foo;
    });

    $page->match('/{id}/inactivate/', function (Request $request, $id) use ($app) {
      $foo = $this->setStatus($request, $app, $id, false);
      return $foo;
    });

    $page->match('/{id}/activate/', function (Request $request, $id) use ($app) {
      $foo = $this->setStatus($request, $app, $id, true);
      return $foo;
    });

    $page->match('/{id}/delete/', function (Request $request, $id) use ($app) {
      $foo = $this->deleteUser($request, $app, $id);
      return $foo;
    });

    $page->match('/register/', function (Request $request) use ($app) {
      return $this->register($request, $app);
    });
    return $page;
  }

  protected function getUserList($request, $app) {
    $user = new \testonaut\User();

    $app['request'] = array(
      'baseUrl' => $request->getBaseUrl(),
      'mode' => 'show',
      'content' => '',
      'userList' => $user->getAll()
    );

    return $app['twig']->render('userList.twig');
  }

  protected function editUser($request, $app, $id) {

    $user = new \testonaut\User();
    $userData = $user->get($id);

    $data = array(
      'email' => $userData['email'],
      'displayName' => $userData['displayName'],
      'password' => $userData['password'],
    );

    $form = $app['form.factory']->createBuilder('form', $data)
      ->add('email')
      ->add('displayName')
      ->add('password', 'password')
      ->getForm();

    $form->handleRequest($request);

    $message = "";
    if ($request->isMethod('POST')) {
      $data = $form->getData();
      
      $user = new \testonaut\User();

      if ($user->save($data['email'], $data['password'], $data['displayName'], $userData['id'])) {
        $message = "Edit User";
      } else {
        $message = "Can't edit User.";
      }
    }

    $app['request'] = array(
      'message' => $message,
      'baseUrl' => $request->getBaseUrl(),
      'mode' => 'edit'
    );

    return $app['twig']->render('user.twig', array('form' => $form->createView()));
  }


  protected function deleteUser($request, $app, $id) {

    $user = new \testonaut\User();
    $userData = $user->get($id);

    $message = "";
    if ($request->isMethod('POST')) {
      if ($user->delete($id)) {
        $message = "Delete User";
      } else {
        $message = "Can't delete User.";
      }
    }

    $app['request'] = array(
      'message' => $message,
      'baseUrl' => $request->getBaseUrl(),
      'mode' => 'delete',
      'displayName' => $userData['displayName']
    );

    return $app['twig']->render('user.twig');
  }
  protected function setStatus($request, $app, $id, $bool) {

    $user = new \testonaut\User();
    $userData = $user->get($id);

    $message = "";
    if ($request->isMethod('POST')) {
      if ($user->changeStatus($id, $bool)) {
        $message = "Change User status ";
      } else {
        $message = "Can't change User status";
      }
    }

    if ($bool) {
      $mode = 'activate';
    } else {
      $mode = 'inactivate';
    }
    $app['request'] = array(
      'message' => $message,
      'baseUrl' => $request->getBaseUrl(),
      'mode' => $mode,
      'displayName' => $userData['displayName']
    );

    return $app['twig']->render('user.twig');
  }


  protected function register($request, $app) {
    $data = array(
      'email' => 'Your email',
      'password' => 'Your password',
      'displayName' => '',
    );

    $form = $app['form.factory']->createBuilder('form', $data)
      ->add('email')
      ->add('displayName')
      ->add('password', 'password')
      ->getForm();

    $form->handleRequest($request);

    $message = "";
    if ($request->isMethod('POST')) {
      $data = $form->getData();

      $user = new \testonaut\User();

      if (!$user->exist($data['email'])) {
        $user->add($data['email'], $data['password'], $data['displayName']);
        $message = "Add User";
      } else {
        $message = "Can't create User. User exists";
      }
    }

    $app['request'] = array(
      'baseUrl' => $request->getBaseUrl(),
      'message' => $message,
    );

    return $app['twig']->render('register.twig', array('form' => $form->createView()));
  }
  
}