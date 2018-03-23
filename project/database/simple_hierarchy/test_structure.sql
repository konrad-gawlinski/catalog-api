CREATE TABLE product (
  id INT PRIMARY KEY,
  sku VARCHAR(10) UNIQUE,
  type VARCHAR(30) NOT NULL,
  global JSONB,
  de JSONB,
  com JSONB,
  de_de JSONB,
  en_en JSONB
);

CREATE TABLE product_config (
  parent INT NOT NULL REFERENCES product(id),
  child INT NOT NULL REFERENCES product(id),
  depth INT NOT NULL
);

-------------------------------------
INSERT INTO product VALUES
  (1, 'id_1', 'simple', '{"name":"id_1","color": "red"}','{"color":"black"}', null, '{"check":"failed"}', '{"check":"passed"}'),
  (31, null, 'config', '{"id_31":true, "brand": "Audi", "model": "A6"}', null, null, null, null),

  (2, 'id_2', 'simple', '{"id_2":true, "name":"id_2", "brand": "BMW", "model": "X6"}', '{"color":"black"}', null, '{"check":"passed"}', null),
  (32, null, 'config', '{"id_32":true, "brand":"BMW", "package":"family"}', null, null, null, null),
  (33, null, 'config', '{"id_33":true, "brand": "BMW"}', null, null, null, null),

  (3, 'id_3', 'simple', '{"name":"id_3", "brand":"Mercedes", "model":"AMG W124"}','{"color":"diamond black"}', null, '{"check":"failed"}', '{"check":"passed"}'),
  (34, null, 'config', '{"id_34":true, "brand": "Mercedes", "model": "AMG"}', null, '{"color":"silver"}', '{"check":"unknown"}', '{"check":"unknown"}'),
  (35, null, 'config', '{"id_35":true, "brand": "Mercedes", "package":"double"}', null, null, null, null),
  (36, null, 'config', '{"id_36":true, "brand": "Mercedes", "package":"double"}', null, null, null, null),

  (4, 'id_4', 'simple', '{"name":"id_4", "brand":"Mercedes", "model":"AMG W124"}','{"color":"diamond black"}', null, '{"check":"failed"}', '{"check":"passed"}'),
  (37, null, 'config', '{"id_37":true, "brand": "Mercedes", "model": "AMG"}', null, '{"color":"silver"}', '{"check":"unknown"}', '{"check":"unknown"}'),
  (38, null, 'config', '{"id_38":true, "brand": "Mercedes", "package":"double"}', null, null, null, null),
  (39, null, 'config', '{"id_39":true, "brand": "Mercedes"}', null, null, null, null),
  (40, null, 'config', '{"id_40":true, "brand": "Mercedes", "package":"sport"}', '{"power": "a lot"}', null, null, null),

  (5, 'id_5', 'simple', '{"name":"id_5", "naked":true}',null, null, null, null),

  (11, 'id_11', 'bundle', '{"name":"id_11", "package":"sport"}', '{"speed":"supper"}', null, '{"speed":"moderate"}', null),

  (12, 'id_12', 'bundle', '{"name":"id_12", "package":"sport"}', '{"speed":"supper"}', null, '{"speed":"moderate"}', null),

  (13, 'id_13', 'bundle', '{"name":"id_13", "package":"total mix"}', '{"speed":"supper"}', '{"color":"custom"}', '{"speed":"moderate"}', '{"color":"custom"}')

;

INSERT INTO product_config VALUES
  (1, 1, 0),
  (31, 31, 0),

  (2, 2, 0),
  (32, 32, 0),
  (33, 33, 0),

  (3, 3, 0),
  (34, 34, 0),
  (35, 35, 0),
  (36, 36, 0),

  (4, 4, 0),
  (37, 37, 0),
  (38, 38, 0),
  (39, 39, 0),
  (40, 40, 0),

  (5, 5, 0),

  (11, 11, 0),

  (12, 12, 0),

  (13, 13, 0)
;

INSERT INTO product_config VALUES
  (1, 31, 1),

  (2, 32, 1),
  (2, 33, 1),

  (3, 34, 1),
  (3, 35, 1),
  (34, 36, 1),
  (3, 36, 2),

  (4, 37, 1),
  (4, 38, 1),
  (37, 39, 1),
  (38, 40, 1),
  (4, 39, 2),
  (4, 40, 2),

  (5, 31, 1),
  (5, 34, 1),
  (5, 40, 1),

  (11, 31, 1),
  (11, 33, 1),

  (12, 36, 1),
  (36, 35, 1),
  (12, 35, 2),

  (13, 33, 1),
  (13, 40, 1),
  (33, 36, 1),
  (40, 39, 1),
  (36, 40, 1),
  (33, 40, 2),
  (13, 36, 2),
  (13, 39, 2),
  (13, 40, 3),

  (11, 2, 1),

  (13, 3, 1),
  (13, 3, 1),
  (13, 5, 1)
;
