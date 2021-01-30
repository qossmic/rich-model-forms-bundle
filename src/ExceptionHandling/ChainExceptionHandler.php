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
 * Delegates execution to a list of handlers, stopping after the first handler that transformed the exception.
 *
 * The execution of the handler chain will be stopped as soon as one of the handlers returns a form error object. Thus,
 * you need to make sure to give specialized exception handlers a higher priority (i.e. place them before more generic
 * handlers in the list that you pass as the argument to the constructor) than any generic handler. This ensures that
 * specialized handlers do their job first before the chain falls back to eventually process the more generic exception
 * handlers.
 *
 * @author Christian Flothmann <christian.flothmann@qossmic.com>
 */
final class ChainExceptionHandler implements ExceptionHandlerInterface
{
    private $exceptionHandlers;

    /**
     * @param ExceptionHandlerInterface[] $exceptionHandlers
     */
    public function __construct(iterable $exceptionHandlers)
    {
        $this->exceptionHandlers = $exceptionHandlers;
    }

    public function getError(FormConfigInterface $formConfig, $data, \Throwable $e): ?Error
    {
        foreach ($this->exceptionHandlers as $exceptionHandler) {
            if (null !== $error = $exceptionHandler->getError($formConfig, $data, $e)) {
                return $error;
            }
        }

        return null;
    }
}
