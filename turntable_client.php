<?php
require_once './sites/all/libraries/turntable/core/turntable_db.php';
require_once './sites/all/libraries/turntable/core/http.php';

/**
 * Main class of the Turntable Client.
 * (Singleton)
 *
 * @author Paul Vorbach
 */
class turntable_client {
  // possible SYNC states
  const SHARED_NONE = 0; // not shared
  const SHARED_COPY = 1; // copy of a node on the master
  const SHARED_REF = 2; // reference to a node on the master
  const SHARED_ORIG = 3; // original node (changes will be sent to master)

  // instance field
  private static $instance = NULL;

  private $db;

  private $client_id;

  private $master_url;

  /**
   * Creates the new Turntable Client.
   */
  protected function __construct() {
    $db_opts = Database::getConnection()->getConnectionOptions();

    // use custom db connection
    $this->db = new turntable_db_client($db_opts['host'], $db_opts['port'],
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
   * Returns the db schema (in Drupals schema DSL).
   *
   * @return array
   */
  public function getDatabaseSchema() {
    return $this->db->getSchema();
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

  public function setClientID($client_id) {
    $this->client_id = $client_id;
  }

  public function setMasterURL($master_url) {
    $this->master_url = $master_url;
  }

  public function sendSharedNode($node, $user) {
    $headers = array(
      'Content-Type' => 'application/json'
    );

    $data = array();

    // set data
    $data['title'] = $node->title;
    $data['body'] = $node->body[$node->language][0]['safe_value'];
    $data['language'] = $node->language;

    // set metadata
    $data['client_id'] = $this->client_id;
    $data['node_id'] = $node->nid;
    $data['revision_uid'] = $node->revision_uid;
    $data['content_type'] = $node->type;
    $data['user_name'] = $user->name;
    $data['author_name'] = $node->name;
    $data['last_sync'] = (string) time();
    $data['complete_content'] = json_encode($node);

    // send the request with JSON encoded data
    $response = http_req('POST', $this->master_url, $headers,
        json_encode($data));

    // TODO parse response
  }
}
