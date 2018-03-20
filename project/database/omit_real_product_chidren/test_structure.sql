CREATE TABLE product_config (
  parent INT NOT NULL REFERENCES product(id),
  child INT NOT NULL REFERENCES product(id),
  depth INT NOT NULL
);

CREATE TABLE product (
  id INT PRIMARY KEY,
  sku VARCHAR(10) UNIQUE,
  status VARCHAR(30) NOT NULL,
  type VARCHAR(30) NOT NULL,
  global JSONB,
  de JSONB,
  com JSONB,
  de_de JSONB,
  en_en JSONB
);

-- products
INSERT INTO product VALUES
  (1, 'id_1', 'active', 'simple', '{"name":"id_1","color": "red"}','{"color":"black"}', null, '{"check":"failed"}', '{"check":"passed"}'),
  (31, null, '', 'config', '{"brand": "Audi", "model": "A6"}', null, null, null, null),

  (3, 'id_3', 'active', 'simple', '{"name":"id_3", "brand":"BMW", "model":"M6"}','{"color":"black"}', null, '{"check":"passed"}', null),
  (2, 'id_2', 'active', 'simple', '{"name":"id_2", "brand": "BMW", "model": "X6"}', '{"color":"black"}', null, '{"check":"passed"}', null),
  (21, 'id_21', 'active', 'bundle', '{"name":"id_21", "brand":"BMW", "package":"family"}', null, null, null, null),
  (22, 'id_22', 'active', 'bundle', '{"name":"id_22", "brand": "BMW", "package":"sport"}', null, null, null, null),

  (4, 'id_4', 'active', 'simple', '{"name":"id_4", "brand":"Mercedes", "model":"AMG W124"}','{"color":"diamond black"}', null, '{"check":"failed"}', '{"check":"passed"}'),
  (32, null, '', 'config', '{"brand": "Mercedes", "model": "AMG"}', null, '{"color":"silver"}', '{"check":"unknown"}', '{"check":"unknown"}'),
  (23, 'id_23', 'active', 'bundle', '{"brand": "Mercedes", "model": "AMG", "package":"double"}', null, null, null, null),

  (5, 'id_5', 'active', 'simple', '{"name":"id_5", "model":"M6"}', null, null, '{"check":"passed"}', null),
  (6, 'id_6', 'active', 'simple', '{"name":"id_6", "model": "X6"}', null, null, '{"check":"passed"}', null),
  (33, null, '', 'config', '{"brand": "BMW", "config": "sport"}', null, '{"color":"dark black"}', '{"check":"unknown"}', '{"check":"unknown"}'),

  (8, 'id_8', 'active', 'simple', '{"name":"id_8", "model": "Phaeton"}', null, null, '{"check":"passed"}', null),
  (35, null, '', 'config', '{"brand": "VW", "config": "lux"}', null, null, '{"check":"unknown"}', '{"check":"unknown"}'),
  (36, null, '', 'config', '{"brand": "VW", "color":"dark"}', '{"color":"expensive black"}', '{"color":"expensive black"}', '{"check":"unknown"}', '{"check":"unknown"}'),

  (9, 'id_9', 'active', 'simple', '{"name":"id_9", "model": "E klasse"}', null, null, '{"check":"passed"}', null),
  (37, null, '', 'config', '{"brand": "Mercedes", "config": "sport"}', null, null, '{"check":"unknown"}', '{"check":"unknown"}'),
  (38, null, '', 'config', '{"brand": "Mercedes", "config":"family"}', '{"color":"white pearl"}', '{"color":"black pearl"}', '{"check":"unknown"}', '{"check":"unknown"}'),

  (10, 'id_10', 'active', 'simple', '{"name":"id_10", "model": "E klasse"}', null, null, '{"check":"passed"}', null),
  (11, 'id_11', 'active', 'simple', '{"name":"id_11", "model": "V klasse"}', null, null, '{"check":"passed"}', null),
  (39, null, '', 'config', '{"brand": "Mercedes", "config": "sport"}', null, null, '{"check":"unknown"}', '{"check":"unknown"}'),
  (40, null, '', 'config', '{"brand": "Mercedes", "config":"family"}', '{"color":"white pearl"}', '{"color":"black pearl"}', '{"check":"unknown"}', '{"check":"unknown"}'),

  (12, 'id_12', 'active', 'simple', '{"name":"id_12", "model": "300GT"}', null, null, '{"check":"passed"}', null),
  (25, 'id_25', 'active', 'bundle', '{"name":"id_25", "package":"sport"}', null, null, null, null),
  (41, null, '', 'config', '{"brand": "Mitsubishi", "config": "AWD", "drive":"4 wheel"}', null, null, '{"check":"unknown"}', '{"check":"unknown"}'),
  (42, null, '', 'config', '{"brand": "Mitsubishi", "config":"fast"}', '{"color":"ferrari red"}', '{"color":"ferrari red"}', null, null),

  (13, 'id_13', 'active', 'simple', '{"name":"id_13", "model": "300GT"}', null, null, '{"check":"passed"}', null),
  (26, 'id_26', 'active', 'bundle', '{"name":"id_26", "package":"sport"}', null, null, null, null),
  (43, null, '', 'config', '{"brand": "Mitsubishi", "config": "AWD", "drive":"4 wheel"}', null, null, '{"check":"unknown"}', '{"check":"unknown"}'),
  (44, null, '', 'config', '{"brand": "Mitsubishi", "config":"fast"}', '{"color":"ferrari red"}', '{"color":"ferrari red"}', null, null),

  (14, 'id_14', 'active', 'simple', '{"name":"id_14", "model": "300GT"}', null, null, '{"check":"passed"}', null),
  (27, 'id_27', 'active', 'bundle', '{"name":"id_27", "package":"sport"}', null, null, null, null),
  (45, null, '', 'config', '{"brand": "Mitsubishi", "config": "AWD", "drive":"4 wheel"}', null, null, '{"check":"unknown"}', '{"check":"unknown"}'),
  (46, null, '', 'config', '{"brand": "Mitsubishi", "config":"fast"}', '{"color":"ferrari red"}', '{"color":"ferrari red"}', null, null),
  (47, null, '', 'config', '{"brand": "Mitsubishi", "fuel":"petrol"}', null, null, null, null)

