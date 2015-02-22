<?php

namespace phpSelenium\Selenese\Command;

use phpSelenium\Selenese\Exception\NoSuchElement,
    phpSelenium\Selenese\Command;


// verifyElementNotPresent(locator)
class verifyElementNotPresent extends Command {

    /**
     * @see Command::runWebDriver()
     */
    public function runWebDriver(\WebDriver $session)
    {
        try {
            $this->getElement($session, $this->arg1);
            return $this->commandResult(true, false, 'Found, should not have been');
        }
        catch (NoSuchElement $e) {
            return $this->commandResult(true, true, 'Not found, as per-request');
        }
    }

}