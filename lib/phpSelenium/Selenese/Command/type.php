<?php

namespace phpSelenium\Selenese\Command;

use phpSelenium\Selenese\Command;

// type(locator,value)
class type extends Command
{
    public function runWebDriver(\WebDriver $session)
    {
        $this->getElement($session, $this->arg1)->sendKeys($this->arg2);
        return $this->commandResult(true, true, 'Typed "' . $this->arg2 . '" into ' . $this->arg1);
    }
}
