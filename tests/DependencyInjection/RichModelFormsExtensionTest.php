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

namespace SensioLabs\RichModelForms\Tests\DependencyInjection;

use SensioLabs\RichModelForms\DataMapper\ExceptionHandler\ArgumentTypeMismatchExceptionHandler;
use SensioLabs\RichModelForms\DataMapper\ExceptionHandler\FallbackExceptionHandler;
use SensioLabs\RichModelForms\Tests\Fixtures\DependencyInjection\Kernel;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class RichModelFormsExtensionTest extends KernelTestCase
{
    public function testServicesCanBeBuilt()
    {
        $container = $this->bootKernel()->getContainer();

        foreach ($container->getParameter('sensiolabs.rich_model_forms.test_service_aliases') as $id => $type) {
            $this->assertInstanceOf($type, $container->get($id));
        }
    }

    public function testArgumentTypeMismatchExceptionHandlingStrategyIsRegistered()
    {
        $container = $this->bootKernel()->getContainer();
        $exceptionHandlerRegistry = $container->get('test.sensiolabs.rich_model_forms.exception_handler.registry');

        $this->assertInstanceOf(ArgumentTypeMismatchExceptionHandler::class, $exceptionHandlerRegistry->get('type_error'));
    }

    public function testFallbackExceptionHandlingStrategyIsRegistered()
    {
        $container = $this->bootKernel()->getContainer();
        $exceptionHandlerRegistry = $container->get('test.sensiolabs.rich_model_forms.exception_handler.registry');

        $this->assertInstanceOf(FallbackExceptionHandler::class, $exceptionHandlerRegistry->get('fallback'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testExceptionIsThrownIfExceptionHandlingStrategyIsNotKnown()
    {
        $container = $this->bootKernel()->getContainer();
        $exceptionHandlerRegistry = $container->get('test.sensiolabs.rich_model_forms.exception_handler.registry');

        $exceptionHandlerRegistry->get('unknown');
    }

    protected static function getKernelClass()
    {
        return Kernel::class;
    }
}
