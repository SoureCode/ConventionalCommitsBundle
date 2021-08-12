<?php
/*
 * This file is part of the SoureCode package.
 *
 * (c) Jason Schilling <jason@sourecode.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SoureCode\Bundle\ConventionalCommits\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('soure_code_conventional_commits');

        /**
         * @var ArrayNodeDefinition $rootNode
         */
        $rootNode = $treeBuilder->getRootNode();

        $this->addTypeSection($rootNode);
        $this->addScopeSection($rootNode);
        $this->addDescriptionSection($rootNode);

        return $treeBuilder;
    }

    private function addTypeSection(ArrayNodeDefinition $node): void
    {
        $typeNode = $node->children()->arrayNode('type');
        $typeNodeChildren = $typeNode->children();

        $typeNode->addDefaultsIfNotSet();

        $typeNodeChildren
            ->integerNode('min')
            ->defaultValue(2)
            ->min(1)
            ->max(5);

        $typeNodeChildren
            ->integerNode('max')
            ->defaultValue(10)
            ->min(5)
            ->max(25);

        $typeNodeChildren
            ->booleanNode('extra')
            ->defaultFalse();

        $typeNodeChildren
            ->variableNode('values')
            ->cannotBeEmpty()
            ->defaultValue(
                [
                    'add',
                    'build',
                    'bump',
                    'chore',
                    'ci',
                    'cut',
                    'docs',
                    'enhance',
                    'feat',
                    'fix',
                    'make',
                    'optimize',
                    'perf',
                    'refactor',
                    'revert',
                    'style',
                    'test',
                ]
            )
            ->validate()
            ->castToArray();
    }

    private function addScopeSection(ArrayNodeDefinition $node): void
    {
        $scopeNode = $node->children()->arrayNode('scope');
        $scopeNodeChildren = $scopeNode->children();

        $scopeNode->addDefaultsIfNotSet();

        $scopeNodeChildren
            ->integerNode('min')
            ->defaultValue(3)
            ->min(1)
            ->max(5);

        $scopeNodeChildren
            ->integerNode('max')
            ->defaultValue(10)
            ->min(5)
            ->max(25);

        $scopeNodeChildren
            ->booleanNode('extra')
            ->defaultTrue();

        $scopeNodeChildren
            ->booleanNode('required')
            ->defaultFalse();

        $scopeNodeChildren
            ->variableNode('values')
            ->cannotBeEmpty()
            ->defaultValue([])
            ->validate()
            ->castToArray();
    }

    private function addDescriptionSection(ArrayNodeDefinition $node): void
    {
        $subjectNode = $node->children()->arrayNode('description');
        $subjectNodeChildren = $subjectNode->children();

        $subjectNode->addDefaultsIfNotSet();

        $subjectNodeChildren
            ->integerNode('min')
            ->defaultValue(5)
            ->min(1)
            ->max(10);

        $subjectNodeChildren
            ->integerNode('max')
            ->defaultValue(50)
            ->min(15)
            ->max(50);
    }
}
