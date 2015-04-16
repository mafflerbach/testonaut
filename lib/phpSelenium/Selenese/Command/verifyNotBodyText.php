<?php

namespace phpSelenium\Selenese\Command;

use phpSelenium\Selenese\Command;

// verifyNotBodyText(pattern)
class verifyNotBodyText extends Command {
  public function runWebDriver(\WebDriver $session) {
    $html = $this->getElement($session, 'css=body')->getAttribute('innerHTML');
    return $this->verifyNot($html, $this->arg1);
  }
}
