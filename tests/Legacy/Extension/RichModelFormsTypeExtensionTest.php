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

namespace Qossmic\RichModelForms\Tests\Legacy\Extension;

use Psr\Container\ContainerInterface;
use Qossmic\RichModelForms\ExceptionHandling\ExceptionHandlerRegistry;
use Qossmic\RichModelForms\ExceptionHandling\FormExceptionHandler;
use Qossmic\RichModelForms\Tests\Legacy\LegacyClassTestCase;
use SensioLabs\RichModelForms\Extension\RichModelFormsTypeExtension;
use Symfony\Component\PropertyAccess\PropertyAccess;

class RichModelFormsTypeExtensionTest extends LegacyClassTestCase
{
    protected function createInstance(): object
    {
        $exceptionHandlerRegistry = new ExceptionHandlerRegistry($this->createMock(ContainerInterface::class), []);

        return new RichModelFormsTypeExtension(
            PropertyAccess::createPropertyAccessor(),
            $exceptionHandlerRegistry,
            new FormExceptionHandler($exceptionHandlerRegistry)
        );
    }
}
