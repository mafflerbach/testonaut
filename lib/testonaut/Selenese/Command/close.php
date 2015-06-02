<?php

namespace testonaut\Selenese\Command;

use testonaut\Selenese\Command;

// Close()
class Close extends Command {
  public function runWebDriver(\WebDriver $session) {
    $result = $this->commandResult(true, true, 'close session');
    $session->close();
    return $result;
  }
}
