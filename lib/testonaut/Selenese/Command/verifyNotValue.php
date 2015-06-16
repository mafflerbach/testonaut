<?php

namespace testonaut\Selenese\Command;

use testonaut\Selenese\Command;

// VerifyNotValue(locator,pattern)
class VerifyNotValue extends Command {
     public function runWebDriver(\WebDriver $session) {
    $elementText = $this->getElement($session, $this->arg1)->getAttribute('value');
    return $this->verifyNot($elementText, $this->arg2);
  }
}
