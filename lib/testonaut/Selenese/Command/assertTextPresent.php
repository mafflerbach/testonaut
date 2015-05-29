<?php

namespace testonaut\Selenese\Command;

use testonaut\Selenese\Command;

// assertTextPresent(pattern)
class assertTextPresent extends Command {
  public function runWebDriver(\WebDriver $session) {
    $bodyValue = $session->getPageSource();
    return $this->assert($bodyValue, $this->arg1);
  }
}
