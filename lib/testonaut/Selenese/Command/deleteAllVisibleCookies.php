<?php

namespace testonaut\Selenese\Command;

use testonaut\Selenese\Command;

class deleteAllVisibleCookies extends Command {
  public function runWebDriver(\WebDriver $session) {
    $session->manage()->deleteAllCookies();
    return $this->commandResult(true, true, 'All cookies deleted');
  }
}
