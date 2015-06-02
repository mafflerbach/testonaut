<?php

namespace testonaut\Selenese\Command;

use testonaut\Selenese\Command;

// AssertTitle(pattern)
class AssertTitle extends Command {
  public function runWebDriver(\WebDriver $session) {
    $title = $session->getTitle();
    return $this->assert($title, $this->arg1);
  }
}
