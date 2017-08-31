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

CREATE OR REPLACE FUNCTION create_product(
  _product_id INTEGER, _sku VARCHAR, _type VARCHAR, _properties JSONB)
  RETURNS INTEGER AS
$$
    var createInputLists = (properties) => {
        var columns = [],
            values = [];

        for (var column of Object.keys(properties)) {
            var value = JSON.stringify(properties[column]).replace(/'/g,"\\'");

            values.push(`'${value}'`);
            columns.push(column);
        }

        return [
          columns.join(), values.join()
        ];
    };

    var inputLists = createInputLists(_properties),
        sku = _sku ? `'${_sku}'` : null,
        type = _type ? `'${_type}'` : null;

    return plv8.execute(
        `INSERT INTO catalog.product_entity(id, sku, type, ${inputLists[0]})
            VALUES (${_product_id}, ${sku}, ${type}, ${inputLists[1]});`);
$$
LANGUAGE PLV8;

CREATE OR REPLACE FUNCTION update_product(_product_id INTEGER, _properties JSONB) RETURNS TEXT AS
$$
    var createUpdateStatements = (properties) => {
        var updateStatements = [];

        for (var column of Object.keys(properties)) {
            var value = JSON.stringify(properties[column]).replace(/'/g,"\\'");

            updateStatements.push(`${column} = ${column} || '${value}'`);
        }

        return updateStatements.join();
    };

    var updateStatements = createUpdateStatements(_properties);

    return plv8.execute(`UPDATE product_entity SET ${updateStatements} WHERE id=${_product_id};`);
$$
LANGUAGE PLV8;

--SELECT create_product(99999993, 'abc', 'Config', '{"de_de":{"text":"value"}}');
--SELECT update_product(99999993, '{"de_de":{"mytext":"his value"}}');
