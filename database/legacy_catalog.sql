CREATE SCHEMA DE_production_zed;
CREATE SCHEMA FR_production_zed;
CREATE SCHEMA CH_production_zed;
GRANT ALL PRIVILEGES ON SCHEMA DE_production_zed TO catalogapi_user;
GRANT ALL PRIVILEGES ON SCHEMA FR_production_zed TO catalogapi_user;
GRANT ALL PRIVILEGES ON SCHEMA CH_production_zed TO catalogapi_user;

CREATE TABLE DE_production_zed.pac_catalog_attribute (
  id_catalog_attribute INTEGER PRIMARY KEY,
  name VARCHAR(100),
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

CREATE TABLE DE_production_zed.pac_catalog_product (
  id_catalog_product INTEGER PRIMARY KEY,
  sku VARCHAR(30),
  variety VARCHAR(30),
  status VARCHAR(30),
  fk_catalog_attribute_set INTEGER,
  cache text,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

CREATE TABLE DE_production_zed.nu3_catalog_bundle (
  id_catalog_bundle INTEGER PRIMARY KEY,
  fk_catalog_product INTEGER REFERENCES DE_production_zed.pac_catalog_product(id_catalog_product),
  fk_catalog_product1 INTEGER REFERENCES DE_production_zed.pac_catalog_product(id_catalog_product),
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

CREATE TABLE DE_production_zed.pac_catalog_value_type (
  id_catalog_value_type INTEGER PRIMARY KEY,
  variety VARCHAR(30),
  fk_catalog_attribute INTEGER REFERENCES DE_production_zed.pac_catalog_attribute(id_catalog_attribute),
  fk_catalog_attribute_set INTEGER,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

CREATE TABLE DE_production_zed.pac_catalog_value_boolean (
  id_catalog_value_boolean INTEGER PRIMARY KEY,
  value SMALLINT,
  fk_catalog_attribute INTEGER REFERENCES DE_production_zed.pac_catalog_attribute(id_catalog_attribute),
  fk_catalog_product INTEGER REFERENCES DE_production_zed.pac_catalog_product(id_catalog_product),
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

CREATE TABLE DE_production_zed.pac_catalog_value_decimal (
  id_catalog_value_integer INTEGER PRIMARY KEY,
  value REAL,
  fk_catalog_attribute INTEGER REFERENCES DE_production_zed.pac_catalog_attribute(id_catalog_attribute),
  fk_catalog_product INTEGER REFERENCES DE_production_zed.pac_catalog_product(id_catalog_product),
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

CREATE TABLE DE_production_zed.pac_catalog_value_integer (
  id_catalog_value_integer INTEGER PRIMARY KEY,
  value INTEGER,
  fk_catalog_attribute INTEGER REFERENCES DE_production_zed.pac_catalog_attribute(id_catalog_attribute),
  fk_catalog_product INTEGER REFERENCES DE_production_zed.pac_catalog_product(id_catalog_product),
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

CREATE TABLE DE_production_zed.pac_catalog_value_option (
  id_catalog_value_option INTEGER PRIMARY KEY,
  name TEXT,
  fk_catalog_value_type INTEGER REFERENCES DE_production_zed.pac_catalog_value_type(id_catalog_value_type),
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

CREATE TABLE DE_production_zed.pac_catalog_value_option_multi (
  id_catalog_value_option_multi INTEGER PRIMARY KEY,
  fk_catalog_value_option INTEGER REFERENCES DE_production_zed.pac_catalog_value_option(id_catalog_value_option),
  fk_catalog_attribute INTEGER REFERENCES DE_production_zed.pac_catalog_attribute(id_catalog_attribute),
  fk_catalog_product INTEGER REFERENCES DE_production_zed.pac_catalog_product(id_catalog_product),
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

CREATE TABLE DE_production_zed.pac_catalog_value_option_single (
  id_catalog_value_option_single INTEGER PRIMARY KEY,
  fk_catalog_value_option INTEGER REFERENCES DE_production_zed.pac_catalog_value_option(id_catalog_value_option),
  fk_catalog_attribute INTEGER REFERENCES DE_production_zed.pac_catalog_attribute(id_catalog_attribute),
  fk_catalog_product INTEGER REFERENCES DE_production_zed.pac_catalog_product(id_catalog_product),
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);

CREATE TABLE DE_production_zed.pac_catalog_value_text (
  id_catalog_value_text INTEGER PRIMARY KEY,
  value TEXT,
  fk_catalog_attribute INTEGER REFERENCES DE_production_zed.pac_catalog_attribute(id_catalog_attribute),
  fk_catalog_product INTEGER REFERENCES DE_production_zed.pac_catalog_product(id_catalog_product),
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);


SELECT * FROM pac_catalog_product prod JOIN LATERAL (
  SELECT prod.sku, prod.id_catalog_product AS product_id, attr.name AS name, attr.id_catalog_attribute AS id, val.value::text, 'text' AS type FROM pac_catalog_attribute attr
    JOIN pac_catalog_value_text val ON attr.id_catalog_attribute = val.fk_catalog_attribute
  WHERE val.fk_catalog_product = prod.id_catalog_product
  UNION ALL
  SELECT prod.sku, prod.id_catalog_product AS product_id, attr.name, attr.id_catalog_attribute, val.value::text, 'integer' FROM pac_catalog_attribute attr
    JOIN pac_catalog_value_integer val ON val.fk_catalog_attribute = attr.id_catalog_attribute
  WHERE val.fk_catalog_product = prod.id_catalog_product
  UNION ALL
  SELECT prod.sku, prod.id_catalog_product, attr.name, attr.id_catalog_attribute, val.value::text, 'decimal' FROM pac_catalog_attribute attr
    JOIN pac_catalog_value_decimal val ON val.fk_catalog_attribute = attr.id_catalog_attribute
  WHERE val.fk_catalog_product = prod.id_catalog_product
  UNION ALL
  SELECT prod.sku, prod.id_catalog_product, attr.name, attr.id_catalog_attribute, val.value::text, 'boolean' FROM pac_catalog_attribute attr
    JOIN pac_catalog_value_boolean val ON val.fk_catalog_attribute = attr.id_catalog_attribute
  WHERE val.fk_catalog_product = prod.id_catalog_product
  UNION ALL
  SELECT prod.sku, prod.id_catalog_product, attr.name, attr.id_catalog_attribute, val.name::text, 'option_single' FROM pac_catalog_attribute attr
    JOIN pac_catalog_value_option_single option_id ON option_id.fk_catalog_attribute = attr.id_catalog_attribute
    JOIN pac_catalog_value_option val ON option_id.fk_catalog_value_option = val.id_catalog_value_option
  WHERE option_id.fk_catalog_product = prod.id_catalog_product
  UNION ALL
  SELECT prod.sku, prod.id_catalog_product, attr.name, attr.id_catalog_attribute, array_agg(val.name )::text, 'option_multi' FROM pac_catalog_attribute attr
    JOIN pac_catalog_value_option_multi option_id ON option_id.fk_catalog_attribute = attr.id_catalog_attribute
    JOIN pac_catalog_value_option val ON option_id.fk_catalog_value_option = val.id_catalog_value_option
  WHERE option_id.fk_catalog_product = prod.id_catalog_product GROUP BY attr.id_catalog_attribute
  ) pof ON prod.id_catalog_product = pof.product_id
WHERE prod.sku='nu3_32';

