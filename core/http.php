<?php

/**
 * Sends an HTTP request and returns the response as a string.
 *
 * @param string $method
 * @param string $url
 * @param array $headers
 * @param string $data
 * @return string
 */
function http_req($method, $url, $headers = array(), $data = '') {
  $header = '';
  foreach ($headers as $key => $value) {
    $header .= $key . ': ' . $value . "\r\n";
  }

  $options = array(
    'http' => array(
      'method' => $method,
      'header' => $header,
      'content' => $data
    )
  );

  $context = stream_context_create($options);

  $response = file_get_contents($url, FALSE, $context);

  return $response;
}

function http_req_open($method, $url, $headers = array(), $data = '') {
  $header = '';
  foreach ($headers as $key => $value) {
    $header .= $key . ': ' . $value . "\r\n";
  }

  $options = array(
    'http' => array(
      'method' => $method,
      'header' => $header,
      'content' => $data
    )
  );

  $context = stream_context_create($options);

  return fopen($url, 'r', FALSE, $context);
}

/**
 * Downloads the file/resource at $source_url to the file at $destination_path.
 *
 * @param string $source_url
 * @param string $destination_path
 */
function http_file_download($source_url, $destination_path) {
  file_put_contents($destination_path, http_req_open('GET', $source_url));
}

function http_file_upload($source_file, $destination_url) {
  $curl = curl_init();
  curl_setopt($curl, CURLOPT_POST, true);
  curl_setopt($curl, CURLOPT_POSTFIELDS,
      array(
        'file' => '@' . $source_file
      ));
  curl_setopt($curl, CURLOPT_URL, $destination_url);
  curl_exec($curl);
  curl_close($curl);
}
