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

namespace Qossmic\RichModelForms\Tests\Fixtures\Model;

class Subscription
{
    private $cancelledBy;
    private $suspended = false;

    public function __construct(\DateTimeInterface $cancellationDate)
    {
        $this->cancelledBy = (new \DateTimeImmutable())->setTimestamp($cancellationDate->getTimestamp());
    }

    public function cancelFrom(\DateTimeInterface $cancellationDate): void
    {
        $this->cancelledBy = (new \DateTimeImmutable())->setTimestamp($cancellationDate->getTimestamp());
    }

    public function cancelledFrom(): ?\DateTimeImmutable
    {
        return $this->cancelledBy;
    }

    public function suspend(): void
    {
        $this->suspended = true;
    }

    public function reactivate(): void
    {
        $this->suspended = false;
    }

    public function isSuspended(): bool
    {
        return $this->suspended;
    }
}
