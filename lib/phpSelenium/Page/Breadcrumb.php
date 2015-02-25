<?php
namespace phpSelenium\Page;


class Breadcrumb {
  private $path;

  public function __construct($path) {
    $this->path = $path;
  }


  public function getBreadcrumb() {
    $links = explode('.', $this->path);
    $path = array();
    $b = '';
    $i = 0;
    foreach ($links as $l) {
      if ($i < count($links)-1 ) {
        $b .= $l. ".";
      } else {
        $b .= $l;
      }
      $path[] = $b;
      print(count($links) ."<br/>");
      print($i ."<br/>");
      $i++;
    }
    return $path;
  }
}