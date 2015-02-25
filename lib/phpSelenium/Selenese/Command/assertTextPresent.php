<?php

namespace phpSelenium\Selenese\Command;

use phpSelenium\Selenese\Command;

// assertTextPresent(pattern)
class assertTextPresent extends Command {
  public function runWebDriver(\WebDriver $session) {
    $bodyValue = $session->getPageSource();
    return $this->assert($bodyValue, $this->arg1);
  }
}
