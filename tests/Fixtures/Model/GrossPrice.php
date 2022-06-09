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

final class GrossPrice
{
    private $amount;
    private $taxRate;

    public function __construct(int $amount, int $taxRate)
    {
        if ($amount < 0) {
            throw new \InvalidArgumentException(sprintf('A price cannot be less than 0 (%d given).', $amount));
        }

        if (!\in_array($taxRate, [7, 19], true)) {
            throw new \InvalidArgumentException(sprintf('The tax rate must be 7%% or 19%% (%d%% given).', $taxRate));
        }

        $this->amount = $amount;
        $this->taxRate = $taxRate;
    }

    public static function withAmountAndTaxRate(int $amount, int $taxRate): self
    {
        return new self($amount, $taxRate);
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function taxRate(): int
    {
        return $this->taxRate;
    }
}
