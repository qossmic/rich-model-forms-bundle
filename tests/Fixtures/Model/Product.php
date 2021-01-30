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

namespace SensioLabs\RichModelForms\Tests\Fixtures\Model;

class Product
{
    private $name;
    private $price;
    private $inStock = 0;

    public function __construct(string $name, Price $price)
    {
        $this->setName($name);
        $this->price = $price;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        if ('' === $name) {
            throw new \InvalidArgumentException('The product name must not be empty.');
        }

        if (\strlen($name) < 10) {
            throw new \InvalidArgumentException('The product name must have a length of 10 characters or more.');
        }

        $this->name = $name;
    }

    public function getPrice(): Price
    {
        return $this->price;
    }

    public function setPrice(Price $price): void
    {
        $this->price = $price;
    }

    public function allocateStock(int $stock): void
    {
        if ($stock <= 0) {
            throw new \InvalidArgumentException('Cannot increase the stock with a negative number.');
        }

        $this->inStock = $stock;
    }

    public function currentStock(): int
    {
        return $this->inStock;
    }
}
