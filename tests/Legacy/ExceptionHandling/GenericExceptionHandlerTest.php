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

use Qossmic\RichModelForms\Tests\Legacy\LegacyClassTestCase;
use SensioLabs\RichModelForms\ExceptionHandling\GenericExceptionHandler;

class GenericExceptionHandlerTest extends LegacyClassTestCase
{
    protected function createInstance(): object
    {
        return new GenericExceptionHandler(\Exception::class);
    }
}
