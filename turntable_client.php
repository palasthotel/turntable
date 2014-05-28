<?php
require_once './sites/all/libraries/turntable/core/turntable_db.php';

/**
 * Main class of the Turntable Client.
 * (Singleton)
 *
 * @author Paul Vorbach
 */
class turntable_client {
  // possible SYNC states
  const SYNC_NONE = 0;
  const SYNC_COPY = 1;
  const SYNC_REF = 2;
  const SYNC_ORIG = 3;

  private static $instance = NULL;

  private $db;

  /**
   * Creates the new Turntable Client.
   */
  private function __construct() {
    $db_conn = Database::getConnection();
    $db_opts = $db_conn->getConnectionOptions();

    // use custom db connection
    $this->db = new turntable_db($db_opts['host'], $db_opts['port'],
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
   * Gets home dir path.
   *
   * @return string
   */
  private function getHome() {
    return dirname(__FILE__) . '/';
  }

  /**
   * Returns the db schema (in Drupals schema DSL).
   *
   * @return array
   */
  public function getDatabaseSchema() {
    return $this->db->getClientSchema();
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
  }

  public function pushNode($node) {
    $master_url = $this->getMasterUrl();
    http_post_fields($master_url, $node);
  }
}
