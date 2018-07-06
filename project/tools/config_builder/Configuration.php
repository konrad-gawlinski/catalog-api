<?php

namespace Nu3\Configuration;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class Configuration implements ConfigurationInterface
{
  public function getConfigTreeBuilder()
  {
    $treeBuilder = new TreeBuilder();
    $rootNode = $treeBuilder->root('config');

    $rootNode
      ->children()
        ->arrayNode('database')->children()
          ->scalarNode('username')->end()
          ->scalarNode('password')->end()
          ->scalarNode('host')->end()
          ->scalarNode('database')->end()
          ->scalarNode('data_schema')->end()
          ->scalarNode('procedures_schema')->end()
        ->end()->end()
        ->arrayNode('region')->children()
          ->arrayNode('global')->prototype('scalar')->end()->end()
          ->arrayNode('country')->prototype('scalar')->end()->end()
          ->arrayNode('language')->prototype('scalar')->end()->end()
        ->end()->end()
        ->variableNode('product')->end()
        ->arrayNode('shop')->children()
          ->arrayNode('region_configurations')->arrayPrototype()->prototype('scalar')->end()->end()->end()
        ->end()->end()
      ->end()
      ->end();

    return $treeBuilder;
  }
}
