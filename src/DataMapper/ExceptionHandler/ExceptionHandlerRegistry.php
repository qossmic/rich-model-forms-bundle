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

namespace SensioLabs\RichModelForms\DataMapper\ExceptionHandler;

use Psr\Container\ContainerInterface;

/**
 * @author Christian Flothmann <christian.flothmann@sensiolabs.de>
 */
class ExceptionHandlerRegistry
{
    private $container;
    private $strategies;

    public function __construct(ContainerInterface $container, array $strategies)
    {
        $this->container = $container;
        $this->strategies = $strategies;
    }

    public function has(string $strategy): bool
    {
        return isset($this->strategies[$strategy]);
    }

    public function get(string $strategy): ExceptionHandler
    {
        if (!isset($this->strategies[$strategy])) {
            throw new \InvalidArgumentException(sprintf('The exception handling strategy "%s" is not registered (use one of ["%s"]).', $strategy, implode(array_keys($this->strategies))));
        }

        return $this->container->get($this->strategies[$strategy]);
    }
}
