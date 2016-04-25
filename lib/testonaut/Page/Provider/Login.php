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

use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Silex\Provider\FormServiceProvider;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;


/**
 * Class Login
 * @package testonaut\Page\Provider
 */
class Login implements ControllerProviderInterface {
  public function connect(Application $app) {
    $app->register(new FormServiceProvider());
    $app->register(new LocaleServiceProvider());
    $app->register(new TranslationServiceProvider(), array(
      'locale_fallbacks' => array('en'),
    ));

    $page = $app['controllers_factory'];
    $page->match('/', function (Request $request) use ($app) {

      $data = array(
        'username' => '',
        'password' => 'Your password',
      );

      $form = $app['form.factory']->createBuilder('form', $data)
        ->add('username')
        ->add('password', 'password')
        ->getForm();

      $form->handleRequest($request);
      $message = '';

      if ($request->isMethod('POST')) {
        $data = $form->getData();
        $user = new \testonaut\User();

        if($user->validate($data['username'], $data['password'])) {
          return $app->redirect($request->getBaseUrl());
        } else {
          $message = 'User not valid';
        }
      }

      $app['request'] = array(
        'baseUrl' => $request->getBaseUrl(),
        'message' => $message,
      );

      return $app['twig']->render('login.twig', array('form' => $form->createView()));
    });
    return $page;
  }
}