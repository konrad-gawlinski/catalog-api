CREATE TABLE products (
  sku VARCHAR(10) PRIMARY KEY,
  properties JSONB,
  created_at TIMESTAMP WITH TIME ZONE,
  update_at TIMESTAMP WITH TIME ZONE
);

CREATE TABLE products_DE (
  sku VARCHAR(10) PRIMARY KEY REFERENCES products (sku),
  properties JSONB,
  created_at TIMESTAMP WITH TIME ZONE,
  update_at TIMESTAMP WITH TIME ZONE
);

CREATE TABLE products_AT (
  sku VARCHAR(10) PRIMARY KEY REFERENCES products (sku),
  properties JSONB,
  created_at TIMESTAMP WITH TIME ZONE,
  update_at TIMESTAMP WITH TIME ZONE
);