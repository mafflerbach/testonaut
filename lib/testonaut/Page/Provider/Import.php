<?php
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

class Import implements ControllerProviderInterface {

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

      $message = 'Upload a file';
      if (isset($app['request'])) {
        $request = $app['request'];
      } else {
      }
      if ($request->isMethod('POST')) {
        $form->handleRequest($request);
        if ($form->isValid()) {

          $files = $request->files->get($form->getName());
          $fileMimeType = $files['FileUpload']->getMimeType();
          if ($fileMimeType == 'application/zip') {
            $message = $this->importFile($files, $page);
          } else {
            $message = "File wasn't uploaded! It was not a Zip file ";
          }
        }
      }
      $crumb = new Page\Breadcrumb($path);
      $app['crumb'] = $crumb->getBreadcrumb();

      $app['request'] = array(
        'content' => '',
        'path'    => $path,
        'baseUrl' => $request->getBaseUrl(),
      );

      $response = $app['twig']->render('import.twig', array(
        'message' => $message,
        'form'    => $form->createView(),
        'path'    => $path,
        'baseUrl' => $request->getBaseUrl(),
      ));

      return $response;
    }, 'GET|POST');

    return $file;
  }

  protected function importFile($files, $page) {
    $fileDir = Config::getInstance()->Path;
    $filename = $files['FileUpload']->getClientOriginalName();
    $tmppath = md5($filename);
    $locationTo = $fileDir . '/tmp/' . $tmppath;

    $files['FileUpload']->move($locationTo . '/zip', $filename);
    $message = 'File was successfully uploaded!';
    $import = new \testonaut\File\Import($locationTo, $filename, $page);
    try {
      $import->doImport();
    } catch(\Exception $e) {
      $message = $e->getMessage();
    }

    return $message;
  }

  protected function buildRespponse() {

  }

}