CREATE OR REPLACE FUNCTION
  catalog_sp.nu3__ct_create_node(__id INTEGER) RETURNS integer AS
$$
INSERT INTO catalog.product_relations (parent_id, child_id, depth)
  VALUES (__id, __id, 0)
RETURNING 1;
$$
LANGUAGE SQL;

CREATE OR REPLACE FUNCTION
  catalog_sp.nu3__ct_make_node_a_child(__parent_id INTEGER, __child_id INTEGER, __depth INTEGER)
  RETURNS INTEGER AS
$$
INSERT INTO catalog.product_relations(parent_id, child_id, depth)
  SELECT p.parent_id, c.child_id, p.depth+c.depth+1
  FROM catalog.product_relations p, catalog.product_relations c
  WHERE p.child_id = __parent_id AND c.parent_id = __child_id
RETURNING 1;
$$
LANGUAGE SQL;

CREATE OR REPLACE FUNCTION
  catalog_sp.nu3__create_product(__sku VARCHAR, __type VARCHAR, __properties JSONB)
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
        `INSERT INTO catalog.product_entity(sku, type, ${inputLists[0]})
            VALUES (${sku}, ${type}, ${inputLists[1]}) RETURNING id;`);

    return result[0]['id'];
$$
LANGUAGE PLV8;

CREATE OR REPLACE FUNCTION
  catalog_sp.nu3__update_product(__product_id INTEGER, __properties JSONB)
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

    return plv8.execute(`UPDATE product_entity SET ${updateStatements} WHERE id=${__product_id};`);
$$
LANGUAGE PLV8;

CREATE OR REPLACE FUNCTION
  catalog_sp.nu3__overwrite_product(__product_id INTEGER, __properties JSONB)
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
  catalog_sp.nu3__jsonb_concat(__A JSONB, __B JSONB)
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

CREATE AGGREGATE catalog_sp.nu3__jsonb_agg_concat (JSONB) (
  sfunc = catalog_sp.nu3__jsonb_concat,
  stype = JSONB
);