<?php

namespace phpSelenium\Selenese\Command;
use phpSelenium\Selenese\Command;

// assertNotText(locator,pattern)
class assertNotText extends Command {
    public function runWebDriver(\WebDriver $session)
    {
        $elementText = $this->getElement($session, $this->arg1)->getText();
        return $this->assertNot($elementText, $this->arg2);
    }
}