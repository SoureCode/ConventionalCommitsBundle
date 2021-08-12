<?php
/*
 * This file is part of the SoureCode package.
 *
 * (c) Jason Schilling <jason@sourecode.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SoureCode\Bundle\ConventionalCommits\Tests\Validator;

use Nyholm\BundleTest\TestKernel;
use SoureCode\Bundle\ConventionalCommits\SoureCodeConventionalCommitsBundle;
use SoureCode\Bundle\ConventionalCommits\Tests\AbstractBaseTestCase;
use SoureCode\Component\ConventionalCommits\Message;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ValidatorTest extends AbstractBaseTestCase
{
    public function testValidate(): void
    {
        $container = self::getContainer();
        /**
         * @var ValidatorInterface $validator
         */
        $validator = $container->get('soure_code.conventional_commits.validator');
        $message = Message::fromString('feataaaaa: Add something');

        // Act
        $violations = $validator->validate($message);

        // Assert
        self::assertCount(1, $violations);
    }

    protected function setUp(): void
    {
        static::bootKernel([
            'config' => static function (TestKernel $kernel) {
                $kernel->setTestProjectDir(realpath(__DIR__.'/..'));
                $kernel->addTestBundle(SoureCodeConventionalCommitsBundle::class);
                $kernel->addTestConfig($kernel->getProjectDir().'/config.yaml');
            },
        ]);
    }
}
