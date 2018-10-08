CREATE AGGREGATE jsonb_merge (JSONB) (
sfunc = jsonb_concat,
stype = JSONB
);

-- select single product with it's properties
select parent, sku, global || de || de_de FROM (
  SELECT parent,
   (array_agg(sku ORDER BY depth ASC))[1] as sku,
   jsonb_merge(global ORDER BY depth DESC) as global,
   jsonb_merge(de ORDER BY depth DESC) as de,
   jsonb_merge(de_de ORDER BY depth DESC) as de_de,
   jsonb_merge(com ORDER BY depth DESC) as com,
   jsonb_merge(en_en ORDER BY depth DESC) as en_en

  FROM product_config JOIN product ON child = id
  WHERE parent = 11 AND (sku IS NULL OR depth = 0)
  GROUP BY parent
 ) AS product
;
-- single product test
SELECT * FROM product_config JOIN product ON child = id
WHERE parent = 11 AND (sku IS NULL OR depth = 0)
ORDER BY depth DESC;


-- select all real_products with their properties
WITH real_products_with_configs AS (
  SELECT * FROM product JOIN product_config ON id = child
    WHERE (sku IS NULL AND depth != 0) OR (SKU IS NOT NULL AND depth = 0)
)
SELECT parent,
  (array_agg(sku ORDER BY depth ASC))[1] as sku,
  jsonb_merge(global ORDER BY depth DESC) as global,
  jsonb_merge(de ORDER BY depth DESC) as de,
  jsonb_merge(de_de ORDER BY depth DESC) as de_de,
  jsonb_merge(com ORDER BY depth DESC) as com,
  jsonb_merge(en_en ORDER BY depth DESC) as en_en
FROM real_products_with_configs
GROUP BY parent
HAVING (array_agg(sku ORDER BY depth ASC))[1] IS NOT NULL
;


--get bundle members
SELECT id,
  (array_agg(sku ORDER BY depth ASC))[1] as sku,
  count(id)
FROM product_config JOIN product ON child = id
WHERE parent = 11 AND sku IS NOT NULL AND depth != 0
GROUP BY id
;
