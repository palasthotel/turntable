<?php

/**
 * Database wrapper.
 *
 * @author Paul Vorbach
 */
abstract class turntable_db {
  // default prefix for turntable db tables
  const DEFAULT_PREFIX = 'tt_';

  protected $connection;

  protected $prefix;

  /**
   * Creates a new connection to the database.
   *
   * @param string $host
   *          DB host
   * @param string $port
   *          DB port
   * @param string $user
   *          DB user name
   * @param string $password
   *          DB user password
   * @param string $database
   *          Database in MySQL installation
   * @param string $prefix
   *          optional prefix string
   */
  protected function __construct($host, $port, $user, $password, $database,
      $prefix = '') {
    // set default port
    if ($port == '') {
      $port = 3306;
    }

    // connect to the db and store the connection
    $this->connection = new mysqli($host, $user, $password, $database, $port);
    $this->connection->set_charset('utf8');

    // remember prefix
    $this->prefix = $prefix . self::DEFAULT_PREFIX;
  }

  /**
   * Closes the DB connection.
   */
  public function __destruct() {
    $this->connection->close();
  }
}

/**
 * Client database wrapper.
 *
 * @author Paul Vorbach
 */
class turntable_db_client extends turntable_db {
  // table name
  const TABLE_NODE_SHARED = 'client_node_shared';

  public function __construct($host, $port, $user, $password, $database,
      $prefix = '') {
    parent::__construct($host, $port, $user, $password, $database, $prefix);
  }

  public function getSchema() {
    return array(
      $this->prefix . self::TABLE_NODE_SHARED => array(
        // Table description
        'description' => t('Additional node information for turntable client.'),
        'fields' => array(
          'nid' => array(
            'type' => 'int',
            'unsigned' => TRUE,
            'not null' => TRUE,
            'default' => 0,
            'description' => t('Local node ID.')
          ),
          'shared_state' => array(
            'type' => 'int',
            'size' => 'tiny',
            'unsigned' => TRUE,
            'not null' => TRUE,
            'default' => 0,
            'description' => t(
                'Shared status: 0 = None, 1 = Copy, 2 = Reference, 3 = Original')
          ),
          'master_node_id' => array(
            'type' => 'int',
            'unsigned' => TRUE,
            'not null' => FALSE,
            'default' => NULL,
            'description' => t('Master node ID.')
          ),
          'original_client_id' => array(
            'type' => 'varchar',
            'length' => 255,
            'not null' => FALSE,
            'default' => NULL,
            'description' => t('ID of the original client.')
          ),
          'original_client_nid' => array(
            'type' => 'int',
            'unsigned' => TRUE,
            'not null' => FALSE,
            'default' => NULL,
            'description' => t('Original node ID.')
          ),
          'original_client_vid' => array(
            'type' => 'int',
            'unsigned' => TRUE,
            'not null' => FALSE,
            'default' => NULL,
            'description' => t('Original node version ID.')
          ),
          'last_sync' => array(
            'mysql_type' => 'datetime',
            'not null' => NULL,
            'description' => t('Time of last sync.')
          )
        ),
        'primary key' => array(
          'nid'
        )
      )
    );
  }

  public function addSharedNode($shared_node) {
    $table = $this->prefix . self::TABLE_NODE_SHARED;

    $nid = $shared_node->nid;
    $shared_state = $shared_node->shared_state;
    $master_node_id = $shared_node->master_node_id;
    $original_client_id = $shared_node->client_id;
    $original_client_nid = $shared_node->client_nid;
    $original_client_vid = $shared_node->revision_uid;
    $last_sync = date('Y-m-d H:i:s', $shared_node->last_sync->getTimestamp());

    $sql = <<<SQL
INSERT INTO $table
  (nid, shared_state, master_node_id, original_client_id, original_client_nid, original_client_vid,
    last_sync)
VALUES
  ($nid, $shared_state, $master_node_id, '$original_client_id',
     '$original_client_nid', '$original_client_vid', '$last_sync');
SQL;

    $res = $this->connection->query($sql);

    return $res;
  }

  public function getSharedNodeID($master_node_id) {
    $table = $this->prefix . self::TABLE_NODE_SHARED;

    $sql = <<<SQL
SELECT nid
FROM $table
WHERE master_node_id=$master_node_id;
SQL;

    $res = $this->connection->query($sql);

    if (!$res || $res->num_rows == 0) {
      return FALSE; // default
    }

    $assoc = $res->fetch_assoc();

    return $assoc['nid'];
  }

  public function deleteSharedNode($nid) {
    $table = $this->prefix . self::TABLE_NODE_SHARED;

    $sql = <<<SQL
DELETE FROM $table
WHERE nid=$nid;
SQL;

    return $this->connection->query($sql);
  }

