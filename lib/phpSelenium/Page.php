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

    public function content($content = NULL)
    {
        $file = $this->transCodePath() . '/content';
        if (!file_exists($file)) {
            return '';
        }

        if ($content == null) {
            $pageContent = file_get_contents($file);

            return $pageContent;
        } else {
            file_put_contents($this->transCodePath() . '/content', $content);
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
        return str_replace('.', '/', $this->root.'/'.$this->path);
    }
} 