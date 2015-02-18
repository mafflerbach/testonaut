<?php
namespace phpSelenium\Page\Provider;

use Silex\Application;
use Silex\Api\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\Request;


class Page implements ControllerProviderInterface
{
    public function connect(Application $app)
    {
        $page = $app['controllers_factory'];
        $page->get('/', function (Request $request, $path) use ($app) {
            $page = new \phpSelenium\Page($path);
            $content = $page->content();

            if ($content == false) {
                $app['request'] = array('path' => $path);
                $foo = $app['twig']->render('add.twig');
            } else {
                $app['request'] = array(
                    'content' => $content,
                    'path' => $path,
                    'baseUrl' => $request->getBaseUrl()
                );
                $foo = $app['twig']->render('page.twig');
            }
            return $foo;
        });

        return $page;
    }
}