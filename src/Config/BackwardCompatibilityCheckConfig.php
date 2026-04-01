<?php declare(strict_types=1);

namespace Tailors\Console\Workflows\Config;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class BackwardCompatibilityCheckConfig implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('backward_compatibility_check');

        $rootNode = $treeBuilder->getRootNode();

        $this->defineConfigTree($rootNode);

        return $treeBuilder;
    }

    /**
     * @param ArrayNodeDefinition $rootNode
     */
    private function defineConfigTree(ArrayNodeDefinition $rootNode): void
    {
        $rootNode
            ->children()
                ->stringNode('script')
                    ->cannotBeEmpty()
                    ->defaultValue($this->getDefaultScriptName())
                ->end()
                ->enumNode('command')
                    ->values($this->getSupportedScriptCommands())
                ->end()
                ->append($this->getOptionsNode())
                ->arrayNode('arguments', 'argument')
                    ->stringPrototype()->end()
                ->end()
            ->end()
        ;
    }

    private function getDefaultScriptName(): string
    {
        return 'roave-backward-compatibility-check';
    }

    /**
     * @return list<string>
     */
    private function getSupportedScriptCommands(): array
    {
        return [
            'roave-backwards-compatibility-check:assert-backwards-compatible'
        ];
    }

    private function getOptionsNode(): ArrayNodeDefinition
    {
        $treeBuilder = new TreeBuilder('options');

        $rootNode = $treeBuilder->getRootNode();

        $node = $rootNode
            ->addDefaultsIfNotSet()
            ->children()
              ->booleanNode('no_interactions')->defaultTrue()->end() // true
              ->booleanNode('ansi')->defaultTrue()->end() // true
              ->stringNode('from')->end() // null
              ->stringNode('to')->end() // null
              ->booleanNode('install_development_dependencies')->end() // null
              ->enumNode('format')->values(['console', 'markdown', 'github-actions'])->end() // null
            ->end()
        ;

        return $node;
    }
}

