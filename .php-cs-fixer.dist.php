<?php declare(strict_types=1);

$finder = PhpCsFixer\Finder::create()
    ->files()
    ->in(__DIR__ . '/src')
    ->in(__DIR__ . '/tests')
    ->name('*.php')
;

$config = new PhpCsFixer\Config();

return $config
    ->setFinder($finder)
    ->setRules([
        '@PSR2' => true,
        '@PhpCsFixer' => true,
        'blank_line_after_opening_tag' => false,
        'linebreak_after_opening_tag' => false,
        'array_syntax' => ['syntax' => 'short'],
        'binary_operator_spaces' => [
            'operators' => [
                '=>' => 'align_single_space_minimal',
                '='  => 'single_space'
            ],
        ],
        // 'phpdoc_to_comment' => true, didn't play well with annotations we
        // needed for psalm
        'phpdoc_to_comment' => false,
        'no_superfluous_phpdoc_tags' => false,
        'ordered_imports' => [
            'imports_order' => ['const', 'class', 'function'],
            'sort_algorithm' => 'alpha',
        ],
    ])
;
// vim: syntax=php sw=4 ts=4 et:
