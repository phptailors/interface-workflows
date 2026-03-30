<?php declare(strict_types=1);

namespace Tailors\Console\Workflows;

use Composer\InstalledVersions;
use Symfony\Component\PropertyAccess\Exception\AccessException;
use Symfony\Component\PropertyAccess\Exception\InvalidArgumentException;
use Symfony\Component\PropertyAccess\Exception\UnexpectedTypeException;
use Symfony\Component\PropertyAccess\PropertyAccess;

/**
 * @internal
 *
 * @psalm-suppress PossiblyUnusedProperty
 */
final class Config
{
    /**
     * @var array<string, array{
     *      bin-name: string,
     *      composer-bin-subdir: string,
     *      options: string[]
     * }>
     */
    public const BINARY_DEFAULTS = [
        'backward-compatibility-check' => [
            'bin-name'            => 'roave-backward-compatibility-check',
            'composer-bin-subdir' => 'backward-compatibility-check',
            'options'             => [],
        ],
        'composer-require-checker' => [
            'bin-name'            => 'composer-require-checker',
            'composer-bin-subdir' => 'composer-require-checker',
            'options'             => ['check', '--ansi'],
        ],
        'php-cs-fixer' => [
            'bin-name'            => 'php-cs-fixer',
            'composer-bin-subdir' => 'php-cs-fixer',
            'options'             => ['fix', '--diff', '--dry-run', '--show-progress=dots', '--using-cache=no', '--verbose', '--ansi'],
        ],
        'phpunit' => [
            'bin-name'            => 'phpunit',
            'composer-bin-subdir' => 'phpunit',
            'options'             => [],
        ],
        'psalm' => [
            'bin-name'            => 'psalm',
            'composer-bin-subdir' => 'psalm',
            'options'             => ['--no-progress', '--php-version='.(PHP_MAJOR_VERSION.'.'.PHP_MINOR_VERSION), '--shepherd', '--show-info=true', '--stats'],
        ],
        'rector' => [
            'bin-name'            => 'rector',
            'composer-bin-subdir' => 'rector',
            'options'             => ['process', '--dry-run'],
        ],
    ];

    /**
     * @param string[] $backwardCompatibilityCheckOptions
     * @param string[] $composerRequireCheckerOptions
     * @param string[] $phpCsFixerOptions
     * @param string[] $phpunitOptions
     * @param string[] $psalmOptions
     * @param string[] $rectorOptions
     */
    private function __construct(
        public string $rootInstallPath,
        public string $rootPackageName,
        public bool $hasComposerBinPlugin,
        public string $composerBinTargetDirectory,
        public string $backwardCompatibilityCheckPath,
        public string $composerRequireCheckerPath,
        public string $phpCsFixerPath,
        public string $phpunitPath,
        public string $psalmPath,
        public string $rectorPath,
        public array $backwardCompatibilityCheckOptions,
        public array $composerRequireCheckerOptions,
        public array $phpCsFixerOptions,
        public array $phpunitOptions,
        public array $psalmOptions,
        public array $rectorOptions,
    ) {}

