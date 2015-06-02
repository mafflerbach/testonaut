<?php

namespace testonaut\Selenese\Command;

use testonaut\Selenese\Command;

// VerifyNotTitle(pattern)
class VerifyNotTitle extends Command {
  public function runWebDriver(\WebDriver $session) {
    $title = $session->getTitle();
    return $this->verifyNot($title, $this->arg1);
  }
}
