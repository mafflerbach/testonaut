<?php

namespace testonaut\Selenese\Command;
use testonaut\Selenese\Command;

class storeText extends Command {
  public function runWebDriver(\WebDriver $session) {
    $elementText = $this->getElement($session, $this->arg1)->getText();
    $storage = \testonaut\Utils\Variablestorage::getInstance();
    
    $storage->define('${' . $this->arg2 . '}', $elementText);
    
    return $this->commandResult(true, true, 'store  ' . $elementText);
  }
}
