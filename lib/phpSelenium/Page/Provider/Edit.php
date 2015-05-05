<?php
namespace phpSelenium\Page\Provider;

use phpSelenium\Config;
use phpSelenium\Page\Breadcrumb;
use Silex\Api\ControllerProviderInterface;
use Silex\Application;
use Symfony\Component\HttpFoundation\Request;

class Edit implements ControllerProviderInterface {
  public function connect(Application $app) {
    $edit = $app['controllers_factory'];
    $edit->get('/', function (Request $request, $path) use ($app) {
      $page = new \phpSelenium\Page($path);
      $content = $page->content();
      $uploadedFiles = $page->getLinkedFiles();

      $app['request'] = array(
        'content'      => $content,
        'path'         => $path,
        'baseUrl'      => $request->getBaseUrl(),
        'linkedFiles'  => $uploadedFiles['documents'],
        'linkedImages' => $uploadedFiles['images'],
        'mode'         => 'edit'
      );

      $crumb = new Breadcrumb($path);
      $app['crumb'] = $crumb->getBreadcrumb();

      return $app['twig']->render('edit.twig');

    });

    $edit->get('/rename/', function (Request $request, $path) use ($app) {
      $page = new \phpSelenium\Page($path);
      $app['request'] = array(
        'path'    => $path,
        'baseUrl' => $request->getBaseUrl(),
        'mode'    => 'edit'
      );
      return $app['twig']->render('rename.twig');

    });
    $edit->post('/rename/', function (Request $request, $path) use ($app) {
      $page = new \phpSelenium\Page($path);
      $newPath = $request->request->get('newPath');
      $app['request'] = array(
        'path'    => $path,
        'baseUrl' => $request->getBaseUrl(),
        'mode'    => 'edit'
      );
      if ($page->rename($path, $newPath)) {
        $message = "rename Page";
        return $app->redirect($request->getBaseUrl() . '/' . $newPath);

      } else {

        $message = "can't rename Page";
      }

      $app['request'] = array(
        'path'    => $path,
        'baseUrl' => $request->getBaseUrl(),
        'mode'    => 'edit',
        'message' => $message
      );

      return $app['twig']->render('rename.twig');

    });

    $edit->post('/', function (Request $request, $path) use ($app) {
      $content = $request->request->get('content');

      $page = new \phpSelenium\Page($path);
      $content = $page->content($content, TRUE);
      return $app->redirect($request->getBaseUrl() . '/' . $path);
    });

    return $edit;
  }
}