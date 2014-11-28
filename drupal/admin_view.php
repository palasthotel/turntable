<?php

function _get_admin_view($is_master = FALSE) {
  $term = variable_get('turntable_term', 'Turntable');

  $view = new view();
  $view->name = 'turntable_admin';
  $view->description = '';
  $view->tag = 'default';
  $view->base_table = 'node';
  $view->human_name = t('@turntable Admin',
      array(
        '@turntable' => $term
      ));
  $view->core = 7;
  $view->api_version = '3.0';
  $view->disabled = FALSE; /* Edit this to true to make a default view disabled initially */

  /* Display: Master */
                $handler = $view->new_display('default', 'Master', 'default');
  $handler->display->display_options['title'] = t('@turntable Admin',
      array(
        '@turntable' => $term
      ));
  $handler->display->display_options['use_more_always'] = FALSE;
  $handler->display->display_options['access']['type'] = 'perm';
  $handler->display->display_options['access']['perm'] = 'administer nodes';
  $handler->display->display_options['cache']['type'] = 'none';
  $handler->display->display_options['query']['type'] = 'views_query';
  $handler->display->display_options['exposed_form']['type'] = 'basic';
  $handler->display->display_options['pager']['type'] = 'full';
  $handler->display->display_options['pager']['options']['items_per_page'] = '30';
  $handler->display->display_options['style_plugin'] = 'table';
  if (!$is_master) {
    $handler->display->display_options['style_options']['columns'] = array(
      'title' => 'title',
      'path' => 'path',
      'shared_state' => 'shared_state',
      'last_sync' => 'last_sync',
      'original_client_id' => 'original_client_id',
      'edit_node' => 'edit_node',
      'delete_node' => 'delete_node',
      'edit_tt_settings' => 'edit_tt_settings'
    );
  } else {
    $handler->display->display_options['style_options']['columns'] = array(
      'title' => 'title',
      'path' => 'path',
      'client_id' => 'client_id',
      'client_user_name' => 'client_user_name',
      'edit_node' => 'edit_node',
      'delete_node' => 'delete_node'
    );
  }
  $handler->display->display_options['style_options']['default'] = 'last_sync';
  if (!$is_master) {
    $handler->display->display_options['style_options']['info'] = array(
      'title' => array(
        'sortable' => 1,
        'default_sort_order' => 'asc',
        'align' => '',
        'separator' => '',
        'empty_column' => 0
      ),
      'path' => array(
        'align' => '',
        'separator' => '',
        'empty_column' => 0
      ),
      'shared_state' => array(
        'sortable' => 1,
        'default_sort_order' => 'asc',
        'align' => '',
        'separator' => '',
        'empty_column' => 0
      ),
      'last_sync' => array(
        'sortable' => 1,
        'default_sort_order' => 'desc',
        'align' => '',
        'separator' => '',
        'empty_column' => 0
      ),
      'original_client_id' => array(
        'sortable' => 1,
        'default_sort_order' => 'asc',
        'align' => '',
        'separator' => '',
        'empty_column' => 0
      ),
      'edit_node' => array(
        'align' => '',
        'separator' => '',
        'empty_column' => 0
      ),
      'delete_node' => array(
        'align' => '',
        'separator' => '',
        'empty_column' => 0
      ),
      'edit_tt_settings' => array(
        'align' => '',
        'separator' => '',
        'empty_column' => 0
      )
    );
  } else {
    $handler->display->display_options['style_options']['info'] = array(
      'title' => array(
        'sortable' => 1,
        'default_sort_order' => 'asc',
        'align' => '',
        'separator' => '',
        'empty_column' => 0
      ),
      'path' => array(
        'align' => '',
        'separator' => '',
        'empty_column' => 0
      ),
      'last_sync' => array(
        'sortable' => 1,
        'default_sort_order' => 'desc',
        'align' => '',
        'separator' => '',
        'empty_column' => 0
      ),
      'client_id' => array(
        'sortable' => 1,
        'default_sort_order' => 'asc',
        'align' => '',
        'separator' => '',
        'empty_column' => 0
      ),
      'edit_node' => array(
        'align' => '',
        'separator' => '',
        'empty_column' => 0
      ),
      'delete_node' => array(
        'align' => '',
        'separator' => '',
        'empty_column' => 0
      )
    );
  }
  /* Field: Content: Title */
  $handler->display->display_options['fields']['title']['id'] = 'title';
  $handler->display->display_options['fields']['title']['table'] = 'node';
  $handler->display->display_options['fields']['title']['field'] = 'title';
  $handler->display->display_options['fields']['title']['alter']['word_boundary'] = FALSE;
  $handler->display->display_options['fields']['title']['alter']['ellipsis'] = FALSE;
  /* Field: Content: Path */
  $handler->display->display_options['fields']['path']['id'] = 'path';
  $handler->display->display_options['fields']['path']['table'] = 'node';
  $handler->display->display_options['fields']['path']['field'] = 'path';
  if (!$is_master) {
    /* Field: Shared Content: Shared state */
    $handler->display->display_options['fields']['shared_state']['id'] = 'shared_state';
    $handler->display->display_options['fields']['shared_state']['table'] = 'tt_client_node_shared';
    $handler->display->display_options['fields']['shared_state']['field'] = 'shared_state';
    /* Field: Shared Content: Origin */
    $handler->display->display_options['fields']['original_client_id']['id'] = 'original_client_id';
    $handler->display->display_options['fields']['original_client_id']['table'] = 'tt_client_node_shared';
    $handler->display->display_options['fields']['original_client_id']['field'] = 'original_client_id';
    /* Field: Shared Content: Last sync */
    $handler->display->display_options['fields']['last_sync']['id'] = 'last_sync';
    $handler->display->display_options['fields']['last_sync']['table'] = 'tt_client_node_shared';
    $handler->display->display_options['fields']['last_sync']['field'] = 'last_sync';
  } else {
    /* Field: Shared Content: Origin */
    $handler->display->display_options['fields']['client_id']['id'] = 'client_id';
    $handler->display->display_options['fields']['client_id']['table'] = 'tt_master_node_shared';
    $handler->display->display_options['fields']['client_id']['field'] = 'client_id';
    /* Field: Shared Content: Origin */
    $handler->display->display_options['fields']['client_author_name']['id'] = 'client_author_name';
    $handler->display->display_options['fields']['client_author_name']['table'] = 'tt_master_node_shared';
    $handler->display->display_options['fields']['client_author_name']['field'] = 'client_author_name';
    /* Field: Shared Content: Last sync */
    $handler->display->display_options['fields']['last_sync']['id'] = 'last_sync';
    $handler->display->display_options['fields']['last_sync']['table'] = 'tt_master_node_shared';
    $handler->display->display_options['fields']['last_sync']['field'] = 'last_sync';
  }
  /* Field: Content: Edit link */
  $handler->display->display_options['fields']['edit_node']['id'] = 'edit_node';
  $handler->display->display_options['fields']['edit_node']['table'] = 'views_entity_node';
  $handler->display->display_options['fields']['edit_node']['field'] = 'edit_node';
  $handler->display->display_options['fields']['edit_node']['label'] = 'Edit';
  /* Field: Content: Delete link */
  $handler->display->display_options['fields']['delete_node']['id'] = 'delete_node';
  $handler->display->display_options['fields']['delete_node']['table'] = 'views_entity_node';
  $handler->display->display_options['fields']['delete_node']['field'] = 'delete_node';
  $handler->display->display_options['fields']['delete_node']['label'] = 'Delete';
  if (!$is_master) {
    /* Field: Shared Content: Turntable settings */
    $handler->display->display_options['fields']['edit_tt_settings']['id'] = 'edit_tt_settings';
    $handler->display->display_options['fields']['edit_tt_settings']['table'] = 'custom';
    $handler->display->display_options['fields']['edit_tt_settings']['field'] = 'edit_tt_settings';
  }
  /* Sort criterion: Content: Post date */
  $handler->display->display_options['sorts']['created']['id'] = 'created';
  $handler->display->display_options['sorts']['created']['table'] = 'node';
  $handler->display->display_options['sorts']['created']['field'] = 'created';
  $handler->display->display_options['sorts']['created']['order'] = 'DESC';
  /* Filter criterion: Search: Search Terms */
  $handler->display->display_options['filters']['keys']['id'] = 'keys';
  $handler->display->display_options['filters']['keys']['table'] = 'search_index';
  $handler->display->display_options['filters']['keys']['field'] = 'keys';
  $handler->display->display_options['filters']['keys']['exposed'] = TRUE;
  $handler->display->display_options['filters']['keys']['expose']['operator_id'] = 'keys_op';
  $handler->display->display_options['filters']['keys']['expose']['label'] = 'Search';
  $handler->display->display_options['filters']['keys']['expose']['operator'] = 'keys_op';
  $handler->display->display_options['filters']['keys']['expose']['identifier'] = 'keys';
  $handler->display->display_options['filters']['keys']['expose']['remember_roles'] = array(
    2 => '2',
    1 => 0,
    3 => 0
  );
  if (!$is_master) {
    /* Filter criterion: Shared Content: Shared state */
    $handler->display->display_options['filters']['shared_state']['id'] = 'shared_state';
    $handler->display->display_options['filters']['shared_state']['table'] = 'tt_client_node_shared';
    $handler->display->display_options['filters']['shared_state']['field'] = 'shared_state';
    $handler->display->display_options['filters']['shared_state']['exposed'] = TRUE;
    $handler->display->display_options['filters']['shared_state']['expose']['operator_id'] = 'shared_state_op';
    $handler->display->display_options['filters']['shared_state']['expose']['label'] = 'Shared state';
    $handler->display->display_options['filters']['shared_state']['expose']['operator'] = 'shared_state_op';
    $handler->display->display_options['filters']['shared_state']['expose']['identifier'] = 'shared_state';
    $handler->display->display_options['filters']['shared_state']['expose']['multiple'] = TRUE;
    $handler->display->display_options['filters']['shared_state']['is_grouped'] = TRUE;
    $handler->display->display_options['filters']['shared_state']['group_info']['label'] = 'Shared state';
    $handler->display->display_options['filters']['shared_state']['group_info']['identifier'] = 'shared_state';
    $handler->display->display_options['filters']['shared_state']['group_info']['widget'] = 'radios';
    $handler->display->display_options['filters']['shared_state']['group_info']['group_items'] = array(
      1 => array(
        'title' => 'Copies',
        'operator' => '=',
        'value' => '1'
      ),
      2 => array(
        'title' => 'References',
        'operator' => '=',
        'value' => '2'
      ),
      3 => array(
        'title' => 'Original nodes',
        'operator' => '=',
        'value' => '3'
      )
    );
  }

  /* Display: Page */
  $handler = $view->new_display('page', 'Page', 'page');
  $handler->display->display_options['path'] = 'admin/content/turntable-admin';
  $handler->display->display_options['menu']['type'] = 'tab';
  $handler->display->display_options['menu']['title'] = t('@turntable Admin',
      array(
        '@turntable' => $term
      ));
  $handler->display->display_options['menu']['description'] = 'Manage shared content';
  $handler->display->display_options['menu']['weight'] = '0';
  $handler->display->display_options['menu']['name'] = 'management';
  $handler->display->display_options['menu']['context'] = 0;
  $handler->display->display_options['menu']['context_only_inline'] = 0;

  return $view;
}
