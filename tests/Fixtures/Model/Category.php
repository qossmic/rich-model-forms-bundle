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

class Category
{
    private $name;
    private $parent;

    public function __construct(string $name, self $parent = null)
    {
        $this->validateName($name);

        $this->name = $name;
        $this->parent = $parent;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function rename(string $name): void
    {
        $this->validateName($name);

        $this->name = $name;
    }

    public function hasParent(): bool
    {
        return null !== $this->parent;
    }

    public function getParent(): self
    {
        if (!$this->hasParent()) {
            throw new \LogicException('The category has no parent category.');
        }

        return $this->parent;
    }

    public function moveTo(self $parent): void
    {
        $this->parent = $parent;
    }

    private function validateName(string $name): void
    {
        if (\strlen($name) < 3) {
            throw new \LengthException(sprintf('The name must have a length of at least three characters ("%s" given).', $name));
        }
    }
}
