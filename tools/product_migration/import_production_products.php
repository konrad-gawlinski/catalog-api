<?php
$mysqli = new mysqli('host', 'user', 'pass', 'db_name');

if ($mysqli->connect_errno) {
    echo "Error: Failed to make a MySQL connection, here is why: \n";
    echo "Errno: " . $mysqli->connect_errno . "\n";
    echo "Error: " . $mysqli->connect_error . "\n";

    exit;
}

$productAttributesQuery = <<<QUERY
SELECT @sku as 'name', @product_id as 'id', @status as 'value', @variety as 'type'
UNION ALL
SELECT attr.name, attr.id_catalog_attribute, CAST(val.value AS CHAR), 'text' FROM pac_catalog_attribute attr
  JOIN pac_catalog_value_text val ON attr.id_catalog_attribute = val.fk_catalog_attribute
WHERE val.fk_catalog_product = @product_id
UNION ALL
SELECT attr.name, attr.id_catalog_attribute, CAST(val.value AS CHAR), 'integer' FROM pac_catalog_attribute attr
  JOIN pac_catalog_value_integer val ON val.fk_catalog_attribute = attr.id_catalog_attribute
WHERE val.fk_catalog_product = @product_id
UNION ALL
SELECT attr.name, attr.id_catalog_attribute, CAST(val.value AS CHAR), 'decimal' FROM pac_catalog_attribute attr
  JOIN pac_catalog_value_decimal val ON val.fk_catalog_attribute = attr.id_catalog_attribute
WHERE val.fk_catalog_product = @product_id
UNION ALL
SELECT attr.name, attr.id_catalog_attribute, CAST(val.value AS CHAR), 'boolean' FROM pac_catalog_attribute attr
  JOIN pac_catalog_value_boolean val ON val.fk_catalog_attribute = attr.id_catalog_attribute
WHERE val.fk_catalog_product = @product_id
UNION ALL
SELECT attr.name, attr.id_catalog_attribute, CAST(val.name AS CHAR), 'option_single' FROM pac_catalog_attribute attr
  JOIN pac_catalog_value_option_single option_id ON option_id.fk_catalog_attribute = attr.id_catalog_attribute
  JOIN pac_catalog_value_option val ON option_id.fk_catalog_value_option = val.id_catalog_value_option
WHERE option_id.fk_catalog_product = @product_id
UNION ALL
SELECT attr.name, attr.id_catalog_attribute, GROUP_CONCAT(val.name SEPARATOR ' --- '), 'option_multi' FROM pac_catalog_attribute attr
  JOIN pac_catalog_value_option_multi option_id ON option_id.fk_catalog_attribute = attr.id_catalog_attribute
  JOIN pac_catalog_value_option val ON option_id.fk_catalog_value_option = val.id_catalog_value_option
WHERE option_id.fk_catalog_product = @product_id GROUP BY attr.id_catalog_attribute
;
QUERY;


$catalogSkus = $mysqli->query("SELECT sku FROM pac_catalog_product");

while ($catalogRow = $catalogSkus->fetch_row()) {
    $sku = $catalogRow[0];
    $mysqli->query("SELECT @product_id:=id_catalog_product, @sku:=sku, @status:=status, @variety:=variety FROM pac_catalog_product WHERE sku='{$sku}'");

    if (!$result = $mysqli->query($productAttributesQuery)) {
        file_put_contents('importer_log', "Could not get the data for '{$sku}': Errno ({$mysqli->errno}), Error ({$mysqli->error}) \n", FILE_APPEND);
    } else {
        $row = $result->fetch_array(MYSQLI_NUM);

        $productJson = sprintf('{"sku":"%s", "product_id":"%s", "status":"%s", "variety":"%s", "attributes":[', $row[0], $row[1], $row[2], $row[3]);
        while ($row = $result->fetch_array(MYSQLI_NUM)) {
            $productJson .= sprintf('{"name":"%s", "value":"%s", "id":"%s"},', addslashes($row[0]), addslashes($row[2]), $row[1]);
        }
        $productJson = rtrim($productJson, ',');
        $productJson .= ']}';
        $productJson .= "\n---\n"; //Next product marker
        file_put_contents('products_DE.json', $productJson, FILE_APPEND);
        file_put_contents('importer_log', "Import success for '{$sku}'\n", FILE_APPEND);
    }
}

$mysqli->close();
