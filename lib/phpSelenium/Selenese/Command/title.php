<?php

namespace phpSelenium\Selenese\Command;

use phpSelenium\Selenese\Command;

// title()
class title extends Command {
  public function runWebDriver(\WebDriver $session) {
    $title = $session->getTitle();
    return $this->commandResult(true, true, 'Got page title: "' . $title . '"');
  }
}
