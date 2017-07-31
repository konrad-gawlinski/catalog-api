<?php

use Nu3\ProductMigration\Database;

define('APPLICATION_ROOT', __DIR__.'/../../');

/** @var \Composer\Autoload\ClassLoader() $loader */
$loader = require_once APPLICATION_ROOT.'vendor/autoload.php';
$loader->addPsr4('Nu3\\ProductMigration\\', APPLICATION_ROOT. 'tools/product_migration');

class Importer
{
  /** @var Database */
  private $db;

  private $dbCon;

  function __construct()
  {
    $this->db = new Database();
  }

  function init()
  {
    $this->dbCon = $con = $this->db->connect();
    pg_query($con, "SET CLIENT_ENCODING TO 'UTF8';");
    pg_query($con, "SET search_path TO migration;");
  }

  function run()
  {
    $file = fopen(APPLICATION_ROOT. 'tools/product_migration/products_DE.json', 'r');
    $totalSkus = 0;

    while($product = $this->readProduct($file)) {
      echo "{$product['sku']}\n";
      $this->writeProduct($product);

      ++$totalSkus;
    }

    echo "\nTotal skus: {$totalSkus}\n";

    fclose($file);
  }

  private function readProduct($file) : array
  {
    do {
      $payload = $this->readPayload($file);
    } while($payload === '<empty>');
    if ($payload === '<end>') return [];

    $product = $this->decodeProduct($payload);
    if (!$product) return [];

    return $product;
  }

  private function readPayload($file) : string
  {
    $payload = '';
    $end = true;

    while (($line = fgets($file)) && (rtrim($line) !== '---')) {
      $end = false;
      $_line = $this->cleanText($line);
      $_line = $this->escapeJsonNotAllowedCharacters($_line);
      $payload .= $_line;
    }

    $trimmedPayload = trim($payload);
    if ($end) return '<end>';
    if (empty($trimmedPayload)) return '<empty>';

    return $payload;
  }

  private function escapeJsonNotAllowedCharacters(string $input) : string
  {
    $search = ["\n", "\r", "\t", "\x08", "\x0c"];
    $replace = ["\\n", "\\r", "\\t", "\\f", "\\b"];
    $output = str_replace($search, $replace, $input);

    $output = str_replace('}]}\n', '}]}', $output);

    return $output;
  }

  private function cleanText(string $input) : string
  {
    $output = str_replace("\\'", "'", $input);
    $output = str_replace('\0', '', $output);
    $output = str_replace('', '', $output);
    $output = str_replace('', '', $output);

    return $output;
  }

  private function unescapeJsonNotAllowedCharacters(string $input) : string
  {
    $search = ["\\n", "\\r", "\\t", "\\f", "\\b"];
    $replace = ["\n", "\r", "\t", "\x08", "\x0c"];
    $output = str_replace($search, $replace, $input);

    return $output;
  }

  private function decodeProduct(string $input) : array
  {
    $product = json_decode($input, true);
    if (!is_array($product)) {
      echo $input;
      echo "\n";
      echo json_last_error_msg();
      return [];
    }

    return $product;
  }

  private function writeProduct(array $product)
  {
    $sku = $product['sku'];
    $json = json_encode($this->prepareAttributes($product));
    $json = str_replace("'", "''", $json);

    pg_query(
      $this->dbCon,
      "INSERT INTO products (sku, attributes) VALUES('{$sku}', '{$json}');"
    );
  }

  private function prepareAttributes(array $product) : array
  {
    $attributes = [
      'product_id' => $product['product_id'],
      'status' => $product['status'],
      'variety' => $product['variety'],
    ];

    foreach ($product['attributes'] as $value) {
      $attributes[$value['name']] = $this->unescapeJsonNotAllowedCharacters($value['value']);
    }

    return $attributes;
  }
}

$importer = new Importer();
$importer->init();
$importer->run();