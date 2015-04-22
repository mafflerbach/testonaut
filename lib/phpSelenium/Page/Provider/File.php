<?php
namespace phpSelenium\Page\Provider;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class File extends Base implements ControllerProviderInterface {

  private $path;

  public function connect(Application $app) {

    $config = $app['controllers_factory'];

    $app->match('/upload/{path}', function (Request $request) use ($app){
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
          $path = __DIR__.'/../web/files/';
          $filename = $files['FileUpload']->getClientOriginalName();
          $files['FileUpload']->move($path,$filename);
          $message = 'File was successfully uploaded!';
        }
      }
      $response =  $app['twig']->render(
        'upload.twig',
        array(
          'message' => $message,
          'form' => $form->createView()
        )
      );

      return $response;

    }, 'GET|POST');

  }

}