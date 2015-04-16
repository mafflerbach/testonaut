<?php

namespace phpSelenium\Selenese\Command;

use phpSelenium\Selenese\Command;

// assertNotBodyText(pattern)
class assertNotBodyText extends Command {
  public function runWebDriver(\WebDriver $session) {
    $html = $this->getElement($session, 'css=body')->getAttribute('innerHTML');
    return $this->assertNot($html, $this->arg1);
  }
}
