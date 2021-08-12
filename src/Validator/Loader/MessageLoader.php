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

use SoureCode\Component\ConventionalCommits\MessageInterface;
use Symfony\Component\Validator\Constraints\Valid;
use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Mapping\Loader\LoaderInterface;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class MessageLoader implements LoaderInterface
{
    public function loadClassMetadata(ClassMetadata $metadata): bool
    {
        $className = $metadata->getClassName();

        if (\in_array(MessageInterface::class, class_implements($className), true)) {
            $metadata->addPropertyConstraints('header', [
                new Valid(),
            ]);

            return true;
        }

        return false;
    }
}
