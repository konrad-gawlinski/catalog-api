CREATE TYPE catalog.country AS ENUM ('DE', 'AT', 'FR');
CREATE TYPE catalog.product_type AS ENUM('config', 'bundle', 'true_config');

CREATE TABLE catalog.product_entity (
  id SERIAL PRIMARY KEY,
  sku VARCHAR UNIQUE,
  type product_type NOT NULL,
  DE JSONB,
  AT JSONB,
  FR JSONB,
  de_DE JSONB,
  fr_FR JSONB,
  at_DE JSONB,
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

CREATE TABLE catalog.product_relations_DE (
  parent_id INTEGER REFERENCES catalog.product_entity(id),
  child_id INTEGER REFERENCES catalog.product_entity(id),
  depth INTEGER NOT NULL
);
