<?php
// name of the master node info table
// (used to store information about nodes that have been pushed by a client)
define('TT_MASTER_NODE_INFO', 'tt_master_node_info');

// name of the master node info table
// (used to store information about pull behavior)
define('TT_MASTER_NODE_PULL', 'tt_master_node_pull');

/**
 * Main class of the Turntable Master.
 *
 * @author Paul Vorbach
 */
class turntable_master {
  /**
   * Creates the new Turntable Master.
   */
  public function __construct() {
    // TODO require_once 'classes/bootstrap.php';
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
    return array(
      'tt_node_master_info' => array(
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
            'type' => 'datetime:normal',
            'not null' => TRUE,
            'description' => t('Time of last sync.')
          ),
          'complete_content' => array(
            'type' => 'text',
            'size' => 'normal',
            'not null' => TRUE,
            'description' => t('Serialization of the complete content on the client.')
          )
        ),
        'primary key' => array(
          'nid'
        )
      ),
      'tt_node_master_pull' => array(
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
            'type' => 'datetime:normal',
            'not null' => TRUE,
            'description' => t('Time of first pull.')
          ),
          'last_sync' => array(
            'type' => 'datetime:normal',
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
   * Installs the module.
   */
  public function install() {
    $shard_type = array(
      'type' => 'shard',
      'name' => t('Shard'),
      'base' => 'node_content',
      'description' => t('Shared content between different Drupal installations. Used by Turntable.'),
      'custom' => TRUE,
      'modified' => TRUE,
      'locked' => TRUE
    );

    $shard_type = node_type_set_defaults($shard_type);
    node_type_save($shard_type);
    node_add_body_field($shard_type);

    // TODO init db (probably with InnoDB foreign key settings)
  }

  /**
   * Uninstalls the module.
   */
  public function uninstall() {
    // nothing to do yet
  }
}