  public function setSharedState($nid, $shared_state) {
    $table = $this->prefix . self::TABLE_NODE_SHARED;

    $sql = <<<SQL
INSERT INTO $table
  (nid, shared_state)
VALUES
  ($nid, $shared_state)
ON DUPLICATE KEY UPDATE
  shared_state=$shared_state;
SQL;

    $res = $this->connection->query($sql);

    return $res;
  }

  public function setSharedLastSync($nid, $last_sync) {
    $table = $this->prefix . self::TABLE_NODE_SHARED;

    $last_sync = date('Y-m-d H:i:s', $last_sync);

    $sql = <<<SQL
UPDATE $table
SET
  last_sync='$last_sync'
WHERE
  nid=$nid;
SQL;

    $res = $this->connection->query($sql);

    return $res;
  }

  public function getSharedState($nid) {
    $table = $this->prefix . self::TABLE_NODE_SHARED;

    $sql = <<<SQL
SELECT shared_state
FROM $table
WHERE nid=$nid;
SQL;

    $res = $this->connection->query($sql);

    if (!$res) {
      return 0; // default
    }

    $row = $res->fetch_assoc();

    return $row['shared_state'];
  }

  public function getSharedStates() {
    $table = $this->prefix . self::TABLE_NODE_SHARED;

    $sql = <<<SQL
SELECT nid, shared_state, master_node_id, last_sync
FROM $table;
SQL;

    $res = $this->connection->query($sql);

    if (!$res) {
      return array(); // default
    }

    $result = array();
    while ($row = $res->fetch_assoc()) {
      // convert strings to ints
      $row['nid'] = (int) $row['nid'];
      $row['shared_state'] = (int) $row['shared_state'];
      $row['master_node_id'] = (int) $row['master_node_id'];

      $result[] = $row;
    }

    return $result;
  }
}

/**
 * Master database wrapper.
 *
 * @author Paul Vorbach
 */
class turntable_db_master extends turntable_db {
  const TABLE_NODE_SHARED = 'master_node_shared';
  const TABLE_NODE_SUBSCRIPTIONS = 'master_node_subscriptions';

  public function __construct($host, $port, $user, $password, $database,
      $prefix = '') {
    parent::__construct($host, $port, $user, $password, $database, $prefix);
  }

  public function getSchema() {
    return array(
      $this->prefix . self::TABLE_NODE_SHARED => array(
        // Table description
        'description' => t('Additional node information for turntable master'),
        'fields' => array(
          'nid' => array(
            'type' => 'int',
            'unsigned' => TRUE,
            'not null' => TRUE,
            'default' => 0,
            'description' => t('Local node ID.')
          ),
          'client_id' => array(
            'type' => 'varchar',
            'length' => 255,
            'not null' => TRUE,
            'default' => '',
            'description' => t('ID of the original client.')
          ),
          'client_nid' => array(
            'type' => 'int',
            'unsigned' => TRUE,
            'not null' => TRUE,
            'default' => 0,
            'description' => t('Original node ID.')
          ),
          'client_vid' => array(
            'type' => 'int',
            'unsigned' => TRUE,
            'not null' => TRUE,
            'default' => 0,
            'description' => t('Original node version ID.')
          ),
          'client_type' => array(
            'type' => 'varchar',
            'length' => 32,
            'not null' => TRUE,
            'default' => '',
            'description' => t('Original node type.')
          ),
          'client_user_name' => array(
            'type' => 'varchar',
            'length' => 255,
            'not null' => TRUE,
            'default' => '',
            'description' => t('Origin client user name.')
          ),
          'client_author_name' => array(
            'type' => 'varchar',
            'length' => 255,
            'not null' => TRUE,
            'default' => '',
            'description' => t('Origin author user name.')
          ),
          'last_sync' => array(
            'mysql_type' => 'datetime',
            'not null' => TRUE,
            'description' => t('Time of last sync.')
          )
        ),
        'primary key' => array(
          'nid'
        )
      ),
      $this->prefix . self::TABLE_NODE_SUBSCRIPTIONS => array(
        // Table description
        'description' => t('Info about turntable subscriptions'),
        'fields' => array(
          'sync_id' => array(
            'type' => 'int',
            'unsigned' => TRUE,
            'not null' => TRUE,
            'default' => 0,
            'description' => t('Unique ID of the pull.')
          ),
          'nid' => array(
            'type' => 'int',
            'unsigned' => TRUE,
            'not null' => TRUE,
            'default' => 0,
            'description' => t('ID of the local node.')
          ),
          'client_id' => array(
            'type' => 'varchar',
            'length' => 255,
            'not null' => TRUE,
            'description' => t('ID of the client that did the pull.')
          ),
          'first_sync' => array(
            'mysql_type' => 'datetime',
            'not null' => TRUE,
            'description' => t('Time of first pull.')
          ),
          'last_sync' => array(
            'mysql_type' => 'datetime',
            'not null' => TRUE,
            'description' => t('Time of most recent pull.')
          )
        ),
        'primary key' => array(
          'sync_id'
        )
      )
    );
  }

