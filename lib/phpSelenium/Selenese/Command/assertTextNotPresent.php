<?php

namespace phpSelenium\Selenese\Command;
use phpSelenium\Selenese\Command;

// assertTextNotPresent(pattern)
class assertTextNotPresent extends Command {
    public function runWebDriver(\WebDriver $session)
    {
        $bodyValue = $session->getPageSource();
        return $this->assertNot($bodyValue, $this->arg1);
    }
}
