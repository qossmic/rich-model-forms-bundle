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

namespace OpenSC\RichModelForms\ExceptionHandling;

use Symfony\Component\Form\FormConfigInterface;

/**
 * Converts all exceptions into form errors.
 *
 * The main purpose of this handler is to catch all remaining exceptions to prevent "internal server error" responses.
 * To not leak any sensitive information the error message presented to the user is generic and likely not very helpful
 * to the users. Thus, this handler should only be used as a fallback in a chain of error handlers.
 *
 * @author Christian Flothmann <christian.flothmann@qossmic.com>
 */
final class FallbackExceptionHandler implements ExceptionHandlerInterface
{
    public function getError(FormConfigInterface $formConfig, mixed $data, \Throwable $e): ?Error
    {
        if (!$e instanceof \Exception) {
            return null;
        }

        $messageTemplate = $formConfig->getOption('invalid_message') ?? 'This value is not valid.';
        $parameters = $formConfig->getOption('invalid_message_parameters') ?? [];

        /* @phpstan-ignore-next-line */
        return new Error($e, $messageTemplate, $parameters);
    }
}
