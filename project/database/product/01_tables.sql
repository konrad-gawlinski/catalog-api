CREATE TYPE <schema_name>.product_type AS ENUM('Config', 'Bundle', 'True_Config');

CREATE TABLE <schema_name>.product_entity (
  id SERIAL PRIMARY KEY,
  sku VARCHAR UNIQUE,
  type <schema_name>.product_type NOT NULL,
  global JSONB,
  DE JSONB,
  AT JSONB,
  FR JSONB,
  de_DE JSONB,
  fr_FR JSONB,
  at_DE JSONB,
  created_at TIMESTAMP DEFAULT now(),
  updated_at TIMESTAMP
);

CREATE TABLE <schema_name>.product_relations (
  parent_id INTEGER REFERENCES <schema_name>.product_entity(id),
  child_id INTEGER REFERENCES <schema_name>.product_entity(id),
  depth INTEGER NOT NULL
);

CREATE INDEX product_relations__child_id
  ON <schema_name>.product_relations USING BTREE (child_id);
