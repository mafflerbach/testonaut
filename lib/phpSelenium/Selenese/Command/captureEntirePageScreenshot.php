<?php

namespace phpSelenium\Selenese\Command;

use phpSelenium\Selenese\Command;

class captureEntirePageScreenshot extends Command {
  public function runWebDriver(\WebDriver $session) {
    $session->takeScreenshot($this->arg1);
    return $this->commandResult(true, true, 'take Screenshot' . $this->arg1);
  }
}