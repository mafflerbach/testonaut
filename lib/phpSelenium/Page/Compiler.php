<?php
namespace phpSelenium\Page;

use phpSelenium\Page;

class Compiler {
  private $page;

  public function __construct(Page $page) {
    $this->page = $page;
  }

  public function compile($variables) {
    $path = $this->page->transCodePath();
    $contentPath = $path . '/content';
    $content = $this->invokePages($contentPath);
    $content = $this->compileTwigTags($content, $variables);
    return $content;
  }


  protected function compileTwigTags($content, array $variables) {

    foreach ($variables as $key => $val) {
      $content = str_replace($key, $val, $content);
    }

    return $content;
  }

  public function getContent() {
    return '';
  }

  protected function invokePages($contentPath) {
    $tmp = array();
    if (file_exists($contentPath)) {
      $lines = file($contentPath);
      $content = $this->parseIncludes($lines);
      $filename = $contentPath . '_includes';
      file_put_contents($filename, $content);
      return $content;
    }
    return file_get_contents($contentPath);
  }

  protected function parseIncludes($fileArr) {
    for ($i = 0; $i < count($fileArr); $i++) {
      preg_match_all('/!include ([a-zA-Z.]+)/', $fileArr[$i], $result, PREG_SET_ORDER);
      rsort($result);
      if (!empty($result[0])) {
        for ($k = 0; $k < count($result); $k++) {
          $page = new Page($result[$k][1]);
          $content = '<div class="box hide"><h3>Include '.$result[$k][1].'<a href="{{ app.request.baseUrl }}/edit/'.$result[$k][1].'">Edit</a></h3><div>'.$page->content().'</div></div>';
          $content = str_replace($result[$k][0], $content, $fileArr[$i]);
          $fileArr[$i] = $content;
        }
      }
    }
    return implode("\n", $fileArr);
  }
}