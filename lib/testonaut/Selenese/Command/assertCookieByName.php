<?php

namespace testonaut\Selenese\Command;
use testonaut\Selenese\Command;

class AssertCookieByName extends Command {
  public function runWebDriver(\WebDriver $session) {
    $cookie = $session->manage()->getCookies();
    $value = $this->arraySearchKey($this->arg1, $cookie);
    return $this->assert($value, $this->arg2);
  }
}
