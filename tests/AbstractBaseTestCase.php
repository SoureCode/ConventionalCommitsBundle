<?php
/*
 * This file is part of the SoureCode package.
 *
 * (c) Jason Schilling <jason@sourecode.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SoureCode\Bundle\ConventionalCommits\Tests;

use Nyholm\BundleTest\TestKernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\Finder\SplFileInfo;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class AbstractBaseTestCase extends KernelTestCase
{
    protected ?string $directory = null;
    private ?string $previousWorkingDirectory = null;

    protected static function getKernelClass(): string
    {
        return TestKernel::class;
    }

    protected static function createKernel(array $options = []): TestKernel
    {
        /**
         * @var TestKernel $kernel
         */
        $kernel = parent::createKernel($options);
        $kernel->handleOptions($options);

        return $kernel;
    }

    protected function setUp(): void
    {
        $filesystem = new Filesystem();
        $tempDirectory = realpath(sys_get_temp_dir());
        $tempName = uniqid('sourecode-git', true);

        $this->directory = $tempDirectory.'/'.$tempName;

        $filesystem->mkdir($this->directory);

        $this->previousWorkingDirectory = getcwd();
        chdir($this->directory);
    }

    protected function tearDown(): void
    {
        if (null !== $this->previousWorkingDirectory) {
            chdir($this->previousWorkingDirectory);
        }

        $filesystem = new Filesystem();

        if ($this->directory && $filesystem->exists($this->directory)) {
            $filesystem->remove($this->directory);
        }

        $this->directory = null;
    }

    protected function createFile(
        string $filename = null,
        string $content = null,
        array $subDirectories = []
    ): SplFileInfo {
        $filename = $filename ?? uniqid('file', true).'.txt';
        $content = $content ?? uniqid('content', true);
        $subDirectories = !empty($subDirectories) ? $subDirectories : [];

        $subDirectory = implode('/', $subDirectories);

        $file = implode('/', [$this->directory, ...$subDirectories, $filename]);

        $filesystem = new Filesystem();
        $filesystem->dumpFile($file, $content);

        return new SplFileInfo($file, $subDirectory, $filename);
    }
}
