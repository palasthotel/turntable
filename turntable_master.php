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
    $db_opts = Database::getConnection()->getConnectionOptions();

    // get database information or use defaults
    $host = isset($db_opts['host']) ? $db_opts['host'] : '';
    $port = isset($db_opts['port']) ? $db_opts['port'] : 3306;
    $username = isset($db_opts['username']) ? $db_opts['username'] : '';
    $password = isset($db_opts['password']) ? $db_opts['password'] : '';
    $database = isset($db_opts['database']) ? $db_opts['database'] : '';

    // use custom db connection
    $this->db = new turntable_db_master($host, $port, $username, $password,
        $database);
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
