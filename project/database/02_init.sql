CREATE EXTENSION plv8;

CREATE SCHEMA catalog;
CREATE SCHEMA catalog_sp;
ALTER DATABASE catalog_api SET SEARCH_PATH TO catalog_sp,catalog;