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

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * @author Christian Flothmann <christian.flothmann@qossmic.com>
 */
class FormExceptionHandler
{
    use ExceptionToErrorMapperTrait;

    private ?TranslatorInterface $translator;
    private ?string $translationDomain;

    public function __construct(ExceptionHandlerRegistry $exceptionHandlerRegistry, TranslatorInterface $translator = null, string $translationDomain = null)
    {
        $this->exceptionHandlerRegistry = $exceptionHandlerRegistry;
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
    }

    /**
     * @param mixed $data
     */
    public function handleException(FormInterface $form, $data, \Throwable $e): void
    {
        if (null !== $error = $this->mapExceptionToError($form->getConfig(), $data, $e)) {
            if (null !== $this->translator) {
                $message = $this->translator->trans($error->getMessageTemplate(), $error->getParameters(), $this->translationDomain);
            } else {
                $message = strtr($error->getMessageTemplate(), $error->getParameters());
            }

            $form->addError(new FormError($message, $error->getMessageTemplate(), $error->getParameters(), null, $error->getCause()));
        } else {
            throw $e;
        }
    }
}
