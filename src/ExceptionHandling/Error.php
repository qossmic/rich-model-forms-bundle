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

namespace SensioLabs\RichModelForms\ExceptionHandling;

/**
 * @author Christian Flothmann <christian.flothmann@qossmic.com>
 */
final class Error
{
    private $cause;
    private $messageTemplate;
    private $parameters;

    public function __construct(\Throwable $cause, string $messageTemplate, array $parameters = [])
    {
        $this->cause = $cause;
        $this->messageTemplate = $messageTemplate;
        $this->parameters = $parameters;
    }

    public function getCause(): \Throwable
    {
        return $this->cause;
    }

    public function getMessageTemplate(): string
    {
        return $this->messageTemplate;
    }

    public function getParameters(): array
    {
        return $this->parameters;
    }
}
