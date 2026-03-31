<?php declare(strict_types=1);

namespace Tailors\Console\Workflows\Config;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;

class BackwardCompatibilityCheckConfig implements ConfigurationInterface
{
    public function addConfigSubtree($rootNode): void
    {
        $rootNode
            ->children()
                ->stringNode('script')
                    ->isRequired()
                ->end()
                ->enumNode('command')
                    ->values(['roave-backwards-compatibility-check:assert-backwards-compatible'])
                ->end()
                ->arrayNode('options', 'option')
                    ->arrayPrototype()
                        ->children()
                          ->booleanNode('no_interactions')->defaultTrue()->end() // true
                          ->booleanNode('ansi')->defaultTrue()->end() // true
                          ->stringNode('from')->end() // null
                          ->stringNode('to')->end() // null
                          ->booleanNode('install_development_dependencies')->end() // null
                          ->enumNode('format')->values(['console', 'markdown', 'github-actions'])->end() // null
                        ->end()
                    ->end()
                ->end()
                ->arrayNode('arguments', 'argument')
                ->end()
            ->end()
        ;
    }

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('backward_compatibility_check');

        $rootNode = $treeBuilder->getRootNode();

        return $treeBuilder;
    }
}

