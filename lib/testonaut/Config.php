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


namespace testonaut;


/**
 * 
 */
class Config {
  private static $instance = NULL;
  private $register = array();
  private $readOnly = array();

  private function __construct() {
  }

  /**
   * 
   * @return self
   */
  public static function getInstance() {
    if (self::$instance === NULL) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  public function __destruct() {
  }

  public function getAll() {
    return $this->register;
  }

  /**
   * 
   * @param String $key
   * @param mixed $value
   */
  public function define($key, $value) {
    if (!$this->exists($key)) {
      $this->register[$key] = $value;
      $this->readOnly[$key] = TRUE;
    } else {
    //  die('<h1>Error: Constant \'<em>' . $key . '</em>\' is already created ' . 'in Registry!</h1>');
    }
  }

  /**
   * 
   * @param String $key
   * @return boolean
   */
  public function exists($key) {
    return array_key_exists($key, $this->register);
  }

  /**
   * 
   * @param string $key
   * @return mixed
   */
  public function __get($key) {
   // debug_print_backtrace();
    return $this->exists($key) ? $this->register[$key] : die('<h1>Error: Key \'<em>' . $key . '</em>\' not found in Registry!</h1>');
  }
  
  /**
   * 
   * @param string $key
   * @param mixed $value
   */
  public function __set($key, $value) {
    if ($this->isConstant($key)) {
      die('<h1>Error: Cannot override Constant \'<em>' . $key . '</em>\' in Registry!</h1>');
    } else {
      $this->register[$key] = $value;
    }
  }
  
  /**
   * 
   * @param String $key
   * @return boolean
   */
  public function isConstant($key) {
    return array_key_exists($key, $this->readOnly) && $this->readOnly[$key] === TRUE ? TRUE : FALSE;
  }
  /**
   * 
   * @param String $key
   * @return boolean
   */
  public function remove($key) {
    if ($this->isConstant($key)) {
      die('<h1>Error: Cannot remove Constant \'<em>' . $key . '</em>\' in Registry!</h1>');
    } elseif ($this->exists($key)) {
      unset($this->register[$key]);

      return TRUE;
    }

    return FALSE;
  }

  /**
   * @param $key
   * @param $args
   * @return mixed
   */
  public function __call($key, $args) {
    if ($this->exists($key) && is_callable($this->register[$key])) {
      return call_user_func_array($this->register[$key], $args);
    }
    trigger_error($key . ' is not callable.', E_USER_ERROR);
  }

  /**
   * 
   */
  public function __clone() {
    trigger_error('Cloning of this object is not allowed.', E_USER_ERROR);
  }
}