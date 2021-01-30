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

use SensioLabs\RichModelForms\Tests\Fixtures\Form\CancellationDateMapper;
use SensioLabs\RichModelForms\Tests\Fixtures\Form\CancelSubscriptionType;
use SensioLabs\RichModelForms\Tests\Fixtures\Model\Subscription;

class PropertyMapperTest extends AbstractDataMapperTest
{
    public function testDataToBeMappedIsReadUsingPropertyMapperOption(): void
    {
        $cancellationDate = new \DateTimeImmutable('2018-07-01');
        $form = $this->createForm(CancelSubscriptionType::class, new Subscription($cancellationDate), [
            'cancellation_date_options' => [
                'property_mapper' => new CancellationDateMapper(),
            ],
        ]);
        $this->assertEquals($cancellationDate, $form['cancellation_date']->getData());
    }

    public function testSubmittedDataForFieldsWithPropertyMapperOptionAreMapped(): void
    {
        $subscription = new Subscription(new \DateTimeImmutable('2018-07-01'));
        $form = $this->createForm(CancelSubscriptionType::class, $subscription, [
            'cancellation_date_options' => [
                'property_mapper' => new CancellationDateMapper(),
            ],
        ]);
        $form->submit([
            'cancellation_date' => [
                'year' => '2019',
                'month' => '1',
                'day' => '7',
            ],
        ]);
        $this->assertEquals(new \DateTimeImmutable('2019-01-07'), $subscription->cancelledFrom());
    }
}
