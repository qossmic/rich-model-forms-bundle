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
use SensioLabs\RichModelForms\Tests\Fixtures\Form\CancelSubscriptionType;
use SensioLabs\RichModelForms\Tests\Fixtures\Form\ProductDataType;
use SensioLabs\RichModelForms\Tests\Fixtures\Model\Product;
use SensioLabs\RichModelForms\Tests\Fixtures\Model\Subscription;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormFactoryBuilder;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\PropertyAccess\PropertyAccess;

class PropertyPathDataMapperTest extends TestCase
{
    public function testDataIsNotMappedToFormWithoutReadPropertyPathIfFormIsNotMapped()
    {
        $formBuilder = $this->createFormBuilder(ProductDataType::class);
        $formBuilder->get('name')->setMapped(false);
        $formBuilder->get('name')->setData('A way more fancy product');

        $form = $formBuilder->getForm();
        $form->setData(new Product('A fancy product'));

        $this->assertSame('A way more fancy product', $form['name']->getData());
    }

    public function testDataIsNotMappedToFormWithReadPropertyPathIfFormIsNotMapped()
    {
        $formBuilder = $this->createFormBuilder(CancelSubscriptionType::class);
        $formBuilder->get('cancellation_date')->setMapped(false);
        $formBuilder->get('cancellation_date')->setData(new \DateTimeImmutable('2019-01-07'));

        $form = $formBuilder->getForm();
        $form->setData(new Subscription(new \DateTimeImmutable('2018-07-01')));

        $this->assertEquals(new \DateTimeImmutable('2019-01-07'), $form['cancellation_date']->getData());
    }

    public function testDataOptionIsMappedToFormWithoutReadPropertyPathIfDataToBeMappedIsNull()
    {
        $formBuilder = $this->createFormBuilder(ProductDataType::class);
        $formBuilder->get('name')->setData('A way more fancy product');

        $form = $formBuilder->getForm();
        $form->setData(null);

        $this->assertSame('A way more fancy product', $form['name']->getData());
    }

    public function testDataOptionIsMappedToFormWithReadPropertyPathIfDataToBeMappedIsNull()
    {
        $formBuilder = $this->createFormBuilder(CancelSubscriptionType::class);
        $formBuilder->get('cancellation_date')->setData(new \DateTimeImmutable('2019-01-07'));

        $form = $formBuilder->getForm();
        $form->setData(null);

        $this->assertEquals(new \DateTimeImmutable('2019-01-07'), $form['cancellation_date']->getData());
    }

    public function testDataOptionIsMappedToFormWithoutReadPropertyPathIfDataToBeMappedIsEmptyArray()
    {
        $formBuilder = $this->createFormBuilder(ProductDataType::class, null, [
            'data_class' => null,
        ]);
        $formBuilder->get('name')->setData('A way more fancy product');

        $form = $formBuilder->getForm();
        $form->setData([]);

        $this->assertSame('A way more fancy product', $form['name']->getData());
    }

    public function testDataOptionIsMappedToFormWithReadPropertyPathIfDataToBeMappedIsEmptyArray()
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
    public function testMappingScalarDataToFormsIsRejected()
    {
        $form = $this->createForm(CancelSubscriptionType::class, null, [
            'data_class' => null,
        ]);
        $form->setData('foo');
    }

    public function testDataForFieldsWithoutReadPropertyPathOptionAreStillMappedUsingDecoratedDataMapper()
    {
        $form = $this->createForm(ProductDataType::class, new Product('A fancy product'));

        $this->assertSame('A fancy product', $form['name']->getData());
    }

    public function testDataToBeMappedIsReadUsingReadPropertyPathOption()
    {
        $cancellationDate = new \DateTimeImmutable('2018-07-01');
        $form = $this->createForm(CancelSubscriptionType::class, new Subscription($cancellationDate));

        $this->assertEquals($cancellationDate, $form['cancellation_date']->getData());
    }

    public function testSubmittedDataForFieldsWithoutWritePropertyPathOptionAreStillMappedUsingDecoratedDataMapper()
    {
        $product = new Product('A fancy product');
        $form = $this->createForm(ProductDataType::class, $product);
        $form->submit([
            'name' => 'A way more fancy product',
        ]);

        $this->assertSame('A way more fancy product', $product->getName());
    }

    public function testSubmittedDataForFieldsWithWritePropertyPathOptionAreMapped()
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

    public function testSubmittingNonMappedTypesDoesNotChangeUnderlyingData()
    {
        $product = new Product('A fancy product');
        $formBuilder = $this->createFormBuilder(ProductDataType::class, $product);
        $formBuilder->get('name')->setMapped(false);

        $form = $formBuilder->getForm();
        $form->submit([
            'name' => 'A way more fancy product',
        ]);

        $this->assertSame('A fancy product', $product->getName());
    }

    public function testSubmittingNonMappedTypesWithReadPropertyPathDoesNotChangeUnderlyingData()
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

    private function createFormBuilder(string $type, $data = null, array $options = []): FormBuilderInterface
    {
        $formFactory = (new FormFactoryBuilder())
            ->addTypeExtension(new RichModelFormsTypeExtension(PropertyAccess::createPropertyAccessor()))
            ->getFormFactory();

        return $formFactory->createBuilder($type, $data, $options);
    }

    private function createForm(string $type, $data, array $options = []): FormInterface
    {
        return $this->createFormBuilder($type, $data, $options)->getForm();
    }
}