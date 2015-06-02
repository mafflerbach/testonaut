<?php

namespace testonaut\Selenese\Command;

use testonaut\Selenese\Command;

// AssertTextPresent(pattern)
class AssertTextPresent extends Command {
  public function runWebDriver(\WebDriver $session) {
    $bodyValue = $session->getPageSource();
    return $this->assert($bodyValue, $this->arg1);
  }
}
