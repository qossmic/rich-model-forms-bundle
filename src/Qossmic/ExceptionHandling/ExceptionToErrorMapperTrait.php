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

namespace Qossmic\RichModelForms\ExceptionHandling;

use Symfony\Component\Form\FormConfigInterface;

/**
 * @author Christian Flothmann <christian.flothmann@qossmic.com>
 *
 * @internal
 */
trait ExceptionToErrorMapperTrait
{
    private ExceptionHandlerRegistry $exceptionHandlerRegistry;

    private function mapExceptionToError(FormConfigInterface $formConfig, mixed $data, \Throwable $e): ?Error
    {
        $exceptionHandlers = [];

        if (null !== $formConfig->getOption('handle_exception')) {
            /* @phpstan-ignore-next-line */
            foreach ($formConfig->getOption('handle_exception') as $exceptionClass) {
                /* @phpstan-ignore-next-line */
                $exceptionHandlers[] = new GenericExceptionHandler($exceptionClass);
            }

            $exceptionHandlers[] = $this->exceptionHandlerRegistry->get('type_error');
        } else {
            /* @phpstan-ignore-next-line */
            foreach ($formConfig->getOption('exception_handling_strategy') as $strategy) {
                /* @phpstan-ignore-next-line */
                $exceptionHandlers[] = $this->exceptionHandlerRegistry->get($strategy);
            }
        }

        if (1 === \count($exceptionHandlers)) {
            $exceptionHandler = reset($exceptionHandlers);
        } else {
            $exceptionHandler = new ChainExceptionHandler($exceptionHandlers);
        }

        return $exceptionHandler->getError($formConfig, $data, $e);
    }
}
