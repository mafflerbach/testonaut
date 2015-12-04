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

use testonaut\Page;
use testonaut\Search;
use testonaut\Config;
use Silex\Provider\LocaleServiceProvider;
use Silex\Provider\TranslationServiceProvider;
use Symfony\Component\HttpFoundation\Request;
use Silex\Provider\FormServiceProvider;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class File implements ControllerProviderInterface {
  public function connect(Application $app) {
    $app->register(new FormServiceProvider());
    $app->register(new LocaleServiceProvider());
    $app->register(new TranslationServiceProvider(), array(
      'locale_fallbacks' => array('en'),
    ))
    ;
    $file = $app['controllers_factory'];
    $file->match('/', function (Request $request, $path) use ($app) {
      $page = new Page($path);
      $this->request = $request;
      $form = $app['form.factory']->createBuilder('form')
        ->add('FileUpload', 'file')
        ->getForm()
      ;
      $image = '';
      $message = 'Upload a file';
      if (isset($app['request'])) {
        $request = $app['request'];
      } else {
      }
      if ($request->isMethod('POST')) {
        $form->bind($request);
        if ($form->isValid()) {
          $files = $request->files->get($form->getName());
          $fileDir = Config::getInstance()->Path;
          $domain = Config::getInstance()->domain;
          $locationTo = $fileDir . '/web/files/' . $page->relativePath();
          $filename = $files['FileUpload']->getClientOriginalName();
          $files['FileUpload']->move($locationTo, $filename);
          $message = 'File was successfully uploaded!';
          $search = new Search\File(Config::getInstance()->Path . '/index.db', 'files', Config::getInstance()->fileRoot);
          $search->updateIndex();
          $image = $domain . $request->getBaseUrl() . '/files/' . $page->relativePath() . '/' . $filename;
        } else {
        }
      }
      $app['request'] = array(
        'path'    => $path,
        'baseUrl' => $request->getBaseUrl(),
        'image'   => $image,
      );

      $response = $app['twig']->render('upload.twig', array(
        'message' => $message,
        'form'    => $form->createView(),
        'path'    => $path,
        'baseUrl' => $request->getBaseUrl(),
      ))
      ;

      return $response;
    }, 'GET|POST');
    $file->get('/search/{term}', function (Request $request, $term) use ($app) {
      $search = new Search\File(Config::getInstance()->Path . '/index.db', 'files', Config::getInstance()->fileRoot);

      return $app->json($search->search($term), 201);
    });

    return $file;
  }
}