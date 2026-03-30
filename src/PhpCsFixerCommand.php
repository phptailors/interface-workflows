<?php declare(strict_types=1);

namespace Tailors\Console\Workflows;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\LogicException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\PropertyAccess\Exception\AccessException;
use Symfony\Component\PropertyAccess\Exception\InvalidArgumentException;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;

#[AsCommand(
    name: 'run:php-cs-fixer',
    description: 'Run php-cs-fixer',
)]
final class PhpCsFixerCommand extends Command
{
    /**
     * @throws LogicException
     * @throws RuntimeException
     * @throws AccessException
     * @throws UnexpectedTypeException
     * @throws InvalidArgumentException
     */
    public function __invoke(InputInterface $input, OutputInterface $output): int
    {
        return $this->execute($input, $output);
    }

    /**
     * @throws LogicException
     * @throws RuntimeException
     * @throws AccessException
     * @throws UnexpectedTypeException
     * @throws InvalidArgumentException
     */
    #[\Override]
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $config = Config::create();
        $runner = new Runner($output, printCommands: true);

        return $runner->runPhpScript($config->phpCsFixerPath, $config->phpCsFixerOptions);
    }
}
