<?php declare(strict_types=1);

namespace Tailors\Console\Workflows\Config;

use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;

abstract class AbstractScriptConfig implements ConfigurationInterface
{
    final public function getConfigTreeBuilder(): TreeBuilder
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

    abstract protected function getDefaultScriptName(): string;

    /**
     * @return list<string>
     */
    abstract protected function getSupportedScriptCommands(): array;

    abstract protected function getOptionsNode(): ArrayNodeDefinition;
}

