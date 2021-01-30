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

use Symfony\Component\Form\FormConfigInterface;

/**
 * A generic exception handler that can transform arbitrary exceptions into form errors.
 *
 * Instances of this exception handler will transform exactly one type of exception (and all of its subtypes) into form
 * errors by extracting the exception's message and passing it to the new FormError instances.
 *
 * CAUTION: Since this listener reuses the exception messages when building form errors these messages will eventually be
 * presented to the end user. Therefore, it should only be used for custom domain exceptions for which developers are
 * absolutely sure not to leak any sensitive information.
 *
 * @author Christian Flothmann <christian.flothmann@qossmic.com>
 */
final class GenericExceptionHandler implements ExceptionHandlerInterface
{
    private $handledExceptionClass;

    public function __construct(string $handledExceptionClass)
    {
        $this->handledExceptionClass = $handledExceptionClass;
    }

    public function getError(FormConfigInterface $formConfig, $data, \Throwable $e): ?Error
    {
        if (!$e instanceof $this->handledExceptionClass) {
            return null;
        }

        return new Error($e, $e->getMessage());
    }
}
