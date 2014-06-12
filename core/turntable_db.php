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
          'origin_client_id' => array(
            'type' => 'int',
            'unsigned' => TRUE,
            'not null' => TRUE,
            'default' => 0,
            'description' => t('ID of the original client.')
          ),
          'origin_client_nid' => array(
            'type' => 'int',
            'unsigned' => TRUE,
            'not null' => TRUE,
            'default' => 0,
            'description' => t('Original node ID.')
          ),
          'origin_client_vid' => array(
            'type' => 'int',
            'unsigned' => TRUE,
            'not null' => TRUE,
            'default' => 0,
            'description' => t('Original node version ID.')
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
      )
    );
  }

  public function setSharedState($nid, $shared_state) {
    $query = 'INSERT INTO ' . $this->prefix . self::TABLE_NODE_SHARED .
         ' (nid, shared_state) VALUES (' . $nid . ',' . $shared_state .
         ') ON DUPLICATE KEY UPDATE shared_state=' . $shared_state . ';';

    $result = $this->connection->query($query);
  }

  public function getSharedState($nid) {
    $query = 'SELECT shared_state FROM ' . $this->prefix .
         self::TABLE_NODE_SHARED . ' WHERE nid= ' . $nid . ';';

    $result = $this->connection->query($query);

    if (!$result) {
      return 0; // default
    }

    $row = $result->fetch_assoc();

    return $row['shared_state'];
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
            'type' => 'int',
            'unsigned' => TRUE,
            'not null' => TRUE,
            'default' => 0,
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
          ),
          'complete_content' => array(
            'type' => 'text',
            'size' => 'normal',
            'not null' => TRUE,
            'description' => t(
                'Serialization of the complete content on the client.')
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
   * Checks, whether the given shared node already exists on the master and
   * returns the corresponding node id.
   *
   * @param object $shared_node
   * @return int
   */
  function getSharedNodeID($shared_node) {
    $client_id = $shared_node['client_id'];
    $client_nid = $shared_node['nid'];

    $query = 'SELECT nid AS same_node FROM ' . $this->prefix .
         self::TABLE_NODE_SHARED . ' WHERE client_id=\'' . $client_id .
         '\' AND client_nid=' . $client_nid . ';';

    $result = $this->connection->query($query);

    if (!$result || $result->num_rows == 0) {
      return FALSE; // default
    }

    $row = $result->fetch_assoc();

    return (int) $row['nid'];
  }

  function saveSharedNode($shared_node) {
    $nid = $shared_node['nid'];
    $client_id = $shared_node['client_id'];
    $client_nid = $shared_node['node_id'];
    $client_vid = $shared_node['revision_uid'];
    $client_type = $shared_node['content_type'];
    $client_user_name = $shared_node['user_name'];
    $client_author_name = $shared_node['author_name'];
    $last_sync = time();
    $complete_content = str_replace('\'', '\\\'',
        $shared_node['complete_content']);

    // insert shared node
    $query = 'INSERT INTO ' . $this->prefix . self::TABLE_NODE_SHARED .
         ' (nid, client_id, client_nid, client_vid, client_type, client_user_name, client_author_name, last_sync, complete_content) VALUES (' .
         $nid . ',\'' . $client_id . '\',' . $client_nid . ',' . $client_vid .
         ',\'' . $client_type . '\',\'' . $client_user_name . '\',\'' .
         $client_author_name . '\',' . $last_sync . ',\'' . $complete_content .
         '\');';

    return $query;

    $result = $this->connection->query($query);

    return $result;
  }
}