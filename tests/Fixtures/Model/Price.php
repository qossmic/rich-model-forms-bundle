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

final class Price
{
    private $amount;

    public function __construct(int $amount)
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException(\sprintf('A price cannot be less than 0 (%d given).', $amount));
        }

        $this->amount = $amount;
    }

    public static function fromAmount(int $amount): self
    {
        return new self($amount);
    }

    public function amount(): int
    {
        return $this->amount;
    }
}
