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
  public function __construct($host, $port, $user, $password, $database, $prefix = '') {
    // set default port
    if ($port == '') {
      $port = 3306;
    }

    // connect to the db and store the connection
    $this->connection = new mysqli($host, $user, $password, $database, $port);
    $this->connection->set_charset('utf8');

    // remember prefix
    $this->prefix = $prefix . DEFAULT_PREFIX;
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
            'description' => t('Shard status: 0 = Not a remote content, 1 = Copy, 2 = Reference')
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
}
