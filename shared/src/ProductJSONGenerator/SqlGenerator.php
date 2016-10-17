<?php

class SqlGenerator
{
	private $jsonGenerator;

	public function __construct(ProductJsonGenerator $jsonGenerator)
	{
		$this->jsonGenerator = $jsonGenerator;
	}

	public function generateInsertValues($sku)
	{
		return "('{$sku}', '{$this->jsonGenerator->generateJSON()}')";
	}
}
