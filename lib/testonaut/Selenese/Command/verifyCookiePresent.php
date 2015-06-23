<?php

namespace testonaut\Selenese\Command;
use testonaut\Selenese\Command;

class VerifyCookiePresent extends Command {

  public function runWebDriver(\WebDriver $session) {
    $cookie = $session->manage()->getCookies();

    $value = $this->arrayKeyExist($this->arg1, $cookie);
    if ($value) {
      return $this->commandResult(true, true, 'Check Cookie on present ' . $this->arg1);
    } else {
      return $this->commandResult(true, false, 'Check Cookie on present ' . $this->arg1);
    }
  }
}
