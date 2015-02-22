<?php

namespace phpSelenium\Selenese;

use phpSelenium\Selenese\Command\Command,
    phpSelenium\Selenese\Command\Stub;

class Test {

    /**
     * @var Command[]
     */
    public $commands = array();

    /**
     * @var string
     */
    public $baseUrl;

    /**
     * @param string $file
     * @return Command[]
     * @throws \InvalidArgumentException
     * @throws \Exception
     */
    public function loadFromSeleneseHtml($file) {

        if (!file_exists($file)) {
            throw new \InvalidArgumentException("$file does not exist");
        }

        if (!is_readable($file)) {
            throw new \InvalidArgumentException("$file is not readable");
        }

        libxml_use_internal_errors(true);
        $dom = new \DOMDocument;
//        $loaded = $dom->loadHTMLFile($file);
        // todo: deal with badly loading HTML if needed
//        if (!$loaded) {
//            foreach (libxml_get_errors() as $error) {
//                // handle errors here
//            }
//            libxml_clear_errors();
//        }

        // get the base url
        $this->baseUrl = $dom->getElementsByTagName('link')->item(0)->getAttribute('href');
        $this->baseUrl = rtrim($this->baseUrl, '/');

        //<link rel="selenium.base" href="https://www.creditkarma.com/" />

        // todo: catch loading of things NOT selenese
        $rows = $dom->getElementsByTagName('tbody')->item(0)->getElementsByTagName('tr');

        // extract the commands
        foreach ($rows as $row) {
            /** @var \DOMElement $row */
            $tds = $row->getElementsByTagName('td');

            // gold!
            $command = $tds->item(0)->nodeValue;
            $target  = $tds->item(1)->nodeValue;
            $value   = $tds->item(2)->nodeValue;

            // there is no "andWait"
            $command = str_replace('andWait', '', $command);

            $commandClass = 'phpSelenium\\Selenese\\Command\\' . $command;
            if (class_exists($commandClass)) {
                /** @var Command $command */
                $commandObj = new $commandClass();
                $commandObj->arg1 = ($command == 'open' ? $this->baseUrl : '') . $target;
                $commandObj->arg2 = $value;
                $this->commands[] = $commandObj;
            }
            else {
                $unknowncmd = new Stub();
                $unknowncmd->command = $command;
                $this->commands[] = $unknowncmd;
            }
        }

    }

}
