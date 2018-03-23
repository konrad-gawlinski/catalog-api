CREATE TYPE <schema_name>.product_type AS ENUM('simple', 'config', 'bundle');

CREATE TABLE <schema_name>.products (
  id SERIAL PRIMARY KEY,
  sku VARCHAR UNIQUE,
  type <schema_name>.product_type NOT NULL,
  global JSONB,
  de JSONB,
  at JSONB,
  fr JSONB,
  de_de JSONB,
  fr_fr JSONB,
  at_de JSONB,
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
