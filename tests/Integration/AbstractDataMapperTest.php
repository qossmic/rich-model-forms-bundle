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

use PHPUnit\Framework\TestCase;
use SensioLabs\RichModelForms\ExceptionHandling\FormExceptionHandler;
use SensioLabs\RichModelForms\Extension\RichModelFormsTypeExtension;
use SensioLabs\RichModelForms\Tests\ExceptionHandlerRegistryTrait;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

abstract class AbstractDataMapperTest extends TestCase
{
    use ExceptionHandlerRegistryTrait;

    protected function createFormBuilder(string $type, $data = null, array $options = [], array $additionalExtensions = []): FormBuilderInterface
    {
        $exceptionHandlerRegistry = $this->createExceptionHandlerRegistry();
        $formFactory = (new FormFactoryBuilder())
            ->addTypeExtension(new RichModelFormsTypeExtension(PropertyAccess::createPropertyAccessor(), $exceptionHandlerRegistry, new FormExceptionHandler($exceptionHandlerRegistry)))
            ->addTypeExtensions($additionalExtensions)
            ->getFormFactory();

        return $formFactory->createBuilder($type, $data, $options);
    }

    protected function createForm(string $type, $data, array $options = [], array $additionalExtensions = []): FormInterface
    {
        return $this->createFormBuilder($type, $data, $options, $additionalExtensions)->getForm();
    }
}
