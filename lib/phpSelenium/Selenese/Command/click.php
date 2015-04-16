<?php

namespace phpSelenium\Selenese\Command;

use phpSelenium\Selenese\Command;

class Click extends Command {
  public function runWebDriver(\WebDriver $session) {
    $this->getElement($session, $this->arg1)->click();
    return $this->commandResult(true, true, 'Clicked ' . $this->arg1);
  }
}
