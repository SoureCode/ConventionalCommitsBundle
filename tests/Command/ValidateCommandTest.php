<?php
/*
 * This file is part of the SoureCode package.
 *
 * (c) Jason Schilling <jason@sourecode.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SoureCode\Bundle\ConventionalCommits\Tests\Command;

use Nyholm\BundleTest\TestKernel;
use SoureCode\Bundle\ConventionalCommits\Command\ValidateCommand;
use SoureCode\Bundle\ConventionalCommits\SoureCodeConventionalCommitsBundle;
use SoureCode\Bundle\ConventionalCommits\Tests\AbstractBaseTestCase;
use SoureCode\Component\Git\Repository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class ValidateCommandTest extends AbstractBaseTestCase
{
    public function testCommandExecutes(): void
    {
        // Arrange
        $repository = Repository::init($this->directory);
        $stage = $repository->getStage();
        $this->createFile();
        $firstCommit = $stage->addAll()->commit('feat: Add feature xy');
        $this->createFile();
        $stage->addAll()->commit('feat: Add file xy');
        $this->createFile();
        $stage->addAll()->commit('fix: Move directory');
        $this->createFile();
        $lastCommit = $stage->addAll()->commit('chore: Add ci');

        $container = self::getContainer();
        $command = $container->get(ValidateCommand::class);
        $tester = new CommandTester($command);
        $input = [
            'begin' => $firstCommit->getHash()->slice(0, 7),
            'end' => $lastCommit->getHash()->slice(0, 7),
        ];

        // Act
        $tester->execute($input);

        // Assert
        self::assertSame(Command::SUCCESS, $tester->getStatusCode());
        self::assertStringContainsString('Messages are valid', $tester->getDisplay(true));
    }

    public function testCommandFails(): void
    {
        // Arrange
        $repository = Repository::init($this->directory);
        $stage = $repository->getStage();
        $this->createFile();
        $firstCommit = $stage->addAll()->commit('feat: Add feature xy');
        $this->createFile();
        $stage->addAll()->commit('feat: Add file xy');
        $this->createFile();
        $stage->addAll()->commit('fix directory');
        $this->createFile();
        $lastCommit = $stage->addAll()->commit('chore: Add ci');

        $container = self::getContainer();
        $command = $container->get(ValidateCommand::class);
        $tester = new CommandTester($command);
        $input = [
            'begin' => $firstCommit->getHash()->slice(0, 7),
            'end' => $lastCommit->getHash()->slice(0, 7),
        ];

        // Act
        $tester->execute($input);

        // Assert
        self::assertSame(Command::FAILURE, $tester->getStatusCode());
        self::assertStringContainsString('Invalid header format', $tester->getDisplay(true));
    }

    public function testVerboseOutput(): void
    {
        // Arrange
        $repository = Repository::init($this->directory);
        $stage = $repository->getStage();
        $this->createFile();
        $stage->addAll()->commit('feat: Add feature xy');
        $this->createFile();
        $commit = $stage->addAll()->commit('feat: Add file xy');

        $container = self::getContainer();
        $command = $container->get(ValidateCommand::class);
        $tester = new CommandTester($command);
        $input = [
            'begin' => substr($commit->getHash(), 0, 7),
        ];

        // Act
        $tester->execute($input, ['verbosity' => Output::VERBOSITY_VERY_VERBOSE]);

        // Assert
        self::assertSame(Command::SUCCESS, $tester->getStatusCode());
        self::assertStringContainsString('Commit: '.$commit->getHash(), $tester->getDisplay(true));
        self::assertStringContainsString('valid!', $tester->getDisplay(true));
    }

    protected function setUp(): void
    {
        parent::setUp();

        static::bootKernel([
            'config' => static function (TestKernel $kernel) {
                $kernel->setTestProjectDir(realpath(__DIR__.'/..'));
                $kernel->addTestBundle(SoureCodeConventionalCommitsBundle::class);
                $kernel->addTestConfig($kernel->getProjectDir().'/config.yaml');
            },
        ]);
    }
}
