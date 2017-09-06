CREATE TYPE catalog.country AS ENUM ('DE', 'AT', 'FR');
CREATE TYPE catalog.product_type AS ENUM('Config', 'Bundle', 'True_Config');

CREATE TABLE catalog.product_entity (
  id SERIAL PRIMARY KEY,
  sku VARCHAR UNIQUE,
  type product_type NOT NULL,
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

CREATE TABLE catalog.product_relations (
  parent_id INTEGER REFERENCES catalog.product_entity(id),
  child_id INTEGER REFERENCES catalog.product_entity(id),
  depth INTEGER NOT NULL
);

CREATE INDEX product_relations__child_id
  ON product_relations USING BTREE (child_id);
