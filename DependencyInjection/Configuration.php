<?php

namespace Avro\CsvBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
* Contains the configuration information for the bundle
*
* @author Joris de Wit <joris.w.dewit@gmail.com>
*/
class Configuration implements ConfigurationInterface
{
    /**
     * Generates the configuration tree.
     *
     * @return TreeBuilder
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('avro_csv');

        $rootNode
            ->children()
                ->scalarNode('db_driver')->defaultValue('orm')->cannotBeEmpty()->end()
                ->scalarNode('batch_size')->defaultValue('15')->cannotBeEmpty()->end()
                ->scalarNode('tmp_upload_dir')->defaultValue('%kernel.root_dir%/../web/uploads/tmp/')->cannotBeEmpty()->end()
                ->scalarNode('sample_count')->defaultValue(1)->cannotBeEmpty()->end()
                ->arrayNode('objects')
                    ->useAttributeAsKey('object')->prototype('array')
                        ->addDefaultsIfNotSet()
                        ->children()
                            ->scalarNode('class')->isRequired()->end()
                            ->scalarNode('redirect_route')->isRequired()->end()
                        ->end()
                    ->end()
                ->end()
            ->end()
        ->end();

        return $treeBuilder;
    }

}
