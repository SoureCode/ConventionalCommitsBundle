<?php
/*
 * This file is part of the SoureCode package.
 *
 * (c) Jason Schilling <jason@sourecode.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use SoureCode\Bundle\ConventionalCommits\Command\ValidateCommand;
use SoureCode\Bundle\ConventionalCommits\Validator\Loader\HeaderLoader;
use SoureCode\Bundle\ConventionalCommits\Validator\Loader\MessageLoader;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ValidatorBuilder;

return static function (ContainerConfigurator $container) {
    $container->services()
        // Validator
        ->set('soure_code.conventional_commits.validator', ValidatorInterface::class)
        ->factory([service('soure_code.conventional_commits.validator.builder'), 'getValidator'])
        ->public()

        // Validation Loader
        ->set('soure_code.conventional_commits.validator.loader.header', HeaderLoader::class)
        ->args([
            param('soure_code.conventional_commits.config.type'),
            param('soure_code.conventional_commits.config.scope'),
            param('soure_code.conventional_commits.config.description'),
        ])
        ->set('soure_code.conventional_commits.validator.loader.message', MessageLoader::class)

        // Validator Builder
        ->set('soure_code.conventional_commits.validator.builder', ValidatorBuilder::class)
        ->factory([Validation::class, 'createValidatorBuilder'])
        ->call('addLoader', [
            service('soure_code.conventional_commits.validator.loader.header'),
        ])
        ->call('addLoader', [
            service('soure_code.conventional_commits.validator.loader.message'),
        ])

        // Validate Command
        ->set('soure_code.conventional_commits.command.validate', ValidateCommand::class)
        ->tag('console.command', ['command' => 'sourecode:conventional:commits:validate'])
        ->args([
            service('soure_code.conventional_commits.validator'),
        ])
        ->alias(ValidateCommand::class, 'soure_code.conventional_commits.command.validate');
};
