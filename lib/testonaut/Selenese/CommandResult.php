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



namespace testonaut\Selenese;

class CommandResult {

  /** @var bool Did the command complete successfully? */
  public $success;

  /** @var bool Should the runner continue execution? */
  public $continue;

  /** @var string Any associate messages */
  public $message;

  /**
   * @param bool $continue Should the test continue?
   * @param bool $success Did this command succeed?
   * @param string $message A message for this commands result
   * @throws \InvalidArgumentException
   */
  public function __construct($continue, $success, $message) {
    if (!is_bool($continue)) {
      throw new \InvalidArgumentException("continue must be a boolean");
    }
    if (!is_bool($success)) {
      throw new \InvalidArgumentException("success must be a boolean");
    }
    if (!is_string($message)) {
      throw new \InvalidArgumentException("message must be a boolean");
    }
    $this->continue = $continue;
    $this->success = $success;
    $this->message = $message;
  }

}
