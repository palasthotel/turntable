<?php
/**
 * Main class of the Turntable Client.
 *
 * @author Paul Vorbach
 */
class turntable_client {
  /**
   * Creates the new Turntable Client.
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
      'tt_node_client_info' => array(
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
            'type' => 'datetime',
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
}
