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

namespace SensioLabs\RichModelForms\Tests\DataMapper;

use PHPUnit\Framework\TestCase;
use SensioLabs\RichModelForms\Extension\RichModelFormsTypeExtension;
use SensioLabs\RichModelForms\Tests\ExceptionHandlerRegistryTrait;
use SensioLabs\RichModelForms\Tests\Fixtures\Form\CancelSubscriptionType;
use SensioLabs\RichModelForms\Tests\Fixtures\Form\ChangeProductStockType;
use SensioLabs\RichModelForms\Tests\Fixtures\Form\ChangeProductStockTypeExtension;
use SensioLabs\RichModelForms\Tests\Fixtures\Form\PauseSubscriptionType;
use SensioLabs\RichModelForms\Tests\Fixtures\Form\ProductDataType;
use SensioLabs\RichModelForms\Tests\Fixtures\Form\TypeMismatchPriceChangeType;
use SensioLabs\RichModelForms\Tests\Fixtures\Model\Price;
use SensioLabs\RichModelForms\Tests\Fixtures\Model\Product;
use SensioLabs\RichModelForms\Tests\Fixtures\Model\ProductWithTypeError;
use SensioLabs\RichModelForms\Tests\Fixtures\Model\Subscription;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\Exception\InvalidArgumentException;
use Symfony\Component\PropertyAccess\PropertyAccess;

class DataMapperTest extends TestCase
{
    use ExceptionHandlerRegistryTrait;

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

    public function testSubmittedDataForFieldsWithoutWritePropertyPathOptionAreStillMappedUsingDecoratedDataMapper(): void
    {
        $product = new Product('A fancy product', Price::fromAmount(500));
        $form = $this->createForm(ProductDataType::class, $product);
        $form->submit([
            'name' => 'A way more fancy product',
        ]);

        $this->assertSame('A way more fancy product', $product->getName());
    }

    public function testSubmittedDataForFieldsWithWritePropertyPathOptionAreMapped(): void
    {
        $subscription = new Subscription(new \DateTimeImmutable('2018-07-01'));
        $form = $this->createForm(CancelSubscriptionType::class, $subscription);
        $form->submit([
            'cancellation_date' => [
                'year' => '2019',
                'month' => '1',
                'day' => '7',
            ],
         ]);

        $this->assertEquals(new \DateTimeImmutable('2019-01-07'), $subscription->cancelledFrom());
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

    public function testSubmittedDataDependentWritePropertyPath(): void
    {
        $subscription = new Subscription(new \DateTimeImmutable());

        $form = $this->createForm(PauseSubscriptionType::class, $subscription);
        $form->submit([
            'state' => '0',
        ]);

        $this->assertTrue($subscription->isSuspended());

        $form = $this->createForm(PauseSubscriptionType::class, $subscription);
        $form->submit([
            'state' => '1',
        ]);

        $this->assertFalse($subscription->isSuspended());
    }

    public function testSubmittingValuesNotResolvingToWritePropertyPathsInvalidateTheForm(): void
    {
        $form = $this->createForm(PauseSubscriptionType::class, new Subscription(new \DateTimeImmutable()));
        $form->submit([
            'state' => '',
        ]);

        $this->assertFalse($form->isValid());
    }

    public function testMismatchingArgumentTypesWillBeConvertedToErrors(): void
    {
        $form = $this->createForm(ChangeProductStockType::class, new Product('A fancy product', Price::fromAmount(500)), [], [new ChangeProductStockTypeExtension(PropertyAccess::createPropertyAccessor())]);
        $form->submit([
            'stock' => '',
        ]);

        $this->assertFalse($form->get('stock')->isValid());
        $this->assertCount(1, $form->get('stock')->getErrors());
        $this->assertSame('This value should be of type integer.', $form->get('stock')->getErrors()[0]->getMessage());
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
        $this->assertSame('This value should be of type SensioLabs\RichModelForms\Tests\Fixtures\Model\Price.', $form->get('price')->getErrors()[0]->getMessage());
        $this->assertInstanceOf(InvalidArgumentException::class, $form->get('price')->getErrors()[0]->getCause());
        $this->assertStringStartsWith('Expected argument of type "SensioLabs\RichModelForms\Tests\Fixtures\Model\Price", "integer" given', $form->get('price')->getErrors()[0]->getCause()->getMessage());
    }

    /**
     * @expectedException \TypeError
     */
    public function testNonArgumentTypeMismatchErrorsWillNotBeHandled(): void
    {
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

    private function createFormBuilder(string $type, $data = null, array $options = [], array $additionalExtensions = []): FormBuilderInterface
    {
        $formFactory = (new FormFactoryBuilder())
            ->addTypeExtension(new RichModelFormsTypeExtension(PropertyAccess::createPropertyAccessor(), $this->createExceptionHandlerRegistry()))
            ->addTypeExtensions($additionalExtensions)
            ->getFormFactory();

        return $formFactory->createBuilder($type, $data, $options);
    }

    private function createForm(string $type, $data, array $options = [], array $additionalExtensions = []): FormInterface
    {
        return $this->createFormBuilder($type, $data, $options, $additionalExtensions)->getForm();
    }
}
