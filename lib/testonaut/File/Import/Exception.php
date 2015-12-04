<?php
/**
 *
 * GNU GENERAL PUBLIC LICENSE testonaut Copyright (C) 2015 Afflerbach
 * This program is free software: you can redistribute it and/or modify it under the terms
 * of the GNU General Public License as published by the Free Software Foundation,
 * either version 3 of the License, or (at your option) any later version.
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 */

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