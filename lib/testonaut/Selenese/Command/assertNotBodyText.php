<?php

namespace testonaut\Selenese\Command;

use testonaut\Selenese\Command;

// AssertNotBodyText(pattern)
class AssertNotBodyText extends Command {
  public function runWebDriver(\WebDriver $session) {
    $html = $this->getElement($session, 'css=body')->getAttribute('innerHTML');
    return $this->assertNot($html, $this->arg1);
  }
}
