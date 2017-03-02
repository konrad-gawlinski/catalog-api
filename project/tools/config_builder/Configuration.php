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
        ->end()
        ->end()
        ->arrayNode('storage')->children()
          ->arrayNode('available')->prototype('scalar')->end()->end()
        ->end()
        ->end()
        ->variableNode('product')->end()
      ->end()
      ->end();

    return $treeBuilder;
  }
}
