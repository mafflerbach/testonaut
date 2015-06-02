<?php

namespace testonaut\Selenese\Command;

use testonaut\Selenese\Command;
use testonaut\Selenese\Exception\NoSuchElement;

// AssertElementPresent(locator)
class AssertElementPresent extends Command {

  /**
   * @see Command::runWebDriver()
   */
  public function runWebDriver(\WebDriver $session) {
    try {
      $this->getElement($session, $this->arg1);
      return $this->commandResult(true, true, 'Found');
    } catch (NoSuchElement $e) {
      return $this->commandResult(false, false, 'Not found');
    }
  }

}
