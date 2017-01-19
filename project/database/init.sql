CREATE DATABASE nu3_catalog
  LC_COLLATE 'C.UTF-8'
  LC_CTYPE 'C.UTF-8';

CREATE SCHEMA catalog;
CREATE SCHEMA catalog_de;
CREATE SCHEMA catalog_at;

CREATE TYPE product_status AS ENUM ('new', 'approved', 'not listed', 'unavailable');

CREATE OR REPLACE FUNCTION public.set_search_path(path TEXT) RETURNS TEXT AS
  $$
    SELECT set_config('search_path', path, false);
  $$
LANGUAGE SQL;