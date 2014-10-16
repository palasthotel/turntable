<?php
/**
 * Returns an array of all available node content types.
 *
 * @return array
 */
function get_available_node_content_types() {
  $cts = db_select('node_type', 'nt')->fields('nt', array(
    'type'
  ))->execute();

  $available_content_types = array();
  while ($ct = $cts->fetchAssoc()) {
    $available_content_types[] = $ct['type'];
  }
  return $available_content_types;
}

/**
 * Converts a stdClass object to an assoc array.
 *
 * @param stdClass $obj
 * @return array
 */
function std_to_array($obj) {
  $array = (array) $obj;
  foreach ($array as $key => &$field) {
    if (is_object($field) || is_array($field))
      $field = std_to_array($field);
  }
  return $array;
}
