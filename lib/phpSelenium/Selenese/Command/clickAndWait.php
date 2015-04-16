<?php

namespace phpSelenium\Selenese\Command;

use phpSelenium\Selenese\Command;

class ClickAndWait extends Command {
  public function runWebDriver(\WebDriver $session) {
    $this->getElement($session, $this->arg1)->click();
    sleep(1);
    return $this->commandResult(true, true, 'Clicked ' . $this->arg1);
  }
}
