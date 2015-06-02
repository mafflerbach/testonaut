<?php

namespace testonaut\Selenese\Command;

use testonaut\Selenese\Command;

// VerifyTitle(pattern)
class VerifyTitle extends Command {
  public function runWebDriver(\WebDriver $session) {
    $title = $session->getTitle();
    return $this->verify($title, $this->arg1);
  }
}
