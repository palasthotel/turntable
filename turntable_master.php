<?php
require_once './sites/all/libraries/turntable/core/turntable_db.php';

/**
 * Main class of the Turntable Master.
 * (Singleton)
 *
 * @author Paul Vorbach
 */
class turntable_master {
  // instance field
  private static $instance = NULL;

  private $db;

  /**
   * Creates the new Turntable Master.
   */
  private function __construct() {
    $db_conn = Database::getConnection();
    $db_opts = $db_conn->getConnectionOptions();

    $port = isset($db_opts['port']) ? $db_opts['port'] : null;
    // use custom db connection
    $this->db = new turntable_db_client($db_opts['host'], $port,
        $db_opts['username'], $db_opts['password'], $db_opts['database']);
  }

  public static function getInstance() {
    if (self::$instance === NULL) {
      self::$instance = new self();
    }

    return self::$instance;
  }

  public function getDB() {
    return $this->db;
  }

  /**
   * Installs the module.
   */
  public function install() {
    // TODO init db (probably with InnoDB foreign key settings)
  }

  /**
   * Uninstalls the module.
   */
  public function uninstall() {
    // TODO what to do here?
    // uninstalling currently undefined
  }
}
