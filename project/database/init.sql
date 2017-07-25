CREATE DATABASE catalog_api ENCODING 'UTF-8'
  LC_COLLATE 'C.UTF-8'
  LC_CTYPE 'C.UTF-8'
TEMPLATE template0;

CREATE EXTENSION plv8;

CREATE SCHEMA catalog;
CREATE SCHEMA catalog_sp;
ALTER DATABASE catalog_api SET SEARCH_PATH TO catalog_sp,catalog;

CREATE OR REPLACE FUNCTION catalog_sp.jsonb_merge_deep(target JSONB, source JSONB) RETURNS JSONB AS
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
