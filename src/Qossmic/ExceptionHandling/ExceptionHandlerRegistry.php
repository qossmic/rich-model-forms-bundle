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

namespace Qossmic\RichModelForms\ExceptionHandling;

use Psr\Container\ContainerInterface;

/**
 * @author Christian Flothmann <christian.flothmann@qossmic.com>
 */
class ExceptionHandlerRegistry
{
    private ContainerInterface $container;
    /** @var array<string,string> */
    private array $strategies;

    /**
     * @param array<string,string> $strategies
     */
    public function __construct(ContainerInterface $container, array $strategies)
    {
        $this->container = $container;
        $this->strategies = $strategies;
    }

    public function has(string $strategy): bool
    {
        return isset($this->strategies[$strategy]);
    }

    public function get(string $strategy): ExceptionHandlerInterface
    {
        if (!isset($this->strategies[$strategy])) {
            throw new \InvalidArgumentException(sprintf('The exception handling strategy "%s" is not registered (use one of ["%s"]).', $strategy, implode(', ', array_keys($this->strategies))));
        }

        /* @phpstan-ignore-next-line */
        return $this->container->get($this->strategies[$strategy]);
    }
}
