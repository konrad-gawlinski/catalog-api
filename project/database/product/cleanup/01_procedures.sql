DROP FUNCTION <schema_name>.nu3__create_product_and_ct_node (VARCHAR, VARCHAR, JSONB);
DROP FUNCTION <schema_name>.nu3__ct_create_node(INTEGER);
DROP FUNCTION <schema_name>.nu3__ct_make_node_a_child(INTEGER, INTEGER, INTEGER);
DROP FUNCTION <schema_name>.nu3__create_product(VARCHAR, VARCHAR, JSONB);
DROP FUNCTION <schema_name>.nu3__update_product(VARCHAR, JSONB);
DROP FUNCTION <schema_name>.nu3__overwrite_product(INTEGER, JSONB);

DROP FUNCTION <schema_name>.nu3__fetch_product_merged (VARCHAR, VARCHAR, VARCHAR);
DROP FUNCTION <schema_name>.nu3__fetch_product (VARCHAR);
DROP FUNCTION <schema_name>.nu3__fetch_product (INTEGER);
DROP FUNCTION <schema_name>.nu3__fetch_all_products ();
DROP FUNCTION <schema_name>.nu3__fetch_product_query (VARCHAR);
DROP AGGREGATE <schema_name>.nu3__jsonb_agg_concat(JSONB);
DROP FUNCTION <schema_name>.nu3__jsonb_concat(JSONB, JSONB);

DROP SCHEMA <schema_name>;