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

namespace SensioLabs\RichModelForms\Tests\Fixtures\Form;

use SensioLabs\RichModelForms\DataMapper\DataMapper;
use SensioLabs\RichModelForms\Tests\ExceptionHandlerRegistryTrait;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

final class ChangeProductStockTypeExtension extends AbstractTypeExtension
{
    use ExceptionHandlerRegistryTrait;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->setDataMapper(new DataMapper($builder->getDataMapper(), PropertyAccess::createPropertyAccessor(), $this->createExceptionHandlerRegistry()));
    }

    public function getExtendedType(): string
    {
        return ChangeProductStockType::class;
    }
}
