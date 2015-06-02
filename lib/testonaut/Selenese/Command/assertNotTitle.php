<?php

namespace testonaut\Selenese\Command;

use testonaut\Selenese\Command;

// AssertNotTitle(pattern)
class AssertNotTitle extends Command {
  public function runWebDriver(\WebDriver $session) {
    $title = $session->getTitle();
    return $this->assertNot($title, $this->arg1);
  }
}
