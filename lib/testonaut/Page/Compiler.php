<?php

namespace testonaut\Page;

use testonaut\Page;

class Compiler {

    private $page;

    public function __construct(Page $page) {

        $this->page = $page;
    }

    public function compile($variables) {

        $path = $this->page->transCodePath();
        $contentPath = $path . '/content';

        $conf = $this->page->config();

        $content = $this->invokePages($contentPath);
        $content = '<div class="pageContent">' . $content . '</div>';
        $content = $this->includeSpecialHeadPages($content, $conf['type']);
        $content = $this->includeSpecialFooterPages($content, $conf['type']);
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
        $path = $this->page->transCodePath();
        $contentPath = $path . '/content';

        $conf = $this->page->config();

        $content = $this->invokePages($contentPath);
        
        $content = $this->includeSpecialHeadPages($content, $conf['type'], false);
        $content = $this->includeSpecialFooterPages($content, $conf['type'], false);
        return $content;
    }

    protected function invokePages($contentPath) {

        $tmp = array();
        if (file_exists($contentPath)) {
            $lines = file($contentPath);
            $content = $this->parseIncludes($lines);

            return $content;
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
                    $c = $page->getCompiledPage();
                    $content = $this->generateIncludeBox($c, $result[$k][1]);
                    $content = str_replace($result[$k][0], $content, $fileArr[$i]);
                    $fileArr[$i] = $content;
                }
            }
        }

        return implode("", $fileArr);
    }

    protected function includeSpecialFooterPages($content, $type, $decorate = true) {

        if ($type == 'suite') {
            $pages[] = 'suiteTearDown';
        }

        if ($type == 'test') {
            $pages[] = 'tearDown';
        }

        $pages[] = 'pageFooter';

        if ($decorate) {
            $content = $this->patchPage($content, $pages);
            
        } else {
            $content = $this->patchTestContent($content, $pages);
        }

        return $content;
    }

    protected function includeSpecialHeadPages($content, $type, $decorate = true) {

        if ($type == 'suite') {
            $pages[] = 'suiteSetUp';
        }

        if ($type == 'test') {
            $pages[] = 'setUp';
        }

        $pages[] = 'pageHeader';
        
        if ($decorate) {
            $content = $this->patchPage($content, $pages, TRUE);
            
        } else {
            $content = $this->patchTestContent($content, $pages, TRUE);
        }
        
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
                        $content = $container . '<div>' . $content . '</div>';
                    } else {
                        $content = '<div>' . $content . '</div>' . $container;
                    }
                }
            }
        }

        return $content;
    }

    protected function generateIncludeBox($content, $path) {
$return = '<button class="btn btn-link btn-xs" type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
  Include ' . $path . '
</button> <a href="{{ app.request.baseUrl }}/edit/' . $path . '"><span class="fa fa-pencil"></span></a>
<div class="collapse" id="collapseExample">
  <div class="well">
    ' . $content . '
  </div>
</div>';

        return $return;
    }

    protected function patchTestContent($content, $pages, $prepend = FALSE) {
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
                    $container = $c;
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

}
