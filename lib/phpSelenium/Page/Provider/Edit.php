<?php
namespace phpSelenium\Page\Provider;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;


class Edit implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $edit = $app['controllers_factory'];
        $edit->get('/', function (Request $request, $path) use ($app) {
            $page = new \phpSelenium\Page($path);
            $content = $page->content();
            $app['request'] = array(
                'content' => $content,
                'path' => $path,
                'baseUrl' => $request->getBaseUrl()
            );
            return $app['twig']->render('edit.twig');
        });

        $edit->post('/', function (Request $request, $path) use ($app) {
            $content = $request->request->get('content');
            $page = new \phpSelenium\Page($path);
            $content = $page->content($content);
            return $app->redirect($request->getBaseUrl() . '/' . $path);
        });
        return $edit;
    }
}