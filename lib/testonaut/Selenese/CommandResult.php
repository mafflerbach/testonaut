<?php

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
