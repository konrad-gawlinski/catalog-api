CREATE EXTENSION plv8;

--CREATE DATABASE json_structure WITH ENCODING 'UTF8' LC_COLLATE='C.UTF-8' LC_CTYPE='C.UTF-8' TEMPLATE template0;
SELECT set_config('search_path', 'version_1, public', false);
SELECT set_config('search_path', 'public', false);


CREATE TYPE product_status AS ENUM ('new', 'approved', 'not listed', 'unavailable');
CREATE TYPE country AS ENUM ('DE', 'AT', 'FR');

CREATE TABLE product_family (
  id SERIAL PRIMARY KEY,
  attributes JSONB
);

CREATE TABLE products (
  sku VARCHAR PRIMARY KEY,
  parent INT REFERENCES product_family(id),
  attributes JSONB
);

CREATE TABLE tax_rates (
  id SMALLSERIAL PRIMARY KEY,
  country country NOT NULL,
  tax_rate SMALLINT NOT NULL,
  UNIQUE (country, tax_rate)
);

CREATE TABLE per_country_attributes (
  sku VARCHAR REFERENCES products(sku),
  DE JSONB,
  AT JSONB,
  FR JSONB
);

CREATE TABLE per_locale_attributes (
  sku VARCHAR REFERENCES products(sku),
  de_DE JSONB,
  fr_FR JSONB,
  at_DE JSONB
);

CREATE OR REPLACE FUNCTION jsonb_merge_deep(target JSONB, source JSONB) RETURNS JSONB AS
$$
    var isObject = (item) => {
        return item && typeof item === 'object' && !Array.isArray(item) && item !== null;
    };

    var mergeObject = (_target, _source) => {
      if (isObject(_target) && isObject(_source)) {
        Object.keys(_source).forEach((key) => {
          if (isObject(_source[key])) {
            if (!_target[key]) Object.assign(_target, { [key]: {} });
            mergeObject(_target[key], _source[key]);
          } else {
            Object.assign(_target, { [key]: _source[key] });
          }
        });
      }

      return _target;
    };

    return mergeObject(target, source);
  $$
LANGUAGE PLV8;

CREATE OR REPLACE FUNCTION print_constant() RETURNS VARCHAR AS
$$
    return 'abc';
$$
LANGUAGE PLV8;
