<?php
/**
 * Database wrapper.
 *
 * @author Paul Vorbach
 */
class turntable_db {
  private $connection;
  private $author;
  private $prefix;

  /**
   * Creates a new connection to the database.
   *
   * @param string $host
   *          DB host and optional port
   * @param string $user
   *          DB user name
   * @param string $password
   *          DB user password
   * @param string $database
   *          Database in MySQL installation
   * @param string $author
   *          DB author
   * @param string $prefix
   *          optional prefix string
   */
  public function __construct($host, $user, $password, $database, $author = 'UNKNOWN', $prefix = '') {
    $port = 3306; // default port

    // host may also contain port separated with ':'
    if (strpos($host, ':') >= 0) {
      $parts = explode(':', $host);
      $host = $parts[0];
      if (isset($parts[1])) {
        $port = $parts[1];
      }
    }

    // connect to the db and store the connection
    $this->connection = new mysqli($host, $user, $password, $database, $port);
    $this->connection->set_charset('utf8');

    // remember author and prefix
    $this->author = $author;
    $this->prefix = $prefix;
  }

  /**
   * Closes the DB connection.
   */
  public function __destruct() {
    $this->connection->close();
  }
}
