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

namespace Qossmic\RichModelForms\Tests\Fixtures\Form;

use Qossmic\RichModelForms\DataMapper\DataMapper;
use Qossmic\RichModelForms\ExceptionHandling\FormExceptionHandler;
use Qossmic\RichModelForms\Tests\ExceptionHandlerRegistryTrait;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

final class ChangeProductStockTypeExtension extends AbstractTypeExtension
{
    use ExceptionHandlerRegistryTrait;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setDataMapper(new DataMapper($builder->getDataMapper(), PropertyAccess::createPropertyAccessor(), new FormExceptionHandler($this->createExceptionHandlerRegistry())));
    }

    public function getExtendedType(): string
    {
        return ChangeProductStockType::class;
    }

    public static function getExtendedTypes(): iterable
    {
        return [ChangeProductStockType::class];
    }
}
