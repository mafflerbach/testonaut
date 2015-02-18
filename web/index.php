<?php
$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->add('phpSelenium', __DIR__ . '/../lib/');

$config = \phpSelenium\Config::getInstance();
$config->define('Path', __DIR__);
$config->define('wikiPath', dirname(dirname(__FILE__)) . '/root');

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/views',
));


$app->mount( '/', new phpSelenium\Page\Provider\Start());
$app->mount('/edit/{path}', new phpSelenium\Page\Provider\Edit());
$app->mount('/{path}', new phpSelenium\Page\Provider\Page());

$app->run();
