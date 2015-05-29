<?php

namespace testonaut\Selenese\Command;

use testonaut\Selenese\Command;
use testonaut\Selenese\Exception\NoSuchElement;

// verifyElementPresent(locator)
class verifyElementPresent extends Command {

  /**
   * @see Command::runWebDriver()
   */
  public function runWebDriver(\WebDriver $session) {
    try {
      $this->getElement($session, $this->arg1);
      return $this->commandResult(true, true, 'Found');
    } catch (NoSuchElement $e) {
      return $this->commandResult(true, false, 'Not found');
    }
  }

}
