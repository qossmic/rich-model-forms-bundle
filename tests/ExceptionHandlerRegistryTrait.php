<?php

/*
 * This file is part of the RichModelFormsBundle package.
 *
 * (c) Christian Flothmann <christian.flothmann@sensiolabs.de>
 * (c) Christopher Hertel <christopher.hertel@sensiolabs.de>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types = 1);

namespace SensioLabs\RichModelForms\Tests;

use SensioLabs\RichModelForms\ExceptionHandling\ArgumentTypeMismatchExceptionHandler;
use SensioLabs\RichModelForms\ExceptionHandling\ExceptionHandlerRegistry;
use SensioLabs\RichModelForms\ExceptionHandling\FallbackExceptionHandler;
use Symfony\Component\DependencyInjection\ContainerBuilder;

trait ExceptionHandlerRegistryTrait
{
    public function createExceptionHandlerRegistry(): ExceptionHandlerRegistry
    {
        $container = new ContainerBuilder();
        $container->set('type_error', new ArgumentTypeMismatchExceptionHandler());
        $container->set('fallback', new FallbackExceptionHandler());

        return new ExceptionHandlerRegistry($container, [
            'type_error' => 'type_error',
            'fallback' => 'fallback',
        ]);
    }
}
