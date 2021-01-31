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

namespace Qossmic\RichModelForms\Tests\Integration;

use Qossmic\RichModelForms\Tests\Fixtures\Form\GrossPriceType;
use Qossmic\RichModelForms\Tests\Fixtures\Form\PriceType;
use Qossmic\RichModelForms\Tests\Fixtures\Model\GrossPrice;
use Qossmic\RichModelForms\Tests\Fixtures\Model\Price;

class FactoryTest extends AbstractDataMapperTest
{
    public function testInitializeNonCompoundRootFormWithEmptyData(): void
    {
        $form = $this->createForm(PriceType::class, null, [
            'factory' => Price::class,
            'property_path' => 'amount',
        ]);

        $this->assertSame('', $form->getViewData());
    }

    public function testInitializeCompoundRootFormWithEmptyData(): void
    {
        $form = $this->createForm(GrossPriceType::class, null, [
            'factory' => GrossPrice::class,
        ]);

        $this->assertSame('', $form->get('amount')->getViewData());
        $this->assertSame('', $form->get('taxRate')->getViewData());
    }

    public function testInitializeEmptyDataWithConstructorFactoryFromNonCompoundForm(): void
    {
        $form = $this->createForm(PriceType::class, null, [
            'factory' => Price::class,
            'property_path' => 'amount',
        ]);
        $form->submit('500');

        $price = $form->getData();

        $this->assertInstanceOf(Price::class, $price);
        $this->assertSame(500, $price->amount());
    }

    public function testInitializeEmptyDataWithConstructorFactoryFromCompoundForm(): void
    {
        $form = $this->createForm(GrossPriceType::class, null, [
            'factory' => GrossPrice::class,
        ]);
        $form->submit([
            'amount' => '500',
            'taxRate' => '7',
        ]);

        $grossPrice = $form->getData();

        $this->assertInstanceOf(GrossPrice::class, $grossPrice);
        $this->assertSame(500, $grossPrice->amount());
        $this->assertSame(7, $grossPrice->taxRate());
    }

    public function testInitializeEmptyDataWithFactoryMethodFromNonCompoundForm(): void
    {
        $form = $this->createForm(PriceType::class, null, [
            'factory' => [Price::class, 'fromAmount'],
            'property_path' => 'amount',
        ]);
        $form->submit('500');

        $price = $form->getData();

        $this->assertInstanceOf(Price::class, $price);
        $this->assertSame(500, $price->amount());
    }

    public function testInitializeEmptyDataWithFactoryMethodFromCompoundForm(): void
    {
        $form = $this->createForm(GrossPriceType::class, null, [
            'factory' => [GrossPrice::class, 'withAmountAndTaxRate'],
        ]);
        $form->submit([
            'amount' => '500',
            'taxRate' => '7',
        ]);

        $grossPrice = $form->getData();

        $this->assertInstanceOf(GrossPrice::class, $grossPrice);
        $this->assertSame(500, $grossPrice->amount());
        $this->assertSame(7, $grossPrice->taxRate());
    }

    public function testInitializeEmptyDataWithClosureFactoryFromNonCompoundForm(): void
    {
        $form = $this->createForm(PriceType::class, null, [
            'factory' => function ($viewData): Price {
                return new Price($viewData);
            },
            'property_path' => 'amount',
        ]);
        $form->submit('500');

        $price = $form->getData();

        $this->assertInstanceOf(Price::class, $price);
        $this->assertSame(500, $price->amount());
    }

    public function testInitializeEmptyDataWithClosureFactoryFromCompoundForm(): void
    {
        $form = $this->createForm(GrossPriceType::class, null, [
            'factory' => function (int $amount, int $taxRate): GrossPrice {
                return new GrossPrice($amount, $taxRate);
            },
        ]);
        $form->submit([
            'amount' => '500',
            'taxRate' => '7',
        ]);

        $grossPrice = $form->getData();

        $this->assertInstanceOf(GrossPrice::class, $grossPrice);
        $this->assertSame(500, $grossPrice->amount());
        $this->assertSame(7, $grossPrice->taxRate());
    }

    public function testInitializeCompoundFormWithPropertyPath(): void
    {
        $form = $this->createForm(GrossPriceType::class, null, [
            'factory' => GrossPrice::class,
            'data_class' => GrossPrice::class,
            'tax_rate_field_name' => 'tax',
            'tax_rate_field_options' => [
                'factory_argument' => 'taxRate',
            ],
        ]);
        $form->submit([
            'amount' => '500',
            'tax' => '7',
        ]);

        $this->assertEquals(new GrossPrice(500, 7), $form->getData());
    }
}
