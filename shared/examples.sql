#remove array element
WITH q as (
SELECT c.sku as sku,  jsonb_set(properties, '{attributes}', jsonb_agg(el)) as new_properties FROM
catalog c JOIN (select sku, jsonb_array_elements_text(properties->'attributes') as el from catalog) c2 ON c.sku=c2.sku where el != 'is_new'
GROUP BY c.sku
)
UPDATE catalog SET properties=q.new_properties FROM q WHERE catalog.sku=q.sku;