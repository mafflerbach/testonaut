<?php

namespace testonaut\Selenese\Command;

use testonaut\Selenese\Command;

// verifyBodyText(pattern)
class verifyBodyText extends Command {
  public function runWebDriver(\WebDriver $session) {
    $html = $this->getElement($session, 'css=body')->getAttribute('innerHTML');
    return $this->verify($html, $this->arg1);
  }
}
