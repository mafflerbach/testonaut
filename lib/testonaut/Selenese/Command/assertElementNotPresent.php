<?php

namespace testonaut\Selenese\Command;

use testonaut\Selenese\Command;
use testonaut\Selenese\Exception\NoSuchElement;

// assertElementNotPresent(locator)
class assertElementNotPresent extends Command {

  /**
   * @see Command::runWebDriver()
   */
  public function runWebDriver(\WebDriver $session) {
    try {
      $this->getElement($session, $this->arg1);
      return $this->commandResult(false, false, 'Found, should not have been');
    } catch (NoSuchElement $e) {
      return $this->commandResult(true, true, 'Not found, as per-request');
    }
  }

}