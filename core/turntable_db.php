<?php

/**
 * Database wrapper.
 *
 * @author Paul Vorbach
 */
class turntable_db {
  // default prefix for turntable db tables
  const DEFAULT_PREFIX = 'tt_';

  private $connection;

  private $author;

  private $prefix;

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
  public function __construct($host, $port, $user, $password, $database,
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

  public function getClientSchema() {
    return array(
      $this->prefix . 'node_client_info' => array(
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
          'shard_status' => array(
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

  public function getMasterSchema() {
    return array(
      $this->prefix . 'node_master_info' => array(
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
            'mysql_type' => 'DATETIME', // for some reason, type=datetime won't work
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
      $this->prefix . 'node_master_pull' => array(
        // Table description
        'description' => t('Info about turntable synchronization (node pulls)'),
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
            'type' => 'int',
            'unsigned' => TRUE,
            'not null' => TRUE,
            'default' => 0,
            'description' => t('ID of the client that did the pull.')
          ),
          'first_sync' => array(
            'mysql_type' => 'DATETIME',
            'not null' => TRUE,
            'description' => t('Time of first pull.')
          ),
          'last_sync' => array(
            'mysql_type' => 'DATETIME',
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
