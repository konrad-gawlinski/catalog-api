CREATE SCHEMA catalog;

CREATE AGGREGATE catalog.jsonb_merge (JSONB) (
sfunc = jsonb_concat,
stype = JSONB
);
