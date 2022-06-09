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

use Qossmic\RichModelForms\Tests\Fixtures\Form\CategoryType;
use Qossmic\RichModelForms\Tests\Fixtures\Form\ChangeProductStockType;
use Qossmic\RichModelForms\Tests\Fixtures\Form\ChangeProductStockTypeExtension;
use Qossmic\RichModelForms\Tests\Fixtures\Form\ProductDataType;
use Qossmic\RichModelForms\Tests\Fixtures\Form\ProductDtoType;
use Qossmic\RichModelForms\Tests\Fixtures\Form\TypeMismatchPriceChangeType;
use Qossmic\RichModelForms\Tests\Fixtures\Model\Category;
use Qossmic\RichModelForms\Tests\Fixtures\Model\Dto\Product as ProductDto;
use Qossmic\RichModelForms\Tests\Fixtures\Model\Price;
use Qossmic\RichModelForms\Tests\Fixtures\Model\Product;
use Qossmic\RichModelForms\Tests\Fixtures\Model\ProductWithTypeError;
use Symfony\Component\PropertyAccess\Exception\InvalidArgumentException;
use Symfony\Component\PropertyAccess\PropertyAccess;

class ExceptionHandlingTest extends AbstractDataMapperTest
{
    /**
     * @requires PHP >= 7.4
     */
    public function testMismatchingPropertyTypesWillBeConvertedToErrors(): void
    {
        $product = new ProductDto();
        $product->name = 'A fancy product';
        $product->description = 'This is a fancy product.';

        $form = $this->createForm(ProductDtoType::class, $product);
        $form->submit([
            'name' => 'The new name',
            'description' => '',
        ]);

        $this->assertTrue($form->get('name')->isValid());
        $this->assertFalse($form->get('description')->isValid());
        $this->assertSame('This value should be of type string.', $form->get('description')->getErrors()[0]->getMessage());
        $this->assertInstanceOf(InvalidArgumentException::class, $form->get('description')->getErrors()[0]->getCause());
    }

    public function testMismatchingArgumentTypesWillBeConvertedToErrors(): void
    {
        $form = $this->createForm(ChangeProductStockType::class, new Product('A fancy product', Price::fromAmount(500)), [], [new ChangeProductStockTypeExtension(PropertyAccess::createPropertyAccessor())]);
        $form->submit([
            'stock' => '',
        ]);

        $this->assertFalse($form->get('stock')->isValid());
        $this->assertCount(1, $form->get('stock')->getErrors());
        $this->assertSame(\PHP_VERSION_ID >= 70300 ? 'This value should be of type int.' : 'This value should be of type integer.', $form->get('stock')->getErrors()[0]->getMessage());
        $this->assertInstanceOf(\TypeError::class, $form->get('stock')->getErrors()[0]->getCause());
    }

    public function testPropertyAccessInvalidArgumentExceptionsAreTreatedTheSameAsTypeErrors(): void
    {
        $form = $this->createForm(TypeMismatchPriceChangeType::class, new Product('A fancy product', Price::fromAmount(500)));
        $form->submit([
            'price' => '750',
        ]);

        $this->assertFalse($form->get('price')->isValid());
        $this->assertCount(1, $form->get('price')->getErrors());
        $this->assertSame('This value should be of type Qossmic\RichModelForms\Tests\Fixtures\Model\Price.', $form->get('price')->getErrors()[0]->getMessage());
        $this->assertInstanceOf(InvalidArgumentException::class, $form->get('price')->getErrors()[0]->getCause());
        $this->assertMatchesRegularExpression('/^Expected argument of type "Qossmic\\\\RichModelForms\\\\Tests\\\\Fixtures\\\\Model\\\\Price", "int(eger)?" given/', $form->get('price')->getErrors()[0]->getCause()->getMessage());
    }

    public function testNonArgumentTypeMismatchErrorsWillNotBeHandled(): void
    {
        $this->expectException(\TypeError::class);

        $form = $this->createForm(ChangeProductStockType::class, new ProductWithTypeError(), [
            'data_class' => ProductWithTypeError::class,
        ]);
        $form->submit([
            'stock' => '5',
        ]);
    }

