<?php

/*
 * This file is part of the RichModelFormsBundle package.
 *
 * (c) Christian Flothmann <christian.flothmann@qossmic.com>
 * (c) Christopher Hertel <mail@christopher-hertel.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace Qossmic\RichModelForms\Tests\Legacy\ExceptionHandling;

use PHPUnit\Framework\TestCase;
use SensioLabs\RichModelForms\ExceptionHandling\ExceptionToErrorMapperTrait;
use Symfony\Bridge\PhpUnit\ExpectDeprecationTrait;

class ExceptionToErrorMapperTraitTest extends TestCase
{
    use ExpectDeprecationTrait;

    /**
     * @group legacy
     */
    public function testTraitIsDeprecated(): void
    {
        $this->expectDeprecation(sprintf('Since sensiolabs-de/rich-model-forms-bundle 0.8: The "%s" is deprecated. Use "%s" instead.', ExceptionToErrorMapperTrait::class, \Qossmic\RichModelForms\ExceptionHandling\ExceptionToErrorMapperTrait::class));

        $classUsingTrait = new class() {
            use ExceptionToErrorMapperTrait;
        };

        self::assertTrue(method_exists($classUsingTrait, 'mapExceptionToError'));
    }
}
