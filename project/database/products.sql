CREATE TYPE catalog.country AS ENUM ('DE', 'AT', 'FR');

CREATE TABLE catalog.product_family (
  id SERIAL PRIMARY KEY,
  attributes JSONB,
  created_at TIMESTAMP DEFAULT now(),
  updated_at TIMESTAMP
);

CREATE TABLE catalog.products (
  sku VARCHAR PRIMARY KEY,
  attributes JSONB,
  created_at TIMESTAMP DEFAULT now(),
  updated_at TIMESTAMP
);

CREATE TABLE catalog.tax_rates (
  id SMALLSERIAL PRIMARY KEY,
  country country NOT NULL,
  tax_rate SMALLINT NOT NULL,
  created_at TIMESTAMP DEFAULT now(),
  updated_at TIMESTAMP,
  UNIQUE (country, tax_rate)
);

CREATE TABLE catalog.per_country_attributes (
  sku VARCHAR REFERENCES products(sku),
  DE JSONB,
  AT JSONB,
  FR JSONB,
  created_at TIMESTAMP DEFAULT now()
);

CREATE TABLE catalog.per_locale_attributes (
  sku VARCHAR REFERENCES products(sku),
  de_DE JSONB,
  fr_FR JSONB,
  at_DE JSONB,
  created_at TIMESTAMP DEFAULT now()
);

CREATE OR REPLACE FUNCTION catalog_sp.save_product(skuIN VARCHAR, attributesIn JSONB) RETURNS integer AS
$$
  INSERT INTO catalog.products (sku, attributes)
      VALUES (skuIN, attributesIn)
  ON CONFLICT (sku) DO UPDATE SET attributes = products.attributes || attributesIn
  RETURNING 1;
$$
LANGUAGE SQL;

CREATE OR REPLACE FUNCTION catalog_sp.fetch_product(skuIN VARCHAR) RETURNS
  table(sku VARCHAR, attributes JSONB) AS
$$
  SELECT sku, attributes FROM catalog.products WHERE sku=skuIN;
$$
LANGUAGE SQL;
