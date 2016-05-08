<?php
/**
 * Created by PhpStorm.
 * User: maren
 * Date: 23.04.2016
 * Time: 22:30
 */

namespace testonaut\Page\Provider;

use mafflerbach\Page\ProviderInterface;
use mafflerbach\Routing;

/**
 * Class Logout
 * @package testonaut\Page\Provider
 */
class Logout extends Base implements ProviderInterface {


  private $routing;
  private $response;

  public function connect() {
    $this->routing = new Routing();
    $this->response = array(
      'system' => $this->system()
    );

    $this->routing->route('/', function () {

      unset($_SESSION['testonaut']);


      $this->response['message'] = 'You are logged out';
      $this->routing->response($this->response);
      $this->routing->render('logout.xsl');
    });
  }


  /*

  public function connect(Application $app) {

    $page = $app['controllers_factory'];
    $page->get('/', function (Request $request) use ($app) {
      unset($_SESSION['testonaut']);

      $app['request'] = array(
        'baseUrl' => $request->getBaseUrl(),
      );

      return $app['twig']->render('logout.twig');
    });
    return $page;
  }*/
}