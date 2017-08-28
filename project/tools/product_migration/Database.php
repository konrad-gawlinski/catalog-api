<?php

namespace Nu3\ProductMigration;

use Nu3\Core\Database as Storage;

class Database
{
  const DB_USER = 'postgres';
  const DB_PASS = 'postgres';
  const DB_HOST = '172.18.0.2';
  const DB_NAME = 'catalog_api';

  /** @var Storage\Connection */
  private $db;

  function __construct()
  {
    $this->db = new Storage\Connection();
  }

  function connect()
  {
    $db = $this->db;
    $con = $db->connect(self::DB_HOST, self::DB_NAME, self::DB_USER, self::DB_PASS);

    pg_query($con, "SET CLIENT_ENCODING TO 'UTF8';");
    pg_query($con, "SET search_path TO public;");

    return $this->db->con();
  }
}