<?php

namespace phpSelenium\Selenese\Command;

use phpSelenium\Selenese\Command;

// assertBodyText(pattern)

class assertBodyText extends Command {
  public function runWebDriver(\WebDriver $session) {
    $html = $this->getElement($session, 'css=body')->getAttribute('innerHTML');
    return $this->assert($html, $this->arg1);
  }
}
