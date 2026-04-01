<?php declare(strict_types=1);

namespace Tailors\Console\Workflows\Config;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

final class BackwardCompatibilityCheckConfig extends AbstractScriptConfig
{
    protected function getDefaultScriptName(): string
    {
        return 'roave-backward-compatibility-check';
    }

    /**
     * @return list<string>
     */
    protected function getSupportedScriptCommands(): array
    {
        return [
            'roave-backwards-compatibility-check:assert-backwards-compatible'
        ];
    }

    protected function getOptionsNode(): ArrayNodeDefinition
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

