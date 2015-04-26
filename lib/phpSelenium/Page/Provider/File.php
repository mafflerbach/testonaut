<?php
namespace phpSelenium\Page\Provider;

use Symfony\Component\HttpFoundation\Request;
use Silex\Provider\FormServiceProvider;

use Silex\Api\ControllerProviderInterface;
use Silex\Application;

class File implements ControllerProviderInterface {
  public function connect(Application $app) {
    $app->register(new FormServiceProvider());
    $app->register(new Silex\Provider\TranslationServiceProvider(), array(
      'locale_fallbacks' => array('en'),
    ));


    $file = $app['controllers_factory'];

    $file->get('/', function (Request $request) use ($app){
      $form = $app['form.factory']
        ->createBuilder('form')
        ->add('FileUpload', 'file')
        ->getForm();
      $request = $app['request'];
      $message = 'Upload a file';
      if ($request->isMethod('POST')) {

        $form->bind($request);

        if ($form->isValid()) {
          $files = $request->files->get($form->getName());
          /* Make sure that Upload Directory is properly configured and writable */
          $path = __DIR__.'/../web/upload/';
          $filename = $files['FileUpload']->getClientOriginalName();
          $files['FileUpload']->move($path,$filename);
          $message = 'File was successfully uploaded!';
        }
      }
      $response =  $app['twig']->render(
        'index.html.twig',
        array(
          'message' => $message,
          'form' => $form->createView()
        )
      );

      return $response;

    });
    return $file;
  }



}