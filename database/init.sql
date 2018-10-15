CREATE AGGREGATE <schema_name>.jsonb_merge (JSONB) (
sfunc = jsonb_concat,
stype = JSONB
);
