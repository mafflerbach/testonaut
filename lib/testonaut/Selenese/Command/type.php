<?php

namespace testonaut\Selenese\Command;

use testonaut\Selenese\Command;

// type(locator,value)
class Type extends Command {
  public function runWebDriver(\WebDriver $session) {
      
    $this->getElement($session, $this->arg1)->clear();
    $this->getElement($session, $this->arg1)->sendKeys($this->arg2);
    return $this->commandResult(true, true, 'Typed "' . $this->arg2 . '" into ' . $this->arg1);
  }
}
