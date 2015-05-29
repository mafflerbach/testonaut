<?php
namespace testonaut\Page;

use Silex\Application;

interface Provider {
  public function connect(Application $app);
}