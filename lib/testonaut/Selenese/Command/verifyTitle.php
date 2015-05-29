<?php

namespace testonaut\Selenese\Command;

use testonaut\Selenese\Command;

// verifyTitle(pattern)
class verifyTitle extends Command {
  public function runWebDriver(\WebDriver $session) {
    $title = $session->getTitle();
    return $this->verify($title, $this->arg1);
  }
}
