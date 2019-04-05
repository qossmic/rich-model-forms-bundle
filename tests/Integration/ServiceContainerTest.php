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

namespace SensioLabs\RichModelForms\Tests\Integration;

use SensioLabs\RichModelForms\ExceptionHandling\ArgumentTypeMismatchExceptionHandler;
use SensioLabs\RichModelForms\ExceptionHandling\FallbackExceptionHandler;
use SensioLabs\RichModelForms\Tests\Fixtures\DependencyInjection\Kernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ServiceContainerTest extends KernelTestCase
{
    public function testServicesCanBeBuilt(): void
    {
        $container = $this->bootKernel()->getContainer();

        foreach ($container->getParameter('sensiolabs.rich_model_forms.test_service_aliases') as $id => $type) {
            $this->assertInstanceOf($type, $container->get($id));
        }
    }

    public function testArgumentTypeMismatchExceptionHandlingStrategyIsRegistered(): void
    {
        $container = $this->bootKernel()->getContainer();
        $exceptionHandlerRegistry = $container->get('test.sensiolabs.rich_model_forms.exception_handler.registry');

        $this->assertInstanceOf(ArgumentTypeMismatchExceptionHandler::class, $exceptionHandlerRegistry->get('type_error'));
    }

    public function testFallbackExceptionHandlingStrategyIsRegistered(): void
    {
        $container = $this->bootKernel()->getContainer();
        $exceptionHandlerRegistry = $container->get('test.sensiolabs.rich_model_forms.exception_handler.registry');

        $this->assertInstanceOf(FallbackExceptionHandler::class, $exceptionHandlerRegistry->get('fallback'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionIsThrownIfExceptionHandlingStrategyIsNotKnown(): void
    {
        $container = $this->bootKernel()->getContainer();
        $exceptionHandlerRegistry = $container->get('test.sensiolabs.rich_model_forms.exception_handler.registry');

        $exceptionHandlerRegistry->get('unknown');
    }

    protected static function getKernelClass(): string
    {
        return Kernel::class;
    }
}
