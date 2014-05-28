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
    return $this->db->getMasterSchema();
  }

  /**
   * Installs the module.
   */
  public function install() {
    $shared_type = array(
      'type' => 'shared',
      'name' => t('Shared'),
      'base' => 'node_content',
      'description' => t(
          'Shared content between different Drupal installations. Used by Turntable.'),
      'custom' => TRUE,
      'modified' => TRUE,
      'locked' => TRUE
    );

    $shared_type = node_type_set_defaults($shared_type);
    node_type_save($shared_type);
    node_add_body_field($shared_type);

    // TODO init db (probably with InnoDB foreign key settings)
  }

  /**
   * Uninstalls the module.
   */
  public function uninstall() {
    // nothing to do yet
  }
}
