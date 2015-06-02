<?php

namespace testonaut\Selenese\Command;

use testonaut\Selenese\Command;

// AssertTextNotPresent(pattern)
class AssertTextNotPresent extends Command {
  public function runWebDriver(\WebDriver $session) {
    $bodyValue = $session->getPageSource();
    return $this->assertNot($bodyValue, $this->arg1);
  }
}
