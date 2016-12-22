CREATE DATABASE nu3_catalog
  LC_COLLATE 'C.UTF-8'
  LC_CTYPE 'C.UTF-8';

CREATE TYPE product_status AS ENUM ('new', 'approved', 'not listed', 'unavailable');
