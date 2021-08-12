<?php
/*
 * This file is part of the SoureCode package.
 *
 * (c) Jason Schilling <jason@sourecode.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SoureCode\Bundle\ConventionalCommits\Validator\Loader;

use SoureCode\Component\ConventionalCommits\HeaderInterface;
use Symfony\Component\Validator\Constraints\Choice;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Loader\LoaderInterface;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class HeaderLoader implements LoaderInterface
{
    /**
     * @var array{min: int, max: int, extra: bool, values: array<int, string>}
     */
    private array $typeConfig;

    /**
     * @var array{min: int, max: int, extra: bool, required: bool, values: array<int, string>}
     */
    private array $scopeConfig;

    /**
     * @var array{min: int, max: int}
     */
    private array $descriptionConfig;

    /**
     * @param array{min: int, max: int, extra: bool, values: array<int, string>}                 $typeConfig
     * @param array{min: int, max: int, extra: bool, required: bool, values: array<int, string>} $scopeConfig
     * @param array{min: int, max: int}                                                          $descriptionConfig
     */
    public function __construct(array $typeConfig, array $scopeConfig, array $descriptionConfig)
    {
        $this->typeConfig = $typeConfig;
        $this->scopeConfig = $scopeConfig;
        $this->descriptionConfig = $descriptionConfig;
    }

    public function loadClassMetadata(ClassMetadata $metadata): bool
    {
        $className = $metadata->getClassName();

        if (\in_array(HeaderInterface::class, class_implements($className), true)) {
            $metadata->addPropertyConstraints('type', [
                new NotBlank(),
                new Length(min: $this->typeConfig['min'], max: $this->typeConfig['max']),
            ]);

            if (!$this->typeConfig['extra']) {
                $metadata->addPropertyConstraint('type', new Choice(choices: $this->typeConfig['values']));
            }

            $metadata->addPropertyConstraints('scope', [
                new Length(min: $this->scopeConfig['min'], max: $this->scopeConfig['max']),
            ]);

            if (!$this->scopeConfig['extra']) {
                $metadata->addPropertyConstraint('type', new Choice(choices: $this->scopeConfig['values']));
            }

            if ($this->scopeConfig['required']) {
                $metadata->addPropertyConstraints('scope', [
                    new NotBlank(),
                ]);
            }

            $metadata->addPropertyConstraints('description', [
                new NotBlank(),
                new Length(min: $this->descriptionConfig['min'], max: $this->descriptionConfig['max']),
            ]);

            return true;
        }

        return false;
    }
}
