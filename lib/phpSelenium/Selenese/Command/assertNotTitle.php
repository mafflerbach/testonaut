<?php

namespace phpSelenium\Selenese\Command;

use phpSelenium\Selenese\Command;

// assertNotTitle(pattern)
class assertNotTitle extends Command {
  public function runWebDriver(\WebDriver $session) {
    $title = $session->getTitle();
    return $this->assertNot($title, $this->arg1);
  }
}
