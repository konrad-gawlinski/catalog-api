CREATE TABLE catalog.product (
  sku VARCHAR(10) PRIMARY KEY,
  status product_status DEFAULT 'new',
  properties JSONB,
  created_at TIMESTAMP WITH TIME ZONE DEFAULT now(),
  update_at TIMESTAMP WITH TIME ZONE DEFAULT now()
);

CREATE TABLE catalog_de.product (
  sku VARCHAR(10) PRIMARY KEY REFERENCES product (sku),
  status product_status DEFAULT 'new',
  properties JSONB,
  created_at TIMESTAMP WITH TIME ZONE,
  update_at TIMESTAMP WITH TIME ZONE
);

CREATE TABLE catalog_at.product (
  sku VARCHAR(10) PRIMARY KEY REFERENCES product (sku),
  status product_status DEFAULT 'new',
  properties JSONB,
  created_at TIMESTAMP WITH TIME ZONE,
  update_at TIMESTAMP WITH TIME ZONE
);

SELECT public.set_search_path('catalog');

CREATE OR REPLACE FUNCTION catalog.save_product(skuIN VARCHAR, statusIN product_status, propertiesIN JSONB) RETURNS integer AS
$$
  INSERT INTO product (sku, status, properties, created_at)
      VALUES (skuIN, statusIN,propertiesIN, now())
  ON CONFLICT (sku) DO UPDATE SET status=statusIN, properties=propertiesIN
  RETURNING 1;
$$
LANGUAGE SQL;

CREATE OR REPLACE FUNCTION catalog.fetch_product(skuIN VARCHAR) RETURNS RECORD AS
$$
  SELECT sku, status, properties FROM product WHERE sku=skuIN;
$$
LANGUAGE SQL;

CREATE OR REPLACE FUNCTION catalog.fetch_product_type(skuIN VARCHAR) RETURNS
  table(sku VARCHAR, type VARCHAR) AS
$$
  SELECT sku, properties->>'type' AS type FROM product WHERE sku=skuIN;
$$
LANGUAGE SQL;

CREATE OR REPLACE FUNCTION catalog.update_product(skuIN VARCHAR, propertiesIN JSONB) RETURNS VARCHAR AS
$$
  UPDATE product SET properties=public.jsonb_merge_deep(properties, propertiesIN) WHERE sku=skuIN
  RETURNING sku;
$$
LANGUAGE SQL;