    /**
     * @param ?string[] $backwardCompatibilityCheckOptions
     * @param ?string[] $composerRequireCheckerOptions
     * @param ?string[] $phpCsFixerOptions
     * @param ?string[] $phpunitOptions
     * @param ?string[] $psalmOptions
     * @param ?string[] $rectorOptions
     *
     * @throws InvalidArgumentException
     * @throws UnexpectedTypeException
     * @throws AccessException
     */
    public static function create(
        ?string $rootInstallPath = null,
        ?string $rootPackageName = null,
        ?bool $hasComposerBinPlugin = null,
        ?string $composerBinTargetDirectory = null,
        ?string $backwardCompatibilityCheckPath = null,
        ?string $composerRequireCheckerPath = null,
        ?string $phpCsFixerPath = null,
        ?string $phpunitPath = null,
        ?string $psalmPath = null,
        ?string $rectorPath = null,
        ?array $backwardCompatibilityCheckOptions = null,
        ?array $composerRequireCheckerOptions = null,
        ?array $phpCsFixerOptions = null,
        ?array $phpunitOptions = null,
        ?array $psalmOptions = null,
        ?array $rectorOptions = null,
    ): self {
        $rootPackage = InstalledVersions::getRootPackage();

        $rootInstallPath ??= ($rootPackage['install_path'] ?? '.');

        $rootComposerJsonPath = "{$rootInstallPath}/composer.json";
        $rootComposerJsonString = \file_get_contents($rootComposerJsonPath);
        if (false === $rootComposerJsonString) {
            $rootComposerJson = [];
        } else {
            /** @var array */
            $rootComposerJson = \json_decode($rootComposerJsonString, true);
        }

        $accessor = PropertyAccess::createPropertyAccessor();

        $rootPackageName ??= $rootPackage['name'];
        $hasComposerBinPlugin ??= InstalledVersions::isInstalled('bamarni/composer-bin-plugin');
        if (null == $composerBinTargetDirectory) {
            $jsonPath = '[extra][bamarni-bin][target-directory]';
            if ($accessor->isReadable($rootComposerJson, $jsonPath)) {
                /** @var mixed */
                $value = $accessor->getValue($rootComposerJson, $jsonPath);
                if (is_string($value)) {
                    $composerBinTargetDirectory = $value;
                }
            }
        }
        $composerBinTargetDirectory ??= 'vendor-bin';

        $defaults = self::BINARY_DEFAULTS;

        // Paths
        $args = compact('rootInstallPath', 'hasComposerBinPlugin', 'composerBinTargetDirectory', 'defaults');

        $phpunitPath ??= self::guessBinPath('phpunit', ...$args);
        $composerRequireCheckerPath ??= self::guessBinPath('composer-require-checker', ...$args);
        $phpCsFixerPath ??= self::guessBinPath('php-cs-fixer', ...$args);
        $backwardCompatibilityCheckPath ??= self::guessBinPath('backward-compatibility-check', ...$args);
        $psalmPath ??= self::guessBinPath('psalm', ...$args);
        $rectorPath ??= self::guessBinPath('rector', ...$args);

        // Options
        $args = compact('defaults');

        $phpunitOptions ??= self::getDefaultOptions('phpunit', ...$args);
        $composerRequireCheckerOptions ??= self::getDefaultOptions('composer-require-checker', ...$args);
        $phpCsFixerOptions ??= self::getDefaultOptions('php-cs-fixer', ...$args);
        $backwardCompatibilityCheckOptions ??= self::getDefaultOptions('backward-compatibility-check', ...$args);
        $psalmOptions ??= self::getDefaultOptions('psalm', ...$args);
        $rectorOptions ??= self::getDefaultOptions('rector', ...$args);

        $options = compact(
            'rootInstallPath',
            'rootPackageName',
            'hasComposerBinPlugin',
            'composerBinTargetDirectory',
            'backwardCompatibilityCheckPath',
            'composerRequireCheckerPath',
            'phpCsFixerPath',
            'phpunitPath',
            'psalmPath',
            'rectorPath',
            'backwardCompatibilityCheckOptions',
            'composerRequireCheckerOptions',
            'phpCsFixerOptions',
            'phpunitOptions',
            'psalmOptions',
            'rectorOptions',
        );

        return new self(...$options);
    }

    /**
     * @param array<string,array> $defaults
     *
     * @psalm-param array<string, array{bin-name: string, composer-bin-subdir: string, ...}> $defaults
     */
    private static function guessBinPath(
        string $name,
        string $rootInstallPath,
        bool $hasComposerBinPlugin,
        string $composerBinTargetDirectory,
        array $defaults
    ): string {
        $binName = $defaults[$name]['bin-name'];
        $composerBinSubdir = $defaults[$name]['composer-bin-subdir'];
        $vendorBinPath = "vendor/bin/{$binName}";
        $composerBinPath = "{$composerBinTargetDirectory}/{$composerBinSubdir}";
        if ($hasComposerBinPlugin && is_file("{$composerBinPath}/composer.json")) {
            return "{$composerBinPath}/{$vendorBinPath}";
        }

        return $vendorBinPath;
    }

    /**
     * @param array<string,array> $defaults
     *
     * @psalm-param array<string, array{options: string[], ...}> $defaults
     *
     * @return string[]
     */
    private static function getDefaultOptions(string $name, array $defaults): array
    {
        return $defaults[$name]['options'] ?? [];
    }
}
