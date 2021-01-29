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

namespace Qossmic\RichModelForms\Tests\Legacy\DataMapper;

use Psr\Container\ContainerInterface;
use Qossmic\RichModelForms\ExceptionHandling\ExceptionHandlerRegistry;
use Qossmic\RichModelForms\ExceptionHandling\FormExceptionHandler;
use Qossmic\RichModelForms\Tests\Legacy\LegacyClassTestCase;
use SensioLabs\RichModelForms\DataMapper\DataMapper;
use Symfony\Component\Form\DataMapperInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class DataMapperTest extends LegacyClassTestCase
{
    protected function createInstance(): object
    {
        return new DataMapper(
            $this->createMock(DataMapperInterface::class),
            PropertyAccess::createPropertyAccessor(),
            new FormExceptionHandler(new ExceptionHandlerRegistry($this->createMock(ContainerInterface::class), []))
        );
    }
}
