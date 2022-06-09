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

namespace Qossmic\RichModelForms\Tests\Integration;

use Qossmic\RichModelForms\Tests\Fixtures\Form\CancellationDateMapper;
use Qossmic\RichModelForms\Tests\Fixtures\Form\CancelSubscriptionType;
use Qossmic\RichModelForms\Tests\Fixtures\Form\ProductDataType;
use Qossmic\RichModelForms\Tests\Fixtures\Model\Price;
use Qossmic\RichModelForms\Tests\Fixtures\Model\Product;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

class CoreDataMapperCompatibilityTest extends AbstractDataMapperTest
{
    public function testDataOptionIsMappedToFormWithPropertyMapperIfDataToBeMappedIsEmptyArray(): void
    {
        $formBuilder = $this->createFormBuilder(CancelSubscriptionType::class, null, [
            'data_class' => null,
            'cancellation_date_options' => [
                'property_mapper' => new CancellationDateMapper(),
            ],
        ]);
        $formBuilder->get('cancellation_date')->setData(new \DateTimeImmutable('2019-01-07'));
        $form = $formBuilder->getForm();
        $form->setData([]);
        $this->assertEquals(new \DateTimeImmutable('2019-01-07'), $form['cancellation_date']->getData());
    }

    public function testMappingScalarDataToFormsIsRejected(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $form = $this->createForm(CancelSubscriptionType::class, null, [
            'data_class' => null,
        ]);
        $form->setData('foo');
    }

    public function testSubmittedDataForFieldsWithoutWritePropertyPathOptionAreStillMappedUsingDecoratedDataMapper(): void
    {
        $product = new Product('A fancy product', Price::fromAmount(500));
        $form = $this->createForm(ProductDataType::class, $product);
        $form->submit([
            'name' => 'A way more fancy product',
        ]);

        $this->assertSame('A way more fancy product', $product->getName());
    }

    public function testSubmittingNonMappedTypesDoesNotChangeUnderlyingData(): void
    {
        $product = new Product('A fancy product', Price::fromAmount(500));
        $formBuilder = $this->createFormBuilder(ProductDataType::class, $product);
        $formBuilder->get('name')->setMapped(false);

        $form = $formBuilder->getForm();
        $form->submit([
            'name' => 'A way more fancy product',
        ]);

        $this->assertSame('A fancy product', $product->getName());
    }
}
