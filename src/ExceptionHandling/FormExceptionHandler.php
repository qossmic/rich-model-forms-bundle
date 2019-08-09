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

use Symfony\Component\Form\FormInterface;

/**
 * @author Christian Flothmann <christian.flothmann@sensiolabs.de>
 */
class FormExceptionHandler
{
    private $exceptionHandlerRegistry;

    public function __construct(ExceptionHandlerRegistry $exceptionHandlerRegistry)
    {
        $this->exceptionHandlerRegistry = $exceptionHandlerRegistry;
    }

    public function handleException(FormInterface $form, $data, \Throwable $e): void
    {
        $exceptionHandlers = [];

        if (null !== $form->getConfig()->getOption('handle_exception')) {
            foreach ($form->getConfig()->getOption('handle_exception') as $exceptionClass) {
                $exceptionHandlers[] = new GenericExceptionHandler($exceptionClass);
            }

            $exceptionHandlers[] = $this->exceptionHandlerRegistry->get('type_error');
        } else {
            foreach ($form->getConfig()->getOption('exception_handling_strategy') as $strategy) {
                $exceptionHandlers[] = $this->exceptionHandlerRegistry->get($strategy);
            }
        }

        if (1 === \count($exceptionHandlers)) {
            $exceptionHandler = reset($exceptionHandlers);
        } else {
            $exceptionHandler = new ChainExceptionHandler($exceptionHandlers);
        }

        if (null !== $error = $exceptionHandler->getError($form->getConfig(), $data, $e)) {
            $form->addError($error);
        } else {
            throw $e;
        }
    }
}
