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

namespace SensioLabs\RichModelForms\ExceptionHandling;

use Symfony\Component\Form\FormConfigInterface;

/**
 * @author Christian Flothmann <christian.flothmann@sensiolabs.de>
 *
 * @internal
 */
trait ExceptionToErrorMapperTrait
{
    private $exceptionHandlerRegistry;

    /**
     * @param mixed $data
     */
    private function mapExceptionToError(FormConfigInterface $formConfig, $data, \Throwable $e): ?Error
    {
        $exceptionHandlers = [];

        if (null !== $formConfig->getOption('handle_exception')) {
            foreach ($formConfig->getOption('handle_exception') as $exceptionClass) {
                $exceptionHandlers[] = new GenericExceptionHandler($exceptionClass);
            }

            $exceptionHandlers[] = $this->exceptionHandlerRegistry->get('type_error');
        } else {
            foreach ($formConfig->getOption('exception_handling_strategy') as $strategy) {
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
