<?php

/**
 * Singleton interface.
 *
 * @author Paul Vorbach
 */
interface singleton {

  /**
   * Returns the instance.
   *
   * @return the instance of this singleton class.
   */
  public static function getInstance();
}

/**
 * Default implementation of the singleton interface.
 *
 * @author Paul Vorbach
 */
abstract class default_singleton implements singleton {
  private static $instance = NULL;

  private abstract function __construct();

  public static function getInstance() {
    if (self::$instance === NULL) {
      self::$instance = new self();
    }

    return self::$instance;
  }
}
