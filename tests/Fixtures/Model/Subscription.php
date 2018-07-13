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

namespace SensioLabs\RichModelForms\Tests\Fixtures\Model;

class Subscription
{
    private $cancelledBy;

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
}
