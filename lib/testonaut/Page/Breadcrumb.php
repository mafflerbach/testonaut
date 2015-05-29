<?php
namespace testonaut\Page;

class Breadcrumb {
  private $path;

  public function __construct($path) {
    $this->path = $path;
  }

  public function getBreadcrumb() {
    $crumbs = explode(".", $this->path);
    $foo = array();
    $b = '';
    $i = 0;
    foreach ($crumbs as $crumb) {
      if ($b != $crumb && $b != '') {
        $b .= '.';
      }
      $foo[$crumb] = $b .= $crumb;
      $i++;
    }
    return $foo;
  }
}