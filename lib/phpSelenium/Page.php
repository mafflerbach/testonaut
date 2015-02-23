<?php

namespace phpSelenium;

class Page
{
    protected $path;
    protected $root;

    public function __construct($path)
    {
        $this->path = $path;
        $this->root = Config::getInstance()->wikiPath;

    }

    public function content($content = NULL, $save = NULL)
    {
        $file = $this->transCodePath() . '/content';
        if (!file_exists($file) && $save === NULL) {
            return '';
        }
        if ($content == null) {
            $pageContent = file_get_contents($file);
            return $pageContent;
        } else {
            $filename = $this->transCodePath() . '/content';

            if(!is_dir($this->transCodePath())) {
                if(!mkdir($this->transCodePath(), 0755, true)) {
                    throw new \Exception();
                }
            }
            file_put_contents($filename, $content);
        }
    }

    public function setConfig($config = array())
    {
        if ($config == null) {
            return json_decode(file_get_contents($this->transCodePath() . '/config'));
        } else {
            file_put_contents(json_encode($this->transCodePath() . '/config'), $config);
        }
    }

    protected function transCodePath()
    {
        return str_replace('.', '/', $this->root . '/' . $this->path);
    }
} 