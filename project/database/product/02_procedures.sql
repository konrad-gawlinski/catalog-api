SELECT set_config('search_path', '<search_path>', false);

CREATE AGGREGATE <schema_name>.jsonb_merge (JSONB) (
sfunc = jsonb_concat,
stype = JSONB
);