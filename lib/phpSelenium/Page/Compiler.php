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
    $contentPath = $path . '/content_includes';
    if (!file_exists($contentPath)) {
      $contentPath = $path . '/content';
    }

    $content = $this->invokePages($contentPath);
    $content = '<div class="pageContent">'.$content.'</div>';
    $content = $this->includeSpecialHeadPages($content);
    $content = $this->includeSpecialFooterPages($content);
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
      $filename = $contentPath;
      file_put_contents($filename, $content);
      return $content;
    }

    if (file_exists($contentPath)) {
      return file_get_contents($contentPath);
    }

    return '';
  }

  protected function parseIncludes($fileArr) {
    for ($i = 0; $i < count($fileArr); $i++) {
      preg_match_all('/!include ([a-zA-Z.]+)/', $fileArr[$i], $result, PREG_SET_ORDER);
      rsort($result);
      if (!empty($result[0])) {
        for ($k = 0; $k < count($result); $k++) {
          $page = new Page($result[$k][1]);
          $content = $this->generateIncludeBox($result[$k][1], $page->getCompiledPage());
          $content = str_replace($result[$k][0], $content, $fileArr[$i]);
          $fileArr[$i] = $content;
        }
      }
    }
    return implode("\n", $fileArr);
  }

  protected function includeSpecialHeadPages($content) {
    $pages = array(
      'setUp',
      'suiteSetUp'
    );
    $content = $this->patchPage($content, $pages, TRUE);

    return $content;
  }

  protected function patchPage($content, $pages, $prepend = FALSE) {
    $path = $this->page->path;

    $pathArr = explode('.', $path);

    for ($k = 0; $k < count($pages); $k++) {
      $tmp = array();
      for ($i = 0; $i < count($pathArr); $i++) {
        $tmp[] = $pathArr[$i];
        $path = implode('.', $tmp) . '.' . $pages[$k];
        $page = new Page($path);
        $c = $page->content();
        if ($c != '') {
          $container = $this->generateIncludeBox($c, $path);
          if ($prepend) {
            $content = $container . $content;
          } else {
            $content = $content . $container;
          }
        }
      }
    }
    return $content;
  }

  protected function includeSpecialFooterPages($content) {
    $pages = array(
      'tearDown',
      'suiteTearDown'
    );
    $content = $this->patchPage($content, $pages);

    return $content;
  }

  protected function generateIncludeBox($content, $path) {
    return '<div class="box hide"><h5>Include ' . $path . '<a class="btn small" href="{{ app.request.baseUrl }}/edit/' . $path . '">Edit</a></h5><div>' . $content . '</div></div>';
  }

}