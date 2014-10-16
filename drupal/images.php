<?php

/**
 * Ensures that the image at $image_dir_uri . $fname is available. If it is not
 * available, it is made available by downloading the image from $img_url.
 *
 * @param string $image_dir_uri uri of the image directory
 * @param string $fname name of the image file
 * @param string $img_url url of the image
 * @return array finfo including fid
 */
function ensure_image_is_available($image_dir_uri, $fname, $img_url,
    $add_to_db = TRUE) {
  $local_uri = $image_dir_uri . $fname;

  // check if image already exists
  $info = image_get_info($local_uri);
  if ($info === FALSE) {
    // prepare the directory
    if (!file_prepare_directory($image_dir_uri, FILE_CREATE_DIRECTORY)) {
      return array(
        'error' => 'Could not create the directory "' . $image_dir_uri . '".'
      );
    }

    // download the file
    $finfo = system_retrieve_file($img_url, $local_uri, FALSE,
        FILE_EXISTS_REPLACE);
    if ($finfo === FALSE) {
      return array(
        'error' => 'Could not retrieve the requested file "' . $img_url . '".'
      );
    }

    $img_info = getimagesize($local_uri);

    if ($add_to_db) {
      $entity_type = 'image';
      $entity = entity_create($entity_type);

      // set meta data
      $ewrapper = entity_metadata_wrapper($entity_type, $entity);
      $ewrapper->field_image_fid->set($finfo->fid);
      $ewrapper->field_image_width->set($img_info[0]);
      $ewrapper->field_image_height->set($img_info[1]);
      $ewrapper->save();
    }

    $info = array(
      'fid' => $finfo->fid,
      'uri' => $local_uri,
      'width' => $img_info[0],
      'height' => $img_info[1],
      'extension' => pathinfo($finfo->filename, PATHINFO_EXTENSION),
      'mime_type' => $finfo->filemime,
      'file_size' => $finfo->filesize
    );
  } else {
    $finfo = reset(
        file_load_multiple(array(), array(
          'uri' => $local_uri
        )));

    $info['fid'] = $finfo->fid;
    $info['uri'] = $local_uri;
  }

  return $info;
}

function download_image($original_image_url) {
  $dir = 'public://turntable_files/';
  $fname = url_to_filename($original_image_url);

  $turntable_client = turntable_client::getInstance();
  $master_url = $turntable_client->getImageURL($original_image_url);

  $info = ensure_image_is_available($dir, $fname, $master_url);

  if (isset($info['fid'])) {
    return $info;
  } else {
    return FALSE;
  }
}

function get_image_url($image) {
  if (isset($image['uri'])) {
    $uri = $image['uri'];
  } else {
    $file = file_load($image['fid']);
    $uri = $file->uri;
  }

  return file_create_url($uri);
}

/**
 * Downloads required images and replaces original image fids with local fids.
 *
 * @param entity_metadata_wrapper $ewrapper
 * @param array $image_refs
 * @param boolean $show_messages
 *          whether to show messages or not
 */
function resolve_image_references($ewrapper, array $image_refs,
    $show_messages = FALSE) {
  $info = $ewrapper->getPropertyInfo();
  // walk fields
  foreach ($info as $field => $field_properties) {
    $is_list = FALSE;
    $type = NULL;
    $inner_type = FALSE;

    // classify the field
    classify_field($field, $field_properties, $is_list, $type, $inner_type);
    if ($type === 'field_item_image') {
      // check if the field is an image
      // get fid and uri/url of single image refs
      $image = $ewrapper->$field->value();
      // track if something changed
      $change = FALSE;
      // find a relevant image_ref, then download the image and replace the fid
      foreach ($image_refs as $image_ref) {
        if ($image['fid'] == $image_ref['fid']) {
          $new_img = download_image($image_ref['uri']);
          if ($new_img !== FALSE) {
            $image['fid'] = $new_img['fid'];
            $image['uri'] = $new_img['uri'];
            $change = TRUE;
          } else {
            if ($show_messages) {
              drupal_set_message(t('Could not download an image.'), 'warning');
            }
            return;
          }
        }
      }
      if ($change) {
        // if something changed, save the changes
        $ewrapper->$field->set($image);
      }
    } else if ($inner_type === 'field_item_image') {
      // check if the field is a list of images

      // track if something changed
      $change = FALSE;

      $images = $ewrapper->$field->value();

      foreach ($images as &$image) {
        // find a relevant image_ref, then download the image and replace the fid
        foreach ($image_refs as $image_ref) {
          if ($image['fid'] == $image_ref['fid']) {
            $new_img = download_image($image_ref['uri']);
            if ($new_img !== FALSE) {
              $image['fid'] = $new_img['fid'];
              $image['uri'] = $new_img['uri'];
              $change = TRUE;
            } else {
              if ($show_messages) {
                drupal_set_message(t('Could not download an image.'), 'warning');
              }
              return;
            }
          }
        }
      }

      if ($change) {
        // if something changed, save the changes
        $ewrapper->$field->set($images);
      }
    }
  }
}
