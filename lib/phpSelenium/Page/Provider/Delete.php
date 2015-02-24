<?php
namespace phpSelenium\Page\Provider;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;


class Delete implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $edit = $app['controllers_factory'];
        $edit->get('/', function (Request $request, $path) use ($app) {
            $page = new \phpSelenium\Page($path);
            $content = $page->delete();
            $app['request'] = array(
                'content' => $content,
                'path' => $path,
                'baseUrl' => $request->getBaseUrl(),
                'mode' => 'delete'
            );
          return $app->redirect($request->getBaseUrl() . '/' . $path);
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