CREATE OR REPLACE FUNCTION catalog_sp.ct_create_node(idIn INTEGER) RETURNS integer AS
$$
INSERT INTO catalog.product_relations (parent_id, child_id, depth)
  VALUES (idIn, IdIn, 0)
RETURNING 1;
$$
LANGUAGE SQL;

CREATE OR REPLACE FUNCTION
  catalog_sp.ct_make_node_a_child(parent_idIn INTEGER, child_idIn INTEGER, depthIn INTEGER)
  RETURNS integer AS
$$
INSERT INTO catalog.product_relations(parent_id, child_id, depth)
  SELECT p.parent_id, c.child_id, p.depth+c.depth+1
  FROM catalog.product_relations p, catalog.product_relations c
  WHERE p.child_id = parent_idIn AND c.parent_id = child_idIn
RETURNING 1;
$$
LANGUAGE SQL;

