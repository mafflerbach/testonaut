<?php
$loader = require __DIR__ . '/../vendor/autoload.php';
$loader->add('phpSelenium', __DIR__ . '/../lib/');
$loader->add('phpSelenium', __DIR__ . '/../lib/');

$config = \phpSelenium\Config::getInstance();
$config->define('Path', __DIR__);
$config->define('wikiPath', dirname(dirname(__FILE__)) . '/root');

$app = new Silex\Application();
$app['debug'] = true;

$app->register(new Silex\Provider\TwigServiceProvider(), array(
  'twig.path' => __DIR__ . '/views',
));

$app->mount('/', new phpSelenium\Page\Provider\Start());
$app->mount('/edit/{path}', new phpSelenium\Page\Provider\Edit());
$app->mount('/delete/{path}', new phpSelenium\Page\Provider\Delete());
$app->mount('/{path}', new phpSelenium\Page\Provider\Page());

$app->run();

/*
use phpSelenium\Selenese\Test,
    phpSelenium\Selenese\Runner;
try {
    // get the test rolling
    $test = new Test();
    $test->loadFromSeleneseHtml('test.html');
    $capabilities = DesiredCapabilities::firefox();
    $runner = new Runner($test, 'http://selenium-hub.dim:4444/wd/hub');
    $runner->run($capabilities);
}
catch (\Exception $e) {
    // oops.
    echo 'Test failed: ' . $e->getMessage() . "\n";
}

 */

