<?php

namespace phpSelenium\Selenese\Command;

use phpSelenium\Selenese\Command;

// verifyNotText(locator,pattern)
class verifyNotText extends Command {
  public function runWebDriver(\WebDriver $session) {
    $elementText = $this->getElement($session, $this->arg1)->getText();
    return $this->verifyNot($elementText, $this->arg2);
  }
}