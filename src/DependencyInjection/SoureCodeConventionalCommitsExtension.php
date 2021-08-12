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

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Loader\PhpFileLoader;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class SoureCodeConventionalCommitsExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();

        /**
         * @var array<string, array<string, mixed>> $config
         */
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new PhpFileLoader($container, new FileLocator(__DIR__.'/../../config'));

        $loader->load('services.php');

        $this->populateConfiguration($container, $config);

        if (!$container->hasParameter('soure_code.conventional_commits.git.directory')) {
            $container->setParameter('soure_code.conventional_commits.git.directory', getcwd());
        }
    }

    /**
     * @param array<string, array<string, mixed>> $config
     */
    private function populateConfiguration(ContainerBuilder $container, array $config): void
    {
        foreach ($config as $key => $value) {
            $container->setParameter(sprintf('soure_code.conventional_commits.config.%s', $key), $value);
        }
    }
}