    public function testExceptionsWillBeConvertedToErrors(): void
    {
        $form = $this->createForm(ProductDataType::class, new Product('A fancy product', Price::fromAmount(500)));
        $form->submit([
            'name' => 'A product',
        ]);

        $this->assertFalse($form->get('name')->isValid());
        $this->assertCount(1, $form->get('name')->getErrors());
        $this->assertSame('This value is not valid.', $form->get('name')->getErrors()[0]->getMessage());
        $this->assertInstanceOf(\InvalidArgumentException::class, $form->get('name')->getErrors()[0]->getCause());
        $this->assertSame('The product name must have a length of 10 characters or more.', $form->get('name')->getErrors()[0]->getCause()->getMessage());
    }

    public function testMatchingExpectedExceptionIsConvertedIntoFormError(): void
    {
        $form = $this->createForm(CategoryType::class, new Category('food'), [
            'expected_name_exception' => \LengthException::class,
        ]);
        $form->submit([
            'name' => 'fo',
        ]);

        $this->assertFalse($form->get('name')->isValid());
        $this->assertSame('The name must have a length of at least three characters ("fo" given).', $form->get('name')->getErrors()[0]->getMessage());
    }

    /**
     * @group legacy
     */
    public function testMatchingExpectedExceptionIsConvertedIntoFormErrorUsingLegacyOptionName(): void
    {
        $form = $this->createForm(CategoryType::class, new Category('food'), [
            'expected_name_exception' => \LengthException::class,
            'legacy_exception_handling_option' => true,
        ]);
        $form->submit([
            'name' => 'fo',
        ]);
        $this->assertFalse($form->get('name')->isValid());
        $this->assertSame('The name must have a length of at least three characters ("fo" given).', $form->get('name')->getErrors()[0]->getMessage());
    }

    public function testNotMatchingExpectedExceptionsAreNotCaught(): void
    {
        $this->expectException(\LengthException::class);

        $form = $this->createForm(CategoryType::class, new Category('food'), [
            'expected_name_exception' => \InvalidArgumentException::class,
        ]);
        $form->submit([
            'name' => 'fo',
        ]);
    }

    public function testMismatchingArgumentTypesWillBeConvertedToErrorsWithConfiguredExpectedExceptions(): void
    {
        $form = $this->createForm(ChangeProductStockType::class, new Product('A fancy product', Price::fromAmount(500)), [
            'expected_stock_exception' => \InvalidArgumentException::class,
        ], [new ChangeProductStockTypeExtension()]);
        $form->submit([
            'stock' => '',
        ]);

        $this->assertFalse($form->get('stock')->isValid());
        $this->assertCount(1, $form->get('stock')->getErrors());
        $this->assertSame(\PHP_VERSION_ID >= 70300 ? 'This value should be of type int.' : 'This value should be of type integer.', $form->get('stock')->getErrors()[0]->getMessage());
        $this->assertInstanceOf(\TypeError::class, $form->get('stock')->getErrors()[0]->getCause());
    }

    public function testPropertyAccessInvalidArgumentExceptionsAreTreatedTheSameAsTypeErrorsWithConfiguredExpectedExceptions(): void
    {
        $form = $this->createForm(TypeMismatchPriceChangeType::class, new Product('A fancy product', Price::fromAmount(500)), [
            'expected_price_exception' => \DomainException::class,
        ]);
        $form->submit([
            'price' => '750',
        ]);

        $this->assertFalse($form->get('price')->isValid());
        $this->assertCount(1, $form->get('price')->getErrors());
        $this->assertSame('This value should be of type Qossmic\RichModelForms\Tests\Fixtures\Model\Price.', $form->get('price')->getErrors()[0]->getMessage());
        $this->assertInstanceOf(InvalidArgumentException::class, $form->get('price')->getErrors()[0]->getCause());
        $this->assertMatchesRegularExpression('/^Expected argument of type "Qossmic\\\\RichModelForms\\\\Tests\\\\Fixtures\\\\Model\\\\Price", "int(eger)?" given/', $form->get('price')->getErrors()[0]->getCause()->getMessage());
    }

    public function testNonArgumentTypeMismatchErrorsWillNotBeHandledWithConfiguredExpectedExceptions(): void
    {
        $this->expectException(\TypeError::class);

        $form = $this->createForm(ChangeProductStockType::class, new ProductWithTypeError(), [
            'data_class' => ProductWithTypeError::class,
            'expected_stock_exception' => \InvalidArgumentException::class,
        ], [new ChangeProductStockTypeExtension()]);
        $form->submit([
            'stock' => '5',
        ]);
    }
}
