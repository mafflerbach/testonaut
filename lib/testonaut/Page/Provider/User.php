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
      $foo = $app['twig']->render('userList.twig');
      return $foo;
    });

    $page->get('/{id}', function (Request $request, $id) use ($app) {
      $foo = $app['twig']->render('user.twig');
      return $foo;
    });

    $page->get('/{id}/edit', function (Request $request, $id) use ($app) {
      $foo = $app['twig']->render('user.twig');
      return $foo;
    });

    $page->match('/register/', function (Request $request) use ($app) {

      return $this->register($request, $app);

    });

    $page->get('/logout', function (Request $request) use ($app) {
      $foo = $app['twig']->render('logout.twig');
      return $foo;
    });
    return $page;
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

      if (!$user->exist($data['email'])){
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