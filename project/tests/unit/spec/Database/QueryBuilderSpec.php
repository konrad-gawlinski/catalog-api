<?php

namespace spec\Database\Nu3\Core\Database;

use Nu3\Spec\App;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class QueryBuilderSpec extends ObjectBehavior
{
  function __construct()
  {
    $app = App::getInstance();
    $app->connectDb();
  }

  function it_concatenates_columns_and_json_values()
  {
    $this->concatColumnsAndJsonValues('global', 'null' , ['de_de' => ['key' => 'value']])
      ->shouldReturn([
        'global,"de_de"',
        'null,\'{"key":"value"}\''
      ]);
  }

  function it_concatenates_columns_and_json_values_with_empty_input()
  {
    $this->concatColumnsAndJsonValues('', '' , ['de_de' => ['key' => 'value']])
      ->shouldReturn([
        '"de_de"',
        '\'{"key":"value"}\''
      ]);
  }

  function it_concatenates_columns_and_empty_json_values()
  {
    $this->concatColumnsAndJsonValues('global', 'null' , [])
      ->shouldReturn([
        'global',
        'null'
      ]);
  }

  function it_escapes_values()
  {
    $this->concatColumnsAndJsonValues('', '' , ['de_de' => ['key' => 'val\'ue']])
      ->shouldReturn([
        '"de_de"',
        '\'{"key":"val\'\'ue"}\''
      ]);
  }

  function it_should_prepare_for_values_expression()
  {
    $this->prepareForValuesExpression([1,2,3])->shouldReturn('(1),(2),(3)');
  }

  function it_should_concatenate_into_empty_values_expression()
  {
    $this->prepareForValuesExpression([])->shouldReturn('');
  }

  function it_should_build_json_merge_update_list_for_single_region()
  {
    $this->buildJsonMergeUpdateList(['global'=>['name' => 'sample_name']])
      ->shouldReturn('"global"="global" || \'{"name":"sample_name"}\'');
  }

  function it_should_build_json_merge_update_list_for_two_regions()
  {
    $this->buildJsonMergeUpdateList([
      'global'=> ['name' => 'sample_name'],
      'de_de' => ['status' => 'new']
    ])
      ->shouldReturn('"global"="global" || \'{"name":"sample_name"}\',"de_de"="de_de" || \'{"status":"new"}\'');
  }

  function it_should_build_json_merge_update_list_and_escape_values()
  {
    $this->buildJsonMergeUpdateList(['global'=>['name' => 'sample_\'name']])
      ->shouldReturn('"global"="global" || \'{"name":"sample_\'\'name"}\'');
  }

  function it_should_build_region_merge_columns()
  {
    $this->buildRegionMergeColumns([['de','de_de'], ['com','en_gb']])->shouldReturn(
      [
        'global || de || de_de as "de-de_de", global || com || en_gb as "com-en_gb"',
         'jsonb_merge(de ORDER BY depth DESC) as de, '
        .'jsonb_merge(de_de ORDER BY depth DESC) as de_de, '
        .'jsonb_merge(com ORDER BY depth DESC) as com, '
        .'jsonb_merge(en_gb ORDER BY depth DESC) as en_gb'
      ]
    );
  }
}
