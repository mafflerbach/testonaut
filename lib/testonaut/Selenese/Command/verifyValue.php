<?php

namespace testonaut\Selenese\Command;
use testonaut\Selenese\Command;

class verifyValue extends Command {
   public function runWebDriver(\WebDriver $session) {
    $elementText = $this->getElement($session, $this->arg1)->getAttribute('value');
    return $this->assert($elementText, $this->arg2);
  }
}
