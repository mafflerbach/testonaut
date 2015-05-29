<?php

namespace testonaut\Selenese\Command;

use testonaut\Selenese\Command;

// assertNotBodyText(pattern)
class assertNotBodyText extends Command {
  public function runWebDriver(\WebDriver $session) {
    $html = $this->getElement($session, 'css=body')->getAttribute('innerHTML');
    return $this->assertNot($html, $this->arg1);
  }
}
