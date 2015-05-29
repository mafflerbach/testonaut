<?php
namespace testonaut\Page\Provider;

use testonaut\Page;
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
    ));

    $file = $app['controllers_factory'];

    $file->match('/', function (Request $request, $path) use ($app) {
      $page = new Page($path);

      $this->request = $request;
      $form = $app['form.factory']->createBuilder('form')->add('FileUpload', 'file')->getForm();
      if (isset($app['request'])) {
        $request = $app['request'];
      } else {

      }
      $image = '';
      $message = 'Upload a file';
      if ($request->isMethod('POST')) {
        $form->bind($request);

        if ($form->isValid()) {
          $files = $request->files->get($form->getName());
          $fileDir = \testonaut\Config::getInstance()->Path;
          $domain = \testonaut\Config::getInstance()->domain;

          $locationTo = $fileDir . '/web/files/' . $page->relativePath();

          $filename = $files['FileUpload']->getClientOriginalName();
          $files['FileUpload']->move($locationTo, $filename);
          $message = 'File was successfully uploaded!';
$search = new \testonaut\Search\File(\testonaut\Config::getInstance()->Path . '/index.db', 'files',\testonaut\Config::getInstance()->fileRoot);
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
      ));

      return $response;

    }, 'GET|POST');

    $file->get('/search/{term}', function (Request $request, $term) use ($app) {
      $search = new \testonaut\Search\File(\testonaut\Config::getInstance()->Path . '/index.db', 'files',\testonaut\Config::getInstance()->fileRoot);
      return $app->json($search->search($term), 201);
    });

    return $file;
  }

}