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

namespace OpenSC\RichModelForms\Tests\Integration;

use OpenSC\RichModelForms\ExceptionHandling\FormExceptionHandler;
use OpenSC\RichModelForms\Extension\RichModelFormsTypeExtension;
use OpenSC\RichModelForms\Tests\ExceptionHandlerRegistryTrait;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

abstract class AbstractDataMapperTest extends TestCase
{
    use ExceptionHandlerRegistryTrait;

    protected function createFormBuilder(string $type, mixed $data = null, array $options = [], array $additionalExtensions = []): FormBuilderInterface
    {
        $exceptionHandlerRegistry = $this->createExceptionHandlerRegistry();
        $formFactory = (new FormFactoryBuilder())
            ->addTypeExtension(new RichModelFormsTypeExtension(PropertyAccess::createPropertyAccessor(), $exceptionHandlerRegistry, new FormExceptionHandler($exceptionHandlerRegistry)))
            ->addTypeExtensions($additionalExtensions)
            ->getFormFactory();

        return $formFactory->createBuilder($type, $data, $options);
    }

    protected function createForm(string $type, mixed $data, array $options = [], array $additionalExtensions = []): FormInterface
    {
        return $this->createFormBuilder($type, $data, $options, $additionalExtensions)->getForm();
    }
}
