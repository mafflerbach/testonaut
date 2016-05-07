<?php
/**
 * Created by PhpStorm.
 * User: maren
 * Date: 25.04.2016
 * Time: 21:21
 */

namespace testonaut\Page\Provider;


use mafflerbach\Page\ProviderInterface;
use \mafflerbach\Http\Request;
use mafflerbach\Routing;

class Reset extends Base implements ProviderInterface {

  private $routing;
  private $response;

  public function connect() {
    $this->routing = new Routing();
    $this->response = array(
      'system' => $this->system()
    );

    $this->routing->route('/', function () {
      $request = new Request();
      $this->response['form'] = $this->getForm();
      $this->reset($request);

      $this->routing->response($this->response);
      $this->routing->render('reset.xsl');
    });

  }


  protected function reset($response) {
    $message = '';

    $request = new Request();
    $data = $request->request;

    if (!empty($request->request) && isset($data['email'])) {
      $name = $data['email'];
      $user = new \testonaut\User();
      $reset = $user->reset($name);

      if ($reset['result']) {
        $message = "Your new password: " . $reset['password'];
      } else {
        $message = "Can't reset password.";
      }
    }
    $this->response['message'] = $message;

    return $response;
  }


  private function getForm() {
    return array(
      'text' => array('label' => 'email')
    );
  }

}