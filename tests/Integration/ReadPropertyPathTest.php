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

use OpenSC\RichModelForms\Tests\Fixtures\Form\CancelSubscriptionType;
use OpenSC\RichModelForms\Tests\Fixtures\Form\CategoryType;
use OpenSC\RichModelForms\Tests\Fixtures\Form\ProductDataType;
use OpenSC\RichModelForms\Tests\Fixtures\Model\Category;
use OpenSC\RichModelForms\Tests\Fixtures\Model\Price;
use OpenSC\RichModelForms\Tests\Fixtures\Model\Product;
use OpenSC\RichModelForms\Tests\Fixtures\Model\Subscription;

class ReadPropertyPathTest extends AbstractDataMapperTest
{
    public function testDataIsNotMappedToFormWithoutReadPropertyPathIfFormIsNotMapped(): void
    {
        $formBuilder = $this->createFormBuilder(ProductDataType::class);
        $formBuilder->get('name')->setMapped(false);
        $formBuilder->get('name')->setData('A way more fancy product');

        $form = $formBuilder->getForm();
        $form->setData(new Product('A fancy product', Price::fromAmount(500)));

        $this->assertSame('A way more fancy product', $form['name']->getData());
    }

    public function testDataIsNotMappedToFormWithReadPropertyPathIfFormIsNotMapped(): void
    {
        $formBuilder = $this->createFormBuilder(CancelSubscriptionType::class);
        $formBuilder->get('cancellation_date')->setMapped(false);
        $formBuilder->get('cancellation_date')->setData(new \DateTimeImmutable('2019-01-07'));

        $form = $formBuilder->getForm();
        $form->setData(new Subscription(new \DateTimeImmutable('2018-07-01')));

        $this->assertEquals(new \DateTimeImmutable('2019-01-07'), $form['cancellation_date']->getData());
    }

    public function testDataOptionIsMappedToFormWithoutReadPropertyPathIfDataToBeMappedIsNull(): void
    {
        $formBuilder = $this->createFormBuilder(ProductDataType::class);
        $formBuilder->get('name')->setData('A way more fancy product');

        $form = $formBuilder->getForm();
        $form->setData(null);

        $this->assertSame('A way more fancy product', $form['name']->getData());
    }

    public function testDataOptionIsMappedToFormWithReadPropertyPathIfDataToBeMappedIsNull(): void
    {
        $formBuilder = $this->createFormBuilder(CancelSubscriptionType::class);
        $formBuilder->get('cancellation_date')->setData(new \DateTimeImmutable('2019-01-07'));

        $form = $formBuilder->getForm();
        $form->setData(null);

        $this->assertEquals(new \DateTimeImmutable('2019-01-07'), $form['cancellation_date']->getData());
    }

    public function testDataOptionIsMappedToFormWithoutReadPropertyPathIfDataToBeMappedIsEmptyArray(): void
    {
        $formBuilder = $this->createFormBuilder(ProductDataType::class, null, [
            'data_class' => null,
        ]);
        $formBuilder->get('name')->setData('A way more fancy product');

        $form = $formBuilder->getForm();
        $form->setData([]);

        $this->assertSame('A way more fancy product', $form['name']->getData());
    }

    public function testDataOptionIsMappedToFormWithReadPropertyPathIfDataToBeMappedIsEmptyArray(): void
    {
        $formBuilder = $this->createFormBuilder(CancelSubscriptionType::class, null, [
            'data_class' => null,
        ]);
        $formBuilder->get('cancellation_date')->setData(new \DateTimeImmutable('2019-01-07'));

        $form = $formBuilder->getForm();
        $form->setData([]);

        $this->assertEquals(new \DateTimeImmutable('2019-01-07'), $form['cancellation_date']->getData());
    }

    public function testDataForFieldsWithoutReadPropertyPathOptionAreStillMappedUsingDecoratedDataMapper(): void
    {
        $form = $this->createForm(ProductDataType::class, new Product('A fancy product', Price::fromAmount(500)));

        $this->assertSame('A fancy product', $form['name']->getData());
    }

    public function testDataToBeMappedIsReadUsingReadPropertyPathOption(): void
    {
        $cancellationDate = new \DateTimeImmutable('2018-07-01');
        $form = $this->createForm(CancelSubscriptionType::class, new Subscription($cancellationDate));

        $this->assertEquals($cancellationDate, $form['cancellation_date']->getData());
    }

    public function testDataToBeMappedIsReadWithClosureReadPropertyPath(): void
    {
        $foodCategory = new Category('food');
        $form = $this->createForm(CategoryType::class, $foodCategory);

        $this->assertSame('food', $form['name']->getData());
        $this->assertNull($form['parent']->getData());

        $vegetablesCategory = new Category('vegetables', $foodCategory);
        $form = $this->createForm(CategoryType::class, $vegetablesCategory, [
            'categories' => [$foodCategory],
        ]);

        $this->assertSame('vegetables', $form['name']->getData());
        $this->assertSame($foodCategory, $form['parent']->getData());
    }

    public function testSubmittingNonMappedTypesWithReadPropertyPathDoesNotChangeUnderlyingData(): void
    {
        $subscription = new Subscription(new \DateTimeImmutable('2018-07-01'));

        $formBuilder = $this->createFormBuilder(CancelSubscriptionType::class, $subscription);
        $formBuilder->get('cancellation_date')->setMapped(false);

        $form = $formBuilder->getForm();
        $form->submit([
            'cancellation_date' => [
                'year' => '2019',
                'month' => '1',
                'day' => '7',
            ],
        ]);

        $this->assertEquals(new \DateTimeImmutable('2018-07-01'), $subscription->cancelledFrom());
    }
}
