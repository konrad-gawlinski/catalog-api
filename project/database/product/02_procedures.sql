SELECT set_config('search_path', '<search_path>', false);

CREATE OR REPLACE FUNCTION
  <schema_name>.nu3__ct_create_node(__id INTEGER) RETURNS integer AS
$$
INSERT INTO product_relations (parent_id, child_id, depth)
  VALUES (__id, __id, 0)
RETURNING 1;
$$
LANGUAGE SQL;

CREATE OR REPLACE FUNCTION
  <schema_name>.nu3__ct_make_node_a_child(__parent_id INTEGER, __child_id INTEGER, __depth INTEGER)
  RETURNS INTEGER AS
$$
INSERT INTO product_relations(parent_id, child_id, depth)
  SELECT p.parent_id, c.child_id, p.depth+c.depth+1
  FROM product_relations p, product_relations c
  WHERE p.child_id = __parent_id AND c.parent_id = __child_id
RETURNING 1;
$$
LANGUAGE SQL;

CREATE OR REPLACE FUNCTION
  <schema_name>.nu3__create_product(__sku VARCHAR, __type VARCHAR, __properties JSONB)
  RETURNS INTEGER AS
$$
    var jsonb2sqlString = (input) => {
      if (!input) return 'null';

      var json = JSON.stringify(input);
      if (json === '[]' || json === '{}') return 'null';

      json = json.replace(/'/g, "''");

      return `'${json}'`;
    }

    var createInputLists = (properties) => {
        var columns = [],
            values = [];

        for (var column of Object.keys(properties)) {
            var value = jsonb2sqlString(properties[column]);

            values.push(value);
            columns.push(column);
        }

        return [columns.join(), values.join()];
    };

    var inputLists = createInputLists(__properties),
        sku = __sku ? `'${__sku}'` : null,
        type = __type ? `'${__type}'` : null;

    var result = plv8.execute(
        `INSERT INTO product_entity(sku, type, ${inputLists[0]})
            VALUES (${sku}, ${type}, ${inputLists[1]}) RETURNING id;`);

    return result[0]['id'];
$$
LANGUAGE PLV8;

CREATE OR REPLACE FUNCTION
  <schema_name>.nu3__create_product_and_ct_node(__sku VARCHAR, __type VARCHAR, __properties JSONB)
  RETURNS INTEGER AS
$$
DECLARE
  product_id int;

BEGIN
  SELECT * INTO STRICT product_id FROM nu3__create_product(__sku, __type, __properties);
  PERFORM nu3__ct_create_node(product_id);

  RETURN product_id;
END;
$$
LANGUAGE PLPGSQL;

CREATE OR REPLACE FUNCTION
  <schema_name>.nu3__update_product(__sku VARCHAR, __properties JSONB)
  RETURNS TEXT AS
$$
    var createUpdateStatements = (properties) => {
        var updateStatements = [];

        for (var column of Object.keys(properties)) {
            var value = properties[column];

            if (!value) continue;

            value = JSON.stringify(properties[column]).replace(/'/g,"''");
            if (value === '[]') value = '{}';

            updateStatements.push(`${column} = ${column} || '${value}'`);
        }

        return updateStatements.join();
    };

    var updateStatements = createUpdateStatements(__properties);

    return plv8.execute(`UPDATE product_entity SET ${updateStatements} WHERE sku='${__sku}';`);
$$
LANGUAGE PLV8;

CREATE OR REPLACE FUNCTION
  <schema_name>.nu3__overwrite_product(__product_id INTEGER, __properties JSONB)
  RETURNS TEXT AS
$$
    var createUpdateStatements = (properties) => {
        var updateStatements = [];

        for (var column of Object.keys(properties)) {
            var value = properties[column];
            if (!value)
                updateStatements.push(`${column} = NULL`);

            value = JSON.stringify(properties[column]).replace(/'/g,"''");
            if (value === '[]') value = '{}';

            updateStatements.push(`${column} = '${value}'`);
        }

        return updateStatements.join();
    };

    var updateStatements = createUpdateStatements(__properties);

    return plv8.execute(`UPDATE product_entity SET ${updateStatements} WHERE id=${__product_id};`);
$$
LANGUAGE PLV8;

CREATE OR REPLACE FUNCTION
  <schema_name>.nu3__jsonb_concat(__A JSONB, __B JSONB)
  RETURNS JSONB AS
$$
BEGIN
  IF __A IS NULL THEN
    RETURN __B;
  END IF;

  IF __B IS NULL THEN
    RETURN __A;
  END IF;

  RETURN __A || __B;
END
$$
LANGUAGE PLPGSQL;

CREATE AGGREGATE <schema_name>.nu3__jsonb_agg_concat (JSONB) (
  sfunc = <schema_name>.nu3__jsonb_concat,
  stype = JSONB
);

CREATE OR REPLACE FUNCTION
  <schema_name>.nu3__fetch_product_query (__condition VARCHAR)
  RETURNS TABLE (
    id INTEGER, sku VARCHAR, type PRODUCT_TYPE, global JSONB, de JSONB, at JSONB, fr JSONB, de_de JSONB, at_de JSONB, fr_fr JSONB
  ) AS
$$
DECLARE
  _columns VARCHAR[] := ARRAY['global', 'de','at','fr', 'de_de', 'at_de', 'fr_fr'];
  _column VARCHAR;
  _selectList TEXT := '';

BEGIN
  FOREACH _column IN ARRAY _columns
  LOOP
    _selectList := _selectList || 'nu3__jsonb_agg_concat(rp.' || _column || '),';
  END LOOP;
  SELECT INTO _selectList trim(TRAILING ',' FROM _selectList);

  RETURN QUERY EXECUTE 'SELECT '
                       || 'rp.product_id as id,'
                       || 'rp.product_sku as sku,'
                       || 'rp.product_type as type,'
                       || _selectList
                       ||  ' FROM (SELECT '
                       ||    'product.id as product_id,'
                       ||    'product.sku as product_sku,'
                       ||    'product.type as product_type,'
                       ||    'relation.*,'
                       ||    'parent.* '
                       ||  'FROM product_entity product '
                       ||    'JOIN product_relations relation ON product.id = relation.child_id '
                       ||    'JOIN product_entity parent ON parent.id = relation.parent_id '
                       ||  'WHERE ' || __condition
                       ||  ' ORDER BY relation.depth DESC'
                       ||') rp '
                       ||  'GROUP BY rp.child_id, rp.product_id, rp.product_sku, rp.product_type';
  RETURN;
END;
$$
LANGUAGE PLPGSQL;

CREATE OR REPLACE FUNCTION
  <schema_name>.nu3__fetch_product (__id INTEGER)
  RETURNS TABLE (
    id INTEGER, sku VARCHAR, type PRODUCT_TYPE, global JSONB, de JSONB, at JSONB, fr JSONB, de_de JSONB, at_de JSONB, fr_fr JSONB
  ) AS
$$ BEGIN
  RETURN QUERY EXECUTE
    format('SELECT * FROM nu3__fetch_product_query(''product.id = %1$s AND (parent.id = %1$s OR parent.sku ISNULL)'');', __id);
  RETURN;
END; $$
LANGUAGE PLPGSQL;

CREATE OR REPLACE FUNCTION
  <schema_name>.nu3__fetch_product (__sku VARCHAR)
  RETURNS TABLE (
    id INTEGER, sku VARCHAR, type PRODUCT_TYPE, global JSONB, de JSONB, at JSONB, fr JSONB, de_de JSONB, at_de JSONB, fr_fr JSONB
  ) AS
$$ BEGIN
  RETURN QUERY EXECUTE
    format('SELECT * FROM nu3__fetch_product_query(''product.sku = ''%1$L'' AND (parent.sku = ''%1$L'' OR parent.sku ISNULL)'');', __sku);
  RETURN;
END; $$
LANGUAGE PLPGSQL;

CREATE OR REPLACE FUNCTION
  <schema_name>.nu3__fetch_all_products ()
  RETURNS TABLE (
    id INTEGER, sku VARCHAR, type PRODUCT_TYPE, global JSONB, de JSONB, at JSONB, fr JSONB, de_de JSONB, at_de JSONB, fr_fr JSONB
  ) AS
$$ BEGIN
    RETURN QUERY EXECUTE
      format('SELECT * FROM nu3__fetch_product_query(''child_id = parent_id OR parent.sku IS NULL'');', null);
  RETURN;
END; $$
LANGUAGE PLPGSQL;

CREATE OR REPLACE FUNCTION
  <schema_name>.nu3__fetch_product_merged (__sku VARCHAR, __country VARCHAR, __lang VARCHAR)
  RETURNS TABLE (
    id INTEGER, sku VARCHAR, type PRODUCT_TYPE, properties JSONB
  ) AS
$$ BEGIN
  RETURN QUERY EXECUTE
    format('SELECT id, sku, "type",  nu3__jsonb_concat(%I, nu3__jsonb_concat("global", %I)) FROM nu3__fetch_product(%L);', __lang, __country, __sku);
  RETURN;
END; $$
LANGUAGE PLPGSQL;

CREATE OR REPLACE FUNCTION
  <schema_name>.nu3__create_product_option_value(__name VARCHAR)
RETURNS INTEGER AS
$$
  INSERT INTO catalog.product_attributes_values (name) VALUES (__name);
  SELECT 1;
$$
LANGUAGE SQL;

CREATE OR REPLACE FUNCTION
  <schema_name>.nu3__update_product_option_value(__name VARCHAR, __column VARCHAR, __values JSONB)
RETURNS INTEGER AS
$$
DECLARE
  affected_rows_count INTEGER;

BEGIN
  EXECUTE 'WITH attribute_values AS ('
  || ' SELECT jsonb_agg(values.value) as concatenated_value FROM ('
  ||    ' SELECT DISTINCT jsonb_array_elements(nu3__jsonb_concat(' || quote_ident(__column) || ', $1)) AS value'
  ||      ' FROM product_attributes_values WHERE name = $2 ORDER BY 1'
  ||    ') values'
  || '), affected_attributes AS ('
  || ' UPDATE product_attributes_values'
  || ' SET ' || quote_ident(__column) || ' = value.concatenated_value FROM attribute_values value WHERE name = $2'
  || ' RETURNING name)'
  || ' SELECT count(*) FROM affected_attributes'
    INTO affected_rows_count USING __values, __name;

  RETURN affected_rows_count;
END; $$
LANGUAGE PLPGSQL;
