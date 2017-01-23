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
  type
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

CREATE OR REPLACE FUNCTION catalog.fetch_product(skuIN VARCHAR) RETURNS text AS
$$
  SELECT jsonb_set(jsonb_set(properties, '{sku}', to_jsonb(sku), true),'{status}', to_jsonb(status), true)::text
  FROM product WHERE sku=skuIN;
$$
LANGUAGE SQL;