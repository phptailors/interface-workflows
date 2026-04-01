<?php declare(strict_types=1);

namespace Tailors\Console\Workflows\Config;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tailors\PHPUnit\KsortedArrayIdenticalToTrait;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\Config\Definition\Exception\InvalidConfigurationException;

final class BackwardCompatibilityCheckConfigTest extends TestCase
{
    use KsortedArrayIdenticalToTrait;

    /**
     * @param array $inputs
     * @param array $expect
     */
    #[DataProvider("provideWithValidConfig")]
    public function testWithValidConfig(array $inputs, array $expect): void
    {
        $processor = new Processor();
        $config = new BackwardCompatibilityCheckConfig();
        $processedConfig = $processor->processConfiguration($config, $inputs);
        $this->assertKsortedArrayIdenticalTo($expect, $processedConfig);
    }

    /**
     * @param array $inputs
     */
    #[DataProvider("provideWithInvalidConfig")]
    public function testWithInvalidConfig(array $inputs, string $expect): void
    {
        $processor = new Processor();
        $config = new BackwardCompatibilityCheckConfig();

        $this->expectException(InvalidConfigurationException::class);
        $this->expectExceptionMessageMatches($expect);

        $processedConfig = $processor->processConfiguration($config, $inputs);
    }

    /**
     * @return iterable<array{inputs:array, expect:array}>
     */
    public static function provideWithValidConfig(): iterable
    {
        $defaults = [
            'script' => 'roave-backward-compatibility-check',
            'options' => [
                'no_interactions' => true,
                'ansi' => true
            ],
            'arguments' => [],
        ];

        yield [
            'inputs' => [[]],
            'expect' => \array_merge($defaults, [])
        ];

        yield [
            'inputs' => [[]],
            'expect' => $defaults,
        ];


        yield [
            'inputs' => [['script' => 'FOO']],
            'expect' => \array_merge($defaults, ['script' => 'FOO'])
        ];

        yield [
            'inputs' => [['script' => 'FOO'], ['script' => 'BAR']],
            'expect' => \array_merge($defaults, ['script' => 'BAR'])
        ];

        yield [
            'inputs' => [['command' => 'roave-backwards-compatibility-check:assert-backwards-compatible']],
            'expect' => \array_merge($defaults, ['command' => 'roave-backwards-compatibility-check:assert-backwards-compatible'])
        ];

        yield [
            'inputs' => [['options' => []]],
            'expect' => $defaults,
        ];

        yield [
            'inputs' => [['options' => []]],
            'expect' => $defaults,
        ];

        yield [
            'inputs' => [['options' => ['no_interactions' => true]]],
            'expect' => \array_replace_recursive($defaults, ['options' => ['no_interactions' => true]]),
        ];

        yield [
            'inputs' => [['options' => ['no_interactions' => false]]],
            'expect' => \array_replace_recursive($defaults, ['options' => ['no_interactions' => false]]),
        ];

        yield [
            'inputs' => [['options' => ['ansi' => true]]],
            'expect' => \array_replace_recursive($defaults, ['options' => ['ansi' => true]]),
        ];

        yield [
            'inputs' => [['options' => ['ansi' => false]]],
            'expect' => \array_replace_recursive($defaults, ['options' => ['ansi' => false]]),
        ];

        yield [
            'inputs' => [['options' => ['from' => 'FOO']]],
            'expect' => \array_replace_recursive($defaults, ['options' => ['from' => 'FOO']]),
        ];

        yield [
            'inputs' => [['options' => ['to' => 'FOO']]],
            'expect' => \array_replace_recursive($defaults, ['options' => ['to' => 'FOO']]),
        ];

//              ->stringNode('from')->end() // null
//              ->stringNode('to')->end() // null
//              ->booleanNode('install_development_dependencies')->end() // null
//              ->enumNode('format')->values(['console', 'markdown', 'github-actions'])->end() // null
    }

    /**
     * @return iterable<array{inputs:array, expect:string}>
     */
    public static function provideWithInvalidConfig(): iterable
    {
        yield [
            'inputs' => [['script' => null]],
            'expect' => '/"backward_compatibility_check.script" cannot contain an empty value/'
        ];

        yield [
            'inputs' => [['command' => 'FOO']],
            'expect' => '/"FOO" is not allowed for path "backward_compatibility_check.command"/'
        ];
    }
}
