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

use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Converts all exceptions into form errors.
 *
 * The main purpose of this handler is to catch all remaining exceptions to prevent "internal server error" responses.
 * To not leak any sensitive information the error message presented to the user is generic and likely not very helpful
 * to the users. Thus, this handler should only be used as a fallback in a chain of error handlers.
 *
 * @author Christian Flothmann <christian.flothmann@sensiolabs.de>
 */
final class FallbackExceptionHandler implements ExceptionHandler
{
    private $translator;
    private $translationDomain;

    public function __construct(TranslatorInterface $translator = null, string $translationDomain = null)
    {
        $this->translator = $translator;
        $this->translationDomain = $translationDomain;
    }

    public function getError(FormInterface $form, $data, \Throwable $e): ?FormError
    {
        if (!$e instanceof \Exception) {
            return null;
        }

        $messageTemplate = $form->getConfig()->getOption('invalid_message') ?? 'This value is not valid.';
        $parameters = $form->getConfig()->getOption('invalid_message_parameters') ?? [];

        if (null !== $this->translator) {
            $message = $this->translator->trans($messageTemplate, $parameters, $this->translationDomain);
        } else {
            $message = strtr($messageTemplate, $parameters);
        }

        return new FormError($message, $messageTemplate, $parameters, null, $e);
    }
}
