<?php

namespace testonaut\Selenese\Command;

// pause(waitTime)
class pause extends Stub {
  public function runWebDriver(\WebDriver $session) {
    sleep($this->arg1 / 1000);
    return $this->commandResult(true, true, 'sleep "' . $this->arg1 / 1000 . '" seconds');
  }
}
