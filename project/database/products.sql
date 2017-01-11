CREATE TABLE catalog.product (
  sku VARCHAR(10) PRIMARY KEY,
  status product_status DEFAULT 'new',
  properties JSONB,
  created_at TIMESTAMP WITH TIME ZONE,
  update_at TIMESTAMP WITH TIME ZONE
);

CREATE TABLE catalog_DE.product (
  sku VARCHAR(10) PRIMARY KEY REFERENCES product (sku),
  status product_status DEFAULT 'new',
  properties JSONB,
  created_at TIMESTAMP WITH TIME ZONE,
  update_at TIMESTAMP WITH TIME ZONE
);

CREATE TABLE catalog_AT.product (
  sku VARCHAR(10) PRIMARY KEY REFERENCES product (sku),
  status product_status DEFAULT 'new',
  properties JSONB,
  created_at TIMESTAMP WITH TIME ZONE,
  update_at TIMESTAMP WITH TIME ZONE
);