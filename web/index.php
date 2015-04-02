<?php
$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->add('phpSelenium', __DIR__ . '/../lib/');

$seleniumAddress = 'http://selenium-hub.dim:4444';
$config = \phpSelenium\Config::getInstance();
$config->define('Path', dirname(dirname(__FILE__)));
$config->define('wikiPath', dirname(dirname(__FILE__)) . '/root');
$config->define('imageRoot', dirname(dirname(__FILE__)) . '/images');
$config->define('result', dirname(dirname(__FILE__)) . '/result');
$config->define('seleniumHub', $seleniumAddress.'/wd/hub');
$config->define('seleniumConsole', $seleniumAddress.'/grid/console');
$config->define('appPath', '');
$config->define('Cache', FALSE);
$config->define('seleniumAddress', $seleniumAddress);

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
  'twig.path' => __DIR__ . '/views',
));

$app->mount('/', new phpSelenium\Page\Provider\Start());
$app->mount('/edit/', new phpSelenium\Page\Provider\Start(true));
$app->mount('/edit/{path}', new phpSelenium\Page\Provider\Edit());
$app->mount('/config/{path}', new phpSelenium\Page\Provider\Config());
$app->mount('/delete/{path}', new phpSelenium\Page\Provider\Delete());
$app->mount('/run/{path}', new phpSelenium\Page\Provider\Run());
$app->mount('/image', new phpSelenium\Page\Provider\Image());
$app->mount('/{path}', new phpSelenium\Page\Provider\Page());

$app->run();

/*
$conf = array(
  'type' => 'static',
  'browser' => array(
    'chrome' => true,
  )
);

print(json_encode($conf));
*/