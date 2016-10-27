#sample
#   sku  |          jsonb_pretty
# -------+--------------------------------
#  nu3_1 | {                             +
#        |     "name": "silly_hodgkin",  +
#        |     "type": "book",           +
#        |     "price": 5172,            +
#        |     "variety": "config",      +
#        |     "tax_rate": 19,           +
#        |     "attributes": [           +
#        |         "is_gluten_free",     +
#        |         "is_lactose_free"     +
#        |     ],                        +
#        |     "seo_robots": [           +
#        |         "noindex",            +
#        |         "follow"              +
#        |     ],                        +
#        |     "manufacturer": "philips",+
#        |     "label_language": [       +
#        |         "en",                 +
#        |         "it"                  +
#        |     ],                        +
#        |     "ingredient_list": 1      +
#        | }

#REMOVE 'is_new' element from attributes array
SELECT c.sku, jsonb_agg(el) FROM
catalog c JOIN (select sku, jsonb_array_elements_text(properties->'attributes') as el from catalog) c2 ON c.sku=c2.sku where el <> 'is_new'
GROUP BY c.sku;

#UPDATE, remove 'is_new' from array from single product
UPDATE catalog SET properties=jsonb_set(properties, '{attributes}', (
    SELECT jsonb_agg(el) FROM
    catalog c JOIN (select sku, jsonb_array_elements_text(properties->'attributes') as el from catalog) c2 ON c.sku=c2.sku
    WHERE el <> 'is_new' AND c.sku='nu3_1'
    GROUP BY c.sku
    )
) WHERE sku='nu3_1';
