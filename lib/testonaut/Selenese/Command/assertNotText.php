<?php

namespace testonaut\Selenese\Command;

use testonaut\Selenese\Command;

// assertNotText(locator,pattern)
class assertNotText extends Command {
  public function runWebDriver(\WebDriver $session) {
    $elementText = $this->getElement($session, $this->arg1)->getText();
    return $this->assertNot($elementText, $this->arg2);
  }
}