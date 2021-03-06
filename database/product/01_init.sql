CREATE TABLE <schema_name>.products (
  id SERIAL PRIMARY KEY,
  sku VARCHAR UNIQUE,
  type VARCHAR(20) NOT NULL,
  global JSONB DEFAULT '{}' NOT NULL,
  de JSONB DEFAULT '{}' NOT NULL,
  at JSONB DEFAULT '{}' NOT NULL,
  fr JSONB DEFAULT '{}' NOT NULL,
  de_de JSONB DEFAULT '{}' NOT NULL,
  fr_fr JSONB DEFAULT '{}' NOT NULL,
  at_de JSONB DEFAULT '{}' NOT NULL,
  created_at TIMESTAMP DEFAULT now()
);

CREATE INDEX products__sku
  ON <schema_name>.products USING BTREE (sku);

CREATE TABLE <schema_name>.product_relations (
  parent_id INTEGER REFERENCES <schema_name>.products(id),
  child_id INTEGER REFERENCES <schema_name>.products(id),
  depth INTEGER NOT NULL
);

CREATE INDEX product_relations__child_id
  ON <schema_name>.product_relations USING BTREE (child_id);

CREATE INDEX product_relations__parent_id
  ON <schema_name>.product_relations USING BTREE (parent_id);
