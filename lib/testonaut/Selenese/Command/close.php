<?php

namespace testonaut\Selenese\Command;

use testonaut\Selenese\Command;

// close()
class close extends Command {
  public function runWebDriver(\WebDriver $session) {
    $result = $this->commandResult(true, true, 'close session');
    $session->close();
    return $result;
  }
}
