<?php

require ('NameGenerator.php');
require ('ProductJsonGenerator.php');
require ('SqlGenerator.php');

$sqlGenerator = new SqlGenerator(new ProductJsonGenerator(new NameGenerator()));

echo "INSERT INTO catalog (sku, properties) VALUES\n";
for ($i=1;$i<101;++$i) {
    echo "{$sqlGenerator->generateInsertValues('nu3_'.$i)},\n";
}
echo "{$sqlGenerator->generateInsertValues('nu3_'.$i)};\n";
