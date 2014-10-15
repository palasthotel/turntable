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

  // master endpoint resource (node-shared)
  const ENDPOINT_NODE_SHARED = 'api/turntable/v1/node-shared';

  // master endpoint resource (image)
  const ENDPOINT_IMAGE = 'api/turntable/v1/image';


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

    // get database information or use defaults
    $host = isset($db_opts['host']) ? $db_opts['host'] : '';
    $port = isset($db_opts['port']) ? $db_opts['port'] : 3306;
    $username = isset($db_opts['username']) ? $db_opts['username'] : '';
    $password = isset($db_opts['password']) ? $db_opts['password'] : '';
    $database = isset($db_opts['database']) ? $db_opts['database'] : '';

    // use custom db connection
    $this->db = new turntable_db_client($host, $port, $username, $password,
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

  public function setClientID($client_id) {
    $this->client_id = $client_id;
  }

  public function setMasterURL($master_url) {
    $this->master_url = $master_url;
  }

  /**
   * Sends a node as a shared node to the turntable master.
   *
   * @param object $shared_node
   */
  public function sendSharedNode($shared_node) {
    $url = $this->master_url . self::ENDPOINT_NODE_SHARED;

    $headers = array(
      'Content-Type' => 'application/json',
      'Turntable-Client-ID' => $this->client_id
    );

    // send the request with JSON encoded data
    $res = http_req('POST', $url, $headers, json_encode($shared_node));

    return $res;
  }

  public function findSharedNodes($query) {
    $url = $this->master_url . self::ENDPOINT_NODE_SHARED . '?query=' .
         urlencode($query);

    $headers = array(
      'Content-Type' => 'application/json',
      'Turntable-Client-ID' => $this->client_id
    );

    $res = http_req('GET', $url, $headers);

    return json_decode($res);
  }

  public function getSharedNode($nid) {
    $url = $this->master_url . self::ENDPOINT_NODE_SHARED . '/' . $nid;

    // set client id in http header
    $headers = array(
      'Turntable-Client-ID' => $this->client_id
    );

    // request shared node
    $res = http_req('GET', $url, $headers);

    return json_decode($res);
  }

  public function getImageURL($original_url) {
    return $this->master_url . self::ENDPOINT_IMAGE . '?url=' . $original_url;
  }
}