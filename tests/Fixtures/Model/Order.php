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

namespace Qossmic\RichModelForms\Tests\Fixtures\Model;

class Order
{
    private $shippingAddress;
    private $trackingNumber;

    public function getShippingAddress(): ?Address
    {
        return $this->shippingAddress;
    }

    public function getTrackingNumber(): ?string
    {
        return $this->trackingNumber;
    }

    public function ship(Address $address, string $trackingNumber): void
    {
        $this->shippingAddress = $address;
        $this->trackingNumber = $trackingNumber;
    }
}
