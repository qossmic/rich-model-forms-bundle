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

use SensioLabs\RichModelForms\Tests\Fixtures\Form\CancelSubscriptionType;
use SensioLabs\RichModelForms\Tests\Fixtures\Form\PauseSubscriptionType;
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
}