;

-- product structure, init
INSERT INTO product_config VALUES
  (1, 1, 0),
  (31, 31, 0),
  (3, 3, 0),
  (2, 2, 0),
  (21, 21, 0),
  (22, 22, 0),
  (4, 4, 0),
  (32, 32, 0),
  (5, 5, 0),
  (6, 6, 0),
  (33, 33, 0),
  (8, 8, 0),
  (35, 35, 0),
  (36, 36, 0),
  (9, 9, 0),
  (37, 37, 0),
  (38, 38, 0),
  (12, 12, 0),
  (25, 25, 0),
  (41, 41, 0),
  (42, 42, 0),
  (10, 10, 0),
  (11, 11, 0),
  (39, 39, 0),
  (40, 40, 0),
  (13, 13, 0),
  (26, 26, 0),
  (44, 44, 0),
  (43, 43, 0),
  (14, 14, 0),
  (27, 27, 0),
  (45, 45, 0),
  (46, 46, 0),
  (47, 47, 0)
;

-- product structure
-- INSERT INTO product_config(parent, child, depth)
--   SELECT p.parent, c.child, p.depth+c.depth+1
--   FROM product_config p, product_config c
--   WHERE p.child = 1 AND c.parent = 31;

INSERT INTO product_config VALUES
  (1, 31, 1),
  (2, 21, 1),
  (3, 21, 1),
  (3, 22, 1),
  (4, 32, 1),
  (4, 23, 1),
  (4, 23, 1),
  (5, 33, 1),
  (6, 33, 1),
  (8, 35, 1),
  (8, 36, 1),
  (9, 37, 1),
  (37, 38, 1),
  (9, 38, 2),
  (12, 25, 1),
  (12, 42, 1),
  (25, 41, 1),
  (12, 41, 2),
  (10, 39, 1),
  (11, 40, 1),
  (39, 40, 1),
  (10, 40, 2),

  (13, 26, 1),
  (13, 43, 1),
  (13, 44, 1),
  (26, 44, 1),
  (13, 44, 2),

  (14, 27, 1),
  (14, 45, 1),
  (14, 47, 1),
  (27, 45, 1),
  (45, 46, 1),
  (14, 45, 2),
  (27, 46, 2),
  (14, 46, 3)
;
