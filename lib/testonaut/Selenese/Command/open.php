<?php

namespace testonaut\Selenese\Command;

use testonaut\Selenese\Command;

class Open extends Command {
  public function runWebDriver(\WebDriver $session) {
    $session->get($this->arg1);
    $url = $session->getCurrentUrl();
    return $this->commandResult(true, true, 'Opened, url now: ' . $url);
  }
}
