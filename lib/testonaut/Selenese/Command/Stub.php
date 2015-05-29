<?php

namespace testonaut\Selenese\Command;

use testonaut\Selenese\Command;

class Stub extends Command {

  public $command;

  public function runWebDriver(\WebDriver $session) {
    return $this->commandResult(true, false, 'This command (' . $this->command . ') is currently unsupported.');
  }

}
