<?php declare(strict_types=1);

namespace Tailors\Console\Workflows;

use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\LogicException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Process;

final class Runner
{
    public function __construct(
        private OutputInterface $output,
        private bool $printCommands = false
    ) {}

    /**
     * @param string[] $arguments
     *
     * @throws RuntimeException
     * @throws LogicException
     */
    public function runPhpScript(string $path, array $arguments = []): int
    {
        if (!file_exists($path)) {
            throw new RuntimeException("{$path} does not exist");
        }

        $command = \array_merge([$path], $arguments);

        $process = new PhpSubprocess($command);

        if ($this->printCommands) {
            $this->output->writeln($process->getCommandLine());
        }

        $process->start();

        foreach ($process as $type => $data) {
            fwrite(Process::ERR === $type ? STDERR : STDOUT, $data);
        }

        return $process->wait();
    }
}
