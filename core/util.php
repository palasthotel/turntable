function url_to_filename($url) {
  return str_replace(array(
    ':',
    '/'
  ), array(
    '_',
    '_'
  ), $url);
}
