<?php

namespace phpSelenium\Selenese\Command;

use phpSelenium\Selenese\Exception\NoSuchElement,
    phpSelenium\Selenese\Command;

// assertElementPresent(locator)
class assertElementPresent extends Command {

    /**
     * @see Command::runWebDriver()
     */
    public function runWebDriver(\WebDriver $session)
    {
        try {
            $this->getElement($session, $this->arg1);
            return $this->commandResult(true, true, 'Found');
        }
        catch (NoSuchElement $e) {
            return $this->commandResult(false, false, 'Not found');
        }
    }

}
