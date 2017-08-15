CREATE OR REPLACE FUNCTION catalog_sp.ct_create_node(idIn INTEGER) RETURNS integer AS
$$
INSERT INTO catalog.product_relations_de (parent_id, child_id, depth)
  VALUES (idIn, IdIn, 0)
RETURNING 1;
$$
LANGUAGE SQL;