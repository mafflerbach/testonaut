<?php

namespace testonaut\Selenese\Command;
use testonaut\Selenese\Command;
// AssertCookieNotPresent(name)
class AssertCookieNotPresent extends Command {

  public function runWebDriver(\WebDriver $session) {
    $cookie = $session->manage()->getCookies();

    $value = $this->arrayKeyExist($this->arg1, $cookie);
    if (!$value) {
      return $this->commandResult(true, true, 'Check Cookie not present ' . $this->arg1);
    } else {
      return $this->commandResult(false, false, 'Check Cookie not present ' . $this->arg1);
    }
  }
}

