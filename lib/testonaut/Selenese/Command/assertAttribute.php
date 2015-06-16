<?php

namespace testonaut\Selenese\Command;

use testonaut\Selenese\Command;
// AssertAttribute(attributeLocator,pattern)
class assertAttribute extends Command {

  /**
   * @example for https://www.google.de/ target://input[@name='btnK']@type value:submit
   */
  public function runWebDriver(\WebDriver $session) {
    $index = strrpos($this->arg1, '/@');
    $leng = strlen($this->arg1);
    $attribute = substr($this->arg1, $index);
    $element = str_replace($attribute, '', $this->arg1);
    $elementText = $this->getElement($session, $element)->getAttribute(str_replace('/@', '', $attribute));
    return $this->assert($elementText, $this->arg2);
  }

}
