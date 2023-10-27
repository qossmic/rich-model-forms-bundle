<?php

/*
 * This file is part of the RichModelFormsBundle package.
 *
 * (c) Christian Flothmann <christian.flothmann@qossmic.com>
 * (c) Christopher Hertel <mail@christopher-hertel.de>
 * (c) QOSSMIC GmbH <info@qossmic.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

$finder = PhpCsFixer\Finder::create()
    ->in(__DIR__.'/src')
    ->in(__DIR__.'/tests')
    ->append([__FILE__]);

if (\PHP_VERSION_ID < 70400) {
    $finder->notPath('Dto/Product.php');
}

$config = (new PhpCsFixer\Config())
    ->setRules([
        '@Symfony' => true,
        '@Symfony:risky' => true,
        '@PHPUnit60Migration:risky' => true,
        'array_syntax' => ['syntax' => 'short'],
        'declare_equal_normalize' => ['space' => 'single'],
        'declare_strict_types' => true,
        'get_class_to_class_keyword' => false,
        'header_comment' => [
            'header' => 'This file is part of the RichModelFormsBundle package.

(c) Christian Flothmann <christian.flothmann@qossmic.com>
(c) Christopher Hertel <mail@christopher-hertel.de>
(c) QOSSMIC GmbH <info@qossmic.com>

For the full copyright and license information, please view the LICENSE
file that was distributed with this source code.',
            'location' => 'after_open',
        ],
        'global_namespace_import' => [
            'import_classes' => false,
            'import_constants' => false,
            'import_functions' => false,
        ],
        'modernize_strpos' => false,
        'ordered_imports' => true,
        'php_unit_no_expectation_annotation' => false,
        'void_return' => true,
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder);

return $config;
