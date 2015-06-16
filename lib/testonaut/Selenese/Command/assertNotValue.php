<?php

namespace testonaut\Selenese\Command;
use testonaut\Selenese\Command;
// AssertNotValue(locator,pattern)
class assertNotValue extends Command {
   public function runWebDriver(\WebDriver $session) {
    $elementText = $this->getElement($session, $this->arg1)->getAttribute('value');
    return $this->assertNot($elementText, $this->arg2);
  }
}
