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

class ProductWithTypeError
{
    private $inStock = 0;

    public function allocateStock(int $stock): self
    {
        if ($stock <= 0) {
            throw new \InvalidArgumentException('Cannot increase the stock with a negative number.');
        }

        $this->inStock = $stock;

        return $this->inStock;
    }

    public function currentStock(): int
    {
        return $this->inStock;
    }
}
