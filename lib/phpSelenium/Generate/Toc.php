<?php

namespace phpSelenium\Generate;

class Toc {

    private $basePath = 'root';
    private $dirArray = array();

    public function __construct($basePath = 'root') {
        if ($basePath != 'root') {
            $this->basePath = $basePath;
        }
    }

    /**
     *
     */
    public function runDir() {
        $ritit
            = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($this->basePath), \RecursiveIteratorIterator::CHILD_FIRST);
        $r = array();
        foreach ($ritit as $splFileInfo) {
            if ($splFileInfo->getFilename() == '.' || $splFileInfo->getFilename() == '..') {
                continue;
            }
            $path = $splFileInfo->isDir()
                ? array($splFileInfo->getFilename() => array())
                : array($splFileInfo->getFilename());

            for ($depth = $ritit->getDepth() - 1; $depth >= 0; $depth--) {
                $path = array($ritit->getSubIterator($depth)->current()->getFilename() => $path);
            }
            $r = array_merge_recursive($r, $path);
        }
        $this->dirArray = $r;
    }

    /**retuns a ul with all pages
     * @return string
     */
    public function generateMenu() {
        $dirlist = str_replace('<ul><ul>', '<ul>', $this->makeList($this->dirArray, 'root'));
        $dirlist = str_replace('</ul></ul>', '</ul>', $dirlist);
        return $dirlist;
    }

    /**
     * return an html ul with the wiki pages
     *
     * @param        $array
     * @param string $path
     *
     * @return string
     */
    protected function makeList($array, $path = 'root', $count = 0) {
        $path = str_replace('root/', '', $path);
        $output = '<ul>';
        foreach ($array as $key => $value) {
            if (is_array($value)) {
                $_path = $path.'/'.$key;
                $count++;
                $output .= $this->makeList($value, $_path, $count);
            } else {
                if ($count == 0) {
                    continue;
                }
                $pathArr = explode('/', $path);
                if (strpos($value, 'content') !== FALSE) {
                    $output .= '<li><a href="' . str_replace('/', '.', $path)  . '" data-action="open">' . $pathArr[count($pathArr) -1]. '</a></li>';
                }
            }
        }
        $output .= '</ul>';
        return $output;
    }

}