<?php
namespace testonaut\File\Import;

class Exception extends \Exception {

  // Die Exception neu definieren, damit die Mitteilung nicht optional ist
  public function __construct($message, $code = 0, Exception $previous = NULL) {
    parent::__construct($message, $code, $previous);
  }

  // maßgeschneiderte Stringdarstellung des Objektes
  public function __toString() {
    return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
  }

}