  /**
   * Checks whether the given shared node already exists on the master and
   * returns the corresponding node id.
   *
   * @param object $shared_node
   * @return int
   */
  function getSharedNodeID($shared_node) {
    $client_id = $shared_node['client_id'];
    $client_nid = $shared_node['node_id'];

    $table = $this->prefix . self::TABLE_NODE_SHARED;

    $sql = <<<SQL
SELECT nid
FROM $table
WHERE client_id='$client_id'
  AND client_nid=$client_nid;
SQL;

    $res = $this->connection->query($sql);

    if (!$res || $res->num_rows == 0) {
      return FALSE; // default
    }

    $row = $res->fetch_assoc();

    return (int) $row['nid'];
  }

  public function addSharedNode($shared_node) {
    $nid = $shared_node['nid'];
    $client_id = $shared_node['client_id'];
    $client_nid = $shared_node['node_id'];
    $client_vid = $shared_node['revision_uid'];
    $client_type = $shared_node['content_type'];
    $client_user_name = $shared_node['user_name'];
    $client_author_name = $shared_node['author_name'];
    $last_sync = date('Y-m-d H:i:s');

    $table = $this->prefix . self::TABLE_NODE_SHARED;

    // insert shared node
    $query = <<<SQL
INSERT INTO $table
  (nid, client_id, client_nid, client_vid, client_type, client_user_name,
  client_author_name, last_sync)
VALUES ($nid, '$client_id', $client_nid, $client_vid, '$client_type',
  '$client_user_name', '$client_author_name', '$last_sync');
SQL;

    return $this->connection->query($query);
  }

  public function updateSharedNode($shared_node) {
    $nid = $shared_node['nid'];
    $client_id = $shared_node['client_id'];
    $client_nid = $shared_node['node_id'];
    $client_vid = $shared_node['revision_uid'];
    $client_type = $shared_node['content_type'];
    $client_user_name = $shared_node['user_name'];
    $client_author_name = $shared_node['author_name'];
    $last_sync = date('Y-m-d H:i:s');

    $table = $this->prefix . self::TABLE_NODE_SHARED;

    $query = <<<SQL
UPDATE $table
SET client_vid=$client_vid,
  client_type='$client_type',
  client_user_name='$client_user_name',
  client_author_name='$client_author_name',
  last_sync='$last_sync'
WHERE nid=$nid;
SQL;

    return $this->connection->query($query);
  }

  public function findSharedNode($query) {
    $table = $this->prefix . self::TABLE_NODE_SHARED;

    $search_terms = preg_split('/\s+/', $query);

    $num_of_terms = count($search_terms);

    // return empty result if no search term is given
    if ($num_of_terms == 1 && $search_terms[0] == '') {
      return array();
    }

    // build the sql condition
    $sql_cond = '';
    for($i = 0; $i < $num_of_terms; ++ $i) {
      $term = $search_terms[$i];
      $esc_term = $this->connection->real_escape_string($term);
      $sql_cond .= '  AND body.body_value LIKE \'%' . $esc_term . '%\''."\n";
    }

    $sql = <<<SQL
SELECT node.nid, node.title, ns.client_id, ns.client_author_name as author, ns.last_sync
FROM $table as ns, node, field_data_body as body
WHERE node.nid=ns.nid
  AND node.nid=body.entity_id
  AND body.bundle='shared'
  $sql_cond
ORDER BY ns.last_sync DESC;
SQL;

    $res = $this->connection->query($sql);

    // convert result set to array
    $results = array();
    while ($row = $res->fetch_assoc()) {
      // ensure integer
      $row['nid'] = (int) $row['nid'];

      $results[] = $row;
    }

    return $results;
  }

  public function getSharedNode($nid) {
    $table = $this->prefix . self::TABLE_NODE_SHARED;

    $sql = <<<SQL
SELECT client_id, client_nid, client_vid, client_type, client_user_name, client_author_name, last_sync
FROM $table
WHERE nid=$nid
SQL;

    $res = $this->connection->query($sql);

    $shared = $res->fetch_assoc();

    // fix types
    $shared['client_nid'] = (int) $shared['client_nid'];
    $shared['client_vid'] = (int) $shared['client_vid'];

    return $shared;
  }

  public function deleteSharedNode($nid) {
    $table = $this->prefix . self::TABLE_NODE_SHARED;

    $sql = <<<SQL
DELETE FROM $table
WHERE nid=$nid;
SQL;

    return $this->connection->query($sql);
  }
}
