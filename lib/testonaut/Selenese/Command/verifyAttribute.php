<?php

namespace testonaut\Selenese\Command;

use testonaut\Selenese\Command;
// VerifyAttribute(attributeLocator,pattern)
class verifyAttribute extends Command {

  public function runWebDriver(\WebDriver $session) {
    $index = strrpos($this->arg1, '/@');
    $pattern = '/@';
    if ($index === FALSE) {
      $index = strrpos($this->arg1, '@');
      $pattern = '@';
    }
    $attribute = substr($this->arg1, $index);
    $element = str_replace($attribute, '', $this->arg1);
    
    $attrPattern = str_replace('/@', '', $attribute);
    $elementText = $this->getElement($session, $element)->getAttribute($attrPattern);
    
    return $this->verify($elementText, $this->arg2);
  }

}
