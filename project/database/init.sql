CREATE DATABASE nu3_catalog
  LC_COLLATE 'C.UTF-8'
  LC_CTYPE 'C.UTF-8';

CREATE SCHEMA catalog;
CREATE SCHEMA catalog_DE;
CREATE SCHEMA catalog_AT;

CREATE TYPE product_status AS ENUM ('new', 'approved', 'not listed', 'unavailable');
