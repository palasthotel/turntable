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
  const TABLE_NODE_SHARED = 'node_client_shared';

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
                'Shard status: 0 = Not a remote content, 1 = Copy, 2 = Reference')
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
            'mysql_type' => 'DATETIME',
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
    $query = 'INSERT INTO `' . $this->prefix . self::TABLE_CLIENT_INFO .
         '` (`nid`, `shared_state`) VALUES (' . $nid . ',' . $shared_state .
         ') ON DUPLICATE KEY UPDATE `shared_state`=' . $shared_state . ';';

    $result = $this->connection->query($query);
  }

  public function getSharedState($nid) {
    $query = 'SELECT `shared_state` FROM `' . $this->prefix .
         self::TABLE_CLIENT_INFO . '` WHERE `nid`= ' . $nid . ';';

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
  const TABLE_NODE_SHARED = 'node_master_shared';
  const TABLE_NODE_SUBSCRIPTIONS = 'node_master_subscriptions';

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
            'unsigned' => TRUE,
            'not null' => TRUE,
            'default' => 0,
            'description' => t('Original node type.')
          ),
          'client_user_name' => array(
            'type' => 'varchar',
            'length' => 255,
            'unsigned' => TRUE,
            'not null' => TRUE,
            'default' => 0,
            'description' => t('Origin client user name.')
          ),
          'client_author_name' => array(
            'type' => 'varchar',
            'length' => 255,
            'unsigned' => TRUE,
            'not null' => TRUE,
            'default' => 0,
            'description' => t('Origin author user name.')
          ),
          'last_sync' => array(
            'type' => 'datetime',
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
          'first_pull' => array(
            'type' => 'datetime',
            'not null' => TRUE,
            'description' => t('Time of first pull.')
          ),
          'last_sync' => array(
            'type' => 'datetime',
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
}