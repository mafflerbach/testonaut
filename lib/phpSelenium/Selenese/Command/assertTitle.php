<?php

namespace phpSelenium\Selenese\Command;
use phpSelenium\Selenese\Command;

// assertTitle(pattern)
class assertTitle extends Command {
    public function runWebDriver(\WebDriver $session)
    {
        $title = $session->getTitle();
        return $this->assert($title, $this->arg1);
    }
}
