<?php

namespace testonaut\Selenese\Command;

use testonaut\Selenese\Command;

class CaptureEntirePageScreenshot extends Command {
  public function runWebDriver(\WebDriver $session) {
    $session->takeScreenshot($this->arg1);
    return $this->commandResult(true, true, 'take Screenshot: ' . $this->arg1);
  }
}