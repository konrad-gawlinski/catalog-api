
BEGIN;

-----------------------------------------------------------------------
-- nu3.product
-----------------------------------------------------------------------

DROP TABLE IF EXISTS "nu3"."product" CASCADE;

CREATE TABLE "nu3"."product"
(
    "sku" varchar(10) NOT NULL,
    "status" product_status NOT NULL,
    "raw" jsonb NOT NULL,
    "computed" jsonb NOT NULL,
    PRIMARY KEY ("sku")
);

COMMIT;
