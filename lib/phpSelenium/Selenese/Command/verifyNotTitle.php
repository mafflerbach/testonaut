<?php

namespace phpSelenium\Selenese\Command;
use phpSelenium\Selenese\Command;

// verifyNotTitle(pattern)
class verifyNotTitle extends Command {
    public function runWebDriver(\WebDriver $session)
    {
        $title = $session->getTitle();
        return $this->verifyNot($title, $this->arg1);
    }
}
