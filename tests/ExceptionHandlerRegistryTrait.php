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

namespace OpenSC\RichModelForms\Tests;

use OpenSC\RichModelForms\ExceptionHandling\ArgumentTypeMismatchExceptionHandler;
use OpenSC\RichModelForms\ExceptionHandling\ExceptionHandlerRegistry;
use OpenSC\RichModelForms\ExceptionHandling\FallbackExceptionHandler;
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
