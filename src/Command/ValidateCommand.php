<?php
/*
 * This file is part of the SoureCode package.
 *
 * (c) Jason Schilling <jason@sourecode.dev>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace SoureCode\Bundle\ConventionalCommits\Command;

use SoureCode\Component\ConventionalCommits\Message;
use SoureCode\Component\Git\CommitInterface;
use SoureCode\Component\Git\Repository;
use SoureCode\Component\Git\RepositoryInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

/**
 * @author Jason Schilling <jason@sourecode.dev>
 */
class ValidateCommand extends Command
{
    protected static $defaultName = 'sourecode:conventional:commits:validate';

    private ValidatorInterface $validator;

    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setDescription('Validate commits')
            ->addArgument('begin', InputArgument::REQUIRED, 'The first commit in range.')
            ->addArgument('end', InputArgument::OPTIONAL, 'The last commit in range.')
            ->addUsage('8648bac1')
            ->addUsage('8648bac1 8d0536a1');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $repository = Repository::open(getcwd());

        $commits = $this->getCommits($repository, $input);

        $exitCode = Command::SUCCESS;

        foreach ($commits as $commit) {
            if ($io->isVeryVerbose()) {
                $io->writeln(sprintf('Commit: %s', (string) $commit->getHash()));
                $io->writeln(sprintf('Subject: %s', $commit->getMessage()->getSubject()));
                $io->writeln(sprintf('Body: %s', $commit->getMessage()->getBody()));

                $io->write('Validate ...', false);
            }

            try {
                $message = Message::fromString((string) $commit->getMessage());

                $this->validator->validate($message);

                if ($io->isVeryVerbose()) {
                    $io->write(' <fg=green>valid!</>', true);
                }
            } catch (Throwable $exception) {
                if ($io->isVeryVerbose()) {
                    $io->write(' <fg=white;bg=red>invalid!</>', true);
                }

                $io->error($exception->getMessage());

                $exitCode = Command::FAILURE;
            }
        }

        if (Command::SUCCESS === $exitCode) {
            $io->success('Messages are valid.');
        }

        return $exitCode;
    }

    /**
     * @return list<CommitInterface>
     */
    private function getCommits(RepositoryInterface $repository, InputInterface $input): array
    {
        $firstCommit = $input->getArgument('begin');
        /**
         * @var string|null $lastCommit
         */
        $lastCommit = $input->getArgument('end');

        $commit = $repository->getCommit($firstCommit);

        if (null !== $lastCommit) {
            return $commit->to($lastCommit);
        }

        return [$commit];
    }
}
