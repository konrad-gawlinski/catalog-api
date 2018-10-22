<?php
namespace Migration\Database;

class Target
{
  private $connection = null;

  private $country = '';
  private $language = '';

  function connect(string $host, string $port, string $dbname, string $user, string $password)
  {
    $this->connection = pg_connect("host={$host} port={$port} dbname={$dbname} user={$user} password={$password} connect_timeout=1", PGSQL_CONNECT_FORCE_NEW);

    if (!$this->connection)
      throw new \Exception('Could not connect: ' . pg_last_error());

    return $this->connection;
  }

  function setSearchPath(string $searchPath)
  {
    $result = pg_query($this->connection, "SELECT set_config('search_path', '{$searchPath}', false);");

    if ($result === false)
      throw new \Exception("Could not set search_path config '{$searchPath}': " . pg_last_error());
  }

  function con()
  {
    return $this->connection;
  }


  function setCountry($country)
  {
    $this->country = $country;
  }

  function setLanguage($language)
  {
    $this->language = $language;
  }

  function insertProduct($sku, $variation, $countryProperties, $languageProperties)
  {
    $jsonCountryProperties = pg_escape_string(json_encode($countryProperties));
    $jsonLanguageProperties = pg_escape_string(json_encode($languageProperties));

    $query = <<<SQL
      INSERT INTO products (sku, type, {$this->country}, {$this->language}) VALUES ('{$sku}', '{$variation}', '{$jsonCountryProperties}', '{$jsonLanguageProperties}')
      ON CONFLICT (sku) DO UPDATE SET type='${variation}', {$this->country}='{$jsonCountryProperties}', {$this->language}='{$jsonLanguageProperties}'; 
SQL;

    $result = pg_query($this->connection, $query);
    if ($result === false)
      throw new \Exception("Could not insert product: " . pg_last_error());
  }

  function updateProduct($sku, $globalProperties, $deProperties, $chProperties, $frProperties)
  {
    $queryPart = $this->buildUpdateQuerySetPart($globalProperties, $deProperties, $chProperties, $frProperties);
    if (empty($queryPart)) return;

    $query = <<<SQL
      UPDATE products SET ${queryPart} WHERE sku = '{$sku}'
SQL;

    $result = pg_query($this->connection, $query);
    if ($result === false)
      throw new \Exception("Could not update product: " . pg_last_error());
  }

  private function buildUpdateQuerySetPart($globalProperties, $deProperties, $chProperties, $frProperties)
  {
    if (empty($globalProperties)) return '';

    $jsonGlobalProperties = pg_escape_string(json_encode($globalProperties));
    $jsonDeProperties = pg_escape_string(json_encode($deProperties));
    $jsonChProperties = pg_escape_string(json_encode($chProperties));
    $jsonFrProperties = pg_escape_string(json_encode($frProperties));

    $query = "global = '{$jsonGlobalProperties}'";

    if (!empty($deProperties)) $query .= ", de = '{$jsonDeProperties}'";
    if (!empty($chProperties)) $query .= ", ch = '{$jsonChProperties}'";
    if (!empty($frProperties)) $query .= ", fr = '{$jsonFrProperties}'";

    return $query;
  }
}