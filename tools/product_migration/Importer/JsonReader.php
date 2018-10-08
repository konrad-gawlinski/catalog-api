<?php

namespace Nu3\ProductMigration\Importer;

class JsonReader
{
  private $input;

  private $logFile;

  function __construct($inputFile, $logFile)
  {
    $this->input = $inputFile;
    $this->logFile = $logFile;
  }

  public function readProduct() : array
  {
    do {
      $payload = $this->readPayload($this->input);
    } while($payload === '<empty>');
    if ($payload === '<end>') return [];

    $product = $this->decodeProduct($payload);
    if (!$product) {
      file_put_contents($this->logFile, 'Error: '. json_last_error_msg(). "\nPayload : {$payload}\n", FILE_APPEND);
      return [];
    }

    file_put_contents($this->logFile, "Read: {$product['sku']}\n", FILE_APPEND);

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

  private function decodeProduct(string $input) : array
  {
    $product = json_decode($input, true);
    if (!is_array($product)) {
      return [];
    }

    return $product;
  }
}