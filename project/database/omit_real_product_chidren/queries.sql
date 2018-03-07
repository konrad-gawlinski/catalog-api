CREATE AGGREGATE jsonb_merge (JSONB) (
sfunc = jsonb_concat,
stype = JSONB
);

CREATE OR REPLACE FUNCTION
  fetch_product(__id INT)
  RETURNS TABLE (
    id INTEGER, sku VARCHAR, status VARCHAR, type VARCHAR,
    global JSONB,
    de JSONB, com JSONB,
    de_de JSONB, en_en JSONB
  ) AS
$$
WITH product_with_children AS (
  SELECT
    id, sku, node.depth,
    node.parent as node_parent, node.child as node_child,
    children.parent as children_parent, children.child as children_child
  FROM product
    JOIN product_config node ON id = child
    JOIN LATERAL (
         SELECT * FROM product_config child_node WHERE child_node.parent = node.child) AS children
      ON TRUE
  WHERE node.parent = __id
),
    children_only AS (
    SELECT * FROM product_with_children WHERE children_parent <> __id
  ),
    excluded_nodes AS (
    SELECT DISTINCT a.children_child as node_id
    FROM children_only a JOIN children_only b ON a.sku IS NOT NULL AND a.node_child = b.children_parent
  ),
    product_tree AS (
    SELECT DISTINCT id, depth
    FROM product_with_children a LEFT JOIN excluded_nodes b ON a.children_child = b.node_id
    WHERE b.node_id IS NULL
    ORDER BY depth DESC
  )
SELECT
  (array_agg(product.id ORDER BY tree.depth))[1] as id,
  (array_agg(product.sku ORDER BY tree.depth))[1] as sku,
  (array_agg(product.status ORDER BY tree.depth))[1] as status,
  (array_agg(product.type ORDER BY tree.depth))[1] as type,
  jsonb_merge(global) as global,
  jsonb_merge(de) as de, jsonb_merge(com) as com,
  jsonb_merge(de_de) as de_de, jsonb_merge(en_en) as en_en
FROM product_tree tree
  JOIN product ON tree.id = product.id
GROUP BY true;
$$
LANGUAGE SQL;

-- select all real product
SELECT * FROM
  (SELECT id FROM product WHERE sku IS NOT NULL) as product_id
  JOIN LATERAL (SELECT * FROM fetch_product(product_id.id)) as properties ON true
;

-- select products by sku
SELECT * FROM
  (SELECT id FROM product WHERE sku IN ('id_11', 'id_10')) as product_id
  JOIN LATERAL (SELECT * FROM fetch_product(product_id.id)) as properties ON true
;

-- select single product
-- WITH product_with_children AS (
--   SELECT
--     id, sku, node.depth,
--     node.parent as node_parent, node.child as node_child,
--     children.parent as children_parent, children.child as children_child
--   FROM product
--     JOIN product_config node ON id = child
--     JOIN LATERAL (
--       SELECT * FROM product_config child_node WHERE child_node.parent = node.child) AS children
--     ON TRUE
--   WHERE node.parent = 11
-- ),
-- children_only AS (
--   SELECT * FROM product_with_children WHERE children_parent <> 11
-- ),
-- excluded_nodes AS (
--     SELECT DISTINCT a.children_child as node_id
--     FROM children_only a JOIN children_only b ON a.sku IS NOT NULL AND a.node_child = b.children_parent
-- ),
-- product_tree AS (
--     SELECT DISTINCT id, depth
--     FROM product_with_children a LEFT JOIN excluded_nodes b ON a.children_child = b.node_id
--     WHERE b.node_id IS NULL
--     ORDER BY depth DESC
-- )
-- SELECT
--     (array_agg(product.id ORDER BY tree.depth))[1] as id,
--     (array_agg(product.sku ORDER BY tree.depth))[1] as sku,
--     (array_agg(product.status ORDER BY tree.depth))[1] as status,
--     (array_agg(product.type ORDER BY tree.depth))[1] as type,
--     jsonb_merge(global) as global,
--     jsonb_merge(de) as de, jsonb_merge(com) as com,
--     jsonb_merge(de_de) as de_de, jsonb_merge(en_en) as en_en
--   FROM product_tree tree
--   JOIN product ON tree.id = product.id
-- GROUP BY true;