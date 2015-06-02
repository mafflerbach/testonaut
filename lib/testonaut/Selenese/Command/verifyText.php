<?php

namespace testonaut\Selenese\Command;

use testonaut\Selenese\Command;

// VerifyText(locator,pattern)
class VerifyText extends Command {
  public function runWebDriver(\WebDriver $session) {
    $elementText = $this->getElement($session, $this->arg1)->getText();
    return $this->verify($elementText, $this->arg2);
  }
}
