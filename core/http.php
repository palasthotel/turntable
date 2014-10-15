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
  $ch = curl_init();

  curl_setopt($ch, CURLOPT_HEADER, FALSE);
  curl_setopt($ch, CURLOPT_FOLLOWLOCATION, TRUE);
  curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE); // Set curl to return the data instead of printing it to the browser.
  curl_setopt($ch, CURLOPT_URL, $url);
  curl_setopt($ch, CURLOPT_PROXY,"http://188.246.6.163:8080");

  if (is_array($headers) && sizeof($headers) > 0) {
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
  }

  if ($method == 'GET' || $method == 'get') {
    curl_setopt($ch, CURLOPT_HTTPGET, TRUE);
  } else if ($method == 'POST' || $method == 'post') {
    curl_setopt($ch, CURLOPT_POST, TRUE);
    if ($data) {
      curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    }
  }

  $data = curl_exec($ch);
  curl_close($ch);

  return $data;
}
