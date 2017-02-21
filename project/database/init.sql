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

CREATE OR REPLACE FUNCTION public.jsonb_merge_deep(target JSONB, source JSONB) RETURNS JSONB AS
  $$
    var isObject = (item) => {
        return item && typeof item === 'object' && !Array.isArray(item) && item !== null;
    };

    var mergeObject = (_target, _source) => {
      if (isObject(_target) && isObject(_source)) {
        Object.keys(_source).forEach((key) => {
          if (isObject(_source[key])) {
            if (!_target[key]) Object.assign(_target, { [key]: {} });
            mergeObject(_target[key], _source[key]);
          } else {
            Object.assign(_target, { [key]: _source[key] });
          }
        });
      }

      return _target;
    };

    return mergeObject(target, source);
  $$
LANGUAGE PLV8;
