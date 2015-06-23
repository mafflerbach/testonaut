<?php

namespace testonaut\Selenese\Command;
use testonaut\Selenese\Command;

class Select extends Command {
  public function runWebDriver(\WebDriver $session) {
    $elementText = $this->getElement($session, $this->arg1);
    $lable = str_replace('label=', '', utf8_decode($this->arg2));
    $select = new \WebDriverSelect($elementText);
    $select->selectByVisibleText($lable); 
    return $this->commandResult(true, true, 'select ' . utf8_decode($this->arg2));
  }
}
