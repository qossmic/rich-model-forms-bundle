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

namespace SensioLabs\RichModelForms\Tests\Integration;

use SensioLabs\RichModelForms\Tests\Fixtures\Form\CancelSubscriptionType;
use SensioLabs\RichModelForms\Tests\Fixtures\Form\PauseSubscriptionType;
use SensioLabs\RichModelForms\Tests\Fixtures\Form\ShipOrderType;
use SensioLabs\RichModelForms\Tests\Fixtures\Model\Address;
use SensioLabs\RichModelForms\Tests\Fixtures\Model\Order;
use SensioLabs\RichModelForms\Tests\Fixtures\Model\Subscription;

class WritePropertyPathTest extends AbstractDataMapperTest
{
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

    public function testSubmittingValuesTriggeringExceptionsInClosureWritePropertyPathsInvalidateForms(): void
    {
        $form = $this->createForm(PauseSubscriptionType::class, new Subscription(new \DateTimeImmutable()));
        $form->submit([
            'state' => '',
        ]);

        $this->assertFalse($form->isValid());
    }

    public function testMapMultipleSubmittedDataToSingleModelMethod(): void
    {
        $order = new Order();

        $form = $this->createForm(ShipOrderType::class, $order);
        $form->submit([
            'address' => [
                'street' => 'Balthasarstraße 79',
                'zipcode' => 'D-50670',
                'city' => 'Köln',
            ],
            'trackingNumber' => 'TX-12345',
        ]);

        $this->assertSame('TX-12345', $order->getTrackingNumber());
        $this->assertInstanceOf(Address::class, $order->getShippingAddress());
        $this->assertSame('Balthasarstraße 79', $order->getShippingAddress()->getStreet());
        $this->assertSame('D-50670', $order->getShippingAddress()->getZipcode());
        $this->assertSame('Köln', $order->getShippingAddress()->getCity());
    }

    public function testMapMultipleSubmittedDataToSingleModelMethodRequiresCallableMethodNames(): void
    {
        $form = $this->createForm(ShipOrderType::class, new Order(), [
            'address_write_property_path' => 'shipTo',
            'tracking_number_write_property_path' => 'shipTo',
        ]);
        $form->submit([
            'address' => [
                'street' => 'Balthasarstraße 79',
                'zipcode' => 'D-50670',
                'city' => 'Köln',
            ],
            'trackingNumber' => 'TX-12345',
        ]);

        $this->assertTrue($form->isSubmitted());
        $this->assertFalse($form->isValid());
    }

    public function testMapMultipleSubmittedDataToSingleModelMethodRequiresObjectData(): void
    {
        $data = [];

        $form = $this->createForm(ShipOrderType::class, $data, [
            'address_read_property_path' => '[shipping_address]',
            'tracking_number_read_property_path' => '[tracking_number]',
            'data_class' => null,
        ]);
        $form->submit([
            'address' => [
                'street' => 'Balthasarstraße 79',
                'zipcode' => 'D-50670',
                'city' => 'Köln',
            ],
            'trackingNumber' => 'TX-12345',
        ]);

        $this->assertTrue($form->isSubmitted());
        $this->assertFalse($form->isValid());
    }
}
