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

use SensioLabs\RichModelForms\Tests\Fixtures\Form\CancellationDateMapper;
use SensioLabs\RichModelForms\Tests\Fixtures\Form\CancelSubscriptionType;
use SensioLabs\RichModelForms\Tests\Fixtures\Form\ProductDataType;
use SensioLabs\RichModelForms\Tests\Fixtures\Model\Price;
use SensioLabs\RichModelForms\Tests\Fixtures\Model\Product;

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

    /**
     * @expectedException \Symfony\Component\Form\Exception\UnexpectedTypeException
     */
    public function testMappingScalarDataToFormsIsRejected(): void
    {
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
