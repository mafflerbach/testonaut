<?php

namespace testonaut\Selenese\Command;

use testonaut\Selenese\Command;

// verifyTextNotPresent(pattern)
class verifyTextNotPresent extends Command {
  public function runWebDriver(\WebDriver $session) {
    $bodyValue = $session->getPageSource();
    return $this->verifyNot($bodyValue, $this->arg1);
  }
}
