<?php

function http_req($method, $url, $headers, $data) {
  $header = '';
  foreach ($headers as $key => $value) {
    $header .= $key . ': ' . $value . "\r\n";
  }

  $options = array(
    'http' => array(
      'header' => $header,
      'method' => $method,
      'content' => $data
    )
  );

  $context = stream_context_create($options);

  $response = file_get_contents($url, false, $context);

  return $response;
}
