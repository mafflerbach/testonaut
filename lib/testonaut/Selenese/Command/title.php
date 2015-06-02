<?php

namespace testonaut\Selenese\Command;

use testonaut\Selenese\Command;

// title()
class Title extends Command {
  public function runWebDriver(\WebDriver $session) {
    $title = $session->getTitle();
    return $this->commandResult(true, true, 'Got page title: "' . $title . '"');
  }
}
